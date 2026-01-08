<?php 
include '../connect.php'; 
session_start();

$id_clb_url = isset($_GET['id_clb']) ? intval($_GET['id_clb']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $masv = $conn->real_escape_string($_POST['masv']);
    $hoten = $conn->real_escape_string($_POST['hoten']);
    $ban = $conn->real_escape_string($_POST['ban']);
    $chucvu = $conn->real_escape_string($_POST['chucvu']);
    $ngaythamgia = $_POST['ngaythamgia'];
    $id_clb = intval($_POST['id_clb']);

    // --- LOGIC MỚI: Kiểm tra xem sinh viên đã ở trong CLB này chưa ---
    $check_sql = "SELECT * FROM thanhvien WHERE masv = '$masv' AND id_clb = $id_clb";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        // Chỉ báo lỗi nếu sinh viên này đã là thành viên của CLB đang chọn
        $error = "Sinh viên này đã tham gia câu lạc bộ này rồi!";
    } else {
        // Cho phép thêm mới (dù mã SV đã tồn tại ở CLB khác)
        $sql = "INSERT INTO thanhvien (masv, hoten, ban, chucvu, ngaythamgia, id_clb) 
                VALUES ('$masv', '$hoten', '$ban', '$chucvu', '$ngaythamgia', $id_clb)";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: danhsach_thanhvien.php?id_clb=$id_clb");
            exit();
        } else {
            $error = "Lỗi hệ thống: " . $conn->error;
        }
    }
}

include '../header.php'; 
?>

<div class="container mt-5">
    <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
        <h3 class="text-center mb-4 text-primary">Thêm Thành Viên Mới</h3>
        
        <?php if(isset($error)): ?>
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>Chú ý:</strong> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label fw-bold">Câu lạc bộ tham gia</label>
                <select name="id_clb" class="form-select" required>
                    <option value="">-- Chọn câu lạc bộ --</option>
                    <?php
                    $clbs = $conn->query("SELECT id, ten_clb FROM clb");
                    while($row = $clbs->fetch_assoc()) {
                        $selected = ($row['id'] == $id_clb_url) ? "selected" : "";
                        echo "<option value='".$row['id']."' $selected>".$row['ten_clb']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Họ và Tên</label>
                <input type="text" name="hoten" class="form-control" placeholder="Nguyễn Văn A" value="<?= isset($_POST['hoten']) ? $_POST['hoten'] : '' ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Mã Sinh Viên</label>
                    <input type="text" name="masv" class="form-control" placeholder="Mã số SV" value="<?= isset($_POST['masv']) ? $_POST['masv'] : '' ?>" required>
                    <div class="form-text">Một SV có thể tham gia nhiều CLB khác nhau.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Ngày tham gia</label>
                    <input type="date" name="ngaythamgia" class="form-control" value="<?= isset($_POST['ngaythamgia']) ? $_POST['ngaythamgia'] : date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Ban chuyên môn</label>
                <select name="ban" class="form-select" required>
                    <option value="">-- Chọn ban --</option>
                    <?php 
                    $ds_ban = ["Truyền thông", "Kỹ thuật", "Sự kiện", "Đối ngoại"];
                    foreach($ds_ban as $b) {
                        $sel = (isset($_POST['ban']) && $_POST['ban'] == $b) ? "selected" : "";
                        echo "<option value='$b' $sel>$b</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Chức vụ</label>
                <select name="chucvu" class="form-select" required>
                    <option value="">-- Chọn chức vụ --</option>
                    <option value="Thành viên" <?= (isset($_POST['chucvu']) && $_POST['chucvu'] == "Thành viên") ? "selected" : "" ?>>Thành viên</option>
                    <option value="Trưởng ban" <?= (isset($_POST['chucvu']) && $_POST['chucvu'] == "Trưởng ban") ? "selected" : "" ?>>Trưởng ban</option>
                    <option value="Phó chủ nhiệm" <?= (isset($_POST['chucvu']) && $_POST['chucvu'] == "Phó chủ nhiệm") ? "selected" : "" ?>>Phó chủ nhiệm</option>
                    <option value="Chủ nhiệm" <?= (isset($_POST['chucvu']) && $_POST['chucvu'] == "Chủ nhiệm") ? "selected" : "" ?>>Chủ nhiệm</option>
                </select>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus-fill"></i> Xác nhận thêm
                </button>
                <a href="danhsach_thanhvien.php?id_clb=<?= $id_clb_url ?>" class="btn btn-light border">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>