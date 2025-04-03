<?php
// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['message'] = 'Bạn không có quyền truy cập trang này';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
    exit;
}

// Pagination settings
$perPage = 4; // Number of students per page
$page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
if ($page < 1) $page = 1;

// Get total number of students
$totalStudents = countAllStudents($pdo);
$totalPages = ceil($totalStudents / $perPage);

// Get students for current page
$students = getAllStudents($pdo, $page, $perPage);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Danh sách Sinh viên</h4>
        <a href="index.php?page=student-create" class="btn btn-success">
            <i class="fas fa-plus"></i> Thêm Sinh viên
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Mã SV</th>
                        <th>Hình</th>
                        <th>Họ tên</th>
                        <th>Giới tính</th>
                        <th>Ngày sinh</th>
                        <th>Ngành học</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo $student['MaSV']; ?></td>
                                <td>
                                    <img src="<?php echo !empty($student['Hinh']) ? $student['Hinh'] : 'assets/images/default-avatar.jpg'; ?>" 
                                         alt="<?php echo $student['HoTen']; ?>" 
                                         class="student-image">
                                </td>
                                <td><?php echo $student['HoTen']; ?></td>
                                <td><?php echo $student['GioiTinh']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($student['NgaySinh'])); ?></td>
                                <td><?php echo $student['TenNganh']; ?></td>
                                <td>
                                    <a href="index.php?page=student-detail&id=<?php echo $student['MaSV']; ?>" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="index.php?page=student-edit&id=<?php echo $student['MaSV']; ?>" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?page=student-delete&id=<?php echo $student['MaSV']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       data-bs-toggle="tooltip" 
                                       title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Không có sinh viên nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php echo generatePagination($page, $totalPages, 'index.php?page=students&page_num=%d'); ?>
                </ul>
            </nav>
        </div>
        <div class="text-center mt-2">
            <small class="text-muted">Hiển thị <?php echo count($students); ?> sinh viên trên tổng số <?php echo $totalStudents; ?> sinh viên</small>
        </div>
        <?php endif; ?>
    </div>
</div>

