<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra nếu chưa đăng nhập HOẶC không phải admin thì đuổi ra trang chủ
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
            alert('Bạn không có quyền truy cập trang quản trị!');
            window.location.href='index.php';
          </script>";
    exit();
}
?>
<?php
include '../connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
        
    $sql = "DELETE FROM thanhvien WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Xóa thành công thì quay về trang danh sách
        header("Location: danhsach_thanhvien.php");
    } else {
        echo "Lỗi xóa dữ liệu: " . $conn->error;
    }
} else {
    header("Location: danhsach_thanhvien.php");
}
?>