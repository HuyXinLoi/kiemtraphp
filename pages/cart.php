<?php
// Get cart items
$cartItems = getCartItems($pdo);

// Get student information
$student = getStudentById($pdo, $_SESSION['user_id']);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Xác nhận đăng ký học phần</h4>
        <a href="index.php?page=register-courses" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Tiếp tục chọn học phần
        </a>
    </div>
    <div class="card-body">
        <!-- Thông tin sinh viên -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Thông tin sinh viên</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 text-center">
                                <img src="<?php echo !empty($student['Hinh']) ? $student['Hinh'] : 'assets/images/default-avatar.jpg'; ?>" 
                                     alt="<?php echo $student['HoTen']; ?>" 
                                     class="student-image mb-2">
                            </div>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Mã sinh viên:</strong> <?php echo $student['MaSV']; ?></p>
                                        <p><strong>Họ tên:</strong> <?php echo $student['HoTen']; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Ngành học:</strong> <?php echo $student['TenNganh']; ?></p>
                                        <p><strong>Giới tính:</strong> <?php echo $student['GioiTinh']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Danh sách học phần đã chọn -->
        <?php if (count($cartItems) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã HP</th>
                        <th>Tên học phần</th>
                        <th>Số tín chỉ</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?php echo $item['MaHP']; ?></td>
                        <td><?php echo $item['TenHP']; ?></td>
                        <td><?php echo $item['SoTinChi']; ?></td>
                        <td>
                            <a href="actions/cart-actions.php?action=remove&course_id=<?php echo $item['MaHP']; ?>" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-primary">
                        <th colspan="2" class="text-end">Tổng số tín chỉ:</th>
                        <th><?php echo array_sum(array_column($cartItems, 'SoTinChi')); ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-4">
            <a href="actions/cart-actions.php?action=clear" class="btn btn-secondary me-2">
                <i class="fas fa-times"></i> Hủy đăng ký
            </a>
            <a href="actions/cart-actions.php?action=checkout" class="btn btn-success">
                <i class="fas fa-check"></i> Xác nhận đăng ký
            </a>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            Bạn chưa chọn học phần nào. <a href="index.php?page=register-courses" class="alert-link">Chọn ngay</a>
        </div>
        <?php endif; ?>
    </div>
</div>

