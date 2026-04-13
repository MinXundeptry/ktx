<?php
include '../header.php';
// Lấy danh sách nhân viên
$res_nv = mysqli_query($conn, "SELECT ma_nv, ho_ten FROM nhan_vien");
$nhan_vien = mysqli_fetch_all($res_nv, MYSQLI_ASSOC);

// Lấy danh sách yêu cầu
$sql = "SELECT yc.*, sv.ho_ten as ten_sv, p.ten_phong, nv.ho_ten as ten_nv
        FROM yeu_cau_sua_chua yc
        JOIN sinh_vien sv ON yc.ma_sv = sv.ma_sv
        JOIN phong p ON yc.ma_phong = p.ma_phong
        LEFT JOIN nhan_vien nv ON yc.ma_nv = nv.ma_nv
        ORDER BY yc.ngay_gui DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container mt-4">
    <h4 class="fw-bold text-primary mb-4"><i class="bi bi-tools"></i> QUẢN LÝ SỬA CHỮA THIẾT BỊ</h4>

    <?php if(isset($_GET['status'])): ?>
        <div class="alert alert-<?= $_GET['status'] == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
            <?= $_GET['status'] == 'success' ? 'Cập nhật thành công!' : 'Có lỗi xảy ra.' ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Phòng / Người gửi</th>
                        <th>Nội dung hỏng</th>
                        <th>Ngày gửi</th>
                        <th>Trạng thái</th>
                        <th>Nhân viên xử lý</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <span class="fw-bold"><?= $row['ten_phong'] ?></span><br>
                                <small class="text-muted"><?= $row['ten_sv'] ?></small>
                            </td>
                            <td><?= htmlspecialchars($row['noi_dung_hong']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['ngay_gui'])) ?></td>
                            <td>
                                <?php 
                                    $class = $row['trang_thai'] == 'Chờ xử lý' ? 'bg-warning text-dark' : ($row['trang_thai'] == 'Đang xử lý' ? 'bg-info text-white' : 'bg-success text-white');
                                ?>
                                <span class="badge <?= $class ?>"><?= $row['trang_thai'] ?></span>
                            </td>
                            <td>
                                <?= !empty($row['ten_nv']) ? $row['ten_nv'] : '<em class="text-muted">Chưa phân công</em>' ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['ma_yc'] ?>">
                                    <i class="bi bi-pencil-square"></i> Xử lý
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
mysqli_data_seek($result, 0); // Reset con trỏ dữ liệu về đầu
while($row = mysqli_fetch_assoc($result)): 
?>
    <div class="modal fade" id="modalEdit<?= $row['ma_yc'] ?>" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="process_sua_chua.php" method="POST" class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Xử lý: <?= $row['ten_phong'] ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="ma_yc" value="<?= $row['ma_yc'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">TRẠNG THÁI</label>
                        <select name="trang_thai" class="form-select">
                            <option value="Chờ xử lý" <?= $row['trang_thai'] == 'Chờ xử lý' ? 'selected' : '' ?>>Chờ xử lý</option>
                            <option value="Đang xử lý" <?= $row['trang_thai'] == 'Đang xử lý' ? 'selected' : '' ?>>Đang xử lý</option>
                            <option value="Đã hoàn thành" <?= $row['trang_thai'] == 'Đã hoàn thành' ? 'selected' : '' ?>>Đã hoàn thành</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">NHÂN VIÊN KỸ THUẬT</label>
                        <select name="ma_nv" class="form-select">
                            <option value="">-- Chọn nhân viên --</option>
                            <?php foreach($nhan_vien as $nv): ?>
                                <option value="<?= $nv['ma_nv'] ?>" <?= $row['ma_nv'] == $nv['ma_nv'] ? 'selected' : '' ?>><?= $nv['ho_ten'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small">GHI CHÚ PHẢN HỒI</label>
                        <textarea name="phan_hoi_nv" class="form-control" rows="3"><?= htmlspecialchars($row['phan_hoi_nv'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" name="btn_cap_nhat" class="btn btn-primary px-4">Lưu dữ liệu</button>
                </div>
            </form>
        </div>
    </div>
<?php endwhile; ?>

<?php include '../footer.php'; ?>