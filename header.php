<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'connect.php'; 

// Kiểm tra đăng nhập
if (!isset($_SESSION['ten_dang_nhap'])) {
    $role = 'guest';
    $user_display = 'Khách';
} else {
    $role = $_SESSION['vai_tro'];
    $username = $_SESSION['ten_dang_nhap'];
    $user_display = $username;

    if ($role == 'sinh_vien') {
        $sql_sv = "SELECT ho_ten FROM sinh_vien WHERE ten_dang_nhap = '$username'";
        $res_sv = mysqli_query($conn, $sql_sv);
        if ($res_sv && $row_sv = mysqli_fetch_assoc($res_sv)) {
            $user_display = $row_sv['ho_ten'];
        }
    } elseif ($role == 'admin') {
        $user_display = "Quản trị viên";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý KTX - <?= ucfirst($role) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root { --primary-color: #2c3e50; }
        body { background-color: #f4f7f6; }
        .navbar { background-color: var(--primary-color) !important; }
        .main-content { min-height: 80vh; padding: 30px 0; }
        .nav-link { font-weight: 500; }
        .dropdown-menu { border: none; shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/ktx/index.php"><i class="bi bi-building"></i> KTX MANAGER</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/ktx/index.php">Trang chủ</a></li>

                <?php if($role == 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Quản lý nội trú</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/ktx/room/phong.php"><i class="bi bi-door-open me-2"></i>Phòng nội trú</a></li>
                            <li><a class="dropdown-item" href="/ktx/student/sinh_vien.php"><i class="bi bi-people me-2"></i>Sinh viên</a></li>
                            <li><a class="dropdown-item" href="/ktx/admin/quan_ly_sua_chua.php"><i class="bi bi-check-circle me-2"></i>Quản lý sửa chữa</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/ktx/admin/duyet_dang_ky.php"><i class="bi bi-check-circle me-2"></i>Duyệt đăng ký mới</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Tài chính</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/ktx/invoice/hoa_don.php"><i class="bi bi-card-list me-2"></i>Danh sách hóa đơn</a></li>
                            <li><a class="dropdown-item" href="/ktx/invoice/tao_hoa_don.php"><i class="bi bi-plus-circle me-2"></i>Tạo hóa đơn mới</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/ktx/report/bao_cao_thong_ke.php"><i class="bi bi-graph-up me-2"></i>Báo cáo doanh thu</a></li>
                        </ul>
                    </li>

                <?php elseif($role == 'sinh_vien'): ?>
                    <li class="nav-item"><a class="nav-link" href="/ktx/student/dang_ky_noi_tru.php">Đăng ký ở</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ktx/phong_cua_toi.php">Phòng của tôi</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ktx/sua_chua.php">Báo hỏng</a></li>
                <?php endif; ?>

                <?php if($role != 'guest'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle btn btn-outline-light btn-sm ms-lg-3 px-3 mt-2 mt-lg-0" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> <?= $user_display ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="/ktx/profile.php"><i class="bi bi-person me-2"></i>Hồ sơ</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/ktx/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/ktx/login.php">Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container main-content">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>