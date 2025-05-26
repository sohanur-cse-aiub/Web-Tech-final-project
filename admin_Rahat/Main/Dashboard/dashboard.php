<?php
session_start();
if (!isset($_SESSION['admin_username'])) {
    header("Location: ../index.php"); 
    exit();
}
if (isset($_GET['logout'])) {
    session_destroy(); 
    $_SESSION = array(); 
    setcookie('remember_admin', '', time() - 3600, '/');
    header("Location: ../index.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_dashboard.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
            </div>
            <ul class="sidebar-nav">
                <li><a href="dashboard.php" class="active"> Dashboard</a></li>
                <li><a href="manage_employees.php"> Manage Employees</a></li>
                <li><a href="manage_admins.php"> Manage Admins</a></li>
                <li><a href="?logout=true"> Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h2>Hello, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h2>
                </div>
            <div class="content-area">
                <div class="dashboard-overview-cards">
                    <div class="overview-card employees">
                        <div class="overview-label">Hi Admin</div>
                    </div>
                </div>
                </div>
        </div>
    </div>
</body>
</html>