    <?php 
    include 'header.php'; 

    // 1. Kiểm tra đăng nhập
    if (!isset($_SESSION['ten_dang_nhap'])) {
        header("Location: login.php");
        exit();
    }

    $ten_dang_nhap = $_SESSION['ten_dang_nhap'];
    $success = "";
    $error = "";

    // 2. Xử lý khi nhấn nút Lưu thay đổi
    if (isset($_POST['btn_update'])) {
        $ho_ten = mysqli_real_escape_string($conn, $_POST['ho_ten']);
        $ngay_sinh = mysqli_real_escape_string($conn, $_POST['ngay_sinh']);
        $gioi_tinh = mysqli_real_escape_string($conn, $_POST['gioi_tinh']);
        $sdt = mysqli_real_escape_string($conn, $_POST['so_dien_thoai']);
        $lop = mysqli_real_escape_string($conn, $_POST['lop']);
        $uu_tien = mysqli_real_escape_string($conn, $_POST['doi_tuong_uu_tien']);

        $sql_update = "UPDATE sinh_vien SET 
                        ho_ten = '$ho_ten',
                        ngay_sinh = '$ngay_sinh',
                        gioi_tinh = '$gioi_tinh',
                        so_dien_thoai = '$sdt',
                        lop = '$lop',
                        doi_tuong_uu_tien = '$uu_tien'
                    WHERE ten_dang_nhap = '$ten_dang_nhap'";

        if (mysqli_query($conn, $sql_update)) {
            $success = "Cập nhật hồ sơ thành công!";
        } else {
            $error = "Lỗi cập nhật: " . mysqli_error($conn);
        }
    }

    // 3. Lấy dữ liệu hiện tại để đổ vào Form
    $sql = "SELECT * FROM sinh_vien WHERE ten_dang_nhap = '$ten_dang_nhap'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);

    if (!$data) {
        echo "<div class='container mt-5'><div class='alert alert-danger'>Không tìm thấy dữ liệu sinh viên.</div></div>";
        include 'footer.php';
        exit();
    }
    ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-pencil-square"></i> CHỈNH SỬA HỒ SƠ CÁ NHÂN</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> <?= $success ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Mã Sinh Viên (Không thể sửa)</label>
                                    <input type="text" class="form-control bg-light" value="<?= $data['ma_sv'] ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Họ và Tên</label>
                                    <input type="text" name="ho_ten" class="form-control" value="<?= htmlspecialchars($data['ho_ten']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Ngày sinh</label>
                                    <input type="date" name="ngay_sinh" class="form-control" value="<?= $data['ngay_sinh'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Giới tính</label>
                                    <select name="gioi_tinh" class="form-select">
                                        <option value="Nam" <?= $data['gioi_tinh'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
                                        <option value="Nữ" <?= $data['gioi_tinh'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Số điện thoại</label>
                                    <input type="text" name="so_dien_thoai" class="form-control" value="<?= $data['so_dien_thoai'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Lớp</label>
                                    <input type="text" name="lop" class="form-control" value="<?= $data['lop'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Đối tượng ưu tiên</label>
                                    <select name="doi_tuong_uu_tien" class="form-select">
                                        <option value="Không" <?= $data['doi_tuong_uu_tien'] == 'Không' ? 'selected' : '' ?>>Không</option>
                                        <option value="Con thương binh" <?= $data['doi_tuong_uu_tien'] == 'Con thương binh' ? 'selected' : '' ?>>Con thương binh</option>
                                        <option value="Vùng sâu vùng xa" <?= $data['doi_tuong_uu_tien'] == 'Vùng sâu vùng xa' ? 'selected' : '' ?>>Vùng sâu vùng xa</option>
                                        <option value="Hộ nghèo" <?= $data['doi_tuong_uu_tien'] == 'Hộ nghèo' ? 'selected' : '' ?>>Hộ nghèo</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Phòng đang ở</label>
                                    <input type="text" class="form-control bg-light" value="<?= $data['ma_phong'] ?? 'Chưa xếp phòng' ?>" readonly>
                                    <div class="form-text">Vui lòng liên hệ quản trị viên để đổi phòng.</div>
                                </div>
                            </div>
                        </div>

                  <hr class="my-4">
<div class="d-flex flex-wrap gap-2 justify-content-between">
    <div>
        <a href="profile.php" class="btn btn-outline-secondary px-4 rounded-pill">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
        <a href="student/change_password.php" class="btn btn-warning px-4 rounded-pill">
            <i class="bi bi-key"></i> Đổi mật khẩu
        </a>
    </div>
    <button type="submit" name="btn_update" class="btn btn-primary px-5 fw-bold rounded-pill shadow-sm">
        <i class="bi bi-save"></i> LƯU THAY ĐỔI
    </button>
</div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>