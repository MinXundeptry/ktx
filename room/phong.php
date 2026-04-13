<?php 
include '../header.php';
include '../connect.php'; 

// Kiểm tra quyền (Chỉ Admin và Nhân viên mới được xem sơ đồ chi tiết)
if ($role == 'sinh_vien') {
    header("Location: ../index.php");
    exit();
}

// --- PHẦN XỬ LÝ PHÂN TRANG ---
$limit = 8; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$start = ($page - 1) * $limit;

$total_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM phong");
$total_rooms = mysqli_fetch_assoc($total_res)['total'];
$total_pages = ceil($total_rooms / $limit);

$sql = "SELECT * FROM phong ORDER BY ma_phong ASC LIMIT $start, $limit";
$res = mysqli_query($conn, $sql);
?>

<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-uppercase">Sơ đồ phòng nội trú</h4>
            <p class="text-muted small mb-0">Quản lý danh sách và tình trạng phòng (Trang <?= $page ?>/<?= $total_pages ?>)</p>
        </div>
        
        <?php if($role == 'admin'): ?>
        <div class="dropdown">
            <button class="btn btn-primary rounded-pill shadow-sm dropdown-toggle px-4" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-gear-fill me-2"></i>Chức năng
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                <li>
                    <a class="dropdown-item py-2" href="them_phong.php">
                        <i class="bi bi-plus-circle me-2 text-primary"></i>Thêm phòng mới
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item py-2 text-muted small" href="#">
                        <i class="bi bi-info-circle me-2"></i>Chọn Sửa/Xóa tại từng phòng
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <?php while($p = mysqli_fetch_assoc($res)): 
            $badge_gender = ($p['loai_phong'] == 'Nam') ? 'bg-primary' : 'bg-danger';
            $status_class = ($p['so_giuong_trong'] > 0) ? 'text-success' : 'text-danger';
            $card_border = ($p['so_giuong_trong'] == 0) ? 'border-top border-danger border-4' : 'border-top border-success border-4';
        ?>
            <div class="col-md-6 col-lg-3">
                <div class="card card-custom shadow-sm border-0 <?= $card_border ?> h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-box <?= ($p['loai_phong'] == 'Nam') ? 'icon-blue' : 'icon-orange' ?>">
                                <i class="bi bi-door-closed-fill"></i>
                            </div>
                            <span class="badge <?= $badge_gender ?> rounded-pill small"><?= $p['loai_phong'] ?></span>
                        </div>
                        
                        <h5 class="fw-bold mb-1">Phòng <?= $p['ma_phong'] ?></h5>
                        <p class="text-muted small mb-3"><?= $p['ten_phong'] ?></p>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Tình trạng:</span>
                                <span class="fw-bold <?= $status_class ?>">
                                    <?= ($p['so_giuong_trong'] > 0) ? 'Còn '.$p['so_giuong_trong'].' chỗ' : 'Hết chỗ' ?>
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <?php $percent = (($p['so_giuong_toi_da'] - $p['so_giuong_trong']) / $p['so_giuong_toi_da']) * 100; ?>
                                <div class="progress-bar <?= ($percent == 100) ? 'bg-danger' : 'bg-success' ?>" style="width: <?= $percent ?>%"></div>
                            </div>
                            <small class="text-muted mt-1 d-block text-end"><?= ($p['so_giuong_toi_da'] - $p['so_giuong_trong']) ?>/<?= $p['so_giuong_toi_da'] ?> giường</small>
                        </div>

                        <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark small"><?= number_format($p['gia_phong']) ?>đ</span>
                            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                <a href="chi_tiet_phong.php?id=<?= $p['ma_phong'] ?>" class="btn btn-sm btn-light border-end" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if($role == 'admin'): ?>
                                    <a href="sua_phong.php?id=<?= $p['ma_phong'] ?>" class="btn btn-sm btn-light border-end text-warning" title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="xoa_phong.php?id=<?= $p['ma_phong'] ?>" class="btn btn-sm btn-light text-danger" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa phòng <?= $p['ma_phong'] ?>?')" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <?php if($total_pages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm mx-1 rounded-3" href="?page=<?= $page - 1 ?>">Trước</a>
            </li>
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link border-0 shadow-sm mx-1 rounded-3" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm mx-1 rounded-3" href="?page=<?= $page + 1 ?>">Sau</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<style>
.card-custom { border-radius: 15px; transition: 0.3s; background: white; }
.card-custom:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
.icon-box { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
.icon-blue { background: #e7f1ff; color: #0d6efd; }
.icon-orange { background: #fff3e0; color: #ef6c00; }
.progress { border-radius: 10px; background-color: #f0f0f0; }

/* Style cụm nút group */
.btn-group .btn {
    border: none;
    padding: 5px 12px;
}
.btn-group .btn:hover {
    background-color: #f8f9fa;
}

/* Style cho phân trang */
.pagination .page-link { padding: 8px 16px; color: #333; font-weight: 500; }
.pagination .page-item.active .page-link { background-color: #0d6efd; color: white; }
</style>

<?php include '../footer.php'; ?>