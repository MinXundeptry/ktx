<?php 
include 'header.php'; 

if ($role != 'sinh_vien') {
    echo "<div class='alert alert-danger'>Chỉ dành cho sinh viên.</div>";
    include 'footer.php'; exit();
}

$user_id = $_SESSION['ten_dang_nhap'];

// 1. Lấy mã phòng của sinh viên hiện tại
$sql_user = "SELECT ma_phong, ma_sv FROM sinh_vien WHERE ten_dang_nhap = '$user_id'";
$res_user = mysqli_query($conn, $sql_user);
$user_data = mysqli_fetch_assoc($res_user);
$ma_phong = $user_data['ma_phong'];

if (!$ma_phong) {
    echo "<div class='alert alert-warning mt-4'>Bạn hiện chưa được xếp phòng. Vui lòng đăng ký nội trú.</div>";
    include 'footer.php'; exit();
}

// 2. Lấy thông tin chi tiết phòng
$phong_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM phong WHERE ma_phong = '$ma_phong'"));
?>

<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold"><i class="bi bi-house-heart text-danger"></i> PHÒNG: <?= $ma_phong ?></h3>
        <p class="text-muted">Thông tin chi tiết về không gian nội trú của bạn.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-people text-primary"></i> DANH SÁCH BẠN CÙNG PHÒNG</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Họ tên</th>
                            <th>Mã SV</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql_roommates = "SELECT ho_ten, ma_sv FROM sinh_vien WHERE ma_phong = '$ma_phong'";
                        $res_roommates = mysqli_query($conn, $sql_roommates);
                        while($row = mysqli_fetch_assoc($res_roommates)):
                        ?>
                        <tr>
                            <td><?= $row['ho_ten'] ?> <?= ($row['ma_sv'] == $user_data['ma_sv']) ? '<span class="badge bg-info text-dark">Tôi</span>' : '' ?></td>
                            <td><?= $row['ma_sv'] ?></td>
                            <td><span class="text-success small"><i class="bi bi-dot fs-4"></i>Đang ở</span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-lamp text-warning"></i> THIẾT BỊ TRONG PHÒNG</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Tên thiết bị</th>
                            <th>Tình trạng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql_tb = "SELECT * FROM thiet_bi WHERE ma_phong = '$ma_phong'";
                        $res_tb = mysqli_query($conn, $sql_tb);
                        if(mysqli_num_rows($res_tb) > 0):
                            while($tb = mysqli_fetch_assoc($res_tb)):
                        ?>
                        <tr>
                            <td class="ps-3"><?= $tb['ten_tb'] ?></td>
                            <td><span class="badge bg-light text-dark border"><?= $tb['tinh_trang'] ?></span></td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="2" class="text-center py-3 text-muted small">Chưa có dữ liệu thiết bị</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
            <div class="card-body">
                <h6 class="small text-uppercase opacity-75">Hóa đơn tháng này</h6>
                <?php 
            // Truy vấn hóa đơn theo ma_phong thay vì ma_sv
                $sql_hd = "SELECT * FROM hoa_don WHERE ma_phong = '$ma_phong' ORDER BY nam DESC, thang DESC LIMIT 1";
                $res_hd = mysqli_query($conn, $sql_hd);
                $hd = mysqli_fetch_assoc($res_hd);
                ?>
                <h2 class="fw-bold mb-1"><?= number_format($hd['tong_tien'] ?? 0) ?> VNĐ</h2>
                <p class="small mb-0">"Hóa đơn tính chung cho cả phòng. Vui lòng thống nhất với các thành viên để thực hiện thanh toán."</p>
                <p class="small mb-3">Tình trạng: <b><?= $hd['trang_thai'] ?? 'N/A' ?></b></p>
                <a href="hoa_don_chi_tiet.php" class="btn btn-light btn-sm w-100 fw-bold">Xem chi tiết & Thanh toán</a>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="fw-bold mb-3 small text-uppercase text-muted">Thao tác nhanh</h6>
                <div class="d-grid gap-2">
                    <a href="sua_chua.php" class="btn btn-outline-danger btn-sm text-start">
                        <i class="bi bi-tools me-2"></i> Báo hỏng thiết bị
                    </a>
                    <a href="quy_dinh.php" class="btn btn-outline-secondary btn-sm text-start">
                        <i class="bi bi-journal-text me-2"></i> Nội quy phòng ở
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>