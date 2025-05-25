<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../../Connection/connection.php';
$loggedInUsername = $_SESSION['username'];
$user_id_to_resign = null;
$sql_fetch_id = "SELECT ID FROM employee WHERE Username = '" . $conn->real_escape_string($loggedInUsername) . "'";
$result_fetch_id = $conn->query($sql_fetch_id);
if ($result_fetch_id && $result_fetch_id->num_rows > 0) {
    $user_data = $result_fetch_id->fetch_assoc();
    $user_id_to_resign = $user_data['ID'];
} else {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: User not found for resignation.'];
    header("Location: account_details.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirm_resign']) && $_POST['confirm_resign'] === 'yes') {
        if ($user_id_to_resign) {
            $delete_sql = "DELETE FROM employee WHERE ID = " . $user_id_to_resign;
            if ($conn->query($delete_sql) === TRUE) {
                session_destroy();
                $_SESSION = array(); 
                setcookie('remember_user', '', time() - 3600, '/');
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Your account has been successfully resigned and deleted.'];
                header("Location: ../index.php"); 
                exit();
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Error resigning account: ' . $conn->error];
                header("Location: account_details.php"); 
                exit();
            }
        }
    } else {
        $_SESSION['message'] = ['type' => 'info', 'text' => 'Resignation cancelled.'];
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
    <title>Confirm Resignation</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_dashboard.css">
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_account_details.css">
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_resign.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="main-content" style="margin-left: 0; width: 100%;">
            <div class="content-area" style="display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 60px);">
                <div class="confirmation-container">
                    <h2>Confirm Account Resignation</h2>
                    <p>Are you absolutely sure you want to resign? This action cannot be undone and will permanently delete your account and all associated data from the system.</p>
                    <form method="POST" class="confirmation-buttons">
                        <button type="submit" name="confirm_resign" value="yes" class="confirm-button">Yes, Resign My Account</button>
                        <button type="submit" name="confirm_resign" value="no" class="cancel-button">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>