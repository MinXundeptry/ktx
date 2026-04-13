<?php 
include '../header.php'; 
include '../connect.php'; 

if(isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM sinh_vien WHERE ma_sv = '$id'"));
    if(!$data) header('location:sinh_vien.php');
}

if (isset($_POST['btn_sua'])) {
    $ho_ten = mysqli_real_escape_string($conn, $_POST['ho_ten']);
    $ngay_sinh = $_POST['ngay_sinh'];
    $lop = mysqli_real_escape_string($conn, $_POST['lop']);
    $gioi_tinh = $_POST['gioi_tinh'];
    $sdt = $_POST['so_dien_thoai'];
    $que_quan = mysqli_real_escape_string($conn, $_POST['que_quan']);
    $doi_tuong = $_POST['doi_tuong_uu_tien'];

    $sql_update = "UPDATE sinh_vien SET 
                    ho_ten = '$ho_ten', 
                    ngay_sinh = '$ngay_sinh', 
                    lop = '$lop', 
                    gioi_tinh = '$gioi_tinh', 
                    so_dien_thoai = '$sdt', 
               
                    doi_tuong_uu_tien = '$doi_tuong' 
                   WHERE ma_sv = '$id'";

    if (mysqli_query($conn, $sql_update)) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='sinh_vien.php';</script>";
    } else {
        echo "<script>alert('Lỗi cập nhật!');</script>";
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 text-center">
                    <h5 class="fw-bold mb-0 text-warning">SỬA THÔNG TIN SINH VIÊN</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mã Sinh Viên (Không được sửa)</label>
                                <input type="text" class="form-control bg-light" value="<?= $data['ma_sv'] ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và Tên</label>
                                <input type="text" name="ho_ten" class="form-control" value="<?= $data['ho_ten'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày sinh</label>
                                <input type="date" name="ngay_sinh" class="form-control" value="<?= $data['ngay_sinh'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Giới tính</label>
                                <select name="gioi_tinh" class="form-select">
                                    <option value="Nam" <?= $data['gioi_tinh'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
                                    <option value="Nữ" <?= $data['gioi_tinh'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Lớp</label>
                                <input type="text" name="lop" class="form-control" value="<?= $data['lop'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại</label>
                                <input type="text" name="so_dien_thoai" class="form-control" value="<?= $data['so_dien_thoai'] ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Đối tượng ưu tiên</label>
                                <select name="doi_tuong_uu_tien" class="form-select">
                                    <option value="" <?= $data['doi_tuong_uu_tien'] == '' ? 'selected' : '' ?>>Không</option>
                                    <option value="Con thương binh/Liệt sĩ" <?= $data['doi_tuong_uu_tien'] == 'Con thương binh/Liệt sĩ' ? 'selected' : '' ?>>Con thương binh/Liệt sĩ</option>
                                    <option value="Vùng sâu vùng xa" <?= $data['doi_tuong_uu_tien'] == 'Vùng sâu vùng xa' ? 'selected' : '' ?>>Vùng sâu vùng xa</option>
                                    <option value="Hộ nghèo/Cận nghèo" <?= $data['doi_tuong_uu_tien'] == 'Hộ nghèo/Cận nghèo' ? 'selected' : '' ?>>Hộ nghèo/Cận nghèo</option>
                                </select>
                            </div>
                        </div>
                          
                        <hr class="my-4">
                        <div class="d-flex gap-2">
                            <button type="submit" name="btn_sua" class="btn btn-warning px-4 rounded-pill w-25 fw-bold">Cập nhật thay đổi</button>
                            <a href="sinh_vien.php" class="btn btn-light px-4 rounded-pill">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../footer.php'; ?>