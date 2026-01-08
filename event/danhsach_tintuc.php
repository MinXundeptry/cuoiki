<?php
include '../connect.php';
include '../header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">📰 Tin tức CLB</h2>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="them_tintuc.php" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Thêm tin
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <?php
        $sql = "SELECT * FROM tintuc ORDER BY ngay_dang DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
            <div class="col-md-4 d-flex">
                <div class="card shadow-sm w-100 d-flex flex-column border-0 news-card">
                    <a href="chitiet_tintuc.php?id=<?= $row['id'] ?>">
                        <img src="<?= htmlspecialchars($row['hinh_anh']) ?>" 
                             alt="<?= htmlspecialchars($row['tieu_de']) ?>"
                             class="news-img" style="height:220px; object-fit:cover; width:100%; border-radius:6px;">
                    </a>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2">
                            <a href="chitiet_tintuc.php?id=<?= $row['id'] ?>" class="text-dark text-decoration-none fw-bold">
                                <?= htmlspecialchars($row['tieu_de']) ?>
                            </a>
                        </h5>

                        <p class="text-muted small mb-1">📍 <?= htmlspecialchars($row['dia_diem']) ?></p>
                        <p class="text-muted small mb-2">🗓 <?= date('d/m/Y', strtotime($row['ngay_dang'])) ?></p>

                        <p class="card-text text-secondary" style="flex-grow:1; overflow:hidden; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical;">
                            <?= htmlspecialchars($row['noi_dung']) ?>
                        </p>

                        <div class="d-flex justify-content-end gap-2 mt-auto">
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <a href="sua_tintuc.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i> Sửa
                                </a>
                                <a href="xoa_tintuc.php?id=<?= $row['id'] ?>" onclick="return confirm('Xóa tin tức này?')" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Xóa
                                </a>
                            <?php else: ?>
                                <a href="chitiet_tintuc.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Đọc tiếp
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php
            endwhile;
        else:
        ?>
            <div class="col-12 text-center text-muted py-5">Chưa có tin tức nào.</div>
        <?php endif; ?>
    </div>
</div>

<?php include '../footer.php'; ?>