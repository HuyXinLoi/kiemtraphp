<?php
// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['message'] = 'Bạn không có quyền truy cập trang này';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
    exit;
}

// Get all students
$students = getAllStudents($pdo);
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
                                    <a href="actions/student-actions.php?action=delete&id=<?php echo $student['MaSV']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="confirmDelete(event, 'sinh viên')" 
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
    </div>
</div>

