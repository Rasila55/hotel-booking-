<?php
session_start();

// Define base path for the application
define('BASE_PATH', '/staymate/admin');

// Include database config and CRUD helper
require_once 'helpers/config.php';
require_once 'helpers/crud.php';

// Check if user is trying to access login page
$url = isset($_GET['url']) ? $_GET['url'] : 'dashboard';

// If trying to access login and already logged in, redirect to dashboard
if ($url === 'login' && isset($_SESSION['is_admin'])) {
    header('Location: ' . BASE_PATH . '/dashboard');
    exit();
}

// If not logged in and not on login page, redirect to login
if (!isset($_SESSION['is_admin']) && $url !== 'login') {
    header('Location: ' . BASE_PATH . '/login');
    exit();
}

// Handle logout
if ($url === 'logout') {
    session_destroy();
    header('Location: ' . BASE_PATH . '/login');
    exit();
}

// Route to appropriate page
if ($url === 'login') {
    include 'login.php';
} else {
    // All other pages are protected - include the page from pages folder
    $page = explode('/', $url)[0]; // Get first segment
    $page_file = "pages/{$page}.php";
    
    if (file_exists($page_file)) {
        include $page_file;
    } else {
        include 'pages/dashboard.php'; // Default to dashboard
    }
}
?>