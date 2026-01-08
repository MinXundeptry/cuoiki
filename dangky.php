<?php 
include '../connect.php'; 
include '../header.php'; 

if (isset($_POST['btnDangKy'])) {
    // Lấy dữ liệu và bảo mật đầu vào
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $hoten = mysqli_real_escape_string($conn, $_POST['hoten']);
    
    // 1. Mã hóa mật khẩu (bắt buộc để đăng nhập được bằng password_verify)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // 2. Kiểm tra xem tên đăng nhập đã tồn tại chưa
    $check_sql = "SELECT id FROM taikhoan WHERE username = '$username'";
    $check_res = $conn->query($check_sql);

    if ($check_res->num_rows > 0) {
        echo "<script>alert('Tên đăng nhập này đã tồn tại!');</script>";
    } else {
        // 3. Thêm vào bảng taikhoan (vaitro mặc định là thanhvien)
        $sql = "INSERT INTO taikhoan (username, password, hoten, vaitro) 
                VALUES ('$username', '$hashed_password', '$hoten', 'thanhvien')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Đăng ký thành công! Hãy đăng nhập.'); window.location.href='dangnhap.php';</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="card shadow mx-auto border-0" style="max-width: 450px; border-radius: 15px;">
        <div class="card-body p-4">
            <h3 class="text-center mb-4 fw-bold text-success">ĐĂNG KÝ THÀNH VIÊN</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Họ và Tên</label>
                    <input type="text" name="hoten" class="form-control" placeholder="Nhập tên của bạn" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Tên đăng nhập (Mã SV)</label>
                    <input type="text" name="username" class="form-control" placeholder="Nhập mã sinh viên" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" name="btnDangKy" class="btn btn-success btn-lg rounded-pill shadow-sm fw-bold">ĐĂNG KÝ NGAY</button>
                </div>
                <div class="text-center mt-3 small">
                    Đã có tài khoản? <a href="dangnhap.php" class="text-decoration-none">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>