<?php
// Get registered courses for current user
$registeredCourses = getRegisteredCourses($pdo, $_SESSION['user_id']);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Học phần đã đăng ký</h4>
        <a href="index.php?page=register-courses" class="btn btn-success">
            <i class="fas fa-plus"></i> Đăng ký học phần mới
        </a>
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
        <div class="mt-3">
            <p><strong>Tổng số tín chỉ:</strong> 
                <?php 
                $totalCredits = array_sum(array_column($registeredCourses, 'SoTinChi'));
                echo $totalCredits;
                ?>
            </p>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            Bạn chưa đăng ký học phần nào. <a href="index.php?page=register-courses" class="alert-link">Đăng ký ngay</a>
        </div>
        <?php endif; ?>
    </div>
</div>

