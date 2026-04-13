<?php 
include 'header.php'; 

if ($role != 'sinh_vien') {
    echo "<div class='alert alert-danger'>Bạn không có quyền truy cập.</div>";
    include 'footer.php'; exit();
}

$message = "";
$user_id = $_SESSION['ten_dang_nhap'];

// 1. Lấy thông tin sinh viên
$sv = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ma_sv, ma_phong FROM sinh_vien WHERE ten_dang_nhap = '$user_id'"));
$ma_phong = $sv['ma_phong'];

if (!$ma_phong) {
    echo "<div class='alert alert-warning mt-4'>Bạn cần có phòng trước khi thực hiện báo hỏng thiết bị.</div>";
    include 'footer.php'; exit();
}

// 2. Xử lý khi gửi form
if (isset($_POST['gui_yeu_cau'])) {
    $ma_tb = mysqli_real_escape_string($conn, $_POST['ma_tb']);
    $mo_ta = mysqli_real_escape_string($conn, $_POST['mo_ta_loi']);
    $ngay_gui = date('Y-m-d H:i:s');
    
    // Lấy thông tin từ mảng $sv đã truy vấn ở Bước 1
    $ma_sv = $sv['ma_sv']; 
    // $ma_phong đã có từ Bước 1

    // Bắt đầu giao dịch để đảm bảo tính toàn vẹn dữ liệu
    mysqli_begin_transaction($conn);

    try {
        // Lệnh 1: Cập nhật tình trạng trong bảng thiet_bi
        $sql_update_tb = "UPDATE thiet_bi SET tinh_trang = 'Đang sửa' WHERE ma_tb = '$ma_tb'";
        mysqli_query($conn, $sql_update_tb);

        // Lệnh 2: Chèn vào bảng yêu cầu (Giả sử tên bảng là yeu_cau_sua_chua)
        // Mình thêm cột ma_tb vào đây theo đúng cấu trúc bạn gửi
        $sql_insert_yc = "INSERT INTO yeu_cau_sua_chua 
            (ma_tb, ma_sv, tinh_trang, ma_phong, noi_dung_hong, ngay_gui, trang_thai, ten_dang_nhap) 
            VALUES 
            ('$ma_tb', '$ma_sv', 'Đang sửa', '$ma_phong', '$mo_ta', '$ngay_gui', 'Chưa xử lý', '$user_id')";
        
        if (mysqli_query($conn, $sql_insert_yc)) {
            mysqli_commit($conn);
            $message = "<div class='alert alert-success shadow-sm'><i class='bi bi-check-all'></i> Gửi báo cáo thành công! Ban quản lý đã nhận được thông tin.</div>";
        } else {
            throw new Exception(mysqli_error($conn));
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "<div class='alert alert-danger'>Lỗi xử lý: " . $e->getMessage() . "</div>";
    }
}

// 3. Lấy ID thiết bị nếu được truyền từ trang phong_cua_toi.php
$selected_tb = isset($_GET['id']) ? $_GET['id'] : "";
?>

<div class="row justify-content-center py-4">
    <div class="col-md-7">
        <div class="card card-custom shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold text-danger mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>BÁO CÁO SỰ CỐ THIẾT BỊ</h5>
            </div>
            <div class="card-body p-4">
                <?= $message ?>
                
                <form action="" method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">Phòng hiện tại</label>
                        <input type="text" class="form-control bg-light" value="<?= $ma_phong ?>" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">Chọn thiết bị gặp sự cố</label>
                        <select name="ma_tb" class="form-select border-2" required>
                            <option value="">-- Chọn thiết bị trong danh sách --</option>
                            <?php 
                            $res_tb = mysqli_query($conn, "SELECT * FROM thiet_bi WHERE ma_phong = '$ma_phong' AND tinh_trang = 'Tốt'");
                            while ($tb = mysqli_fetch_assoc($res_tb)):
                                $selected = ($selected_tb == $tb['ma_tb']) ? "selected" : "";
                            ?>
                                <option value="<?= $tb['ma_tb'] ?>" <?= $selected ?>>
                                    <?= $tb['ten_tb'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="form-text mt-2 small text-muted">Lưu ý: Chỉ những thiết bị có tình trạng "Tốt" mới hiện ở đây.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">Mô tả chi tiết lỗi</label>
                        <textarea name="mo_ta_loi" class="form-control border-2" rows="4" placeholder="Ví dụ: Bóng đèn bị nhấp nháy liên tục, Quạt quay chậm và phát ra tiếng kêu..." required></textarea>
                    </div>

                    <div class="alert alert-light border small">
                        <i class="bi bi-info-circle me-1"></i> Sau khi gửi đơn, trạng thái thiết bị sẽ chuyển sang <b>"Đang sửa"</b> trong trang thông tin phòng của bạn.
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3">
                        <a href="phong_cua_toi.php" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left"></i> Quay lại</a>
                        <button type="submit" name="gui_yeu_cau" class="btn btn-danger px-4 rounded-pill fw-bold">
                            <i class="bi bi-megaphone me-2"></i> GỬI YÊU CẦU
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>