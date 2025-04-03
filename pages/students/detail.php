<?php
// Get student ID from URL
$maSV = isset($_GET['id']) ? $_GET['id'] : '';

// Check if student exists
if (empty($maSV) || !studentExists($pdo, $maSV)) {
    $_SESSION['message'] = 'Sinh viên không tồn tại';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
    exit;
}

// Check if user is admin or viewing their own profile
if (!isset($_SESSION['is_admin']) && $_SESSION['user_id'] !== $maSV) {
    $_SESSION['message'] = 'Bạn không có quyền truy cập trang này';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
    exit;
}

// Get student data
$student = getStudentById($pdo, $maSV);

// Get registered courses
$registeredCourses = getRegisteredCourses($pdo, $maSV);
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Thông tin Sinh viên</h4>
            </div>
            <div class="card-body text-center">
                <img src="<?php echo !empty($student['Hinh']) ? $student['Hinh'] : 'assets/images/default-avatar.jpg'; ?>" 
                     alt="<?php echo $student['HoTen']; ?>" 
                     class="student-detail-image mb-3">
                <h3><?php echo $student['HoTen']; ?></h3>
                <p class="text-muted">Mã SV: <?php echo $student['MaSV']; ?></p>
                <hr>
                <div class="text-start">
                    <p><strong>Giới tính:</strong> <?php echo $student['GioiTinh']; ?></p>
                    <p><strong>Ngày sinh:</strong> <?php echo date('d/m/Y', strtotime($student['NgaySinh'])); ?></p>
                    <p><strong>Ngành học:</strong> <?php echo $student['TenNganh']; ?></p>
                </div>
                <?php if (isset($_SESSION['is_admin']) || $_SESSION['user_id'] === $student['MaSV']): ?>
                <div class="mt-3">
                    <a href="index.php?page=student-edit&id=<?php echo $student['MaSV']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Học phần đã đăng ký</h4>
            </div>
            <div class="card-body">
                <?php if (count($registeredCourses) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mã HP</th>
                                <th>Tên học phần</th>
                                <th>Số tín chỉ</th>
                                <th>Ngày đăng ký</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registeredCourses as $course): ?>
                            <tr>
                                <td><?php echo $course['MaHP']; ?></td>
                                <td><?php echo $course['TenHP']; ?></td>
                                <td><?php echo $course['SoTinChi']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($course['NgayDK'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    Chưa có học phần nào được đăng ký.
                </div>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_id'] === $student['MaSV']): ?>
                <div class="mt-3">
                    <a href="index.php?page=register-courses" class="btn btn-success">
                        <i class="fas fa-plus"></i> Đăng ký học phần mới
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

