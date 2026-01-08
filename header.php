<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * BASE URL của dự án
 */
define('BASE_URL', '/BAICUOIKY/');

/**
 * Kết nối Database ngay tại đầu Header để dùng chung cho các Menu
 */
include_once __DIR__ . '/connect.php';

/**
 * Helper check active menu
 */
function isActive($file)
{
    return basename($_SERVER['PHP_SELF']) === $file
        ? 'active text-primary fw-bold'
        : '';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý CLB Sinh Viên</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/header.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm bg-white">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>index.php">
            <i class="bi bi-rocket-takeoff-fill fs-3 text-primary me-2"></i>
            <span class="fw-bold text-dark">SV-MANAGER</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?= isActive('index.php') ?>" href="<?= BASE_URL ?>index.php">
                        Trang chủ
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= (strpos($_SERVER['PHP_SELF'], 'thanhvien') !== false) ? 'active text-primary fw-bold' : '' ?>"
                       href="#" role="button" data-bs-toggle="dropdown">
                        Thành viên
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        <li><hr class="dropdown-divider"></li>
                        <?php
                        // Lấy danh sách CLB cho menu Thành viên
                        $menu_clb_thanhvien = $conn->query("SELECT id, ten_clb FROM clb");
                        if ($menu_clb_thanhvien && $menu_clb_thanhvien->num_rows > 0):
                            while ($clb = $menu_clb_thanhvien->fetch_assoc()):
                        ?>
                            <li>
                                <a class="dropdown-item" 
                                   href="<?= BASE_URL ?>event/danhsach_thanhvien.php?id_clb=<?= $clb['id'] ?>">
                                    <?= htmlspecialchars($clb['ten_clb']) ?>
                                </a>
                            </li>
                        <?php endwhile; endif; ?>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= (strpos($_SERVER['PHP_SELF'], 'sukien') !== false) ? 'active text-primary fw-bold' : '' ?>"
                       href="#" role="button" data-bs-toggle="dropdown">
                        Sự kiện
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        <li>
                            <a class="dropdown-item" href="<?= BASE_URL ?>event/danhsach_sukien.php">
                                <i class="bi bi-calendar-event me-2"></i>Tất cả sự kiện
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <?php
                        // Reset con trỏ dữ liệu hoặc truy vấn lại cho menu Sự kiện
                        $menu_clb_sukien = $conn->query("SELECT id, ten_clb FROM clb");
                        if ($menu_clb_sukien && $menu_clb_sukien->num_rows > 0):
                            while ($clb = $menu_clb_sukien->fetch_assoc()):
                        ?>
                            <li>
                                <a class="dropdown-item"
                                   href="<?= BASE_URL ?>event/danhsach_sukien.php?id_clb=<?= $clb['id'] ?>">
                                    <?= htmlspecialchars($clb['ten_clb']) ?>
                                </a>
                            </li>
                        <?php endwhile; endif; ?>
                    </ul>
                </li>

                <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= isActive('danhsachdki.php') ?>" href="<?= BASE_URL ?>danhsachdki.php">
                        Đăng ký
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?= isActive('danhsach_tintuc.php') ?>" href="<?= BASE_URL ?>danhsach_tintuc.php">
                        Tin tức
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle"
                           href="#" role="button" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                 style="width:35px;height:35px;">
                                <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="fw-semibold lh-1"><?= $_SESSION['username'] ?></div>
                                <small class="text-muted">
                                    <?= $_SESSION['role'] === 'admin' ? 'Quản trị viên' : 'Thành viên' ?>
                                </small>
                            </div>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>event/duyetdangki.php">
                                        <i class="bi bi-clipboard-check me-2"></i>Duyệt đăng ký
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>event/them_sukien.php">
                                        <i class="bi bi-plus-circle me-2"></i>Thêm sự kiện
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>

                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>ho_so.php">
                                    <i class="bi bi-person me-2"></i>Hồ sơ
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= BASE_URL ?>dangxuat.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>dangnhap.php" class="btn btn-outline-primary rounded-pill px-4">
                        Đăng nhập
                    </a>
                    <a href="<?= BASE_URL ?>dangky.php" class="btn btn-primary rounded-pill px-4">
                        Đăng ký
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>