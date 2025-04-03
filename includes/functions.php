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

// Function to get all students with pagination
function getAllStudents($pdo, $page = 1, $perPage = 10) {
    // Calculate offset
    $offset = ($page - 1) * $perPage;
    
    // Get students for current page
    $stmt = $pdo->prepare("SELECT SinhVien.*, NganhHoc.TenNganh 
                         FROM SinhVien 
                         LEFT JOIN NganhHoc ON SinhVien.MaNganh = NganhHoc.MaNganh
                         ORDER BY SinhVien.MaSV
                         LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

// Function to count total students
function countAllStudents($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM SinhVien");
    return $stmt->fetchColumn();
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
    $stmt = $pdo->prepare("SELECT HocPhan.*, DangKy.NgayDK, DangKy.MaDK
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

// // Function to clear cart
// function clearCart() {
//     $_SESSION['cart'] = [];
// }

// Function to generate pagination links
function generatePagination($currentPage, $totalPages, $urlPattern) {
    $links = '';
    
    // Previous button
    if ($currentPage > 1) {
        $links .= '<li class="page-item"><a class="page-link" href="' . sprintf($urlPattern, $currentPage - 1) . '">&laquo;</a></li>';
    } else {
        $links .= '<li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    // Always show first page
    if ($startPage > 1) {
        $links .= '<li class="page-item"><a class="page-link" href="' . sprintf($urlPattern, 1) . '">1</a></li>';
        if ($startPage > 2) {
            $links .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
    }
    
    // Page links
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $links .= '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
        } else {
            $links .= '<li class="page-item"><a class="page-link" href="' . sprintf($urlPattern, $i) . '">' . $i . '</a></li>';
        }
    }
    
    // Always show last page
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $links .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
        $links .= '<li class="page-item"><a class="page-link" href="' . sprintf($urlPattern, $totalPages) . '">' . $totalPages . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $links .= '<li class="page-item"><a class="page-link" href="' . sprintf($urlPattern, $currentPage + 1) . '">&raquo;</a></li>';
    } else {
        $links .= '<li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>';
    }
    
    return $links;
}
?>

