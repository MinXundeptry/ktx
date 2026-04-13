<?php 
include '../header.php';

// Kiểm tra quyền truy cập
if (!isset($role) || ($role != 'nhan_vien' && $role != 'admin')) {
    echo "<script>alert('Bạn không có quyền!'); window.location='../index.php';</script>";
    exit();
}

$success = "";
$error = "";

/* ================= 1. XỬ LÝ XÓA (DELETE) ================= */
if (isset($_GET['delete_id'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    // Thực thi lệnh xóa
    $sql_del = "DELETE FROM thiet_bi WHERE ma_tb = '$del_id'";
    if (mysqli_query($conn, $sql_del)) {
        $success = "Đã xóa thiết bị mã #$del_id thành công!";
    } else {
        $error = "Lỗi khi xóa: " . mysqli_error($conn);
    }
}

/* ================= 2. XỬ LÝ CẬP NHẬT (UPDATE) ================= */
if (isset($_POST['btn_update'])) {
    $ma_tb = mysqli_real_escape_string($conn, $_POST['ma_tb']);
    $tinh_trang = mysqli_real_escape_string($conn, $_POST['tinh_trang']);
    $so_luong = (int)$_POST['so_luong'];

    $sql_up = "UPDATE thiet_bi SET tinh_trang='$tinh_trang', so_luong=$so_luong WHERE ma_tb='$ma_tb'";
    if (mysqli_query($conn, $sql_up)) {
        $success = "Cập nhật thiết bị #$ma_tb thành công!";
    } else {
        $error = "Lỗi: " . mysqli_error($conn);
    }
}

/* ================= 3. XỬ LÝ THÊM MỚI (ADD) ================= */
if (isset($_POST['btn_add'])) {
    $ten_tb = mysqli_real_escape_string($conn, $_POST['ten_tb']);
    $ma_phong = mysqli_real_escape_string($conn, $_POST['ma_phong']);
    $sl = (int)$_POST['so_luong'];
    $tt = mysqli_real_escape_string($conn, $_POST['tinh_trang']);

    $sql_in = "INSERT INTO thiet_bi(ten_tb, ma_phong, so_luong, tinh_trang) VALUES('$ten_tb', '$ma_phong', $sl, '$tt')";
    if (mysqli_query($conn, $sql_in)) {
        $success = "Thêm thiết bị mới thành công!";
    } else {
        $error = "Lỗi: " . mysqli_error($conn);
    }
}
?>

<style>
    /* HIỆU ỨNG CSS TÙY CHỈNH */
    :root {
        --primary-color: #4e73df;
        --success-bg: #d1e7dd; --success-text: #0f5132;
        --warning-bg: #fff3cd; --warning-text: #664d03;
        --danger-bg: #f8d7da; --danger-text: #842029;
    }

    /* Bo góc và đổ bóng cho bảng */
    .table-container {
        background: white; border-radius: 15px; overflow: hidden;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }

    .table tbody tr { transition: all 0.2s ease; cursor: default; }
    .table tbody tr:hover { background-color: #f8f9ff !important; transform: scale(1.005); }

    /* Badge trạng thái */
    .badge-status {
        padding: 6px 12px; border-radius: 30px;
        font-weight: 600; font-size: 0.75rem; text-transform: uppercase;
    }
    .status-tot { background: var(--success-bg); color: var(--success-text); }
    .status-cu { background: var(--warning-bg); color: var(--warning-text); }
    .status-hong { background: var(--danger-bg); color: var(--danger-text); }

    /* Nút bấm */
    .btn { border-radius: 10px; font-weight: 600; transition: all 0.3s; }
    .btn-action { width: 32px; height: 32px; padding: 0; line-height: 32px; }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary m-0"><i class="bi bi-cpu-fill me-2"></i>QUẢN LÝ THIẾT BỊ</h3>
        <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
            <i class="bi bi-plus-lg me-1"></i> THÊM THIẾT BỊ
        </button>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-4 p-3 rounded-4">
        <form method="GET" class="row align-items-center g-2">
            <div class="col-auto text-muted small fw-bold text-uppercase">Lọc phòng:</div>
            <div class="col-md-3">
                <select name="filter_phong" class="form-select border-0 bg-light" onchange="this.form.submit()">
                    <option value="">-- Tất cả phòng --</option>
                    <?php 
                    $phongs = mysqli_query($conn,"SELECT ma_phong FROM phong");
                    while($p=mysqli_fetch_assoc($phongs)){
                        $sel = (isset($_GET['filter_phong']) && $_GET['filter_phong']==$p['ma_phong'])?'selected':'';
                        echo "<option $sel value='{$p['ma_phong']}'>Phòng {$p['ma_phong']}</option>";
                    }
                    ?>
                </select>
            </div>
        </form>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="bg-light">
                        <th class="ps-4 py-3">Mã</th>
                        <th>Tên thiết bị</th>
                        <th>Phòng</th>
                        <th>SL</th>
                        <th>Tình trạng</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $where = "";
                    if(isset($_GET['filter_phong']) && $_GET['filter_phong']!=''){
                        $fp = mysqli_real_escape_string($conn,$_GET['filter_phong']);
                        $where = "WHERE ma_phong='$fp'";
                    }
                    $res = mysqli_query($conn, "SELECT * FROM thiet_bi $where ORDER BY ma_tb DESC");

                    while($row = mysqli_fetch_assoc($res)):
                        $st_class = ($row['tinh_trang']=='Tốt')?'status-tot':(($row['tinh_trang']=='Cũ')?'status-cu':'status-hong');
                    ?>
                    <tr>
                        <td class="ps-4 text-muted small">#<?= $row['ma_tb'] ?></td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($row['ten_tb']) ?></td>
                        <td><span class="badge bg-light text-dark border px-2"><?= $row['ma_phong'] ?></span></td>
                        <td><span class="fw-bold"><?= $row['so_luong'] ?></span></td>
                        <td><span class="badge-status <?= $st_class ?>"><?= $row['tinh_trang'] ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary btn-action me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal<?= $row['ma_tb'] ?>" title="Sửa">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="?delete_id=<?= $row['ma_tb'] ?>" 
                               class="btn btn-sm btn-outline-danger btn-action" 
                               onclick="return confirm('Xác nhận xóa thiết bị #<?= $row['ma_tb'] ?>?')" title="Xóa">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
mysqli_data_seek($res, 0); 
while($row = mysqli_fetch_assoc($res)):
?>
<div class="modal fade" id="editModal<?= $row['ma_tb'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Sửa #<?= $row['ma_tb'] ?></h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <input type="hidden" name="ma_tb" value="<?= $row['ma_tb'] ?>">
                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-1">Số lượng</label>
                    <input type="number" name="so_luong" class="form-control bg-light border-0" value="<?= $row['so_luong'] ?>" required min="0">
                </div>
                <div class="mb-1">
                    <label class="small fw-bold text-muted mb-1">Tình trạng</label>
                    <select name="tinh_trang" class="form-select bg-light border-0">
                        <option <?= $row['tinh_trang']=="Tốt"?"selected":"" ?>>Tốt</option>
                        <option <?= $row['tinh_trang']=="Cũ"?"selected":"" ?>>Cũ</option>
                        <option <?= $row['tinh_trang']=="Hỏng"?"selected":"" ?>>Hỏng</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" name="btn_update" class="btn btn-primary w-100 py-2 text-uppercase">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
<?php endwhile; ?>

<div class="modal fade" id="addDeviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="fw-bold mb-0">Thêm thiết bị mới</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-1 text-uppercase">Tên thiết bị</label>
                    <input name="ten_tb" class="form-control bg-light border-0 py-2" placeholder="Ví dụ: Quạt trần Vinawind" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-1 text-uppercase">Vị trí (Phòng)</label>
                    <select name="ma_phong" class="form-select bg-light border-0 py-2">
                        <?php 
                        $p2 = mysqli_query($conn,"SELECT ma_phong FROM phong");
                        while($r = mysqli_fetch_assoc($p2)){
                            echo "<option value='{$r['ma_phong']}'>Phòng {$r['ma_phong']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small fw-bold text-muted mb-1 text-uppercase">Số lượng</label>
                        <input type="number" name="so_luong" class="form-control bg-light border-0 py-2" value="1" min="1">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small fw-bold text-muted mb-1 text-uppercase">Tình trạng</label>
                        <select name="tinh_trang" class="form-select bg-light border-0 py-2">
                            <option>Tốt</option>
                            <option>Cũ</option>
                            <option>Hỏng</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Hủy</button>
                <button name="btn_add" class="btn btn-primary px-4">XÁC NHẬN THÊM</button>
            </div>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>