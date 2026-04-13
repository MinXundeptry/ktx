<?php 
include 'header.php'; 

if ($role != 'sinh_vien') {
    echo "<div class='alert alert-danger'>Bạn không có quyền truy cập.</div>";
    include 'footer.php'; exit();
}

$user_id = $_SESSION['ten_dang_nhap'];

// 1. Lấy mã sinh viên và mã phòng
$sv_query = "SELECT ma_sv, ma_phong FROM sinh_vien WHERE ten_dang_nhap = '$user_id'";
$sv_res = mysqli_query($conn, $sv_query);
$sv = mysqli_fetch_assoc($sv_res);
$ma_phong = $sv['ma_phong'];

if (!$ma_phong) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Bạn chưa được xếp phòng nên chưa có hóa đơn.</div></div>";
    include 'footer.php'; exit();
}

// 2. Xử lý lấy thông tin hóa đơn
$id_hd = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : "";
if ($id_hd) {
    $sql_hd = "SELECT * FROM hoa_don WHERE ma_hd = '$id_hd' AND ma_phong = '$ma_phong'";
} else {
    $sql_hd = "SELECT * FROM hoa_don WHERE ma_phong = '$ma_phong' ORDER BY nam DESC, thang DESC LIMIT 1";
}

$res_hd = mysqli_query($conn, $sql_hd);
$hd = mysqli_fetch_assoc($res_hd);

if (!$hd) {
    echo "<div class='container mt-5'><div class='alert alert-info'>Hiện chưa có dữ liệu hóa đơn cho phòng của bạn.</div></div>";
    include 'footer.php'; exit();
}

// 3. Tính toán tiền (Giữ nguyên logic cũ của bạn)
$don_gia_dien = 3000;  
$don_gia_nuoc = 15000; 
$so_dien = max(0, $hd['chi_so_dien_moi'] - $hd['chi_so_dien_cu']);
$so_nuoc = max(0, $hd['chi_so_nuoc_moi'] - $hd['chi_so_nuoc_cu']);
$tien_dien = $so_dien * $don_gia_dien;
$tien_nuoc = $so_nuoc * $don_gia_nuoc;
$tien_phong = $hd['tong_tien'] - ($tien_dien + $tien_nuoc);
?>

