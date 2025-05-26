<?php
session_start();
if (!isset($_SESSION['admin_username'])) {
    header("Location: ../index.php"); 
    exit();
}
include '../../Connection/connection.php'; 
$loggedInUsername = $_SESSION['admin_username'];
$admin_id_to_resign = null;
$sql_fetch_id = "SELECT ID FROM admin WHERE username = '" . $conn->real_escape_string($loggedInUsername) . "'";
$result_fetch_id = $conn->query($sql_fetch_id);
if ($result_fetch_id && $result_fetch_id->num_rows > 0) {
    $admin_data = $result_fetch_id->fetch_assoc();
    $admin_id_to_resign = $admin_data['ID'];
} else {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: Admin user not found for resignation.'];
    header("Location: account_details.php"); 
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirm_resign']) && $_POST['confirm_resign'] === 'yes') {
        if ($admin_id_to_resign) {
            $delete_sql = "DELETE FROM admin WHERE ID = " . $admin_id_to_resign;
            if ($conn->query($delete_sql) === TRUE) {
                session_destroy();
                $_SESSION = array(); 
                setcookie('remember_admin', '', time() - 3600, '/'); 
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Your admin account has been successfully resigned.'];
                header("Location: ../index.php"); 
                exit();
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Error resigning account: ' . $conn->error];
                header("Location: account_details.php"); 
                exit();
            }
        }
    } elseif (isset($_POST['confirm_resign']) && $_POST['confirm_resign'] === 'no') {
        $_SESSION['message'] = ['type' => 'info', 'text' => 'Admin account resignation cancelled.'];
        header("Location: account_details.php");
        exit();
    }
}
$conn->close(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Admin Resignation</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_dashboard.css">
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_account_details.css">
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_resign.css">
    <link rel="stylesheet" href="https:
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
            </div>
            <ul class="sidebar-nav">
                <li><a href="../Dashboard/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="account_details.php"><i class="fas fa-user-circle"></i> Admin Account Details</a></li>
                <li><a href="?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        <div class="main-content" style="margin-left: 0; width: 100%;">
            <div class="content-area" style="display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 60px);">
                <div class="confirmation-container">
                    <h2>Confirm Admin Account Resignation</h2>
                    <p>Are you absolutely sure you want to resign your admin account? This action cannot be undone and will permanently delete your admin account and all associated data from the system.</p>
                    <form method="POST" class="confirmation-buttons">
                        <button type="submit" name="confirm_resign" value="yes" class="confirm-button">Yes, Resign My Admin Account</button>
                        <button type="submit" name="confirm_resign" value="no" class="cancel-button">No, Go Back</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>