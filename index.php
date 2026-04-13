<?php include 'header.php'; ?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between bg-white p-4 rounded-4 shadow-sm border-start border-primary border-5">
                <div>
                    <h3 class="fw-bold mb-1">Xin chào, <?= $user_display ?>!</h3>
                    <p class="text-muted mb-0">Hệ thống quản lý KTX. Vai trò: <span class="badge bg-primary text-uppercase"><?= $role ?></span></p>
                </div>
                <div class="text-end d-none d-md-block">
                    <span class="fw-bold text-primary"><i class="bi bi-calendar3 me-2"></i><?= date('d/m/Y') ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php if($role == 'admin'): 
            $revenue_res = mysqli_query($conn, "SELECT SUM(tong_tien) as total FROM hoa_don WHERE trang_thai = 'Đã thanh toán'");
            $revenue = mysqli_fetch_assoc($revenue_res)['total'] ?? 0;

            $revenue_months = array_fill(1, 12, 0);
            $res_chart = mysqli_query($conn, "SELECT MONTH(ngay_tao) as thang, SUM(tong_tien) as doanh_thu FROM hoa_don WHERE trang_thai = 'Đã thanh toán' AND YEAR(ngay_tao) = YEAR(CURDATE()) GROUP BY MONTH(ngay_tao)");
            while($row_c = mysqli_fetch_assoc($res_chart)) { $revenue_months[(int)$row_c['thang']] = (float)$row_c['doanh_thu']; }
            $chart_values = json_encode(array_values($revenue_months));
        ?>
            <div class="col-md-3">
                <div class="card card-custom border-0 shadow-sm p-3 text-center">
                    <div class="text-primary mb-2"><i class="bi bi-cash-stack fs-3"></i></div>
                    <small class="text-muted fw-bold d-block">DOANH THU</small>
                    <h4 class="fw-bold mb-0"><?= number_format($revenue) ?>đ</h4>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card card-custom shadow-sm border-0 p-4 mb-4">
                    <h5 class="fw-bold mb-4">Thống kê doanh thu năm <?= date('Y') ?></h5>
                    <canvas id="revenueChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-custom shadow-sm border-0 p-4 bg-dark text-white">
                    <h5 class="fw-bold mb-3">Thao tác Admin</h5>
                    <div class="d-grid gap-2">
                        <a href="invoice/tao_hoa_don.php" class="btn btn-primary text-start"><i class="bi bi-plus-lg me-2"></i>Tạo hóa đơn tháng</a>
                        <a href="room/phong.php" class="btn btn-outline-light text-start"><i class="bi bi-door-open me-2"></i>Quản lý phòng</a>
                    </div>
                </div>
            </div>

        <?php elseif($role == 'nhan_vien'): 
            // 1. Thống kê số liệu
            $res1 = mysqli_query($conn, "SELECT COUNT(*) as total FROM yeu_cau_sua_chua WHERE trang_thai = 'Chờ xử lý'");
            $cho_sua = mysqli_fetch_assoc($res1)['total'] ?? 0;

            $res2 = mysqli_query($conn, "SELECT COUNT(*) as total FROM yeu_cau_sua_chua WHERE trang_thai = 'Đang xử lý'");
            $dang_sua = mysqli_fetch_assoc($res2)['total'] ?? 0;

            $res3 = mysqli_query($conn, "SELECT COUNT(*) as total FROM vi_pham WHERE DATE(ngay_vi_pham) = CURDATE()");
            $vi_pham_moi = mysqli_fetch_assoc($res3)['total'] ?? 0;
        ?>
            <div class="col-md-4">
                <div class="card card-custom border-0 shadow-sm p-3 bg-warning text-dark text-center h-100">
                    <h4 class="fw-bold mb-0"><?= $cho_sua ?></h4>
                    <small class="fw-bold">YÊU CẦU MỚI</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom border-0 shadow-sm p-3 bg-primary text-white text-center h-100">
                    <h4 class="fw-bold mb-0"><?= $dang_sua ?></h4>
                    <small class="fw-bold">ĐANG SỬA CHỮA</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom border-0 shadow-sm p-3 bg-danger text-white text-center h-100">
                    <h4 class="fw-bold mb-0"><?= $vi_pham_moi ?></h4>
                    <small class="fw-bold">VI PHẠM HÔM NAY</small>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card card-custom shadow-sm border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Sửa chữa cần xử lý</h5>
                        <a href="nhanvien/suachuathietbi.php" class="btn btn-sm btn-outline-primary">Tất cả</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr><th>Phòng</th><th>Nội dung</th><th>Ngày gửi</th></tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sc_list = mysqli_query($conn, "SELECT * FROM yeu_cau_sua_chua WHERE trang_thai != 'Đã hoàn thành' ORDER BY ngay_gui DESC LIMIT 5");
                                if(mysqli_num_rows($sc_list) > 0):
                                    while($sc = mysqli_fetch_assoc($sc_list)): ?>
                                    <tr>
                                        <td class="fw-bold"><?= $sc['ma_phong'] ?></td>
                                        <td class="text-truncate" style="max-width: 200px;"><?= $sc['noi_dung_hong'] ?></td>
                                        <td><small><?= date('d/m H:i', strtotime($sc['ngay_gui'])) ?></small></td>
                                    </tr>
                                <?php endwhile; 
                                else: echo "<tr><td colspan='3' class='text-center'>Không có yêu cầu nào</td></tr>";
                                endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-custom shadow-sm border-0 p-4 bg-light h-100">
                    <h5 class="fw-bold mb-4">Lối tắt công việc</h5>
                    <div class="d-grid gap-3">
                        <a href="nhanvien/suachuathietbi.php" class="btn btn-white shadow-sm text-start p-3 rounded-3 border-0">
                            <i class="bi bi-tools text-primary me-3"></i> Danh sách sửa chữa
                        </a>
                        <a href="nhanvien/kiemsoatravao.php" class="btn btn-white shadow-sm text-start p-3 rounded-3 border-0">
                            <i class="bi bi-clock-history text-info me-3"></i> Kiểm soát ra vào
                        </a>
                        <a href="nhanvien/ghivipham.php" class="btn btn-white shadow-sm text-start p-3 rounded-3 border-0">
                            <i class="bi bi-shield-exclamation text-danger me-3"></i> Ghi nhận vi phạm
                        </a>
                        <a href="nhanvien/quanlithietbi.php" class="btn btn-white shadow-sm text-start p-3 rounded-3 border-0">
                            <i class="bi bi-cpu text-success me-3"></i> Quản lý thiết bị
                        </a>
                    </div>
                </div>
            </div>

        <?php elseif($role == 'sinh_vien'): 
            $ma_sv = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ma_sv FROM sinh_vien WHERE ten_dang_nhap = '{$_SESSION['ten_dang_nhap']}'"))['ma_sv'];
            $hd_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM hoa_don WHERE ma_phong = (SELECT ma_phong FROM sinh_vien WHERE ma_sv = '$ma_sv') AND trang_thai = 'Chưa thanh toán'");
            $hd_pending = mysqli_fetch_assoc($hd_res)['total'] ?? 0;
        ?>
            <div class="col-md-6">
                <div class="card card-custom border-0 shadow-sm p-4 h-100 bg-info text-white">
                    <h5 class="fw-bold">Thông tin phòng ở</h5>
                    <p class="mb-3">Xem danh sách bạn cùng phòng và báo hỏng thiết bị.</p>
                    <a href="phong_cua_toi.php" class="btn btn-light fw-bold">VÀO PHÒNG CỦA TÔI</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-custom border-0 shadow-sm p-4 h-100 <?= ($hd_pending > 0) ? 'bg-danger text-white' : 'bg-success text-white' ?>">
                    <h5 class="fw-bold">Hóa đơn & Thanh toán</h5>
                    <p class="mb-3">Bạn đang có <b><?= $hd_pending ?></b> hóa đơn chưa thanh toán.</p>
                    <a href="hoa_don_chi_tiet.php" class="btn btn-light fw-bold">XEM CHI TIẾT</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.card-custom { border-radius: 15px; transition: 0.3s; border: none; }
.card-custom:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
.btn-white { background: white; color: #333; }
.btn-white:hover { background: #f8f9fa; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
if (document.getElementById('revenueChart')) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?= $chart_values ?? '[]' ?>,
                backgroundColor: '#0d6efd',
                borderRadius: 5
            }]
        },
        options: { 
            responsive: true, 
            scales: { 
                y: { beginAtZero: true, ticks: { callback: (v) => v.toLocaleString() + 'đ' } } 
            } 
        }
    });
}
</script>

<?php include 'footer.php'; ?>