<div class="row justify-content-center py-4">
    <div class="col-md-9 col-lg-7">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="phong_cua_toi.php" class="text-decoration-none text-muted small">
                <i class="bi bi-chevron-left"></i> Quay lại trang phòng
            </a>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> In hóa đơn
            </button>
        </div>

        <div class="card shadow-lg border-0 overflow-hidden">
            <div class="card-header bg-primary text-white p-4 text-center">
                <h4 class="fw-bold mb-1 text-uppercase">Hóa Đơn Dịch Vụ KTX</h4>
                <p class="mb-0 opacity-75 small">Mã số: #<?= $hd['ma_hd'] ?> | Kỳ: <?= $hd['thang'] ?>/<?= $hd['nam'] ?></p>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="row mb-4">
                    <div class="col-6">
                        <label class="small text-muted text-uppercase d-block">Đại diện thanh toán</label>
                        <span class="fw-bold"><?= $user_display ?></span>
                        <div class="small text-muted">MSV: <?= $sv['ma_sv'] ?></div>
                    </div>
                    <div class="col-6 text-end">
                        <label class="small text-muted text-uppercase d-block">Phòng ở</label>
                        <span class="fw-bold fs-5"><?= $ma_phong ?></span>
                    </div>
                </div>

                <hr class="my-4 border-dashed">

                <table class="table table-borderless align-middle">
                    <tbody>
                        <tr>
                            <td><div class="fw-bold">Tiền thuê phòng</div></td>
                            <td class="text-end"><?= number_format($tien_phong) ?> đ</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-bold">Tiền điện</div>
                                <div class="small text-muted">Sử dụng: <?= $so_dien ?> số</div>
                            </td>
                            <td class="text-end"><?= number_format($tien_dien) ?> đ</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-bold">Tiền nước</div>
                                <div class="small text-muted">Sử dụng: <?= $so_nuoc ?> khối</div>
                            </td>
                            <td class="text-end"><?= number_format($tien_nuoc) ?> đ</td>
                        </tr>
                        <tr class="border-top">
                            <td class="pt-3 fs-5 fw-bold">TỔNG CỘNG:</td>
                            <td class="pt-3 fs-4 fw-bold text-danger text-end"><?= number_format($hd['tong_tien']) ?> VNĐ</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-5 p-4 rounded-4 bg-light border">
                    <div class="text-center mb-4">
                        <span class="small text-muted d-block text-uppercase fw-bold mb-2">Trạng thái hiện tại:</span>
                        <span class="badge rounded-pill px-4 py-2 <?= ($hd['trang_thai'] == 'Đã thanh toán') ? 'bg-success' : 'bg-danger' ?>">
                            <?= strtoupper($hd['trang_thai']) ?>
                        </span>
                    </div>

                    <?php if ($hd['trang_thai'] != 'Đã thanh toán'): ?>
                        <div id="payment-options">
                            <h6 class="text-center fw-bold mb-3">CHỌN HÌNH THỨC THANH TOÁN</h6>
                            <div class="row g-3">
                                <div class="col-6">
                                    <button onclick="showMethod('qr')" class="btn btn-outline-primary w-100 py-3">
                                        <i class="bi bi-qr-code-scan d-block fs-3 mb-2"></i>
                                        Chuyển khoản
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button onclick="showMethod('cash')" class="btn btn-outline-secondary w-100 py-3">
                                        <i class="bi bi-cash-stack d-block fs-3 mb-2"></i>
                                        Tiền mặt
                                    </button>
                                </div>
                            </div>
                        </div>

                       <div id="method-qr" class="payment-method-content text-center mt-4" style="display:none;">
    <div class="p-3 bg-white rounded shadow-sm d-inline-block border">
        <img src="img/qr.jpg" alt="QR Thanh toán" class="img-fluid" style="max-width: 250px;">
    </div>
    
    <div class="mt-3">
        <p class="mb-1 fw-bold text-primary">Ngân hàng Quân Đội (MB)</p>
        <p class="mb-1">Chủ TK: <strong>LE MINH XUAN</strong></p>
        <p class="mb-0">STK: <strong>010501056868</strong></p>
    </div>
    
    <button onclick="backToOptions()" class="btn btn-link btn-sm mt-3 text-decoration-none">
        <i class="bi bi-arrow-left"></i> Chọn phương thức khác
    </button>
</div>

                        <div id="method-cash" class="payment-method-content text-center mt-4" style="display:none;">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Vui lòng đến <strong>Văn phòng Ban quản lý KTX</strong> để nộp tiền mặt.
                                <br><small>(Mang theo mã hóa đơn: #<?= $hd['ma_hd'] ?>)</small>
                            </div>
                            <button onclick="backToOptions()" class="btn btn-link btn-sm">Chọn phương thức khác</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showMethod(type) {
    document.getElementById('payment-options').style.display = 'none';
    document.querySelectorAll('.payment-method-content').forEach(el => el.style.display = 'none');
    document.getElementById('method-' + type).style.display = 'block';
}

function backToOptions() {
    document.getElementById('payment-options').style.display = 'block';
    document.querySelectorAll('.payment-method-content').forEach(el => el.style.display = 'none');
}
</script>

<style>
    .border-dashed { border-top: 2px dashed #dee2e6; }
    .card { border-radius: 15px; }
    @media print {
        .btn, #header, #footer, .mb-3, #payment-options, .payment-method-content, .btn-link { display: none !important; }
        body { background: white !important; }
        .card { shadow: none !important; border: 1px solid #eee !important; }
    }
</style>

<?php include 'footer.php'; ?>