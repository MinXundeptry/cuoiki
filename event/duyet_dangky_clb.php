<?php
include '../connect.php'; // Thoát ra ngoài để vào connect.php
session_start();
include '../header.php'; // Thoát ra ngoài để vào header.php

// 1. Kiểm tra quyền truy cập (Admin '0' hoặc quản lý CLB '1')
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != '0' && $_SESSION['role'] != '1')) {
    echo "<div class='container mt-5 alert alert-danger shadow-sm'>Bạn không có quyền quản trị!</div>";
    include '../footer.php';
    exit();
}

$id_clb = isset($_GET['id_clb']) ? intval($_GET['id_clb']) : 0;

// 2. Xử lý khi nhấn nút Duyệt hoặc Từ chối
if (isset($_GET['action']) && isset($_GET['id_dk'])) {
    $id_dk = intval($_GET['id_dk']);
    $action = $_GET['action'];

    if ($action == 'approve') {
        // Lấy thông tin từ bảng dangkyclb
        $info_sql = "SELECT d.*, t.hoten, t.username FROM dangkyclb d 
                     JOIN taikhoan t ON d.id_taikhoan = t.id 
                     WHERE d.id = $id_dk";
        $info_res = $conn->query($info_sql);
        
        if ($info_res && $info_res->num_rows > 0) {
            $info = $info_res->fetch_assoc();
            $masv = $info['username']; 
            $hoten = $info['hoten'];
            $clb_id = $info['id_clb'];
            $id_tk = $info['id_taikhoan']; // Lấy giá trị id_taikhoan từ kết quả truy vấn

            // Cập nhật trạng thái
            $conn->query("UPDATE dangkyclb SET trang_thai = 'Đã duyệt' WHERE id = $id_dk");

            // SỬA TẠI ĐÂY: Thêm cột id_taikhoan và giá trị $id_tk vào câu lệnh INSERT
            $sql_insert = "INSERT INTO thanhvien (id_taikhoan, masv, hoten, id_clb, ngaythamgia, chucvu, ban) 
                           VALUES ($id_tk, '$masv', '$hoten', $clb_id, NOW(), 'Thành viên', 'Chưa xếp ban')";
            
            if($conn->query($sql_insert)) {
                echo "<script>alert('Duyệt thành công!'); window.location.href='duyet_dangky_clb.php?id_clb=$id_clb';</script>";
            }
        }
    } 
    elseif ($action == 'reject') {
        $conn->query("UPDATE dangkyclb SET trang_thai = 'Từ chối' WHERE id = $id_dk");
        echo "<script>alert('Đã từ chối yêu cầu.'); window.location.href='duyet_dangky_clb.php?id_clb=$id_clb';</script>";
    }
}

// 3. Lấy danh sách chờ duyệt (Giữ nguyên)
$sql = "SELECT d.*, t.hoten, t.username FROM dangkyclb d 
        JOIN taikhoan t ON d.id_taikhoan = t.id 
        WHERE d.id_clb = $id_clb AND d.trang_thai = 'Chờ duyệt'
        ORDER BY d.ngay_dang_ky DESC";
$list_dk = $conn->query($sql);
?>

<div class="container mt-5">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-person-check-fill me-2"></i> Duyệt Đơn Gia Nhập</h5>
            <a href="../dashboard.php?id_clb=<?= $id_clb ?>" class="btn btn-light btn-sm rounded-pill px-3">Về Dashboard</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã Sinh Viên</th>
                            <th>Họ và Tên</th>
                            <th>Ngày gửi</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($list_dk && $list_dk->num_rows > 0): ?>
                            <?php while($row = $list_dk->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?= $row['username'] ?></td>
                                    <td><?= $row['hoten'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['ngay_dang_ky'])) ?></td>
                                    <td class="text-center">
                                        <a href="?id_clb=<?= $id_clb ?>&id_dk=<?= $row['id'] ?>&action=approve" class="btn btn-success btn-sm px-3">Duyệt</a>
                                        <a href="?id_clb=<?= $id_clb ?>&id_dk=<?= $row['id'] ?>&action=reject" class="btn btn-danger btn-sm px-3">Xóa</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted">Không có đơn nào cần duyệt.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>