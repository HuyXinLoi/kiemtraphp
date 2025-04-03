<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // AJAX request
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để thực hiện hành động này']);
        exit;
    } else {
        // Regular request
        $_SESSION['message'] = 'Bạn cần đăng nhập để thực hiện hành động này';
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=login');
        exit;
    }
}

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Process based on action
switch ($action) {
    case 'add':
        addCourseToCart();
        break;
    case 'remove':
        removeCourseFromCart();
        break;
    case 'clear':
        clearCart();
        break;
    case 'checkout':
        checkoutCart();
        break;
    default:
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // AJAX request
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
            exit;
        } else {
            // Regular request
            $_SESSION['message'] = 'Hành động không hợp lệ';
            $_SESSION['message_type'] = 'danger';
            header('Location: ../index.php');
            exit;
        }
}

// Function to add a course to cart
function addCourseToCart() {
    global $pdo;
    
    // Get course ID from URL
    $courseId = isset($_GET['course_id']) ? $_GET['course_id'] : '';
    
    // Validate input
    if (empty($courseId)) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // AJAX request
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Mã học phần không hợp lệ']);
            exit;
        } else {
            // Regular request
            $_SESSION['message'] = 'Mã học phần không hợp lệ';
            $_SESSION['message_type'] = 'danger';
            header('Location: ../index.php?page=register-courses');
            exit;
        }
    }
    
    // Check if course exists
    $stmt = $pdo->prepare("SELECT * FROM HocPhan WHERE MaHP = ?");
    $stmt->execute([$courseId]);
    $course = $stmt->fetch();
    
    if (!$course) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // AJAX request
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Học phần không tồn tại']);
            exit;
        } else {
            // Regular request
            $_SESSION['message'] = 'Học phần không tồn tại';
            $_SESSION['message_type'] = 'danger';
            header('Location: ../index.php?page=register-courses');
            exit;
        }
    }
    
    // Check if course is already registered
    if (isCourseRegistered($pdo, $_SESSION['user_id'], $courseId)) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // AJAX request
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Bạn đã đăng ký học phần này']);
            exit;
        } else {
            // Regular request
            $_SESSION['message'] = 'Bạn đã đăng ký học phần này';
            $_SESSION['message_type'] = 'warning';
            header('Location: ../index.php?page=register-courses');
            exit;
        }
    }
    
    // Check if course is available (has slots)
    if (isset($course['SoLuongDuKien']) && $course['SoLuongDuKien'] <= 0) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // AJAX request
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Học phần đã hết chỗ']);
            exit;
        } else {
            // Regular request
            $_SESSION['message'] = 'Học phần đã hết chỗ';
            $_SESSION['message_type'] = 'warning';
            header('Location: ../index.php?page=register-courses');
            exit;
        }
    }
    
    // Add course to cart
    addToCart($courseId);
    
    // Thay đổi thông báo khi thêm học phần
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // AJAX request
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Đã chọn học phần',
            'count' => count($_SESSION['cart'])
        ]);
        exit;
    } else {
        // Regular request
        $_SESSION['message'] = 'Đã chọn học phần';
        $_SESSION['message_type'] = 'success';
        header('Location: ../index.php?page=register-courses');
        exit;
    }
}

// Function to remove a course from cart
function removeCourseFromCart() {
    // Get course ID from URL
    $courseId = isset($_GET['course_id']) ? $_GET['course_id'] : '';
    
    // Validate input
    if (empty($courseId)) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // AJAX request
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Mã học phần không hợp lệ']);
            exit;
        } else {
            // Regular request
            $_SESSION['message'] = 'Mã học phần không hợp lệ';
            $_SESSION['message_type'] = 'danger';
            header('Location: ../index.php?page=cart');
            exit;
        }
    }
    
    // Remove course from cart
    removeFromCart($courseId);
    
    // Thay đổi thông báo khi xóa học phần
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // AJAX request
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Đã bỏ chọn học phần',
            'count' => isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0
        ]);
        exit;
    } else {
        // Regular request
        $_SESSION['message'] = 'Đã bỏ chọn học phần';
        $_SESSION['message_type'] = 'success';
        header('Location: ../index.php?page=cart');
        exit;
    }
}

// Function to clear cart
function clearCart() {
    // Clear cart
    $_SESSION['cart'] = [];
    
    // Thay đổi thông báo khi xóa tất cả học phần
    $_SESSION['message'] = 'Đã bỏ chọn tất cả học phần';
    $_SESSION['message_type'] = 'success';
    header('Location: ../index.php?page=register-courses');
    exit;
}

// Function to checkout cart
function checkoutCart() {
    global $pdo;
    
    // Thay đổi thông báo khi giỏ trống
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $_SESSION['message'] = 'Bạn chưa chọn học phần nào';
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
        
        // Add courses to registration
        foreach ($_SESSION['cart'] as $courseId) {
            // Check if course exists and has available slots
            $stmt = $pdo->prepare("SELECT * FROM HocPhan WHERE MaHP = ?");
            $stmt->execute([$courseId]);
            $course = $stmt->fetch();
            
            if (!$course) {
                throw new Exception('Học phần không tồn tại: ' . $courseId);
            }
            
            // Check if course is already registered
            if (isCourseRegistered($pdo, $_SESSION['user_id'], $courseId)) {
                throw new Exception('Bạn đã đăng ký học phần: ' . $course['TenHP']);
            }
            
            // Check if course has available slots
            if (isset($course['SoLuongDuKien'])) {
                if ($course['SoLuongDuKien'] <= 0) {
                    throw new Exception('Học phần đã hết chỗ: ' . $course['TenHP']);
                }
                
                // Decrease available slots
                $stmt = $pdo->prepare("UPDATE HocPhan SET SoLuongDuKien = SoLuongDuKien - 1 WHERE MaHP = ?");
                $stmt->execute([$courseId]);
            }
            
            // Add course to registration
            $stmt = $pdo->prepare("INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (?, ?)");
            $stmt->execute([$maDK, $courseId]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        $_SESSION['message'] = 'Đăng ký học phần thành công';
        $_SESSION['message_type'] = 'success';
        header('Location: ../index.php?page=my-courses');
        exit;
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        $_SESSION['message'] = 'Lỗi: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
        header('Location: ../index.php?page=cart');
        exit;
    }
}
?>

