<?php
include '../header.php';

// 1. Xử lý xác nhận thanh toán nhanh
if (isset($_GET['action']) && $_GET['action'] == 'pay' && isset($_GET['id'])) {
    $ma_hd = $_GET['id'];
    $sql_pay = "UPDATE hoa_don SET trang_thai = 'Đã thanh toán' WHERE ma_hd = $ma_hd";
    if (mysqli_query($conn, $sql_pay)) {
        echo "<script>alert('Xác nhận thanh toán thành công!'); window.location.href='hoa_don.php';</script>";
    }
}

// 2. Xử lý bộ lọc
$thang_filter = isset($_GET['thang']) ? $_GET['thang'] : date('m');
$nam_filter = isset($_GET['nam']) ? $_GET['nam'] : date('Y');

// 3. Lấy danh sách hóa đơn kèm tên phòng
$sql = "SELECT h.*, p.ten_phong 
        FROM hoa_don h 
        JOIN phong p ON h.ma_phong = p.ma_phong 
        WHERE h.thang = $thang_filter AND h.nam = $nam_filter
        ORDER BY h.ngay_tao DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary"><i class="bi bi-receipt"></i> QUẢN LÝ HÓA ĐƠN</h4>
        <a href="/ktx/invoice/tao_hoa_don.php" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tạo hóa đơn mới
        </a>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Tháng</label>
                    <input type="number" name="thang" class="form-control" value="<?= $thang_filter ?>" min="1" max="12">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Năm</label>
                    <input type="number" name="nam" class="form-control" value="<?= $nam_filter ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Phòng</th>
                        <th>Kỳ hóa đơn</th>
                        <th>Chỉ số Điện (Cũ/Mới)</th>
                        <th>Chỉ số Nước (Cũ/Mới)</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="fw-bold"><?= $row['ten_phong'] ?></td>
                                <td>Tháng <?= $row['thang'] ?>/<?= $row['nam'] ?></td>
                                <td>
                                    <small class="text-muted"><?= $row['chi_so_dien_cu'] ?> → <?= $row['chi_so_dien_moi'] ?></small><br>
                                    <span class="badge bg-warning text-dark"><?= $row['chi_so_dien_moi'] - $row['chi_so_dien_cu'] ?> số</span>
                                </td>
                                <td>
                                    <small class="text-muted"><?= $row['chi_so_nuoc_cu'] ?> → <?= $row['chi_so_nuoc_moi'] ?></small><br>
                                    <span class="badge bg-info text-dark"><?= $row['chi_so_nuoc_moi'] - $row['chi_so_nuoc_cu'] ?> khối</span>
                                </td>
                                <td class="fw-bold text-danger"><?= number_format($row['tong_tien'], 0, ',', '.') ?> VNĐ</td>
                                <td>
                                    <?php if($row['trang_thai'] == 'Đã thanh toán'): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Đã thanh toán</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="bi bi-clock-history"></i> Chưa thanh toán</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($row['trang_thai'] == 'Chưa thanh toán'): ?>
                                        <a href="?action=pay&id=<?= $row['ma_hd'] ?>" 
                                           class="btn btn-sm btn-outline-success" 
                                           onclick="return confirm('Xác nhận phòng này đã nộp tiền?')">
                                            <i class="bi bi-cash-stack"></i> Thu tiền
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i> In</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Không có hóa đơn nào trong tháng này.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>