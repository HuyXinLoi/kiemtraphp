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

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Process based on action
switch ($action) {
    case 'cancel':
        cancelCourseRegistration();
        break;
    case 'cancel_all':
        cancelAllCourseRegistrations();
        break;
    default:
        $_SESSION['message'] = 'Hành động không hợp lệ';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=my-courses');
        exit;
}

// Function to cancel a course registration
function cancelCourseRegistration() {
    global $pdo;
    
    // Get course ID from URL
    $courseId = isset($_GET['course_id']) ? $_GET['course_id'] : '';
    
    // Validate input
    if (empty($courseId)) {
        $_SESSION['message'] = 'Mã học phần không hợp lệ';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=my-courses');
        exit;
    }
    
    // Check if course is registered by the user
    if (!isCourseRegistered($pdo, $_SESSION['user_id'], $courseId)) {
        $_SESSION['message'] = 'Bạn chưa đăng ký học phần này';
        $_SESSION['message_type'] = 'warning';
        header('Location: ../index.php?page=my-courses');
        exit;
    }
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Get course information
        $stmt = $pdo->prepare("SELECT * FROM HocPhan WHERE MaHP = ?");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch();
        
        // Get registration ID
        $stmt = $pdo->prepare("SELECT DangKy.MaDK 
                               FROM DangKy 
                               JOIN ChiTietDangKy ON DangKy.MaDK = ChiTietDangKy.MaDK 
                               WHERE DangKy.MaSV = ? AND ChiTietDangKy.MaHP = ?");
        $stmt->execute([$_SESSION['user_id'], $courseId]);
        $registration = $stmt->fetch();
        
        if (!$registration) {
            throw new Exception('Không tìm thấy thông tin đăng ký');
        }
        
        // Delete course registration detail
        $stmt = $pdo->prepare("DELETE FROM ChiTietDangKy WHERE MaDK = ? AND MaHP = ?");
        $stmt->execute([$registration['MaDK'], $courseId]);
        
        // Check if there are any other courses in this registration
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ChiTietDangKy WHERE MaDK = ?");
        $stmt->execute([$registration['MaDK']]);
        $remainingCourses = $stmt->fetchColumn();
        
        // If no other courses, delete the registration
        if ($remainingCourses == 0) {
            $stmt = $pdo->prepare("DELETE FROM DangKy WHERE MaDK = ?");
            $stmt->execute([$registration['MaDK']]);
        }
        
        // Increase available slots for the course
        if (isset($course['SoLuongDuKien'])) {
            $stmt = $pdo->prepare("UPDATE HocPhan SET SoLuongDuKien = SoLuongDuKien + 1 WHERE MaHP = ?");
            $stmt->execute([$courseId]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        $_SESSION['message'] = 'Hủy đăng ký học phần thành công';
        $_SESSION['message_type'] = 'success';
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        $_SESSION['message'] = 'Lỗi: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }
    
    header('Location: ../index.php?page=my-courses');
    exit;
}

// Function to cancel all course registrations
function cancelAllCourseRegistrations() {
    global $pdo;
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Get all registered courses for the user
        $registeredCourses = getRegisteredCourses($pdo, $_SESSION['user_id']);
        
        if (count($registeredCourses) == 0) {
            $_SESSION['message'] = 'Bạn chưa đăng ký học phần nào';
            $_SESSION['message_type'] = 'warning';
            header('Location: ../index.php?page=my-courses');
            exit;
        }
        
        // Get all registration IDs for the user
        $stmt = $pdo->prepare("SELECT MaDK FROM DangKy WHERE MaSV = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $registrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Increase available slots for each course
        foreach ($registeredCourses as $course) {
            if (isset($course['SoLuongDuKien'])) {
                $stmt = $pdo->prepare("UPDATE HocPhan SET SoLuongDuKien = SoLuongDuKien + 1 WHERE MaHP = ?");
                $stmt->execute([$course['MaHP']]);
            }
        }
        
        // Delete all course registration details for the user
        foreach ($registrations as $maDK) {
            $stmt = $pdo->prepare("DELETE FROM ChiTietDangKy WHERE MaDK = ?");
            $stmt->execute([$maDK]);
        }
        
        // Delete all registrations for the user
        $stmt = $pdo->prepare("DELETE FROM DangKy WHERE MaSV = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        // Commit transaction
        $pdo->commit();
        
        $_SESSION['message'] = 'Hủy tất cả đăng ký học phần thành công';
        $_SESSION['message_type'] = 'success';
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        $_SESSION['message'] = 'Lỗi: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }
    
    header('Location: ../index.php?page=my-courses');
    exit;
}
?>

