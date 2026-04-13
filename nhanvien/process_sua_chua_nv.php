<?php
include '../connect.php'; 
session_start();

if (isset($_POST['btn_nv_update'])) {
    $ma_yc = mysqli_real_escape_string($conn, $_POST['ma_yc']);
    $trang_thai = mysqli_real_escape_string($conn, $_POST['trang_thai']);
    $phan_hoi = mysqli_real_escape_string($conn, $_POST['phan_hoi_nv']);

    $sql = "UPDATE yeu_cau_sua_chua 
            SET trang_thai = '$trang_thai', 
                phan_hoi_nv = '$phan_hoi' 
            WHERE ma_yc = '$ma_yc'";

    if (mysqli_query($conn, $sql)) {
        header("Location: suachuathietbi.php?status=success");
    } else {
        header("Location: suachuathietbi.php?status=error");
    }
    exit();
}
?>