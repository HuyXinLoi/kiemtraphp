<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in for protected pages
$protected_pages = ['dashboard.php', 'register-courses.php', 'my-courses.php'];
$current_page = basename($_SERVER['PHP_SELF']);

if (in_array($current_page, $protected_pages) && !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Include header
include 'includes/header.php';

// Determine which page to load
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Load the appropriate page
switch ($page) {
    case 'home':
        include 'pages/home.php';
        break;
    case 'students':
        include 'pages/students/index.php';
        break;
    case 'student-create':
        include 'pages/students/create.php';
        break;
    case 'student-edit':
        include 'pages/students/edit.php';
        break;
    case 'student-detail':
        include 'pages/students/detail.php';
        break;
    case 'student-delete':
        include 'pages/students/delete.php';
        break;
    case 'courses':
        include 'pages/courses/index.php';
        break;
    case 'register-courses':
        include 'pages/courses/register.php';
        break;
    case 'my-courses':
        include 'pages/courses/my-courses.php';
        break;
    case 'login':
        include 'pages/login.php';
        break;
    case 'logout':
        include 'pages/logout.php';
        break;
    default:
        include 'pages/404.php';
        break;
}

// Include footer
include 'includes/footer.php';
?>

