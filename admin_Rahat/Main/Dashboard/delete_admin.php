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
include '../../Connection/connection.php'; 
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $admin_id = $conn->real_escape_string($_GET['id']);
    $delete_sql = "DELETE FROM admin WHERE ID = " . $admin_id;
    if ($conn->query($delete_sql) === TRUE) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Admin record deleted successfully!'];
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Error deleting admin record: ' . $conn->error];
    }
} else {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'No admin ID provided for deletion.'];
}
$conn->close(); 
header("Location: manage_admins.php");
exit();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Admin</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_dashboard.css">
    <link rel="stylesheet" type="text/css" href="../../CSS/styles.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="main-content">
            <div class="content-area">
                <p style="text-align: center; padding: 20px;">Processing deletion...</p>
            </div>
        </div>
    </div>
</body>
</html>