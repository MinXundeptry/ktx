<?php 
include '../header.php'; 

// Kiểm tra quyền truy cập
if ($role != 'nhan_vien' && $role != 'admin') {
    echo "<script>alert('Bạn không có quyền!'); window.location='../index.php';</script>";
    exit();
}

$sv_info = null;
$error = "";
$success = "";

// 1. Xử lý Tìm kiếm sinh viên (Bước 4, 5, 6 trong biểu đồ)
if (isset($_POST['btn_search'])) {
    $ma_sv = mysqli_real_escape_string($conn, $_POST['ma_sv']);
    $sql = "SELECT * FROM sinh_vien WHERE ma_sv = '$ma_sv'";
    $res = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($res) > 0) {
        $sv_info = mysqli_fetch_assoc($res);
    } else {
        $error = "Không tìm thấy sinh viên có mã: $ma_sv"; // Bước 7.1 & 7.2
    }
}

// 2. Xử lý Ghi nhận Ra/Vào (Bước 8, 9 trong biểu đồ)
if (isset($_POST['btn_save'])) {
    $ma_sv = $_POST['ma_sv_save'];
    $loai = $_POST['loai_hinh']; // 'ra' hoặc 'vao'
    $ly_do = mysqli_real_escape_string($conn, $_POST['ly_do']);
    $now = date('Y-m-d H:i:s');

    if ($loai == 'ra') {
        // Ghi mới lượt ra
        $sql_save = "INSERT INTO lich_su_ra_vao (ma_sv, thoi_gian_ra, ly_do) VALUES ('$ma_sv', '$now', '$ly_do')";
    } else {
        // Cập nhật lượt vào cho bản ghi gần nhất đang trống thoi_gian_vao
        $sql_check = "SELECT id FROM lich_su_ra_vao WHERE ma_sv = '$ma_sv' AND thoi_gian_vao IS NULL ORDER BY id DESC LIMIT 1";
        $check_res = mysqli_query($conn, $sql_check);
        
        if (mysqli_num_rows($check_res) > 0) {
            $row_id = mysqli_fetch_assoc($check_res)['id'];
            $sql_save = "UPDATE lich_su_ra_vao SET thoi_gian_vao = '$now' WHERE id = '$row_id'";
        } else {
            // Nếu không thấy lượt ra trước đó, tạo bản ghi mới chỉ có giờ vào
            $sql_save = "INSERT INTO lich_su_ra_vao (ma_sv, thoi_gian_vao, ly_do) VALUES ('$ma_sv', '$now', '$ly_do')";
        }
    }

    if (mysqli_query($conn, $sql_save)) {
        $success = "Đã ghi nhận thành công!"; // Bước 9.4 & 9.5
    } else {
        $error = "Lỗi hệ thống: " . mysqli_error($conn); // Bước 9.6 & 9.7
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h3 class="fw-bold text-primary mb-4 text-center"><i class="bi bi-shield-lock me-2"></i>KIỂM SOÁT RA VÀO</h3>

                <form method="POST" class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="ma_sv" class="form-control border-start-0 shadow-none" placeholder="Nhập Mã Sinh Viên để kiểm tra..." required value="<?= $_POST['ma_sv'] ?? '' ?>">
                        <button name="btn_search" class="btn btn-primary px-4 fw-bold">TÌM KIẾM</button>
                    </div>
                </form>

                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 rounded-3"><?= $error ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success border-0 rounded-3"><?= $success ?></div>
                <?php endif; ?>

                <?php if ($sv_info): ?>
                <div class="bg-light p-4 rounded-4 border">
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-person-fill fs-3"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h5 class="fw-bold mb-0"><?= $sv_info['ho_ten'] ?></h5>
                            <span class="text-muted">Mã SV: <?= $sv_info['ma_sv'] ?> | Phòng: <?= $sv_info['ma_phong'] ?></span>
                        </div>
                    </div>

                    <hr>

                    <form method="POST">
                        <input type="hidden" name="ma_sv_save" value="<?= $sv_info['ma_sv'] ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="fw-bold mb-1">Loại hình</label>
                                <select name="loai_hinh" class="form-select border-primary shadow-sm">
                                    <option value="ra">Ghi nhận RA KTX</option>
                                    <option value="vao">Ghi nhận VÀO KTX</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold mb-1">Lý do</label>
                                <input type="text" name="ly_do" class="form-control border-primary shadow-sm" placeholder="Ví dụ: Đi học, Về quê..." required>
                            </div>
                            <div class="col-12 mt-4">
                                <button name="btn_save" class="btn btn-dark w-100 py-2 fw-bold rounded-3">XÁC NHẬN GHI NHẬN</button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-4 p-4">
                <h6 class="fw-bold mb-3">Lịch sử ra vào hôm nay</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mã SV</th>
                                <th>Giờ ra</th>
                                <th>Giờ vào</th>
                                <th>Lý do</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $today = date('Y-m-d');
                            $logs = mysqli_query($conn, "SELECT * FROM lich_su_ra_vao WHERE DATE(thoi_gian_ra) = '$today' OR DATE(thoi_gian_vao) = '$today' ORDER BY id DESC LIMIT 5");
                            while($l = mysqli_fetch_assoc($logs)): ?>
                            <tr>
                                <td><?= $l['ma_sv'] ?></td>
                                <td class="text-danger"><?= $l['thoi_gian_ra'] ? date('H:i', strtotime($l['thoi_gian_ra'])) : '--:--' ?></td>
                                <td class="text-success"><?= $l['thoi_gian_vao'] ? date('H:i', strtotime($l['thoi_gian_vao'])) : '--:--' ?></td>
                                <td class="small"><?= $l['ly_do'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>