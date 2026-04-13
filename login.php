<?php
include 'connect.php'; 
session_start();

// Nếu đã đăng nhập rồi thì cho vào index luôn
if (isset($_SESSION['ten_dang_nhap'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        // 1. Truy vấn thông tin tài khoản
        $sql = "SELECT * FROM tai_khoan WHERE ten_dang_nhap = '$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // 2. Kiểm tra mật khẩu (hỗ trợ cả mật khẩu mã hóa hash và mật khẩu thô)
            $check_pass = false;
            if (password_verify($password, $user['mat_khau'])) {
                $check_pass = true; 
            } elseif ($password === $user['mat_khau']) {
                $check_pass = true; 
            }

            if ($check_pass) {
                // ĐĂNG NHẬP THÀNH CÔNG
                $_SESSION['ten_dang_nhap'] = $user['ten_dang_nhap'];
                $_SESSION['vai_tro'] = $user['vai_tro'];
                
                // 3. GÁN MÃ NHÂN VIÊN VÀO SESSION (Dùng để lọc nhiệm vụ sửa chữa)
                // Ưu tiên dùng cột ma_nv, nếu trống thì lấy tạm ten_dang_nhap
                $_SESSION['ma_nv'] = !empty($user['ma_nv']) ? $user['ma_nv'] : $user['ten_dang_nhap'];

                // Lưu thêm họ tên nếu có trong bảng tài khoản để hiển thị trên Header
                if (isset($user['ho_ten'])) {
                    $_SESSION['ho_ten'] = $user['ho_ten'];
                }

                header("Location: index.php");
                exit();
            } else {
                $error = "Mật khẩu không chính xác!";
            }
        } else {
            $error = "Tên đăng nhập không tồn tại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống KTX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .login-card { width: 100%; max-width: 400px; padding: 20px; border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: white; }
        .btn-primary { background-color: #2c3e50; border: none; }
        .btn-primary:hover { background-color: #1a252f; }
    </style>
</head>
<body>

<div class="card login-card">
    <div class="card-body">
        <div class="text-center mb-4">
            <i class="bi bi-building-lock fs-1 text-primary"></i>
            <h3 class="fw-bold mt-2">ĐĂNG NHẬP</h3>
            <p class="text-muted small">Hệ thống Quản lý Ký túc xá</p>
        </div>

        <?php if ($error != ""): ?>
            <div class="alert alert-danger py-2 small text-center"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Mã nhân viên / Username" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-bold">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
                </div>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100 py-2 fw-bold">ĐĂNG NHẬP</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>