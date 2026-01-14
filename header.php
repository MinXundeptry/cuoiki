<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * BASE URL của dự án
 */
if (!defined('BASE_URL')) {
    define('BASE_URL', '/BAICUOIKY/');
}

/**
 * Kết nối Database
 */
include_once __DIR__ . '/connect.php';

/**
 * 1. Tối ưu: Lấy danh sách CLB một lần duy nhất
 */
$list_clb = [];
if (isset($conn)) {
    $query_clb = $conn->query("SELECT id, ten_clb FROM clb");
    if ($query_clb && $query_clb->num_rows > 0) {
        while ($row = $query_clb->fetch_assoc()) {
            $list_clb[] = $row;
        }
    }
}

/**
 * 2. Kiểm tra vai trò (Fix lỗi hiển thị Chủ nhiệm)
 */
$is_management = false;
$role_display = 'Thành viên';

if (isset($_SESSION['role'])) {
    // Chuyển về chữ thường, bỏ khoảng trắng để so sánh chuẩn xác
    $role = trim(mb_strtolower($_SESSION['role'], 'UTF-8'));
    if ($role === 'admin' || $role === 'chủ nhiệm' || $role === 'chunhiem') {
        $is_management = true;
        $role_display = ($role === 'admin') ? 'Quản trị viên' : 'Chủ nhiệm CLB';
    }
}

/**
 * Helper check active menu
 */
function isActive($file)
{
    return basename($_SERVER['PHP_SELF']) === $file ? 'active text-primary fw-bold' : '';
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
    
    <style>
        /* CSS bổ trợ để đảm bảo menu không bị đè */
        .dropdown-menu {
            z-index: 2000 !important;
            margin-top: 10px !important;
        }
        .user-nav-item {
            cursor: pointer;
            user-select: none;
        }
    </style>
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
                    <a class="nav-link <?= isActive('index.php') ?>" href="<?= BASE_URL ?>index.php">Trang chủ</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Thành viên</a>
                    <ul class="dropdown-menu shadow border-0">
                        <?php foreach ($list_clb as $clb): ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>member/danhsach_thanhvien.php?id_clb=<?= $clb['id'] ?>"><?= htmlspecialchars($clb['ten_clb']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Sự kiện</a>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>event/danhsach_sukien.php"><i class="bi bi-calendar-event me-2"></i>Tất cả</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php foreach ($list_clb as $clb): ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>event/danhsach_sukien.php?id_clb=<?= $clb['id'] ?>"><?= htmlspecialchars($clb['ten_clb']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>

                <?php if (!$is_management): ?>
                <li class="nav-item">
                    <a class="nav-link <?= isActive('danhsachdki.php') ?>" href="<?= BASE_URL ?>danhsachdki.php">Đăng ký</a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?= isActive('danhsach_tintuc.php') ?>" href="<?= BASE_URL ?>danhsach_tintuc.php">Tin tức</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="dropdown">
                        <div class="user-nav-item d-flex align-items-center dropdown-toggle" 
                             id="dropdownUser" 
                             data-bs-toggle="dropdown" 
                             aria-expanded="false">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                 style="width:35px;height:35px; font-weight: bold;">
                                <?= mb_strtoupper(mb_substr($_SESSION['username'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                            </div>
                            <div class="lh-1">
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($_SESSION['username']) ?></div>
                                <small class="text-muted" style="font-size: 11px;"><?= $role_display ?></small>
                            </div>
                        </div>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="dropdownUser">
                            <?php if ($is_management): ?>
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
                                    <i class="bi bi-person me-2"></i>Hồ sơ cá nhân
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
                    <a href="<?= BASE_URL ?>dangnhap.php" class="btn btn-outline-primary rounded-pill px-4">Đăng nhập</a>
                    <a href="<?= BASE_URL ?>dangky.php" class="btn btn-primary rounded-pill px-4">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
