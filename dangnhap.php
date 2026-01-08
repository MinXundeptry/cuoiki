<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'connect.php';
include 'header.php';

if (isset($_POST['btnDangNhap'])) {
    // Sử dụng real_escape_string để bảo mật cơ bản
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Sửa tên bảng thành 'taikhoan' theo database thực tế của bạn
    $sql = "SELECT * FROM taikhoan WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Kiểm tra mật khẩu mã hóa
        if ($password == $user['password'] || password_verify($password, $user['password'])) {
            // Thiết lập Session khớp với file header.php đang dùng
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['vaitro']; // 'vaitro' thay vì 'role'
            $_SESSION['hoten'] = $user['hoten'];

            echo "<script>alert('Đăng nhập thành công! Chào " . $user['hoten'] . "'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Mật khẩu không chính xác!');</script>";
        }
    } else {
        echo "<script>alert('Tài khoản không tồn tại!');</script>";
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="card shadow-lg mx-auto border-0" style="max-width: 400px; border-radius: 15px;">
        <div class="card-body p-4">
            <h3 class="text-center mb-4 fw-bold text-primary">ĐĂNG NHẬP</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Tên đăng nhập</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control bg-light border-start-0" placeholder="Nhập username" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="Nhập mật khẩu" required>
                    </div>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" name="btnDangNhap" class="btn btn-primary btn-lg rounded-pill shadow-sm fw-bold">ĐĂNG NHẬP</button>
                </div>
                <div class="text-center mt-3 small">
                    Chưa có tài khoản? <a href="dangky.php" class="text-decoration-none">Đăng ký ngay</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>