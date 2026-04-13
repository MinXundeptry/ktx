<?php 
include '../header.php'; 
include '../connect.php'; 

$id = $_GET['id'] ?? '';
// Lấy thông tin phòng hiện tại
$sql_get = "SELECT * FROM phong WHERE ma_phong = '$id'";
$res_get = mysqli_query($conn, $sql_get);
$p = mysqli_fetch_assoc($res_get);

if (!$p) { 
    echo "<div class='container mt-5'><div class='alert alert-danger'>Không tìm thấy phòng!</div></div>"; 
    exit(); 
}

// Tính số sinh viên đang ở thực tế
$dang_o = $p['so_giuong_toi_da'] - $p['so_giuong_trong'];

$message = "";

if (isset($_POST['btn_sua'])) {
    $ten = mysqli_real_escape_string($conn, $_POST['ten_phong']);
    $loai = $_POST['loai_phong'];
    $gia = $_POST['gia_phong'];
    $so_giuong_moi = (int)$_POST['so_giuong_toi_da'];

    // Kiểm tra: Số giường mới không được ít hơn số người đang ở
    if ($so_giuong_moi < $dang_o) {
        $message = "<div class='alert alert-danger'>Lỗi: Số giường tối đa không thể nhỏ hơn số người đang ở ($dang_o người).</div>";
    } else {
        // Tính lại số giường trống mới
        $so_giuong_trong_moi = $so_giuong_moi - $dang_o;
        
        $sql_update = "UPDATE phong SET 
                        ten_phong='$ten', 
                        loai_phong='$loai', 
                        gia_phong='$gia', 
                        so_giuong_toi_da='$so_giuong_moi',
                        so_giuong_trong='$so_giuong_trong_moi'
                      WHERE ma_phong='$id'";
        
        if (mysqli_query($conn, $sql_update)) {
            echo "<script>window.location.href='phong.php';</script>";
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Lỗi cập nhật: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4 text-primary"><i class="bi bi-pencil-square me-2"></i>SỬA PHÒNG <?= $id ?></h4>
                    <?= $message ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tên phòng</label>
                            <input type="text" name="ten_phong" class="form-control" value="<?= $p['ten_phong'] ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Loại phòng</label>
                                <select name="loai_phong" class="form-select">
                                    <option value="Nam" <?= $p['loai_phong']=='Nam'?'selected':'' ?>>Phòng Nam</option>
                                    <option value="Nữ" <?= $p['loai_phong']=='Nữ'?'selected':'' ?>>Phòng Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Số giường tối đa</label>
                                <input type="number" name="so_giuong_toi_da" class="form-control" value="<?= $p['so_giuong_toi_da'] ?>" min="1" required>
                                <div class="form-text text-info">Đang có <?= $dang_o ?> người ở.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Giá phòng (VNĐ)</label>
                            <input type="number" name="gia_phong" class="form-control" value="<?= $p['gia_phong'] ?>" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="btn_sua" class="btn btn-primary fw-bold py-2 rounded-pill">LƯU THAY ĐỔI</button>
                            <a href="phong.php" class="btn btn-light py-2 rounded-pill">Hủy bỏ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>