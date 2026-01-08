<?php include '../header.php'; ?>
<?php include '../connect.php'; ?>

<div class="row mb-3 align-items-center">
    <div class="col-md-6">
        <h2>Danh Sách Thành Viên</h2>
    </div>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <div class="col-md-6 text-end">
            <a href="them_thanhvien.php" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Thêm thành viên
            </a>
        </div>
    <?php endif; ?>
</div>


    <div class="card mb-4 bg-light">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="q" class="form-control" 
                           placeholder="Nhập tên, mã sinh viên hoặc ban..." 
                           value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>STT</th>
                    <th>Mã SV</th>
                    <th>Họ và Tên</th>
                    <th>Ban</th>
                    <th>Chức vụ</th>
                    <th>Ngày tham gia</th>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <th class="text-center">Hành động</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $search = "";
                if (isset($_GET['q']) && !empty($_GET['q'])) {
                    $keyword = $conn->real_escape_string($_GET['q']);
                    $search = "WHERE hoten LIKE '%$keyword%' OR masv LIKE '%$keyword%' OR ban LIKE '%$keyword%'";
                }

                $sql = "SELECT * FROM thanhvien $search ORDER BY id DESC";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    $stt = 1;
                    while($row = $result->fetch_assoc()) {
                ?>
                        <tr>
                            <td><?php echo $stt++; ?></td>
                            <td><?php echo $row['masv']; ?></td>
                            <td><b><?php echo $row['hoten']; ?></b></td>
                            <td><span class='badge bg-info text-dark'><?php echo $row['ban']; ?></span></td>
                            <td><?php echo $row['chucvu']; ?></td>
                            <td><?php echo $row['ngaythamgia']; ?></td>
                            
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="sua_thanhvien.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning border-0" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="xoa_thanhvien.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Xóa thành viên này?')" title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                      
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                <?php
                    }       
                } else {
                    // Tính toán số cột cần gộp (colspan) tùy theo vai trò
                    $col_count = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 7 : 6;
                    echo "<tr><td colspan='$col_count' class='text-center'>Không tìm thấy dữ liệu</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>