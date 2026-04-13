<?php
include '../connect.php'; // Đảm bảo file này chứa kết nối $conn

if (isset($_POST['btn_nv_update'])) {
    // Ép kiểu int để bảo mật và đảm bảo trỏ đúng ID duy nhất
    $ma_yc = intval($_POST['ma_yc']); 
    $trang_thai = $_POST['trang_thai'];
    $tinh_trang = $_POST['tinh_trang']; 
    $phan_hoi = mysqli_real_escape_string($conn, $_POST['phan_hoi_nv']);

    // 1. CẬP NHẬT đúng dòng đang sửa trong bảng yeu_cau_sua_chua
    $sql_update_yc = "UPDATE yeu_cau_sua_chua SET 
                      trang_thai = '$trang_thai', 
                      tinh_trang = '$tinh_trang',
                      phan_hoi_nv = '$phan_hoi' 
                      WHERE ma_yc = $ma_yc";

    if (mysqli_query($conn, $sql_update_yc)) {
        
        // 2. Tìm mã thiết bị liên quan để đồng bộ trạng thái sang bảng thiet_bi
        $res = mysqli_query($conn, "SELECT ma_tb FROM yeu_cau_sua_chua WHERE ma_yc = $ma_yc");
        if ($yc_info = mysqli_fetch_assoc($res)) {
            $ma_tb = $yc_info['ma_tb'];
            
            // Cập nhật bảng gốc thiet_bi để các phòng khác/admin thấy trạng thái mới
            mysqli_query($conn, "UPDATE thiet_bi SET tinh_trang = '$tinh_trang' WHERE ma_tb = '$ma_tb'");
        }
        
        // Chuyển hướng về trang danh sách với thông báo thành công
        header("Location: suachuathietbi.php?status=success");
        exit(); // Cực kỳ quan trọng để dừng script ngay lập tức
    } else {
        echo "Lỗi cập nhật: " . mysqli_error($conn);
    }
}
?>