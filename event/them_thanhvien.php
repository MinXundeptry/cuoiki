<?php 
include '../connect.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * 1. KIỂM TRA QUYỀN TRUY CẬP
 */
$user_role = isset($_SESSION['role']) ? mb_strtolower($_SESSION['role'], 'UTF-8') : '';
$is_management = in_array($user_role, ['admin', 'chunhiem', 'chủ nhiệm']);

if (!$is_management) {
    echo "<script>alert('Bạn không có quyền thực hiện hành động này!'); window.location.href='../index.php';</script>";
    exit();
}

// XÁC ĐỊNH ID CLB: 
// Nếu là chủ nhiệm, lấy id_clb từ session. Nếu là admin, lấy từ URL.
$id_clb_user = (int)($_SESSION['id_clb'] ?? 0);
$id_clb_target = ($user_role === 'admin') ? (isset($_GET['id_clb']) ? intval($_GET['id_clb']) : 0) : $id_clb_user;

if ($id_clb_target <= 0) {
    echo "<script>alert('Không xác định được câu lạc bộ!'); window.location.href='danhsach_thanhvien.php';</script>";
    exit();
}

// Lấy tên CLB để hiển thị tiêu đề
$clb_query = $conn->query("SELECT ten_clb FROM clb WHERE id = $id_clb_target");
$clb_data = $clb_query->fetch_assoc();
$ten_clb_hien_tai = $clb_data['ten_clb'] ?? "Câu lạc bộ";

$error = "";

/**
 * 2. XỬ LÝ KHI SUBMIT FORM
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $masv = trim($_POST['masv']);
    $hoten = trim($_POST['hoten']);
    $ban = $_POST['ban'];
    $chucvu = $_POST['chucvu'];
    $ngaythamgia = $_POST['ngaythamgia'];

    // Kiểm tra xem sinh viên đã tồn tại trong CLB này chưa
    $stmt_check = $conn->prepare("SELECT id FROM thanhvien WHERE masv = ? AND id_clb = ?");
    $stmt_check->bind_param("si", $masv, $id_clb_target);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $error = "Mã sinh viên này đã tham gia $ten_clb_hien_tai rồi!";
    } else {
        // Thêm mới thành viên với id_clb_target đã xác định sẵn
        $stmt_ins = $conn->prepare("INSERT INTO thanhvien (masv, hoten, ban, chucvu, ngaythamgia, id_clb) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_ins->bind_param("sssssi", $masv, $hoten, $ban, $chucvu, $ngaythamgia, $id_clb_target);
        
        if ($stmt_ins->execute()) {
            header("Location: danhsach_thanhvien.php?id_clb=$id_clb_target");
            exit();
        } else {
            $error = "Lỗi hệ thống: " . $conn->error;
        }
    }
}

include '../header.php'; 
?>

<div class="container mt-5 mb-5">
    <div class="card shadow border-0 col-md-8 mx-auto" style="border-radius: 15px;">
        <div class="card-header bg-primary text-white py-3" style="border-radius: 15px 15px 0 0;">
            <h5 class="mb-0 fw-bold text-center">
                <i class="bi bi-person-plus-fill me-2"></i> THÊM THÀNH VIÊN
            </h5>
        </div>
        
        <div class="card-body p-4">
            <?php if(!empty($error)): ?>
                <div class='alert alert-danger alert-dismissible fade show border-0 shadow-sm' role='alert'>
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="id_clb" value="<?= $id_clb_target ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">HỌ VÀ TÊN</label>
                    <input type="text" name="hoten" class="form-control shadow-sm" placeholder="Ví dụ: Nguyễn Văn A" 
                           value="<?= htmlspecialchars($_POST['hoten'] ?? '') ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">MÃ SINH VIÊN</label>
                        <input type="text" name="masv" class="form-control shadow-sm" placeholder="Mã số SV" 
                               value="<?= htmlspecialchars($_POST['masv'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">NGÀY THAM GIA</label>
                        <input type="date" name="ngaythamgia" class="form-control shadow-sm" 
                               value="<?= $_POST['ngaythamgia'] ?? date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">BAN CHUYÊN MÔN</label>
                        <select name="ban" class="form-select shadow-sm" required>
                            <option value="">-- Chọn ban --</option>
                            <?php 
                            $ds_ban = ["Truyền thông", "Kỹ thuật", "Hậu cần", "Sự kiện", "Đối ngoại", "Văn nghệ", "Chưa xếp ban"];
                            foreach($ds_ban as $b) {
                                $sel = (isset($_POST['ban']) && $_POST['ban'] == $b) ? "selected" : "";
                                echo "<option value='$b' $sel>$b</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">CHỨC VỤ</label>
                        <select name="chucvu" class="form-select shadow-sm" required>
                            <option value="">-- Chọn chức vụ --</option>
                            <?php 
                            $ds_cv = ["Thành viên", "Trưởng ban", "Phó ban", "Phó chủ nhiệm", "Chủ nhiệm"];
                            foreach($ds_cv as $cv) {
                                $sel = (isset($_POST['chucvu']) && $_POST['chucvu'] == $cv) ? "selected" : "";
                                echo "<option value='$cv' $sel>$cv</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between gap-2 mt-4 pt-3 border-top">
                    <a href="danhsach_thanhvien.php?id_clb=<?= $id_clb_target ?>" class="btn btn-light border px-4 shadow-sm">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                        <i class="bi bi-check-lg me-2"></i> Xác nhận thêm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>