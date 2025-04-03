<?php
// Get all courses
$courses = getAllCourses($pdo);
?>

<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Danh sách Học phần</h4>
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
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Không có học phần nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

