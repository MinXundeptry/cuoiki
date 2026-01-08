<?php
include 'connect.php';
include 'header.php';

// Kiểm tra nếu chưa đăng nhập thì yêu cầu đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: dangnhap.php");
    exit();
}

$username = $_SESSION['username'];

// 1. Lấy thông tin tài khoản từ bảng taikhoan
$sql_user = "SELECT * FROM taikhoan WHERE username = '$username'";
$res_user = $conn->query($sql_user);
$user_info = $res_user->fetch_assoc();

// 2. Lấy danh sách sự kiện đã đăng ký từ bảng dangky
$sql_reg = "SELECT d.*, s.ten as ten_sk, s.ngay as ngay_sk 
            FROM dangky d 
            JOIN sukien s ON d.id_sukien = s.id 
            WHERE d.masv = '$username' 
            ORDER BY d.id DESC";
$res_reg = $conn->query($sql_reg);
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0 text-center p-4">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 30px;">
                    <?= strtoupper(substr($user_info['username'], 0, 1)) ?>
                </div>
                <h4 class="fw-bold"><?= htmlspecialchars($user_info['hoten']) ?></h4>
                <p class="text-muted small mb-3">@<?= htmlspecialchars($user_info['username']) ?></p>
                <span class="badge <?= $user_info['vaitro'] == 'admin' ? 'bg-danger' : 'bg-info' ?> rounded-pill px-3 py-2 mb-4">
                    <?= $user_info['vaitro'] == 'admin' ? 'Quản trị viên' : 'Thành viên CLB' ?>
                </span>
                <hr>
                <div class="text-start small">
                    <p><strong><i class="bi bi-calendar3 me-2"></i>Ngày tham gia hệ thống:</strong> <br><?= date('d/m/Y', strtotime($user_info['ngaytao'])) ?></p>
                </div>
                <a href="dangxuat.php" class="btn btn-outline-danger btn-sm mt-3 w-100">Đăng xuất</a>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow border-0 p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-clock-history me-2 text-primary"></i>Lịch sử Đăng ký Sự kiện</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tên Sự Kiện</th>
                                <th>Ngày Tổ Chức</th>
                                <th>Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($res_reg->num_rows > 0): ?>
                                <?php while($reg = $res_reg->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark"><?= htmlspecialchars($reg['ten_sk']) ?></span>
                                        </td>
                                        <td><small><?= date('d/m/Y', strtotime($reg['ngay_sk'])) ?></small></td>
                                        <td>
                                            <?php 
                                            $statusClass = 'bg-secondary'; // Chờ duyệt
                                            if($reg['trangthai'] == 'Da duyet') $statusClass = 'bg-success';
                                            if($reg['trangthai'] == 'Da huy') $statusClass = 'bg-danger';
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $reg['trangthai'] ?></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">Bạn chưa đăng ký sự kiện nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <a href="danhsachdki.php" class="btn btn-primary btn-sm rounded-pill px-3">Khám phá sự kiện mới</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>