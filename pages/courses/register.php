<?php
// Get all courses
$stmt = $pdo->query("SELECT * FROM HocPhan WHERE SoLuongDuKien > 0 OR SoLuongDuKien IS NULL ORDER BY TenHP");
$courses = $stmt->fetchAll();

// Get registered courses for current user
$registeredCourses = getRegisteredCourses($pdo, $_SESSION['user_id']);
$registeredCourseIds = array_column($registeredCourses, 'MaHP');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Đăng ký Học phần</h4>
                <a href="index.php?page=cart" class="btn btn-primary">
                    <i class="fas fa-clipboard-list"></i> Xem học phần đã chọn
                    <?php if (count($_SESSION['cart']) > 0): ?>
                    <span class="badge bg-danger"><?php echo count($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Mã HP</th>
                                <th>Tên học phần</th>
                                <th>Số tín chỉ</th>
                                <th>Số lượng còn lại</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($courses) > 0): ?>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?php echo $course['MaHP']; ?></td>
                                        <td><?php echo $course['TenHP']; ?></td>
                                        <td><?php echo $course['SoTinChi']; ?></td>
                                        <td><?php echo isset($course['SoLuongDuKien']) ? $course['SoLuongDuKien'] : 'Không giới hạn'; ?></td>
                                        <td>
                                            <?php if (in_array($course['MaHP'], $registeredCourseIds)): ?>
                                                <button class="btn btn-success btn-sm" disabled>
                                                    <i class="fas fa-check"></i> Đã đăng ký
                                                </button>
                                            <?php elseif (in_array($course['MaHP'], $_SESSION['cart'])): ?>
                                                <a href="actions/cart-actions.php?action=remove&course_id=<?php echo $course['MaHP']; ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-check"></i> Đã chọn
                                                </a>
                                            <?php else: ?>
                                                <a href="actions/cart-actions.php?action=add&course_id=<?php echo $course['MaHP']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> Chọn học phần
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Không có học phần nào khả dụng</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

