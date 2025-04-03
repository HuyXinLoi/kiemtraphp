<?php
// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['message'] = 'Bạn không có quyền truy cập trang này';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
    exit;
}

// Get student ID from URL
$maSV = isset($_GET['id']) ? $_GET['id'] : '';

// Check if student exists
if (empty($maSV) || !studentExists($pdo, $maSV)) {
    $_SESSION['message'] = 'Sinh viên không tồn tại';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php?page=students');
    exit;
}

// Get student data
$student = getStudentById($pdo, $maSV);
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">Xác nhận xóa sinh viên</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="<?php echo !empty($student['Hinh']) ? $student['Hinh'] : 'assets/images/default-avatar.jpg'; ?>" 
                             alt="<?php echo $student['HoTen']; ?>" 
                             class="student-detail-image mb-3">
                    </div>
                    <div class="col-md-8">
                        <h3><?php echo $student['HoTen']; ?></h3>
                        <p class="text-muted">Mã SV: <?php echo $student['MaSV']; ?></p>
                        <hr>
                        <div>
                            <p><strong>Giới tính:</strong> <?php echo $student['GioiTinh']; ?></p>
                            <p><strong>Ngày sinh:</strong> <?php echo date('d/m/Y', strtotime($student['NgaySinh'])); ?></p>
                            <p><strong>Ngành học:</strong> <?php echo $student['TenNganh']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-4">
                    <i class="fas fa-exclamation-triangle"></i> Cảnh báo: Hành động này không thể hoàn tác. Tất cả dữ liệu liên quan đến sinh viên này, bao gồm thông tin đăng ký học phần, sẽ bị xóa vĩnh viễn.
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="index.php?page=students" class="btn btn-secondary me-2">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                    <a href="actions/student-actions.php?action=delete&id=<?php echo $student['MaSV']; ?>" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Xác nhận xóa
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

