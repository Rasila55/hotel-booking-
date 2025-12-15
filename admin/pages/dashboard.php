<?php
$page_title = "Dashboard";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="card">
        <h2>Welcome to Dashboard</h2>
        <p>You are successfully logged in as an admin.</p>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3>Total Users</h3>
            <p style="font-size: 32px; margin-top: 10px;">
                <?php
                $user_count = readAll('users');
                echo count($user_count);
                ?>
            </p>
        </div>
        
        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <h3>Active Sessions</h3>
            <p style="font-size: 32px; margin-top: 10px;">
                
        </div>
        
        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <h3>Reports</h3>
            <p style="font-size: 32px; margin-top: 10px;">23</p>
        </div>
        
        <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <h3>Tasks</h3>
            <p style="font-size: 32px; margin-top: 10px;">12</p>
        </div>
    </div>
    
    <div class="card">
        <h2>Recent Activity</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #ddd;">
                    <th style="padding: 10px; text-align: left;">User</th>
                    <th style="padding: 10px; text-align: left;">Action</th>
                    <th style="padding: 10px; text-align: left;">Time</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;">John Doe</td>
                    <td style="padding: 10px;">Logged in</td>
                    <td style="padding: 10px;">2 mins ago</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;">Jane Smith</td>
                    <td style="padding: 10px;">Updated profile</td>
                    <td style="padding: 10px;">15 mins ago</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Bob Johnson</td>
                    <td style="padding: 10px;">Created report</td>
                    <td style="padding: 10px;">1 hour ago</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>