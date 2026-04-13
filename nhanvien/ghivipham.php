<?php 
// 1. Nhúng header (trong header nên đã có session_start() và connect.php)
if (file_exists('../header.php')) {
    include '../header.php'; 
} else {
    die("Lỗi: Không tìm thấy file header.php ở thư mục cha.");
}

// 2. Kiểm tra quyền (nhân viên hoặc admin)
// Lưu ý: Biến $role này phải được lấy từ $_SESSION['vai_tro'] trong header.php
if (!isset($role) || ($role != 'nhan_vien' && $role != 'admin')) {
    echo "<script>alert('Bạn không có quyền truy cập chức năng này!'); window.location='../index.php';</script>";
    exit();
}

$sv_info = null;
$error = "";
$success = "";

// 3. Xử lý tìm kiếm SV
if (isset($_POST['btn_search'])) {
    $ma_sv = mysqli_real_escape_string($conn, trim($_POST['ma_sv']));
    
    // Truy vấn lấy thông tin SV và tên phòng
    $sql = "SELECT s.*, p.ten_phong FROM sinh_vien s 
            LEFT JOIN phong p ON s.ma_phong = p.ma_phong 
            WHERE s.ma_sv = '$ma_sv'";
    $res = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($res) > 0) {
        $sv_info = mysqli_fetch_assoc($res);
    } else {
        $error = "Không tìm thấy sinh viên có mã: <b>$ma_sv</b>";
    }
}

// 4. Xử lý lưu biên bản vi phạm
if (isset($_POST['btn_save_vp'])) {
    $ma_sv = mysqli_real_escape_string($conn, $_POST['ma_sv_save']);
    $noi_dung_vp = mysqli_real_escape_string($conn, $_POST['hinh_thuc_vi_pham']); // Hành vi
    $hinh_thuc_xl = mysqli_real_escape_string($conn, $_POST['hinh_thuc_xl']); // Hình thức xử lý
    $ghi_chu = mysqli_real_escape_string($conn, $_POST['ghi_chu']); // LẤY GHI CHÚ TỪ FORM
    
    $ngay_hien_tai = date('Y-m-d H:i:s');

    $sql_insert = "INSERT INTO vi_pham (ma_sv, ngay_vi_pham, hinh_thuc_vi_pham, hinh_thuc_xu_ly, ghi_chu) 
                   VALUES ('$ma_sv', '$ngay_hien_tai', '$noi_dung_vp', '$hinh_thuc_xl', '$ghi_chu')";
    
    if (mysqli_query($conn, $sql_insert)) {
        $success = "Đã lưu biên bản vi phạm thành công!";
    } else {
        $error = "Lỗi hệ thống: " . mysqli_error($conn);
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-danger py-3">
                    <h5 class="card-title text-white mb-0 fw-bold">
                        <i class="bi bi-exclamation-octagon me-2"></i>LẬP BIÊN BẢN VI PHẠM
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Nhập mã sinh viên vi phạm</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                <input type="text" name="ma_sv" class="form-control shadow-none" 
                                       placeholder="Ví dụ: SV001..." required 
                                       value="<?= isset($_POST['ma_sv']) ? htmlspecialchars($_POST['ma_sv']) : '' ?>">
                                <button name="btn_search" class="btn btn-danger px-4">
                                    <i class="bi bi-search me-1"></i> Kiểm tra
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php if ($error): ?>
                        <div class="alert alert-warning border-0 shadow-sm animate__animated animate__fadeIn"><?= $error ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success border-0 shadow-sm animate__animated animate__fadeIn"><?= $success ?></div>
                    <?php endif; ?>

           <?php if ($sv_info): ?>
<form method="POST">
    <input type="hidden" name="ma_sv_save" value="<?= $sv_info['ma_sv'] ?>">
    
    <div class="mb-3">
        <label class="form-label fw-bold">Hành vi vi phạm</label>
        <textarea name="hinh_thuc_vi_pham" class="form-control shadow-sm" rows="2" 
                  placeholder="Ví dụ: Đi về muộn, sử dụng điện sai quy định..." required></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Ghi chú thêm (nếu có)</label>
        <textarea name="ghi_chu" class="form-control shadow-sm" rows="2" 
                  placeholder="Ví dụ: Sinh viên trình bày lý do đi làm thêm về muộn..."></textarea>
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold">Hình thức xử lý dự kiến</label>
        <select name="hinh_thuc_xl" class="form-select shadow-sm">
            <option value="Nhắc nhở">Nhắc nhở trực tiếp</option>
            <option value="Cảnh cáo">Cảnh cáo bằng văn bản</option>
            <option value="Phạt tiền">Phạt tiền theo nội quy</option>
            <option value="Trừ điểm rèn luyện">Trừ điểm rèn luyện</option>
            <option value="Buộc thôi ở">Đề nghị buộc thôi ở KTX</option>
        </select>
    </div>

    <div class="text-end">
        <button name="btn_save_vp" class="btn btn-danger px-5 fw-bold py-2 shadow-sm">
            <i class="bi bi-file-earmark-check me-2"></i>XÁC NHẬN LƯU BIÊN BẢN
        </button>
    </div>
</form>
<?php endif; ?>
                </div>
            </div>
            

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-muted mb-0"><i class="bi bi-clock-history me-2"></i>CÁC VI PHẠM MỚI GHI NHẬN</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Thời gian</th>
                                <th>Sinh viên</th>
                                <th>Nội dung</th>
                                <th>Xử lý</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $list = mysqli_query($conn, "SELECT v.*, s.ho_ten FROM vi_pham v 
                                                        JOIN sinh_vien s ON v.ma_sv = s.ma_sv 
                                                        ORDER BY v.ngay_vi_pham DESC LIMIT 10");
                            if(mysqli_num_rows($list) > 0):
                                while($r = mysqli_fetch_assoc($list)): ?>
                                <tr>
                                    <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($r['ngay_vi_pham'])) ?></td>
                                    <td>
                                        <div class="fw-bold"><?= $r['ho_ten'] ?></div>
                                        <small class="text-muted"><?= $r['ma_sv'] ?></small>
                                    </td>
                                    <td class="small" style="max-width: 300px;"><?= htmlspecialchars($r['ghi_chu']) ?></td>
                                    <td>
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3">
                                            <?= $r['hinh_thuc_xu_ly'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; 
                            else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu vi phạm nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-danger-subtle { background-color: #f8d7da; }
    .text-danger-subtle { color: #842029; }
    .card { transition: transform 0.2s; }
    .form-control:focus, .form-select:focus { border-color: #dc3545; box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25); }
</style>

<?php include '../footer.php'; ?>