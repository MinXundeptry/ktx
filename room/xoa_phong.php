<?php 
include '../connect.php'; 

$id = $_GET['id'] ?? '';

// Kiểm tra xem phòng có sinh viên không
$check_sv = mysqli_query($conn, "SELECT COUNT(*) as total FROM sinh_vien WHERE ma_phong = '$id'");
$has_sv = mysqli_fetch_assoc($check_sv)['total'];

if ($has_sv > 0) {
    echo "<script>alert('Không thể xóa! Phòng đang có sinh viên ở.'); window.location.href='phong.php';</script>";
} else {
    mysqli_query($conn, "DELETE FROM phong WHERE ma_phong = '$id'");
    header("Location: phong.php");
}
?>