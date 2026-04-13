</div> <footer class="bg-white border-top py-4 mt-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-md-start text-muted text-center">
                <small>&copy; <?= date('Y') ?> <strong>Hệ Thống Quản Lý KTX</strong>. Tất cả quyền được bảo lưu.</small>
            </div>
            <div class="col-md-6 text-md-end text-center mt-2 mt-md-0">
                <a href="#" class="text-decoration-none text-muted me-3 small">Hướng dẫn</a>
                <a href="#" class="text-decoration-none text-muted small">Hỗ trợ 24/7</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php 
// Đóng kết nối để giải phóng bộ nhớ XAMPP
if(isset($conn)) { mysqli_close($conn); } 
?>