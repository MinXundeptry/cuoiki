<?php 
include 'header.php'; 
include 'connect.php';

// Kiểm tra xem có ID câu lạc bộ không
if (!isset($_GET['id_clb'])) {
    header("Location: index.php");
    exit();
}

$id_clb = intval($_GET['id_clb']);
$_SESSION['current_clb_id'] = $id_clb; 

// Lấy thông tin CLB
$sql_clb = "SELECT ten_clb FROM clb WHERE id = $id_clb";
$res_clb = $conn->query($sql_clb);
$clb_info = $res_clb->fetch_assoc();

// Đảm bảo lấy đúng ID người dùng từ Session (Bạn cần kiểm tra tên biến session khi đăng nhập)
$user_id = $_SESSION['user_id'] ?? 0; 
$is_member = false;
$is_pending = false;

if ($user_id > 0) {
    // 1. Kiểm tra đã là thành viên chính thức chưa
    $check_tv = $conn->query("SELECT id FROM thanhvien WHERE id_taikhoan = $user_id AND id_clb = $id_clb");
    if ($check_tv && $check_tv->num_rows > 0) $is_member = true;

    // 2. Kiểm tra đang chờ duyệt không (Sửa tên bảng thành dangkyclb)
    $check_dk = $conn->query("SELECT id FROM dangkyclb WHERE id_taikhoan = $user_id AND id_clb = $id_clb AND trang_thai = 'Chờ duyệt'");
    if ($check_dk && $check_dk->num_rows > 0) $is_pending = true;
}

// Kiểm tra quyền Quản lý (Admin '0' hoặc 'admin', hoặc Chủ nhiệm '1')
$is_admin_or_manager = (isset($_SESSION['role']) && ($_SESSION['role'] == '0' || $_SESSION['role'] == 'admin' || ($_SESSION['role'] == '1' && $_SESSION['id_clb'] == $id_clb)));
?>

<link href="css/index.css" rel="stylesheet">

<section class="hero text-center text-lg-start bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-white text-primary mb-3 px-3 py-2 rounded-pill shadow-sm">Sáng Tạo - Kết Nối - Phát Triển</span>
                <h1 class="mb-4 fw-bold display-4">
                    Chào mừng bạn đến với <br>
                    <span class="text-warning"><?= htmlspecialchars($clb_info['ten_clb']) ?></span>
                </h1>
                <p class="mb-5 opacity-75 fs-5">Nơi hội tụ những tài năng và niềm đam mê. Hãy cùng nhau xây dựng cộng đồng vững mạnh!</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="#modules" class="btn btn-light text-primary fw-bold shadow-lg px-4 py-3 rounded-pill">Khám phá ngay</a>
                    <a href="index.php" class="btn btn-outline-light px-4 py-3 rounded-pill"> <i class="bi bi-arrow-left"></i> Trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="modules" class="modules-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">Hệ Thống Chức Năng</h2>
            <div class="line mx-auto" style="width: 60px; height: 3px; background: #0dcaf0;"></div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <?php if ($is_admin_or_manager): ?>
                    <a href="event/duyet_dangky_clb.php?id_clb=<?= $id_clb ?>" class="text-decoration-none text-dark">
                        <div class="module-card border-warning shadow-sm p-4 text-center rounded-4" style="border: 2px solid #ffc107; background: #fff;">
                            <div class="module-icon text-warning mb-3 fs-1"><i class="bi bi-person-check-fill"></i></div>
                            <h4>Duyệt Thành Viên</h4>
                            <p class="text-muted small">Quản lý yêu cầu tham gia và phê duyệt thành viên mới.</p>
                        </div>
                    </a>
                <?php else: ?>
                    <a href="event/danhsach_thanhvien.php?id_clb=<?= $id_clb ?>" class="text-decoration-none text-dark">
                        <div class="module-card shadow-sm p-4 text-center rounded-4" style="background: #fff;">
                            <div class="module-icon text-primary mb-3 fs-1"><i class="bi bi-people-fill"></i></div>
                            <h4>Thành Viên CLB</h4>
                            <p class="text-muted small">Xem danh sách những người bạn đồng hành trong gia đình CLB.</p>
                        </div>
                    </a>
                <?php endif; ?>
            </div>

            <div class="col-lg-4 col-md-6">
                <a href="event/danhsach_sukien.php?id_clb=<?= $id_clb ?>" class="text-decoration-none text-dark">
                    <div class="module-card shadow-sm p-4 text-center rounded-4" style="background: #fff;">
                        <div class="module-icon text-success mb-3 fs-1"><i class="bi bi-calendar-event-fill"></i></div>
                        <h4>Sự Kiện CLB</h4>
                        <p class="text-muted small">Cập nhật lịch trình và tham gia các hoạt động sôi nổi.</p>
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-6">
                <?php if ($is_member): ?>
                    <div class="module-card bg-light border-0 shadow-sm p-4 text-center rounded-4">
                        <div class="module-icon text-success mb-3 fs-1"><i class="bi bi-shield-check"></i></div>
                        <h4>Đã Tham Gia</h4>
                        <p class="text-muted small">Bạn đã là thành viên chính thức của CLB.</p>
                    </div>
                <?php elseif ($is_pending): ?>
                    <div class="module-card bg-light border-0 shadow-sm p-4 text-center rounded-4">
                        <div class="module-icon text-warning mb-3 fs-1"><i class="bi bi-hourglass-split"></i></div>
                        <h4>Đang Chờ Duyệt</h4>
                        <p class="text-muted small">Yêu cầu đã gửi thành công. Vui lòng đợi Ban quản lý phê duyệt.</p>
                    </div>
                <?php else: ?>
                    <a href="event/xuly_thamgia_clb.php?id_clb=<?= $id_clb ?>" class="text-decoration-none text-dark">
                        <div class="module-card border-info shadow-sm p-4 text-center rounded-4" style="border: 2px solid #0dcaf0; background: #fff;">
                            <div class="module-icon text-info mb-3 fs-1"><i class="bi bi-pencil-square"></i></div>
                            <h4>Đăng Ký Tham Gia</h4>
                            <p class="text-muted small">Hãy gửi đơn gia nhập để cùng tham gia các hoạt động thú vị.</p>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="p-5 text-white rounded-5 text-center shadow-lg" style="background: linear-gradient(45deg, #2b2d42 0%, #1a1b26 100%) !important;">
            <?php if (!$is_member && !$is_pending && !$is_admin_or_manager): ?>
                <h2 class="fw-bold mb-4">Bạn Đã Sẵn Sàng Trải Nghiệm?</h2>
                <a href="event/xuly_thamgia_clb.php?id_clb=<?= $id_clb ?>" class="btn btn-info px-4 py-2 rounded-pill fw-bold text-white shadow">Gia Nhập Ngay</a>
            <?php else: ?>
                <h2 class="fw-bold mb-4">Cảm Ơn Bạn Đã Đồng Hành!</h2>
                <p class="mb-4 opacity-75">Cùng nhau xây dựng một cộng đồng sinh viên năng động.</p>
                <a href="ho_tro.php" class="btn btn-outline-light px-4 py-2 rounded-pill">Gửi Ý Kiến Đóng Góp</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>