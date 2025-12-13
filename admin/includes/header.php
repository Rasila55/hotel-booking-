<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        
        .header {
            background: white;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }
        
        .header h1 {
            font-size: 24px;
            color: #333;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info {
            color: #666;
        }
        
        .logout-btn {
            padding: 8px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #c82333;
        }
        
        .container {
            display: flex;
            margin-top: 60px;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: 250px;
        }
        
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card h2 {
            margin-bottom: 15px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Panel</h1>
        <div class="header-right">
            <span class="user-info">Welcome, <?php echo $_SESSION['username'] ?? 'Admin'; ?></span>
            <a href="<?php echo BASE_PATH; ?>/logout" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="container">