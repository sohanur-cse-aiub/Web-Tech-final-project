<?php
include '../PHP/login_session.php';
if (isset($_SESSION['username'])) {
    header("Location: Dashboard/dashboard.php");
    exit();
}
$profileImageErr = ""; 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="../CSS/styles_login.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <?php if (!empty($loginError)) { ?>
                <p class="error"><?php echo $loginError; ?></p>
            <?php } ?>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?php echo isset($username) ? $username : ''; ?>">
                <span class="error"><?php echo isset($usernameErr) ? $usernameErr : ''; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" value="<?php echo isset($password) ? $password : ''; ?>">
                <span class="error"><?php echo isset($passwordErr) ? $passwordErr : ''; ?></span>
            </div>
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="registration.php">Register</a></p>
        </form>
    </div>
</body>
</html>