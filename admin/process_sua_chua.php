<?php
include '../connect.php'; // Đường dẫn kết nối DB của ông
session_start();

// Kiểm tra quyền Admin (phòng hờ trường hợp truy cập trực tiếp file này)
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['btn_cap_nhat'])) {
    // 1. Lấy dữ liệu từ form Modal
    $ma_yc = mysqli_real_escape_string($conn, $_POST['ma_yc']);
    $trang_thai = mysqli_real_escape_string($conn, $_POST['trang_thai']);
    $ma_nv = mysqli_real_escape_string($conn, $_POST['ma_nv']);
    $phan_hoi = mysqli_real_escape_string($conn, $_POST['phan_hoi_nv']);

    // 2. Câu lệnh SQL cập nhật
    // Chúng ta cập nhật cả trạng thái, nhân viên xử lý và ghi chú
    $sql = "UPDATE yeu_cau_sua_chua 
            SET trang_thai = '$trang_thai', 
                ma_nv = '$ma_nv', 
                phan_hoi_nv = '$phan_hoi' 
            WHERE ma_yc = '$ma_yc'";

    // 3. Thực thi và điều hướng
    if (mysqli_query($conn, $sql)) {
        // Dẫn về trang quản lý với trạng thái thành công
        header("Location: quan_ly_sua_chua.php?status=success");
    } else {
        // Dẫn về trang quản lý với trạng thái lỗi
        header("Location: quan_ly_sua_chua.php?status=error");
    }
    exit();
} else {
    // Nếu không bấm nút mà truy cập thẳng file này thì đuổi về trang chủ
    header("Location: quan_ly_sua_chua.php");
    exit();
}
?>