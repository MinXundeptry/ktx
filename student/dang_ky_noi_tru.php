<?php 
include '../header.php'; 
include '../connect.php';

if ($role != 'sinh_vien') {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Bạn không có quyền truy cập trang này.</div></div>";
    include '../footer.php'; exit();
}

$message = "";
$user_id = $_SESSION['ten_dang_nhap'];

// Lấy thông tin chi tiết của sinh viên đang đăng nhập
$sql_sv = "SELECT * FROM sinh_vien WHERE ten_dang_nhap = '$user_id'";
$res_sv = mysqli_query($conn, $sql_sv);
$sv = mysqli_fetch_assoc($res_sv);

// 2. Xử lý khi nhấn nút Gửi đơn
if (isset($_POST['gui_don'])) {
    if(!isset($_POST['ma_phong']) || empty($_POST['ma_phong'])) {
        $message = "<div class='alert alert-danger shadow-sm'>Vui lòng chọn 1 phòng để đăng ký!</div>";
    } else {
        $ma_phong = mysqli_real_escape_string($conn, $_POST['ma_phong']);
        $ma_sv = $sv['ma_sv'];
        $ngay_dk = date('Y-m-d H:i:s');

        // Kiểm tra sinh viên đã có phòng thực tế chưa
        if (!empty($sv['ma_phong'])) {
            $message = "<div class='alert alert-warning shadow-sm'>Bạn đã có phòng nội trú ({$sv['ma_phong']}), không thể đăng ký thêm.</div>";
        } else {
            // Kiểm tra xem có đơn nào đang "Chờ duyệt" của sinh viên này không
            $check_pending = mysqli_query($conn, "SELECT * FROM dang_ky_noi_tru WHERE ma_sv = '$ma_sv' AND trang_thai = 'Chờ duyệt'");
            
            if (mysqli_num_rows($check_pending) > 0) {
                $message = "<div class='alert alert-info shadow-sm'>Bạn đã có một đơn đăng ký đang chờ xét duyệt. Không thể gửi thêm đơn mới.</div>";
            } else {
                
                $sql_insert = "INSERT INTO dang_ky_noi_tru (ma_sv, ma_phong, ngay_dang_ky, trang_thai) 
                               VALUES ('$ma_sv', '$ma_phong', '$ngay_dk', 'Chờ duyệt')";
                
                if (mysqli_query($conn, $sql_insert)) {
                    $message = "<div class='alert alert-success shadow-sm text-center'>
                                    <i class='bi bi-check-circle-fill me-2'></i>Gửi đơn thành công! Vui lòng đợi Admin duyệt.
                                </div>";
                } else {
                    $message = "<div class='alert alert-danger shadow-sm'>Lỗi hệ thống: " . mysqli_error($conn) . "</div>";
                }
            }
        }
    }
}

// 3. Lấy danh sách phòng TRỐNG và PHẢI ĐÚNG GIỚI TÍNH
$gioi_tinh_sv = $sv['gioi_tinh'];
$sql_phong = "SELECT * FROM phong WHERE so_giuong_trong > 0 AND loai_phong = '$gioi_tinh_sv'";
$res_phong = mysqli_query($conn, $sql_phong);
?>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-building-add me-2"></i>ĐĂNG KÝ NỘI TRÚ</h5>
                </div>
                <div class="card-body p-4">
                    <?= $message ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-3"><span class="badge bg-primary me-2">1</span> Xác nhận thông tin</h6>
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="row">
                                    <div class="col-md-6 mb-2">Họ tên: <strong><?= $sv['ho_ten'] ?></strong></div>
                                    <div class="col-md-6 mb-2">Giới tính: <span class="badge bg-info text-dark"><?= $sv['gioi_tinh'] ?></span></div>
                                    <div class="col-md-6">Mã sinh viên: <strong><?= $sv['ma_sv'] ?></strong></div>
                                    <div class="col-md-6">Lớp: <strong><?= $sv['lop'] ?></strong></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-3"><span class="badge bg-primary me-2">2</span> Chọn phòng (Dành cho <?= $sv['gioi_tinh'] ?>)</h6>
                            <div class="row g-3">
                                <?php if(mysqli_num_rows($res_phong) > 0): ?>
                                    <?php while($p = mysqli_fetch_assoc($res_phong)): ?>
                                        <div class="col-md-6">
                                            <input type="radio" name="ma_phong" value="<?= $p['ma_phong'] ?>" class="btn-check" id="room_<?= $p['ma_phong'] ?>" autocomplete="off" required>
                                            <label class="btn btn-outline-primary w-100 p-3 text-start rounded-4 room-label" for="room_<?= $p['ma_phong'] ?>">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold fs-5 text-dark">Phòng <?= $p['ma_phong'] ?></div>
                                                        <div class="small text-muted">Còn trống: <?= $p['so_giuong_trong'] ?> chỗ</div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="fw-bold text-danger"><?= number_format($p['gia_phong']) ?> đ</div>
                                                        <div class="small text-muted">/tháng</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center py-5">
                                        <i class="bi bi-door-closed fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">Rất tiếc, hiện tại không còn phòng trống nào dành cho giới tính <?= $sv['gioi_tinh'] ?>.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" name="gui_don" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-send-check-fill me-2"></i>GỬI ĐƠN ĐĂNG KÝ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Đơn đăng ký gần đây</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php 
                        $ls = mysqli_query($conn, "SELECT * FROM dang_ky_noi_tru WHERE ma_sv = '{$sv['ma_sv']}' ORDER BY ngay_dang_ky DESC LIMIT 8");
                        if(mysqli_num_rows($ls) > 0):
                            while($row = mysqli_fetch_assoc($ls)): 
                                $status_class = ($row['trang_thai'] == 'Đã duyệt') ? 'success' : (($row['trang_thai'] == 'Từ chối') ? 'danger' : 'warning');
                        ?>
                        <li class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold">Phòng <?= $row['ma_phong'] ?></span>
                                <span class="badge rounded-pill bg-<?= $status_class ?>"><?= $row['trang_thai'] ?></span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span>Ngày: <?= date('d/m/Y', strtotime($row['ngay_dang_ky'])) ?></span>
                                <span><?= date('H:i', strtotime($row['ngay_dang_ky'])) ?></span>
                            </div>
                        </li>
                        <?php endwhile; else: ?>
                            <li class="list-group-item py-4 text-center text-muted small">Bạn chưa gửi đơn đăng ký nào.</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="card-footer bg-light border-0 py-3 rounded-bottom-4">
                    <p class="small text-muted mb-0"><i class="bi bi-info-circle me-1"></i> Đơn sẽ được Admin xét duyệt trong vòng 24h-48h.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Style cho các Card chọn phòng */
    .room-label { 
        border: 2px solid #f0f0f0; 
        background-color: #fff;
        transition: all 0.2s ease-in-out; 
    }
    .room-label:hover { 
        background-color: #f8f9fa; 
        border-color: #0d6efd;
        transform: translateY(-3px); 
    }
    /* Khi radio được check, label phía sau nó sẽ đổi màu */
    .btn-check:checked + .room-label {
        border-color: #0d6efd;
        background-color: #e7f1ff;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
    }
    .btn-check:checked + .room-label .text-dark {
        color: #0d6efd !important;
    }
</style>

<?php include '../footer.php'; ?>