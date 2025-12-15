<style>
    .sidebar {
        position: fixed;
        left: 0;
        top: 60px;
        width: 250px;
        height: calc(100vh - 60px);
        background: white;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        overflow-y: auto;
    }

    .sidebar ul {
        list-style: none;
        padding: 20px 0;
    }

    .sidebar ul li {
        margin-bottom: 5px;
    }

    .sidebar ul li a {
        display: block;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        transition: background 0.3s;
    }

    .sidebar ul li a:hover,
    .sidebar ul li a.active {
        background: #667eea;
        color: white;
    }

    .sidebar ul li a i {
        margin-right: 10px;
    }
</style>

<div class="sidebar">
    <ul>
<li>
  <a href="http://localhost/staymate" target="_blank" rel="noopener noreferrer">
    Visit Website
  </a>
</li>

        <li><a href="<?php echo BASE_PATH; ?>/dashboard" class="<?php echo ($url === 'dashboard') ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="<?php echo BASE_PATH; ?>/hotels" class="<?php echo ($url === 'hotels') ? 'active' : ''; ?>">Hotels</a></li>
                <li><a href="<?php echo BASE_PATH; ?>/rooms" class="<?php echo ($url === 'rooms') ? 'active' : ''; ?>">Rooms</a></li>

                 

    </ul>
</div>