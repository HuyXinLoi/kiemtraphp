<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'Bạn cần đăng nhập để thực hiện hành động này';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../index.php?page=login');
    exit;
}

// Get course ID from URL
$courseId = isset($_GET['course_id']) ? $_GET['course_id'] : '';

// Validate input
if (empty($courseId)) {
    $_SESSION['message'] = 'Mã học phần không hợp lệ';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../index.php?page=register-courses');
    exit;
}

// Check if course exists
$stmt = $pdo->prepare("SELECT * FROM HocPhan WHERE MaHP = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch();

if (!$course) {
    $_SESSION['message'] = 'Học phần không tồn tại';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../index.php?page=register-courses');
    exit;
}

// Check if course is already registered
if (isCourseRegistered($pdo, $_SESSION['user_id'], $courseId)) {
    $_SESSION['message'] = 'Bạn đã đăng ký học phần này';
    $_SESSION['message_type'] = 'warning';
    header('Location: ../index.php?page=register-courses');
    exit;
}

// Check if course is available (has slots)
if (isset($course['SoLuongDuKien']) && $course['SoLuongDuKien'] <= 0) {
    $_SESSION['message'] = 'Học phần đã hết chỗ';
    $_SESSION['message_type'] = 'warning';
    header('Location: ../index.php?page=register-courses');
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Create registration
    $stmt = $pdo->prepare("INSERT INTO DangKy (NgayDK, MaSV) VALUES (NOW(), ?)");
    $stmt->execute([$_SESSION['user_id']]);
    $maDK = $pdo->lastInsertId();
    
    // Add course to registration
    $stmt = $pdo->prepare("INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (?, ?)");
    $stmt->execute([$maDK, $courseId]);
    
    // Decrease available slots
    if (isset($course['SoLuongDuKien'])) {
        $stmt = $pdo->prepare("UPDATE HocPhan SET SoLuongDuKien = SoLuongDuKien - 1 WHERE MaHP = ?");
        $stmt->execute([$courseId]);
    }
    
    // Commit transaction
    $pdo->commit();
    
    $_SESSION['message'] = 'Đăng ký học phần thành công';
    $_SESSION['message_type'] = 'success';
    header('Location: ../index.php?page=my-courses');
    exit;
} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    
    $_SESSION['message'] = 'Lỗi: ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
    header('Location: ../index.php?page=register-courses');
    exit;
}
?>

