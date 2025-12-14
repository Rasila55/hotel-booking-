<?php
session_start();

define('BASE_PATH', '/staymate/admin');

require_once 'helpers/config.php';
require_once 'helpers/crud.php';

$url = isset($_GET['url']) ? $_GET['url'] : 'dashboard';

if ($url === 'login' && isset($_SESSION['is_admin'])) {
    header('Location: ' . BASE_PATH . '/dashboard');
    exit();
}

if (!isset($_SESSION['is_admin']) && $url !== 'login') {
    header('Location: ' . BASE_PATH . '/login');
    exit();
}

if ($url === 'logout') {
    session_destroy();
    header('Location: ' . BASE_PATH . '/login');
    exit();
}

if ($url === 'login') {
    include 'login.php';
} else {
    $page = explode('/', $url)[0]; 
    $page_file = "pages/{$page}.php";
    
    if (file_exists($page_file)) {
        include $page_file;
    } else {
        include 'pages/dashboard.php'; 
    }
}
?>