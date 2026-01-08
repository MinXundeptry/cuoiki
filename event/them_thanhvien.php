<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../connect.php'; 
include '../header.php'; 

if (isset($_POST['btnThem'])) {
    // Lấy dữ liệu và bảo mật
    $masv = $conn->real_escape_string($_POST['masv']);
    $hoten = $conn->real_escape_string($_POST['hoten']);
    $ban = $conn->real_escape_string($_POST['ban']);
    $chucvu = $conn->real_escape_string($_POST['chucvu']);
    $ngaythamgia = $_POST['ngaythamgia'];

    $sql = "INSERT INTO thanhvien (masv, hoten, ban, chucvu, ngaythamgia) 
            VALUES ('$masv', '$hoten', '$ban', '$chucvu', '$ngaythamgia')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Thêm thành viên thành công!'); window.location.href='danhsach_thanhvien.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Lỗi: " . $conn->error . "</div>";
    }
     header("Location: danhsach_thanhvien.php");    
}

   
?>


<div class="container mt-5">
    <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
        <h3 class="text-center mb-4 text-primary">Thêm Thành Viên Mới</h3>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Họ và Tên</label>
                <input type="text" name="hoten" class="form-control" placeholder="Nguyễn Văn A" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Mã Sinh Viên</label>
                    <input type="text" name="masv" class="form-control" placeholder="Mã số SV" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Ngày tham gia</label>
                    <input type="date" name="ngaythamgia" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Ban chuyên môn</label>
                <select name="ban" class="form-select" required>
                    <option value="">-- Chọn ban --</option>
                    <option value="Truyền thông">Truyền thông</option>
                    <option value="Kỹ thuật">Kỹ thuật</option>
                    <option value="Sự kiện">Sự kiện</option>
                    <option value="Đối ngoại">Đối ngoại</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Chức vụ</label>
                <select name="chucvu" class="form-select" required>
                    <option value="">-- Chọn chức vụ --</option>
                    <option value="Thành viên">Thành viên</option>
                    <option value="Trưởng ban">Trưởng ban</option>
                    <option value="Phó chủ nhiệm">Phó chủ nhiệm</option>
                    <option value="Chủ nhiệm">Chủ nhiệm</option>
                </select>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" name="btnThem" class="btn btn-primary">
                    <i class="bi bi-person-plus-fill"></i> Lưu Thành Viên
                </button>
                <a href="danhsach_thanhvien.php" class="btn btn-light border">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>