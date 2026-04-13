<?php 
include '../header.php'; 
include '../connect.php'; 

// 1. Lấy mã phòng từ URL
$ma_phong = $_GET['id'] ?? '';

if (empty($ma_phong)) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Không tìm thấy mã phòng!</div></div>";
    include '../footer.php'; exit();
}

// 2. Lấy thông tin cơ bản của phòng
$sql_phong = "SELECT * FROM phong WHERE ma_phong = '$ma_phong'";
$res_phong = mysqli_query($conn, $sql_phong);
$p = mysqli_fetch_assoc($res_phong);

// 3. Lấy danh sách sinh viên đang ở phòng này
$sql_sv = "SELECT * FROM sinh_vien WHERE ma_phong = '$ma_phong'";
$res_sv = mysqli_query($conn, $sql_sv);

// 4. Lấy danh sách thiết bị trong phòng
$sql_tb = "SELECT * FROM thiet_bi WHERE ma_phong = '$ma_phong'";
$res_tb = mysqli_query($conn, $sql_tb);
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="phong.php">Sơ đồ phòng</a></li>
        <li class="breadcrumb-item active">Chi tiết phòng <?= $ma_phong ?></li>
      </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3">Thông tin phòng</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><strong>Tên:</strong> <?= $p['ten_phong'] ?></li>
                    <li class="mb-2"><strong>Loại:</strong> <span class="badge bg-info"><?= $p['loai_phong'] ?></span></li>
                    <li class="mb-2"><strong>Giá:</strong> <span class="text-danger fw-bold"><?= number_format($p['gia_phong']) ?>đ</span></li>
                    <li class="mb-2"><strong>Số giường:</strong> <?= ($p['so_giuong_toi_da'] - $p['so_giuong_trong']) ?>/<?= $p['so_giuong_toi_da'] ?></li>
                </ul>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3">Thiết bị phòng</h5>
                <table class="table table-sm small">
                    <thead><tr><th>Tên thiết bị</th><th>Tình trạng</th></tr></thead>
                    <tbody>
                        <?php while($tb = mysqli_fetch_assoc($res_tb)): ?>
                        <tr>
                            <td><?= $tb['ten_tb'] ?></td>
                            <td><span class="badge <?= ($tb['tinh_trang'] == 'Tốt') ? 'bg-success' : 'bg-warning' ?>"><?= $tb['tinh_trang'] ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4">Sinh viên đang lưu trú</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mã SV</th><th>Họ tên</th><th>Lớp</th><th>SĐT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($res_sv) > 0): 
                                while($sv = mysqli_fetch_assoc($res_sv)): ?>
                                <tr>
                                    <td class="fw-bold"><?= $sv['ma_sv'] ?></td>
                                    <td><?= $sv['ho_ten'] ?></td>
                                    <td><?= $sv['lop'] ?></td>
                                    <td><?= $sv['so_dien_thoai'] ?></td>
                                </tr>
                            <?php endwhile; 
                            else: ?>
                                <tr><td colspan="4" class="text-center text-muted">Phòng hiện đang trống.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>