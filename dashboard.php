<?php 
include 'header.php'; 
include 'connect.php';

// Kiểm tra xem có ID câu lạc bộ không
if (!isset($_GET['id_clb'])) {
    header("Location: index.php");
    exit();
}

$id_clb = $_GET['id_clb'];
$_SESSION['current_clb_id'] = $id_clb; // Lưu vào session để dùng cho các trang con

// Lấy tên CLB để hiển thị
$sql_clb = "SELECT ten_clb FROM clb WHERE id = $id_clb";
$res_clb = $conn->query($sql_clb);
$clb_info = $res_clb->fetch_assoc();
?>
<!-- Index Css -->
<link href="css/index.css" rel="stylesheet">
<!-- HERO SECTION -->
<section class="hero text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7" data-aos="fade-right">
                <span class="badge bg-white text-primary mb-3 px-3 py-2 rounded-pill shadow-sm">
                    Chào mừng bạn đến với không gian sáng tạo
                </span>
                
                <h1 class="mb-4 fw-bold display-4">
                    Chào mừng bạn đến với <br>
                    <span class="text-warning"><?= htmlspecialchars($clb_info['ten_clb']) ?></span>
                </h1>
                
                <p class="mb-5 opacity-75 fs-5">
                    Rất vui được gặp bạn! Đây là trang quản trị dành riêng cho các hoạt động của 
                    <strong><?= htmlspecialchars($clb_info['ten_clb']) ?></strong>. 
                    Hãy cùng nhau xây dựng một cộng đồng sinh viên năng động và bứt phá nhé!
                </p>

                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="#modules" class="btn btn-light text-primary fw-bold shadow-lg px-4 py-3 rounded-pill btn-glow"> Bắt đầu khám phá</a>
                    <a href="index.php" class="btn btn-outline-light px-4 py-3 rounded-pill"> <i class="bi bi-arrow-left"></i> Quay lại trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- MODULES PREVIEW SECTION (5 MỤC CHÍNH) -->
<section id="modules" class="modules-section">
    <div class="container">
        <div class="section-header">
            <h2>Hệ Thống Chức Năng</h2>
            <div class="line"></div>
        </div>

        <div class="row g-4">
            <!-- 1. Thành viên -->
            <div class="col-lg-4 col-md-6">
                <a href="danhsach_thanhvien.php" class="text-decoration-none text-dark">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h4>Thành Viên CLB</h4>
                        <p class="text-muted small">Quản lý danh sách, hồ sơ và phân loại chức vụ thành viên một cách khoa học.</p>
                    </div>
                </a>
            </div>
            <!-- 2. Sự kiện -->
            <div class="col-lg-4 col-md-6">
                <a href="event/danhsach_sukien.php" class="text-decoration-none text-dark">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="bi bi-calendar-event-fill"></i>
                        </div>
                        <h4>Sự Kiện CLB</h4>
                        <p class="text-muted small">Lên kế hoạch, quản lý địa điểm và nội dung các chương trình sắp diễn ra.</p>
                    </div>
                </a>
            </div>
            <!-- 3. Đăng ký -->
            <div class="col-lg-4 col-md-6">
                <a href="formdangki.php" class="text-decoration-none text-dark">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h4>Đăng Ký Tham Gia</h4>
                        <p class="text-muted small">Điền thông tin trực tiếp để đăng ký tham gia vào các hoạt động của CLB.</p>
                    </div>
                </a>
            </div>
            <!-- 4. Tin tức -->
            <div class="col-lg-4 col-md-6">
                <a href="danhsach_tintuc.php" class="text-decoration-none text-dark">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="bi bi-newspaper"></i>
                        </div>
                        <h4>Tin Tức CLB</h4>
                        <p class="text-muted small">Cập nhật những hoạt động mới nhất và các bài viết chia sẻ từ ban chủ nhiệm.</p>
                    </div>
                </a>
            </div>
            <!-- 6. Mở rộng hệ thống trong tương lai -->
            <div class="col-lg-4 col-md-6">
                <div class="module-card border-dashed">
                    <div class="module-icon" style="background: #eee; color: #999;">
                        <i class="bi bi-plus-circle-dotted"></i>
                    </div>
                    <h4>Mở Rộng</h4>
                    <p class="text-muted small">Khả năng nâng cấp thêm nhiều tính năng hữu ích trong tương lai.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- CALL TO ACTION -->
<section class="py-5">
    <div class="container">
        <div class="p-5 bg-dark text-white rounded-5 text-center shadow-lg" style="background: linear-gradient(45deg, #2b2d42 0%, #1a1b26 100%) !important;">
            <h2 class="fw-bold mb-4">Bạn Đã Sẵn Sàng Trải Nghiệm?</h2>
            <p class="mb-4 opacity-75">Tham gia cùng chúng tôi để xây dựng một môi trường sinh viên năng động hơn.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="formdangki.php" class="btn btn-info px-4 py-2 rounded-pill fw-bold text-decoration-none">Tham Gia Ngay</a>
                <a href="ho_tro.php" class="btn btn-outline-light px-4 py-2 rounded-pill">Liên Hệ Ban Quản Trị</a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>