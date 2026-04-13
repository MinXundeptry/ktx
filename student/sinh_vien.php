<?php 
include '../header.php'; 
include '../connect.php'; 

// Xử lý Xóa sinh viên
if(isset($_GET['delete_id'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    // Lấy thông tin phòng trước khi xóa
    $sv_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ma_phong FROM sinh_vien WHERE ma_sv = '$id'"));
    $ma_phong = $sv_data['ma_phong'];
    
    mysqli_begin_transaction($conn);
    try {
        // Xóa sinh viên
        mysqli_query($conn, "DELETE FROM sinh_vien WHERE ma_sv = '$id'");
        // Xóa tài khoản
        mysqli_query($conn, "DELETE FROM tai_khoan WHERE ten_dang_nhap = '$id'");
        // Hoàn trả giường nếu có phòng
        if($ma_phong){
            mysqli_query($conn, "UPDATE phong SET so_giuong_trong = so_giuong_trong + 1 WHERE ma_phong = '$ma_phong'");
        }
        mysqli_commit($conn);
        echo "<script>alert('Xóa thành công sinh viên và tài khoản!'); window.location.href='sinh_vien.php';</script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Lỗi: Không thể xóa!');</script>";
    }
}

// Xử lý Tìm kiếm
$search = "";
if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// Lấy danh sách (Có lọc theo tìm kiếm)
$sql = "SELECT s.*, p.ten_phong 
        FROM sinh_vien s 
        LEFT JOIN phong p ON s.ma_phong = p.ma_phong 
        WHERE s.ma_sv LIKE '%$search%' OR s.ho_ten LIKE '%$search%'
        ORDER BY s.ma_sv DESC";
$res = mysqli_query($conn, $sql);
?>

<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h4 class="fw-bold mb-0 text-uppercase">Danh sách sinh viên nội trú</h4>
        
        <div class="d-flex gap-2">
            <form method="GET" action="" class="d-flex border rounded-pill bg-white px-2 py-1 shadow-sm">
                <input type="text" name="search" class="form-control border-0 shadow-none" 
                       placeholder="Tìm MSSV hoặc tên..." value="<?= htmlspecialchars($search) ?>" style="width: 200px;">
                <button type="submit" class="btn btn-link text-primary"><i class="bi bi-search"></i></button>
                <?php if($search != ""): ?>
                    <a href="sinh_vien.php" class="btn btn-link text-muted"><i class="bi bi-x-circle"></i></a>
                <?php endif; ?>
            </form>

            <a href="them_sinh_vien.php" class="btn btn-primary rounded-pill shadow-sm d-flex align-items-center">
                <i class="bi bi-plus-lg me-1"></i> Thêm mới
            </a>
        </div>
    </div>

    <?php if(mysqli_num_rows($res) == 0): ?>
        <div class="alert alert-warning rounded-4 border-0 shadow-sm">
            Không tìm thấy sinh viên nào khớp với từ khóa "<strong><?= htmlspecialchars($search) ?></strong>".
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>MSSV</th>
                        <th>Họ tên / Ngày sinh</th>
                        <th>Lớp / Giới tính</th>
                        <th>Quê quán</th>
                        <th>Phòng</th>
                        <th>Liên hệ</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><code class="fw-bold text-primary"><?= $row['ma_sv'] ?></code></td>
                        <td>
                            <div class="fw-bold"><?= $row['ho_ten'] ?></div>
                            <small class="text-muted">NS: <?= $row['ngay_sinh'] ? date('d/m/Y', strtotime($row['ngay_sinh'])) : 'Chưa cập nhật' ?></small><br>
                            <small class="text-danger"><?= $row['doi_tuong_uu_tien'] ? '★ '.$row['doi_tuong_uu_tien'] : '' ?></small>
                        </td>
                        <td>
                            <div><?= $row['lop'] ?></div>
                            <small class="badge bg-light text-dark border"><?= $row['gioi_tinh'] ?></small>
                        </td>
                        <td><span class="small"><?= $row['que_quan'] ?? 'Chưa rõ' ?></span></td>
                        <td>
                            <span class="badge <?= $row['ma_phong'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $row['ten_phong'] ?? 'Chưa có' ?>
                            </span>
                        </td>
                        <td><i class="bi bi-telephone me-1"></i><?= $row['so_dien_thoai'] ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="sua_sinh_vien.php?id=<?= $row['ma_sv'] ?>" class="btn btn-sm btn-outline-warning" title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="?delete_id=<?= $row['ma_sv'] ?>" class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Sẽ xóa cả tài khoản của sinh viên này. Tiếp tục?')" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>