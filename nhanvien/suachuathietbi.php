<?php 
include '../header.php'; 

// 1. Kiểm tra quyền
if ($role != 'nhan_vien' && $role != 'admin') {
    echo "<script>alert('Bạn không có quyền truy cập!'); window.location='../index.php';</script>";
    exit();
}

// 2. Lấy thông tin định danh từ Session
$username = isset($_SESSION['ten_dang_nhap']) ? $_SESSION['ten_dang_nhap'] : '';

// 3. TRUY VẤN LẤY HỌ TÊN NHÂN VIÊN ĐỂ HIỂN THỊ
$ten_hien_thi = "Nhân viên"; // Mặc định nếu không tìm thấy
$res_info = mysqli_query($conn, "SELECT ho_ten FROM nhan_vien WHERE ten_dang_nhap = '$username'");
if ($row_nv = mysqli_fetch_assoc($res_info)) {
    $ten_hien_thi = $row_nv['ho_ten'];
}

// 4. TRUY VẤN DANH SÁCH NHIỆM VỤ
// Lọc những yêu cầu có ma_nv trùng với mã của nhân viên đang đăng nhập
$sql = "SELECT yc.*, sv.ho_ten as ten_sv, p.ten_phong
        FROM yeu_cau_sua_chua yc
        LEFT JOIN sinh_vien sv ON yc.ma_sv = sv.ma_sv
        LEFT JOIN phong p ON yc.ma_phong = p.ma_phong
        WHERE yc.ma_nv IN (SELECT ma_nv FROM nhan_vien WHERE ten_dang_nhap = '$username')
        OR yc.ten_dang_nhap = '$username'
        ORDER BY CASE 
            WHEN yc.trang_thai = 'Đang xử lý' THEN 1 
            WHEN yc.trang_thai = 'Chờ xử lý' THEN 2 
            ELSE 3 END, yc.ngay_gui DESC";

$result = mysqli_query($conn, $sql);
?>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-primary mb-1"><i class="bi bi-wrench-adjustable"></i> NHIỆM VỤ SỬA CHỮA</h4>
                <p class="text-muted small mb-0">Danh sách các thiết bị hỏng cần xử lý</p>
            </div>
            <span class="badge bg-primary px-3 py-2">Nhân viên: <?= $ten_hien_thi ?></span>
        </div>

        <?php if(isset($_GET['status'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i> Cập nhật trạng thái thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-3">
            <?php if (mysqli_num_rows($result) == 0): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-muted">Bạn chưa có nhiệm vụ nào được phân công.</p>
                </div>
            <?php endif; ?>

            <?php while($row = mysqli_fetch_assoc($result)): 
                // Định nghĩa màu sắc theo trạng thái
                $status_class = 'border-secondary';
                $bg_status = 'bg-secondary';
                
                if($row['trang_thai'] == 'Chờ xử lý') {
                    $status_class = 'border-warning'; $bg_status = 'bg-warning text-dark';
                } elseif($row['trang_thai'] == 'Đang xử lý') {
                    $status_class = 'border-info'; $bg_status = 'bg-info text-white';
                } elseif($row['trang_thai'] == 'Đã hoàn thành') {
                    $status_class = 'border-success'; $bg_status = 'bg-success text-white';
                }
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-start border-4 <?= $status_class ?> rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <h5 class="fw-bold mb-0 text-dark">Phòng <?= $row['ten_phong'] ?></h5>
                            <span class="badge <?= $bg_status ?>"><?= $row['trang_thai'] ?></span>
                        </div>
                        <p class="text-muted small mb-3"><i class="bi bi-person"></i> Sinh viên: <?= $row['ten_sv'] ?></p>
                        
                        <div class="bg-light p-2 rounded mb-3">
                            <strong class="small text-uppercase text-muted d-block mb-1">Nội dung hỏng:</strong>
                            <span class="text-danger fw-medium"><?= htmlspecialchars($row['noi_dung_hong']) ?></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="bi bi-clock"></i> <?= date('d/m/Y', strtotime($row['ngay_gui'])) ?></small>
                            <button class="btn btn-sm btn-dark px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#updateModal<?= $row['ma_yc'] ?>">
                                Cập nhật <i class="bi bi-arrow-right-short"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

           <div class="modal fade" id="updateModal<?= $row['ma_yc'] ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="process_sua_chua_nv.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold">Cập nhật xử lý: Phòng <?= $row['ten_phong'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="ma_yc" value="<?= $row['ma_yc'] ?>">
                
                <div class="mb-3">
                    <label class="form-label fw-bold small">TÌNH TRẠNG THIẾT BỊ</label>
                    <select name="tinh_trang" class="form-select shadow-none border-primary">
                        <option value="Tốt" <?= $row['tinh_trang'] == 'Tốt' ? 'selected' : '' ?>>Tốt (Hoạt động bình thường)</option>
                        <option value="Đang sửa" <?= $row['tinh_trang'] == 'Đang sửa' ? 'selected' : '' ?>>Đang sửa</option>
                        <option value="Hỏng" <?= $row['tinh_trang'] == 'Hỏng' ? 'selected' : '' ?>>Hỏng (Cần thay thế/Chờ linh kiện)</option>
                    </select>
                    <div class="form-text small">Tình trạng thực tế của thiết bị tại phòng.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">TRẠNG THÁI XỬ LÝ ĐƠN</label>
                    <select name="trang_thai" class="form-select shadow-none">
                        <option value="Chờ xử lý" <?= $row['trang_thai'] == 'Chờ xử lý' ? 'selected' : '' ?>>Chờ xử lý</option>
                        <option value="Đang xử lý" <?= $row['trang_thai'] == 'Đang xử lý' ? 'selected' : '' ?>>Đang xử lý</option>
                        <option value="Đã hoàn thành" <?= $row['trang_thai'] == 'Đã hoàn thành' ? 'selected' : '' ?>>Đã hoàn thành</option>
                    </select>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold small">PHẢN HỒI / GHI CHÚ</label>
                    <textarea name="phan_hoi_nv" class="form-control shadow-none" rows="4" placeholder="Nhập vật tư đã thay..."><?= htmlspecialchars($row['phan_hoi_nv'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" name="btn_nv_update" class="btn btn-primary px-4 fw-bold">LƯU KẾT QUẢ</button>
            </div>
        </form>
    </div>
</div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include '../footer.php'; ?>