<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['message'] = 'Bạn không có quyền thực hiện hành động này';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../index.php');
    exit;
}

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Process based on action
switch ($action) {
    case 'create':
        createStudent();
        break;
    case 'update':
        updateStudent();
        break;
    case 'delete':
        deleteStudent();
        break;
    default:
        $_SESSION['message'] = 'Hành động không hợp lệ';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=students');
        exit;
}

// Function to create a new student
function createStudent() {
    global $pdo;
    
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['message'] = 'Phương thức không hợp lệ';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=students');
        exit;
    }
    
    // Get form data
    $maSV = sanitize($_POST['maSV']);
    $hoTen = sanitize($_POST['hoTen']);
    $gioiTinh = sanitize($_POST['gioiTinh']);
    $ngaySinh = sanitize($_POST['ngaySinh']);
    $maNganh = sanitize($_POST['maNganh']);
    $password = sanitize($_POST['password']);
    $confirmPassword = sanitize($_POST['confirmPassword']);
    
    // Validate input
    if (empty($maSV) || empty($hoTen) || empty($gioiTinh) || empty($ngaySinh) || empty($maNganh) || empty($password)) {
        $_SESSION['message'] = 'Vui lòng nhập đầy đủ thông tin';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=student-create');
        exit;
    }
    
    // Check if passwords match
    if ($password !== $confirmPassword) {
        $_SESSION['message'] = 'Mật khẩu xác nhận không khớp';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=student-create');
        exit;
    }
    
    // Check if student ID already exists
    if (studentExists($pdo, $maSV)) {
        $_SESSION['message'] = 'Mã sinh viên đã tồn tại';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=student-create');
        exit;
    }
    
    // Handle image upload
    $hinh = '';
    if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/images/students/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['hinh']['name']);
        $targetFile = $uploadDir . $fileName;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['hinh']['tmp_name'], $targetFile)) {
            $hinh = 'assets/images/students/' . $fileName;
        } else {
            $_SESSION['message'] = 'Lỗi khi tải lên hình ảnh';
            $_SESSION['message_type'] = 'danger';
            header('Location: ../index.php?page=student-create');
            exit;
        }
    }
    
    try {
        // Insert new student
        $stmt = $pdo->prepare("INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh, MatKhau) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$maSV, $hoTen, $gioiTinh, $ngaySinh, $hinh, $maNganh, md5($password)]);
        
        if ($result) {
            $_SESSION['message'] = 'Thêm sinh viên thành công';
            $_SESSION['message_type'] = 'success';
            header('Location: ../index.php?page=students');
            exit;
        } else {
            $_SESSION['message'] = 'Lỗi khi thêm sinh viên';
            $_SESSION['message_type'] = 'danger';
            header('Location: ../index.php?page=student-create');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Lỗi: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=student-create');
        exit;
    }
}

// Function to update a student
function updateStudent() {
    global $pdo;
    
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['message'] = 'Phương thức không hợp lệ';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=students');
        exit;
    }
    
    // Get form data
    $maSV = sanitize($_POST['maSV']);
    $hoTen = sanitize($_POST['hoTen']);
    $gioiTinh = sanitize($_POST['gioiTinh']);
    $ngaySinh = sanitize($_POST['ngaySinh']);
    $maNganh = sanitize($_POST['maNganh']);
    $password = sanitize($_POST['password']);
    
    // Validate input
    if (empty($maSV) || empty($hoTen) || empty($gioiTinh) || empty($ngaySinh) || empty($maNganh)) {
        $_SESSION['message'] = 'Vui lòng nhập đầy đủ thông tin';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=student-edit&id=' . $maSV);
        exit;
    }
    
    // Check if student exists
    if (!studentExists($pdo, $maSV)) {
        $_SESSION['message'] = 'Sinh viên không tồn tại';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=students');
        exit;
    }
    
    // Get current student data
    $stmt = $pdo->prepare("SELECT * FROM SinhVien WHERE MaSV = ?");
    $stmt->execute([$maSV]);
    $student = $stmt->fetch();
    
    // Handle image upload
    $hinh = $student['Hinh'];
    if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/images/students/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['hinh']['name']);
        $targetFile = $uploadDir . $fileName;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['hinh']['tmp_name'], $targetFile)) {
            $hinh = 'assets/images/students/' . $fileName;
            
            // Delete old image if it exists
            if (!empty($student['Hinh']) && file_exists('../' . $student['Hinh'])) {
                unlink('../' . $student['Hinh']);
            }
        } else {
            $_SESSION['message'] = 'Lỗi khi tải lên hình ảnh';
            $_SESSION['message_type'] = 'danger';
            header('Location: ../index.php?page=student-edit&id=' . $maSV);
            exit;
        }
    }
    
    try {
        // Update student
        if (!empty($password)) {
            // Update with new password
            $stmt = $pdo->prepare("UPDATE SinhVien SET HoTen = ?, GioiTinh = ?, NgaySinh = ?, Hinh = ?, MaNganh = ?, MatKhau = ? WHERE MaSV = ?");
            $result = $stmt->execute([$hoTen, $gioiTinh, $ngaySinh, $hinh, $maNganh, md5($password), $maSV]);
        } else {
            // Update without changing password
            $stmt = $pdo->prepare("UPDATE SinhVien SET HoTen = ?, GioiTinh = ?, NgaySinh = ?, Hinh = ?, MaNganh = ? WHERE MaSV = ?");
            $result = $stmt->execute([$hoTen, $gioiTinh, $ngaySinh, $hinh, $maNganh, $maSV]);
        }
        
        if ($result) {
            $_SESSION['message'] = 'Cập nhật sinh viên thành công';
            $_SESSION['message_type'] = 'success';
            header('Location: ../index.php?page=students');
            exit;
        } else {
            $_SESSION['message'] = 'Lỗi khi cập nhật sinh viên';
            $_SESSION['message_type'] = 'danger';
            header('Location: ../index.php?page=student-edit&id=' . $maSV);
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Lỗi: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=student-edit&id=' . $maSV);
        exit;
    }
}

