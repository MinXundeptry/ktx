<?php
include '../connect.php';

if (isset($_GET['id'])) {
    $ma_sv = mysqli_real_escape_string($conn, $_GET['id']);

    // Bắt đầu Transaction để xóa sạch ở cả 2 bảng
    mysqli_begin_transaction($conn);

    try {
        // 1. Xóa trong bảng sinh_vien trước
        $sql_sv = "DELETE FROM sinh_vien WHERE ma_sv = '$ma_sv'";
        mysqli_query($conn, $sql_sv);

        // 2. Xóa tài khoản tương ứng trong bảng tai_khoan
        // Vì ten_dang_nhap được tạo bằng ma_sv khi thêm mới
        $sql_tk = "DELETE FROM tai_khoan WHERE ten_dang_nhap = '$ma_sv'";
        mysqli_query($conn, $sql_tk);

        // Nếu cả 2 lệnh trên chạy ok thì mới xác nhận lưu thay đổi
        mysqli_commit($conn);
        echo "<script>alert('Đã xóa sinh viên và tài khoản liên quan!'); window.location.href='sinh_vien.php';</script>";
    } catch (Exception $e) {
        // Nếu có lỗi (ví dụ sinh viên đang có hóa đơn chưa xóa), sẽ khôi phục lại dữ liệu
        mysqli_rollback($conn);
        echo "<script>alert('Lỗi: Không thể xóa sinh viên này do có dữ liệu liên quan (Hóa đơn, vi phạm...)!'); window.location.href='sinh_vien.php';</script>";
    }
} else {
    header('location:sinh_vien.php');
}
?>