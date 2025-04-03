<?php
// Get cart items
$cartItems = getCartItems($pdo);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Học phần đã chọn</h4>
        <a href="index.php?page=register-courses" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Tiếp tục chọn học phần
        </a>
    </div>
    <div class="card-body">
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
                            <button class="btn btn-danger btn-sm" onclick="removeFromCart('<?php echo $item['MaHP']; ?>')">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <p><strong>Tổng số tín chỉ:</strong> 
                <?php 
                $totalCredits = array_sum(array_column($cartItems, 'SoTinChi'));
                echo $totalCredits;
                ?>
            </p>
        </div>
        <div class="d-flex justify-content-end mt-3">
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

