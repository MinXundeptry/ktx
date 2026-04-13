<?php
// Vì file này nằm trong thư mục admin/ nên lùi 1 cấp để gọi header
include '../header.php';

// 1. Lấy tham số tháng/năm lọc, mặc định là tháng hiện tại
$thang = isset($_GET['thang']) ? (int)$_GET['thang'] : (int)date('m');
$nam = isset($_GET['nam']) ? (int)$_GET['nam'] : (int)date('Y');

// 2. Truy vấn tổng hợp dữ liệu từ bảng hoa_don
// Loại bỏ cột 'tien_phong' không tồn tại và thay bằng công thức tính toán
$sql_stats = "SELECT 
                COUNT(ma_hd) as tong_don,
                SUM((chi_so_dien_moi - chi_so_dien_cu) * 3500) as tong_tien_dien,
                SUM((chi_so_nuoc_moi - chi_so_nuoc_cu) * 10000) as tong_tien_nuoc,
                SUM(tong_tien) as doanh_thu_tong
              FROM hoa_don 
              WHERE thang = $thang AND nam = $nam AND trang_thai = 'Đã thanh toán'";

$res_stats = mysqli_query($conn, $sql_stats);
$stats = mysqli_fetch_assoc($res_stats);

// Logic: Tiền phòng = Tổng doanh thu - (Tiền điện + Tiền nước)
$tong_dien = $stats['tong_tien_dien'] ?? 0;
$tong_nuoc = $stats['tong_tien_nuoc'] ?? 0;
$doanh_thu_tong = $stats['doanh_thu_tong'] ?? 0;
$tong_tien_phong = $doanh_thu_tong - ($tong_dien + $tong_nuoc);

// 3. Lấy dữ liệu biểu đồ/danh sách doanh thu theo các phòng
$sql_chart = "SELECT p.ten_phong, SUM(h.tong_tien) as doanh_thu 
              FROM hoa_don h 
              JOIN phong p ON h.ma_phong = p.ma_phong 
              WHERE h.thang = $thang AND h.nam = $nam AND h.trang_thai = 'Đã thanh toán'
              GROUP BY p.ma_phong";
$res_chart = mysqli_query($conn, $sql_chart);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary"><i class="bi bi-graph-up-arrow"></i> BÁO CÁO DOANH THU</h4>
        <form class="d-flex gap-2" method="GET">
            <input type="number" name="thang" class="form-control form-control-sm" value="<?= $thang ?>" min="1" max="12" style="width: 80px;">
            <input type="number" name="nam" class="form-control form-control-sm" value="<?= $nam ?>" style="width: 100px;">
            <button type="submit" class="btn btn-primary btn-sm px-3">Xem</button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white p-3 h-100">
                <small class="opacity-75">Tổng doanh thu</small>
                <h3 class="fw-bold mb-0"><?= number_format($doanh_thu_tong, 0, ',', '.') ?>đ</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <small class="text-muted">Tiền phòng (Ước tính)</small>
                <h4 class="fw-bold text-dark mb-0"><?= number_format($tong_tien_phong, 0, ',', '.') ?>đ</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <small class="text-muted">Tiền điện</small>
                <h4 class="fw-bold text-warning mb-0"><?= number_format($tong_dien, 0, ',', '.') ?>đ</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <small class="text-muted">Tiền nước</small>
                <h4 class="fw-bold text-info mb-0"><?= number_format($tong_nuoc, 0, ',', '.') ?>đ</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3">
                    Chi tiết doanh thu thực thu theo phòng (Tháng <?= $thang ?>/<?= $nam ?>)
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Phòng</th>
                                <th class="text-end">Doanh thu (Tổng)</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($res_chart) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($res_chart)): ?>
                                    <tr>
                                        <td><i class="bi bi-door-closed text-secondary me-2"></i><?= $row['ten_phong'] ?></td>
                                        <td class="text-end fw-bold text-success"><?= number_format($row['doanh_thu'], 0, ',', '.') ?> VNĐ</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer"></i> In</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Không có dữ liệu thanh toán trong tháng này.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>