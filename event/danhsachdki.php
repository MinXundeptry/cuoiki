<?php 
include '../connect.php'; 
include '../header.php'; 
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Sự Kiện <span class="text-primary">Đang Mở Đăng Ký</span></h2>
        <p class="text-muted">Tìm kiếm và chọn sự kiện bạn muốn tham gia.</p>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-md-6">
            <form action="" method="GET" class="d-flex shadow-sm rounded-pill overflow-hidden border">
                <input type="text" name="search" class="form-control border-0 px-4 py-2" 
                       placeholder="Nhập tên sự kiện hoặc địa điểm..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-search"></i> Tìm
                </button>
            </form>
            <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
                <div class="text-center mt-2">
                    <a href="danhsachdki.php" class="text-decoration-none small text-muted">
                        <i class="bi bi-x-circle"></i> Xóa tìm kiếm
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <?php
        // Xử lý logic tìm kiếm
        $where_clause = "";
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $keyword = mysqli_real_escape_string($conn, $_GET['search']);
            $where_clause = "WHERE s.ten LIKE '%$keyword%' OR s.diadiem LIKE '%$keyword%'";
        }

        // Truy vấn danh sách sự kiện kèm số lượng người đã đăng ký
        $sql = "SELECT s.*, 
                (SELECT COUNT(*) FROM dangky WHERE id_sukien = s.id AND trangthai != 'Da huy') as da_dang_ky 
                FROM sukien s 
                $where_clause 
                ORDER BY s.ngay DESC";
        
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $con_trong = $row['soluong'] - $row['da_dang_ky'];
                $phan_tram = ($row['soluong'] > 0) ? ($row['da_dang_ky'] / $row['soluong']) * 100 : 0;
                ?>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm border-0 transition-hover">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><?= htmlspecialchars($row['ten']) ?></h5>
                            <div class="mb-3">
                                <p class="small text-muted mb-1">
                                    <i class="bi bi-geo-alt-fill text-danger me-2"></i><?= htmlspecialchars($row['diadiem']) ?>
                                </p>
                                <p class="small text-muted">
                                    <i class="bi bi-calendar-event-fill text-primary me-2"></i><?= date("d/m/Y", strtotime($row['ngay'])) ?>
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Tình trạng chỗ:</small>
                                    <small class="fw-bold <?= ($con_trong <= 5) ? 'text-danger' : 'text-success' ?>">
                                        <?= $con_trong ?>/<?= $row['soluong'] ?> chỗ trống
                                    </small>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 10px;">
                                    <div class="progress-bar <?= ($phan_tram >= 90) ? 'bg-danger' : 'bg-primary' ?>" 
                                         style="width: <?= $phan_tram ?>%">
                                    </div>
                                </div>
                            </div>

                            <?php if($con_trong > 0): ?>
                                <a href="formdangki.php?id_sukien=<?= $row['id'] ?>" class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm">
                                    Đăng ký tham gia
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100 fw-bold rounded-pill" disabled>
                                    Đã hết chỗ
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php
            }
        } else {
            echo '
            <div class="col-12 text-center py-5">
                <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                <p class="text-muted">Không tìm thấy sự kiện nào phù hợp với từ khóa của bạn.</p>
                <a href="danhsachdki.php" class="btn btn-primary btn-sm rounded-pill px-4">Xem tất cả sự kiện</a>
            </div>';
        }
        ?>
    </div>
</div>

<style>
    .transition-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>

<?php include '../footer.php'; ?>