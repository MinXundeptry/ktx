<?php 
include '../header.php'; 
include '../connect.php'; 

if (isset($_POST['btn_them'])) {
    // 1. Lấy dữ liệu và làm sạch
    $ma_sv = mysqli_real_escape_string($conn, trim($_POST['ma_sv']));
    $ho_ten = mysqli_real_escape_string($conn, trim($_POST['ho_ten']));
    $ngay_sinh = $_POST['ngay_sinh']; 
    $lop = mysqli_real_escape_string($conn, trim($_POST['lop']));
    $gioi_tinh = $_POST['gioi_tinh'];
    $sdt = mysqli_real_escape_string($conn, $_POST['so_dien_thoai']);
    $doi_tuong = mysqli_real_escape_string($conn, $_POST['doi_tuong_uu_tien']);
    
    // Mật khẩu mặc định
    $password_default = password_hash('123456', PASSWORD_DEFAULT);

    // 2. Kiểm tra trùng mã sinh viên
    $check = mysqli_query($conn, "SELECT ma_sv FROM sinh_vien WHERE ma_sv = '$ma_sv'");
    
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Lỗi: Mã sinh viên này đã tồn tại!');</script>";
    } else {
        mysqli_begin_transaction($conn);

        try {
            // Bước A: Chèn vào bảng tai_khoan
            $sql_tk = "INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, vai_tro) 
                       VALUES ('$ma_sv', '$password_default', 'sinh_vien')";
            mysqli_query($conn, $sql_tk);

            // Bước B: Chèn vào bảng sinh_vien (ma_phong để mặc định là NULL)
            $sql_sv = "INSERT INTO sinh_vien (ma_sv, ten_dang_nhap, ho_ten, ngay_sinh, gioi_tinh, so_dien_thoai, lop, ma_phong, doi_tuong_uu_tien) 
                       VALUES ('$ma_sv', '$ma_sv', '$ho_ten', '$ngay_sinh', '$gioi_tinh', '$sdt', '$lop', NULL, '$doi_tuong')";
            
            if(mysqli_query($conn, $sql_sv)) {
                mysqli_commit($conn);
                echo "<script>alert('Thêm sinh viên thành công!'); window.location.href='sinh_vien.php';</script>";
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script>alert('Lỗi hệ thống: " . $e->getMessage() . "');</script>";
        }
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 text-center">
                    <h5 class="fw-bold mb-0 text-primary text-uppercase">Thêm Sinh Viên Mới</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mã Sinh Viên</label>
                                <input type="text" name="ma_sv" class="form-control" placeholder="Nhập mã SV" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và Tên</label>
                                <input type="text" name="ho_ten" class="form-control" placeholder="Nhập họ tên" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày sinh</label>
                                <input type="date" name="ngay_sinh" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Giới tính</label>
                                <select name="gioi_tinh" class="form-select">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Lớp</label>
                                <input type="text" name="lop" class="form-control" placeholder="Ví dụ: CNTT K15" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại</label>
                                <input type="text" name="so_dien_thoai" class="form-control" placeholder="Nhập SĐT">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Đối tượng ưu tiên</label>
                                <select name="doi_tuong_uu_tien" class="form-select">
                                    <option value="Bình thường">Bình thường</option>
                                    <option value="Con thương binh/Liệt sĩ">Con thương binh/Liệt sĩ</option>
                                    <option value="Vùng sâu vùng xa">Vùng sâu vùng xa</option>
                                    <option value="Hộ nghèo/Cận nghèo">Hộ nghèo/Cận nghèo</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 p-2 bg-light rounded text-muted small">
                            <i class="bi bi-info-circle"></i> Tài khoản và mật khẩu mặc định (<strong>123456</strong>) sẽ khớp với Mã SV.
                        </div>

                        <hr class="my-4">
                        <div class="d-flex gap-2">
                            <button type="submit" name="btn_them" class="btn btn-primary px-4 rounded-pill shadow-sm">
                                <i class="bi bi-check-lg"></i> Lưu sinh viên
                            </button>
                            <a href="sinh_vien.php" class="btn btn-outline-secondary px-4 rounded-pill">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>