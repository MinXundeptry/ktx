<?php 
include '../header.php'; 
include '../connect.php'; 

$message = "";

// 1. Xử lý khi nhấn nút Tạo hóa đơn
if (isset($_POST['btn_tao'])) {
    $ma_phong = mysqli_real_escape_string($conn, $_POST['ma_phong']);
    $thang = (int)$_POST['thang'];
    $nam = (int)$_POST['nam'];
    $tien_phong = (int)$_POST['tien_phong'];
    $dien_cu = (int)$_POST['chi_so_dien_cu'];
    $dien_moi = (int)$_POST['chi_so_dien_moi'];
    $nuoc_cu = (int)$_POST['chi_so_nuoc_cu'];
    $nuoc_moi = (int)$_POST['chi_so_nuoc_moi'];
    $tong_tien = (float)$_POST['tong_tien_input'];

    // INSERT khớp chính xác với bảng hoa_don trong file SQL của bạn
    $sql = "INSERT INTO hoa_don (ma_phong, thang, nam, tien_phong, chi_so_dien_cu, chi_so_dien_moi, chi_so_nuoc_cu, chi_so_nuoc_moi, tong_tien, trang_thai) 
            VALUES ('$ma_phong', $thang, $nam, $tien_phong, $dien_cu, $dien_moi, $nuoc_cu, $nuoc_moi, $tong_tien, 'Chưa thanh toán')";

    if (mysqli_query($conn, $sql)) {
        $message = "<div class='alert alert-success shadow-sm border-0 rounded-4'>✅ Đã tạo hóa đơn thành công cho Phòng $ma_phong!</div>";
    } else {
        $message = "<div class='alert alert-danger shadow-sm border-0 rounded-4'>❌ Lỗi: " . mysqli_error($conn) . "</div>";
    }
}

// 2. Lấy danh sách phòng và ĐẾM số người đang ở thực tế
$sql_phong = "SELECT p.*, COUNT(s.ma_sv) as so_nguoi_hien_tai 
              FROM phong p 
              LEFT JOIN sinh_vien s ON p.ma_phong = s.ma_phong 
              GROUP BY p.ma_phong";
$res_phong = mysqli_query($conn, $sql_phong);
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom-0 text-center">
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-calculator me-2"></i>TẠO HÓA ĐƠN THEO PHÒNG</h5>
                </div>
                <div class="card-body p-4">
                    <?= $message ?>
                    
                    <form method="POST" id="formHoaDon">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Chọn phòng nội trú</label>
                                <select name="ma_phong" id="select_phong" class="form-select rounded-3 border-0 bg-light" required onchange="updateGiaPhong()">
                                    <option value="" data-gia="0" data-count="0">-- Chọn phòng --</option>
                                    <?php while($p = mysqli_fetch_assoc($res_phong)): ?>
                                        <option value="<?= $p['ma_phong'] ?>" 
                                                data-gia="<?= $p['gia_phong'] ?>" 
                                                data-count="<?= $p['so_nguoi_hien_tai'] ?>">
                                            <?= $p['ten_phong'] ?> (Đang ở: <?= $p['so_nguoi_hien_tai'] ?> người)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Tháng</label>
                                <input type="number" name="thang" class="form-control border-0 bg-light" value="<?= date('m') ?>" min="1" max="12">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Năm</label>
                                <input type="number" name="nam" class="form-control border-0 bg-light" value="<?= date('Y') ?>">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase text-muted">Tổng tiền thuê phòng (Giá x Số người)</label>
                                <input type="number" name="tien_phong" id="tien_phong" class="form-control fw-bold text-primary border-0 bg-light" readonly value="0">
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 rounded-4 bg-light border-0">
                                    <h6 class="fw-bold text-warning mb-3"><i class="bi bi-lightning-charge-fill"></i> CHỈ SỐ ĐIỆN</h6>
                                    <div class="mb-2">
                                        <label class="small text-muted">Chỉ số cũ</label>
                                        <input type="number" name="chi_so_dien_cu" id="dien_cu" class="form-control border-0" value="0" oninput="tinhToan()">
                                    </div>
                                    <div>
                                        <label class="small text-muted">Chỉ số mới</label>
                                        <input type="number" name="chi_so_dien_moi" id="dien_moi" class="form-control border-0" value="0" oninput="tinhToan()">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 rounded-4 bg-light border-0">
                                    <h6 class="fw-bold text-info mb-3"><i class="bi bi-droplet-fill"></i> CHỈ SỐ NƯỚC</h6>
                                    <div class="mb-2">
                                        <label class="small text-muted">Chỉ số cũ</label>
                                        <input type="number" name="chi_so_nuoc_cu" id="nuoc_cu" class="form-control border-0" value="0" oninput="tinhToan()">
                                    </div>
                                    <div>
                                        <label class="small text-muted">Chỉ số mới</label>
                                        <input type="number" name="chi_so_nuoc_moi" id="nuoc_moi" class="form-control border-0" value="0" oninput="tinhToan()">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="card border-0 bg-primary text-white p-3 rounded-4 mt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h5 mb-0 fw-light">Tổng số tiền hóa đơn:</span>
                                        <h3 class="mb-0 fw-bold" id="hien_thi_tong">0 VNĐ</h3>
                                    </div>
                                    <input type="hidden" name="tong_tien_input" id="tong_tien_input" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 d-grid">
                            <button type="submit" name="btn_tao" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm py-3">
                                <i class="bi bi-check2-all me-2"></i>XUẤT HÓA ĐƠN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const GIA_DIEN = 3500;
const GIA_NUOC = 10000;

function updateGiaPhong() {
    const select = document.getElementById('select_phong');
    const option = select.options[select.selectedIndex];
    
    // Lấy đơn giá và số người từ thuộc tính data
    const giaDonVi = parseFloat(option.getAttribute('data-gia')) || 0;
    const soNguoi = parseInt(option.getAttribute('data-count')) || 0;
    
    // Tự động nhân tiền phòng
    const tongTienPhong = giaDonVi * soNguoi;
    
    document.getElementById('tien_phong').value = tongTienPhong;
    tinhToan();
}

function tinhToan() {
    let tienPhong = parseFloat(document.getElementById('tien_phong').value) || 0;
    
    let dienCu = parseFloat(document.getElementById('dien_cu').value) || 0;
    let dienMoi = parseFloat(document.getElementById('dien_moi').value) || 0;
    let soDien = (dienMoi > dienCu) ? (dienMoi - dienCu) : 0;

    let nuocCu = parseFloat(document.getElementById('nuoc_cu').value) || 0;
    let nuocMoi = parseFloat(document.getElementById('nuoc_moi').value) || 0;
    let soNuoc = (nuocMoi > nuocCu) ? (nuocMoi - nuocCu) : 0;

    let tongTien = tienPhong + (soDien * GIA_DIEN) + (soNuoc * GIA_NUOC);

    document.getElementById('hien_thi_tong').innerText = tongTien.toLocaleString('vi-VN') + " VNĐ";
    document.getElementById('tong_tien_input').value = tongTien;
}
</script>

<?php include '../footer.php'; ?>