// Function to delete a student
function deleteStudent() {
    global $pdo;
    
    // Get student ID from URL
    $maSV = isset($_GET['id']) ? $_GET['id'] : '';
    
    // Validate input
    if (empty($maSV)) {
        $_SESSION['message'] = 'Mã sinh viên không hợp lệ';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=students');
        exit;
    }
    
    // Check if student exists
    if (!studentExists($pdo, $maSV)) {
        $_SESSION['message'] = 'Sinh viên không tồn tại';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=students');
        exit;
    }
    
    // Get student data to delete image
    $stmt = $pdo->prepare("SELECT Hinh FROM SinhVien WHERE MaSV = ?");
    $stmt->execute([$maSV]);
    $student = $stmt->fetch();
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Delete registrations
        $stmt = $pdo->prepare("SELECT MaDK FROM DangKy WHERE MaSV = ?");
        $stmt->execute([$maSV]);
        $registrations = $stmt->fetchAll();
        
        foreach ($registrations as $registration) {
            // Delete registration details
            $stmt = $pdo->prepare("DELETE FROM ChiTietDangKy WHERE MaDK = ?");
            $stmt->execute([$registration['MaDK']]);
            
            // Delete registration
            $stmt = $pdo->prepare("DELETE FROM DangKy WHERE MaDK = ?");
            $stmt->execute([$registration['MaDK']]);
        }
        
        // Delete student
        $stmt = $pdo->prepare("DELETE FROM SinhVien WHERE MaSV = ?");
        $result = $stmt->execute([$maSV]);
        
        // Commit transaction
        $pdo->commit();
        
        if ($result) {
            // Delete student image if it exists
            if (!empty($student['Hinh']) && file_exists('../' . $student['Hinh'])) {
                unlink('../' . $student['Hinh']);
            }
            
            $_SESSION['message'] = 'Xóa sinh viên thành công';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Lỗi khi xóa sinh viên';
            $_SESSION['message_type'] = 'danger';
        }
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        $_SESSION['message'] = 'Lỗi: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }
    
    header('Location: ../index.php?page=students');
    exit;
}
?>

