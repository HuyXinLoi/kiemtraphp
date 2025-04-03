<?php
// Function to sanitize input data
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if student ID exists
function studentExists($pdo, $maSV) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM SinhVien WHERE MaSV = ?");
    $stmt->execute([$maSV]);
    return $stmt->fetchColumn() > 0;
}

// Function to get all students
function getAllStudents($pdo) {
    $stmt = $pdo->query("SELECT SinhVien.*, NganhHoc.TenNganh 
                         FROM SinhVien 
                         LEFT JOIN NganhHoc ON SinhVien.MaNganh = NganhHoc.MaNganh
                         ORDER BY SinhVien.MaSV");
    return $stmt->fetchAll();
}

// Function to get student by ID
function getStudentById($pdo, $maSV) {
    $stmt = $pdo->prepare("SELECT SinhVien.*, NganhHoc.TenNganh 
                           FROM SinhVien 
                           LEFT JOIN NganhHoc ON SinhVien.MaNganh = NganhHoc.MaNganh
                           WHERE SinhVien.MaSV = ?");
    $stmt->execute([$maSV]);
    return $stmt->fetch();
}

// Function to get all majors
function getAllMajors($pdo) {
    $stmt = $pdo->query("SELECT * FROM NganhHoc ORDER BY TenNganh");
    return $stmt->fetchAll();
}

// Function to get all courses
function getAllCourses($pdo) {
    $stmt = $pdo->query("SELECT * FROM HocPhan ORDER BY TenHP");
    return $stmt->fetchAll();
}

// Function to get course by ID
function getCourseById($pdo, $maHP) {
    $stmt = $pdo->prepare("SELECT * FROM HocPhan WHERE MaHP = ?");
    $stmt->execute([$maHP]);
    return $stmt->fetch();
}

// Function to get registered courses for a student
function getRegisteredCourses($pdo, $maSV) {
    $stmt = $pdo->prepare("SELECT HocPhan.*, DangKy.NgayDK 
                           FROM HocPhan 
                           JOIN ChiTietDangKy ON HocPhan.MaHP = ChiTietDangKy.MaHP
                           JOIN DangKy ON ChiTietDangKy.MaDK = DangKy.MaDK
                           WHERE DangKy.MaSV = ?
                           ORDER BY DangKy.NgayDK DESC");
    $stmt->execute([$maSV]);
    return $stmt->fetchAll();
}

// Function to check if a course is already registered by a student
function isCourseRegistered($pdo, $maSV, $maHP) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM DangKy 
                           JOIN ChiTietDangKy ON DangKy.MaDK = ChiTietDangKy.MaDK
                           WHERE DangKy.MaSV = ? AND ChiTietDangKy.MaHP = ?");
    $stmt->execute([$maSV, $maHP]);
    return $stmt->fetchColumn() > 0;
}

// Function to add course to cart
function addToCart($courseId) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Check if course is already in cart
    if (!in_array($courseId, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $courseId;
    }
}

// Function to remove course from cart
function removeFromCart($courseId) {
    if (isset($_SESSION['cart'])) {
        $key = array_search($courseId, $_SESSION['cart']);
        if ($key !== false) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
        }
    }
}

// Function to get cart items
function getCartItems($pdo) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return [];
    }
    
    $items = [];
    foreach ($_SESSION['cart'] as $courseId) {
        $course = getCourseById($pdo, $courseId);
        if ($course) {
            $items[] = $course;
        }
    }
    
    return $items;
}

// Function to clear cart
function clearCart() {
    $_SESSION['cart'] = [];
}
?>
