<?php 
include '../header.php'; 
include '../connect.php'; 

// Chỉ Admin mới có quyền thêm phòng
if ($role != 'admin') {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Bạn không có quyền thực hiện chức năng này.</div></div>";
    include '../footer.php'; exit();
}

$message = "";

if (isset($_POST['btn_them'])) {
    $ma_phong = mysqli_real_escape_string($conn, $_POST['ma_phong']);
    $ten_phong = mysqli_real_escape_string($conn, $_POST['ten_phong']);
    $loai_phong = $_POST['loai_phong'];
    $so_giuong = (int)$_POST['so_giuong'];
    $gia_phong = (double)$_POST['gia_phong'];

    // Kiểm tra xem mã phòng đã tồn tại chưa
    $check = mysqli_query($conn, "SELECT ma_phong FROM phong WHERE ma_phong = '$ma_phong'");
    if (mysqli_num_rows($check) > 0) {
        $message = "<div class='alert alert-danger'>Lỗi: Mã phòng này đã tồn tại!</div>";
    } else {
        // SQL thêm phòng dựa trên cấu trúc bảng 'phong' của bạn
        $sql = "INSERT INTO phong (ma_phong, ten_phong, loai_phong, so_giuong_toi_da, so_giuong_trong, gia_phong, tinh_trang) 
                VALUES ('$ma_phong', '$ten_phong', '$loai_phong', $so_giuong, $so_giuong, $gia_phong, 'Trống')";
        
        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert alert-success'>Thêm phòng mới thành công! <a href='phong.php'>Xem danh sách</a></div>";
        } else {
            $message = "<div class='alert alert-danger'>Lỗi hệ thống: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4 text-primary"><i class="bi bi-plus-circle me-2"></i>THÊM PHÒNG MỚI</h4>
                    <?= $message ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mã phòng (Ví dụ: P301)</label>
                            <input type="text" name="ma_phong" class="form-control" required placeholder="Nhập mã phòng...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tên phòng</label>
                            <input type="text" name="ten_phong" class="form-control" required placeholder="Ví dụ: Phòng 301">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Loại phòng</label>
                                <select name="loai_phong" class="form-select">
                                    <option value="Nam">Phòng Nam</option>
                                    <option value="Nữ">Phòng Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Số giường tối đa</label>
                                <input type="number" name="so_giuong" class="form-control" value="4" min="1" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Giá phòng (VNĐ/Tháng)</label>
                            <input type="number" name="gia_phong" class="form-control" required placeholder="Ví dụ: 500000">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="btn_them" class="btn btn-primary fw-bold py-2 rounded-pill">XÁC NHẬN THÊM</button>
                            <a href="phong.php" class="btn btn-light py-2 rounded-pill">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>