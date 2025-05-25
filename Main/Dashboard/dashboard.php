<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}
if (isset($_GET['logout'])) {
    session_destroy();
    $_SESSION = array(); 
    setcookie('remember_user', '', time() - 3600, '/'); 
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_dashboard.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>My Account</h3>
            </div>
            <ul class="sidebar-nav">
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="../Account/account_details.php"><i class="fas fa-user-circle"></i> Account Details</a></li>
                <li><a href="?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h2>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
            </div>
            <div class="content-area">
                <div class="dashboard-overview-cards">
                    <div class="overview-card employees">
                        <div class="overview-label">Hi Employees</div>  
                    </div>
                </div>
                    </div>
                </div>
            </div>
</body>
</html>