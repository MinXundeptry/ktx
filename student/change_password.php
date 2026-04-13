<?php 
include '../header.php'; // Lưu ý đường dẫn ../ để quay lại thư mục gốc nếu cần
include '../connect.php'; 

if (!isset($_SESSION['ten_dang_nhap'])) {
    header("Location: ../login.php");
    exit();
}

$ten_dang_nhap = $_SESSION['ten_dang_nhap'];
$success = "";
$error = "";

if (isset($_POST['btn_change_pass'])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // 1. Kiểm tra mật khẩu mới và xác nhận mật khẩu
    if ($new_pass !== $confirm_pass) {
        $error = "Mật khẩu mới và xác nhận không khớp!";
    } elseif (strlen($new_pass) < 6) {
        $error = "Mật khẩu mới phải có ít nhất 6 ký tự!";
    } else {
        // 2. Lấy mật khẩu hiện tại trong bảng tai_khoan
        $sql = "SELECT mat_khau FROM tai_khoan WHERE ten_dang_nhap = '$ten_dang_nhap'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);

        // 3. Xác thực mật khẩu cũ bằng password_verify
        if ($user && password_verify($old_pass, $user['mat_khau'])) {
            // 4. Mã hóa mật khẩu mới và cập nhật
            $new_pass_hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $sql_update = "UPDATE tai_khoan SET mat_khau = '$new_pass_hashed' WHERE ten_dang_nhap = '$ten_dang_nhap'";
            
            if (mysqli_query($conn, $sql_update)) {
                $success = "Đổi mật khẩu thành công!";
            } else {
                $error = "Lỗi hệ thống: " . mysqli_error($conn);
            }
        } else {
            $error = "Mật khẩu cũ không chính xác!";
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 text-center border-0">
                    <h5 class="mb-0 fw-bold text-danger text-uppercase">
                        <i class="bi bi-shield-lock-fill me-2"></i>Đổi Mật Khẩu
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i> <?= $success ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mật khẩu hiện tại</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-key"></i></span>
                                <input type="password" name="old_password" class="form-control border-start-0 bg-light" placeholder="Nhập mật khẩu cũ" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mật khẩu mới</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                                <input type="password" name="new_password" class="form-control border-start-0" placeholder="Ít nhất 6 ký tự" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Xác nhận mật khẩu mới</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-patch-check"></i></span>
                                <input type="password" name="confirm_password" class="form-control border-start-0" placeholder="Nhập lại mật khẩu mới" required>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="btn_change_pass" class="btn btn-danger py-2 fw-bold rounded-pill">
                                CẬP NHẬT NGAY
                            </button>
                            <a href="../profile.php" class="btn btn-light py-2 rounded-pill">
                                <i class="bi bi-arrow-left me-1"></i> Quay lại hồ sơ
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>