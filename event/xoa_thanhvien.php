<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../connect.php';

/**
 * 1. KIỂM TRA QUYỀN TRUY CẬP (Middleware)
 */
$user_role = isset($_SESSION['role']) ? mb_strtolower($_SESSION['role'], 'UTF-8') : '';
// Cho phép cả admin và chủ nhiệm vào trang này
$is_management = ($user_role === 'admin' || $user_role === 'chủ nhiệm' || $user_role === 'chunhiem');

if (!$is_management) {
    echo "<script>
            alert('Bạn không có quyền thực hiện hành động này!');
            window.location.href='../index.php';
          </script>";
    exit();
}

/**
 * 2. XỬ LÝ XÓA
 */
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Lấy thông tin thành viên trước khi xóa để kiểm tra ID CLB
    $check_res = $conn->query("SELECT id_clb FROM thanhvien WHERE id = $id");
    
    if ($check_res && $check_res->num_rows > 0) {
        $member = $check_res->fetch_assoc();
        $id_clb_member = intval($member['id_clb']);
        
        // KIỂM TRA BẢO MẬT: 
        // Nếu là Chủ nhiệm, chỉ được xóa nếu ID CLB của thành viên trùng với ID CLB quản lý
        $can_delete = false;
        if ($user_role === 'admin') {
            $can_edit = true; // Admin xóa ai cũng được
        } elseif (($user_role === 'chủ nhiệm' || $user_role === 'chunhiem') && $id_clb_member === intval($_SESSION['id_clb'])) {
            $can_delete = true; // Chủ nhiệm đúng CLB thì được xóa
        }

        if ($can_delete) {
            $sql = "DELETE FROM thanhvien WHERE id = $id";
            if ($conn->query($sql) === TRUE) {
                // Xóa thành công, quay về danh sách kèm theo id_clb để lọc đúng
                echo "<script>
                        alert('Xóa thành viên thành công!');
                        window.location.href='danhsach_thanhvien.php?id_clb=$id_clb_member';
                      </script>";
            } else {
                echo "Lỗi xóa dữ liệu: " . $conn->error;
            }
        } else {
            echo "<script>
                    alert('Bạn không có quyền xóa thành viên của câu lạc bộ khác!');
                    window.location.href='danhsach_thanhvien.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Thành viên không tồn tại!');
                window.location.href='danhsach_thanhvien.php';
              </script>";
    }
} else {
    header("Location: danhsach_thanhvien.php");
}
?>