<?php 
include '../header.php'; 
include '../connect.php'; 

// 1. Thống kê theo trạng thái hóa đơn
$stats_bill = mysqli_query($conn, "SELECT trang_thai, COUNT(*) as sl, SUM(tong_tien) as tien FROM hoa_don GROUP BY trang_thai");

// 2. Thống kê loại phòng (Nam/Nữ)
$stats_room = mysqli_query($conn, "SELECT loai_phong, COUNT(*) as sl FROM phong GROUP BY loai_phong");
?>

<div class="container py-4">
    <h4 class="fw-bold mb-4">BÁO CÁO & THỐNG KÊ CHI TIẾT</h4>

    <div class="row g-4 mb-4">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3 small text-muted">TÌNH HÌNH THU TIỀN PHÒNG</h5>
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Trạng thái</th><th>Số lượng đơn</th><th>Tổng số tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($s = mysqli_fetch_assoc($stats_bill)): ?>
                        <tr>
                            <td>
                                <span class="badge <?= $s['trang_thai']=='Đã thanh toán'?'bg-success':'bg-danger' ?> rounded-pill">
                                    <?= $s['trang_thai'] ?>
                                </span>
                            </td>
                            <td><?= $s['sl'] ?> đơn</td>
                            <td class="fw-bold"><?= number_format($s['tien']) ?>đ</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3 small text-muted">CƠ CẤU PHÒNG NỘI TRÚ</h5>
                <?php while($r = mysqli_fetch_assoc($stats_room)): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Phòng <?= $r['loai_phong'] ?></span>
                    <span class="badge bg-primary fs-6"><?= $r['sl'] ?> phòng</span>
                </div>
                <?php endwhile; ?>
                <hr>
                <div class="alert alert-info border-0 mb-0">
                    <small>Mẹo: Admin nên theo dõi tỷ lệ hóa đơn treo để nhắc nhở sinh viên nộp tiền đúng hạn.</small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../footer.php'; ?>