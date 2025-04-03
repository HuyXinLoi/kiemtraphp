<?php
// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maSV = sanitize($_POST['maSV']);
    $password = sanitize($_POST['password']);
    
    // Validate input
    if (empty($maSV) || empty($password)) {
        $_SESSION['message'] = 'Vui lòng nhập đầy đủ thông tin đăng nhập';
        $_SESSION['message_type'] = 'danger';
    } else {
        // Check if student exists and password is correct
        $stmt = $pdo->prepare("SELECT * FROM SinhVien WHERE MaSV = ? AND MatKhau = ?");
        $stmt->execute([$maSV, md5($password)]);
        $student = $stmt->fetch();
        
        if ($student) {
            // Set session variables
            $_SESSION['user_id'] = $student['MaSV'];
            $_SESSION['user_name'] = $student['HoTen'];
            $_SESSION['is_admin'] = ($student['MaSV'] === 'admin'); // Simple admin check
            
            // Redirect to home page
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['message'] = 'Mã sinh viên hoặc mật khẩu không đúng';
            $_SESSION['message_type'] = 'danger';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Đăng nhập</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="maSV" class="form-label">Mã sinh viên</label>
                        <input type="text" class="form-control" id="maSV" name="maSV" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Đăng nhập</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
