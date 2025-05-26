<?php
session_start();
include '../PHP/login_session.php';
if (isset($_SESSION['admin_username'])) {
    header("Location: Dashboard/dashboard.php");
    exit();
}
$loginError = $loginError ?? "";
$usernameErr = $usernameErr ?? "";
$passwordErr = $passwordErr ?? "";
$username = $username ?? ""; 
$password = $password ?? ""; 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" type="text/css" href="../CSS/styles_login.css">
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <?php if (!empty($loginError)) { ?>
                <p class="error"><?php echo htmlspecialchars($loginError); ?></p>
            <?php } ?>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>">
                <?php if (!empty($usernameErr)) { ?>
                    <span class="error"><?php echo htmlspecialchars($usernameErr); ?></span>
                <?php } ?>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" value="">
                <?php if (!empty($passwordErr)) { ?>
                    <span class="error"><?php echo htmlspecialchars($passwordErr); ?></span>
                <?php } ?>
            </div>
            <div style="margin-bottom: 15px; display: flex; align-items: center;">
                <input type="checkbox" name="remember_me" id="remember_me" style="margin-right: 5px;">
                <label for="remember_me" style="display: inline-block; margin-bottom: 0;">Remember me</label>
            </div>
            <div class="form-group" style="text-align: center;">
                <button type="submit">Login</button>
            </div>
            <p>Don't have an account? <a href="registration.php">Register</a></p>
        </form>
    </div>
</body>
</html>