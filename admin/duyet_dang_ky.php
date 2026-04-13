<?php 
include '../header.php'; 
include '../connect.php'; 
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Xử lý Duyệt hoặc Từ chối
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id']; 
    $action = $_GET['action'];

    if ($action == 'approve') {
        $sql_don = "SELECT * FROM dang_ky_noi_tru WHERE ma_dk = '$id'";
        $res_don = mysqli_query($conn, $sql_don);
        $don = mysqli_fetch_assoc($res_don);
        
        if ($don) {
            $ma_phong = $don['ma_phong'];
            $ma_sv = $don['ma_sv'];

            // 1. Cập nhật trạng thái đơn thành "Đã duyệt"
            mysqli_query($conn, "UPDATE dang_ky_noi_tru SET trang_thai = 'Đã duyệt' WHERE ma_dk = '$id'");
            
            // 2. Cập nhật mã phòng cho sinh viên mới được duyệt
            mysqli_query($conn, "UPDATE sinh_vien SET ma_phong = '$ma_phong' WHERE ma_sv = '$ma_sv'");
            
            // 3. LOGIC TỰ ĐỘNG CẬP NHẬT LẠI SỐ GIƯỜNG TRỐNG CHUẨN
            // Đếm số sinh viên thực tế đang ở phòng này sau khi thêm người mới
            $sql_count = "SELECT COUNT(*) as total FROM sinh_vien WHERE ma_phong = '$ma_phong'";
            $res_count = mysqli_query($conn, $sql_count);
            $row_count = mysqli_fetch_assoc($res_count);
            $so_nguoi_dang_o = $row_count['total'];

            // Lấy số giường tối đa của phòng đó
            $sql_room_info = "SELECT so_giuong_toi_da FROM phong WHERE ma_phong = '$ma_phong'";
            $res_room_info = mysqli_query($conn, $sql_room_info);
            $room_info = mysqli_fetch_assoc($res_room_info);
            $max_giuong = $room_info['so_giuong_toi_da'];

            // Tính toán lại con số giường trống chính xác
            $giuong_trong_moi = $max_giuong - $so_nguoi_dang_o;

            // Cập nhật lại vào bảng phong
            mysqli_query($conn, "UPDATE phong SET so_giuong_trong = '$giuong_trong_moi' WHERE ma_phong = '$ma_phong'");
            
            echo "<script>alert('Đã phê duyệt và đồng bộ chỗ trống thành công!'); window.location.href='duyet_dang_ky.php';</script>";
        }
    } elseif ($action == 'reject') {
        mysqli_query($conn, "UPDATE dang_ky_noi_tru SET trang_thai = 'Từ chối' WHERE ma_dk = '$id'");
        echo "<script>alert('Đã từ chối đơn!'); window.location.href='duyet_dang_ky.php';</script>";
    }
}

$sql = "SELECT d.ma_dk, d.ngay_dang_ky, d.ma_sv, p.ten_phong, s.ho_ten 
        FROM dang_ky_noi_tru d 
        JOIN phong p ON d.ma_phong = p.ma_phong 
        JOIN sinh_vien s ON d.ma_sv = s.ma_sv 
        WHERE d.trang_thai = 'Chờ duyệt'";
$res = mysqli_query($conn, $sql);
?>

<div class="container py-4">
    <h4 class="fw-bold mb-4">PHÊ DUYỆT ĐƠN ĐĂNG KÝ MỚI</h4>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive p-3">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Ngày gửi</th>
                        <th>Họ tên</th>
                        <th>MSSV</th>
                        <th>Phòng đăng ký</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['ngay_dang_ky'])) ?></td>
                            <td class="fw-bold"><?= $row['ho_ten'] ?></td>
                            <td><?= $row['ma_sv'] ?></td>
                            <td><span class="badge bg-info text-white"><?= $row['ten_phong'] ?></span></td>
                            <td class="text-center">
                                <a href="?action=approve&id=<?= $row['ma_dk'] ?>" 
                                   class="btn btn-sm btn-success rounded-pill px-3" 
                                   onclick="return confirm('Duyệt sinh viên này?')">Duyệt</a>

                                <a href="?action=reject&id=<?= $row['ma_dk'] ?>" 
                                   class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                   onclick="return confirm('Từ chối đơn?')">Từ chối</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Không có đơn nào chờ duyệt.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>