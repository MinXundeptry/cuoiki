<?php 
include '../header.php'; 
include '../connect.php'; 

// 1. Khởi tạo các biến tránh lỗi Undefined
$id_clb_filter = isset($_GET['id_clb']) ? intval($_GET['id_clb']) : 0;
$keyword = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

/**
 * 2. LOGIC PHÂN QUYỀN (ĐÃ SỬA)
 * - Nếu không chọn CLB nào (?id_clb trống) và là Chủ nhiệm -> Mặc định xem CLB của mình.
 * - Nếu có chọn CLB cụ thể -> Cho phép xem (nhưng sẽ chặn Sửa/Xóa ở phần hiển thị bên dưới).
 */
if ($id_clb_filter <= 0 && isset($_SESSION['role'])) {
    $role_check = mb_strtolower($_SESSION['role'], 'UTF-8');
    if (($role_check === 'chunhiem' || $role_check === 'chủ nhiệm') && isset($_SESSION['id_clb'])) {
        $id_clb_filter = intval($_SESSION['id_clb']);
    }
}

// 3. LOGIC PHÂN TRANG
$limit = 8; 
$page = isset($_GET['p_mem']) ? intval($_GET['p_mem']) : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

// 4. Xây dựng điều kiện lọc WHERE
$conditions = [];
if ($id_clb_filter > 0) {
    $conditions[] = "t.id_clb = $id_clb_filter";
}
if (!empty($keyword)) {
    $conditions[] = "(t.hoten LIKE '%$keyword%' OR t.masv LIKE '%$keyword%' OR t.ban LIKE '%$keyword%')";
}
$where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// 5. Tính tổng số thành viên để chia trang
$total_res = $conn->query("SELECT COUNT(*) as total FROM thanhvien t $where_clause");
$total_data = $total_res->fetch_assoc();
$total_mem = $total_data['total'];
$total_pages = ceil($total_mem / $limit);

// Kiểm tra xem Chủ nhiệm có đang xem đúng CLB của mình hay không
$is_my_clb = false;
if (isset($_SESSION['role'])) {
    $role_session = mb_strtolower($_SESSION['role'], 'UTF-8');
    if ($role_session === 'admin') {
        $is_my_clb = true; // Admin có quyền mọi nơi
    } elseif (($role_session === 'chunhiem' || $role_session === 'chủ nhiệm') && $id_clb_filter == $_SESSION['id_clb']) {
        $is_my_clb = true; // Chủ nhiệm xem đúng CLB của mình
    }
}
?>

<style>
    .card-hover {
        transition: all 0.3s ease-in-out;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .card-hover:hover {
        transform: translateY(-12px);
        box-shadow: 0 15px 30px rgba(13, 110, 253, 0.15) !important;
        border-color: #0dcaf0;
    }
    .bg-soft-primary {
        background-color: #e7f1ff;
        color: #0d6efd;
    }
    .avatar-circle {
        width: 60px; height: 60px;
        background: #f8f9fa;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; margin: 0 auto 15px;
        font-size: 1.5rem; color: #6c757d;
    }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-right">
        <div>
            <h2 class="fw-bold mb-1">
                <?php 
                if ($id_clb_filter > 0) {
                    $clb_res = $conn->query("SELECT ten_clb FROM clb WHERE id = $id_clb_filter");
                    if($clb_res && $clb_res->num_rows > 0) {
                        $clb = $clb_res->fetch_assoc();
                        echo "Thành Viên: <span class='text-info'>" . htmlspecialchars($clb['ten_clb']) . "</span>";
                    }
                } else {
                    echo "Danh Sách <span class='text-info'>Thành Viên</span>";
                }
                ?>
            </h2>
            <p class="text-muted">Quản lý nhân sự và thành viên các câu lạc bộ</p>
        </div>
        
        <?php if ($is_my_clb): ?>
            <a href="them_thanhvien.php?id_clb=<?= $id_clb_filter ?>" class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="bi bi-person-plus-fill me-2"></i>Thêm Thành Viên
            </a>
        <?php endif; ?>
    </div>

    <div class="card mb-5 border-0 shadow-sm rounded-4 bg-light">
        <div class="card-body p-3">
            <form action="" method="GET" class="row g-2">
                <input type="hidden" name="id_clb" value="<?= $id_clb_filter ?>">
                <div class="col-md-9">
                    <input type="text" name="q" class="form-control border-0 px-4 py-2 rounded-pill shadow-sm" 
                           placeholder="Tìm tên, mã sinh viên, ban..." value="<?= htmlspecialchars($keyword) ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php
        $sql = "SELECT t.*, c.ten_clb 
                FROM thanhvien t 
                LEFT JOIN clb c ON t.id_clb = c.id 
                $where_clause 
                ORDER BY t.id DESC 
                LIMIT $start, $limit";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
        <div class="col-lg-3 col-md-6" data-aos="fade-up">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover text-center">
                <div class="card-body p-4 d-flex flex-column">
                    <span class="badge bg-soft-primary mb-3 align-self-center px-3 py-2 rounded-pill">
                        <i class="bi bi-bookmark-fill me-1"></i>
                        <?= htmlspecialchars($row['ten_clb'] ?? 'Thành viên chung') ?>
                    </span>
                    
                    <div class="avatar-circle">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($row['hoten']) ?></h5>
                    <p class="text-muted small mb-3"><?= htmlspecialchars($row['masv']) ?></p>
                    
                    <div class="mb-3">
                        <div class="text-info fw-bold small text-uppercase"><?= htmlspecialchars($row['ban']) ?></div>
                        <div class="badge bg-light text-dark border"><?= htmlspecialchars($row['chucvu']) ?></div>
                    </div>

                    <div class="mt-auto pt-3 border-top">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-calendar-event me-1"></i> Tham gia: <?= date("d/m/Y", strtotime($row['ngaythamgia'])) ?>
                        </p>
                        
                        <?php 
                        $can_edit = false;
                        if (isset($_SESSION['role'])) {
                            $role = mb_strtolower($_SESSION['role'], 'UTF-8');
                            if ($role === 'admin') $can_edit = true;
                            if (($role === 'chunhiem' || $role === 'chủ nhiệm') && $row['id_clb'] == $_SESSION['id_clb']) $can_edit = true;
                        }
                        
                        if ($can_edit): 
                        ?>
                            <div class="btn-group w-100">
                                <a href="sua_thanhvien.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning btn-sm border-0"><i class="bi bi-pencil-square"></i></a>
                                <a href="xoa_thanhvien.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm border-0" onclick="return confirm('Xóa thành viên này?')"><i class="bi bi-trash"></i></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-person-x" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Không tìm thấy dữ liệu thành viên.</p>
                <a href="danhsach_thanhvien.php" class="btn btn-outline-primary btn-sm rounded-pill">Làm mới danh sách</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm mx-1 rounded-circle" 
                   href="?id_clb=<?= $id_clb_filter ?>&q=<?= urlencode($keyword) ?>&p_mem=<?= $page - 1 ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link border-0 shadow-sm mx-1 rounded-circle <?= ($page == $i) ? 'bg-primary text-white' : '' ?>" 
                       href="?id_clb=<?= $id_clb_filter ?>&q=<?= urlencode($keyword) ?>&p_mem=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm mx-1 rounded-circle" 
                   href="?id_clb=<?= $id_clb_filter ?>&q=<?= urlencode($keyword) ?>&p_mem=<?= $page + 1 ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>