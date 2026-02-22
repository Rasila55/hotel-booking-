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
        
        <div class="card" style="background: linear-gradient(135deg, #06d559 0%, #f5576c 100%); color: white;">
            <h3>All Bookings</h3>
            <p style="font-size: 32px; margin-top: 10px;">
                   <?php
                $user_count = readAll('bookings');
                echo count($user_count);
                ?>
                </p>
        </div>

          <div class="card" style="background: linear-gradient(135deg, #039122 0%, #f5576c 100%); color: white;">
            <h3>Pending Bookings</h3>
            <p style="font-size: 32px; margin-top: 10px;">
                   <?php
                $user_count = readAll('bookings', ['status' => 'pending']);
                echo count($user_count);
                ?>
                </p>
        </div>

            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h3>Total Hotels</h3>
                <p style="font-size: 32px; margin-top: 10px;">
                    <?php
                    $user_count = readAll('hotels');
                    echo count($user_count);
                    ?>
                </p>
                </div>

                <div class="card" style="background: linear-gradient(135deg, #06d559 0%, #f5576c 100%); color: white;">
                    <h3>Total Rooms</h3>
                    <p style="font-size: 32px; margin-top: 10px;">
                        <?php
                        $user_count = readAll('rooms');
                        echo count($user_count);
                        ?>
                    </p>
                </div>
        
        
        
       
    </div>
    
  
</div>

<?php include 'includes/footer.php'; ?>