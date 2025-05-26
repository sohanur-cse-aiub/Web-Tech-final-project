<?php 
include '../Connection/connection.php'; 
$username = $password = "";
$usernameErr = $passwordErr = $loginError = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = test_input($_POST["username"]);
    }
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
    }
    if (empty($usernameErr) && empty($passwordErr)) {
        $username = $conn->real_escape_string($username); 
        $sql = "SELECT ID, username, password FROM admin WHERE username = '$username'";
        $result = $conn->query($sql);
        if ($result) {
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $storedUsername = $row["username"];
                $storedPassword = $row["password"]; 
                $admin_id = $row["ID"];
                if ($username === $storedUsername && $password === $storedPassword) {
                    $_SESSION['admin_id'] = $admin_id;
                    $_SESSION['admin_username'] = $username;
                    session_regenerate_id(true); 
                    if (isset($_POST['remember_me'])) {
                        $cookieValue = $username;
                        $expire = time() + (86400 * 30); 
                        setcookie('remember_admin', $cookieValue, $expire, '/');
                    } else {
                        setcookie('remember_admin', '', time() - 3600, '/');
                    }
                    header("Location: Dashboard/dashboard.php");
                    exit();
                } else {
                    $loginError = "Invalid username or password";
                }
            } else {
                $loginError = "Invalid username or password";
            }
        } else {
            $loginError = "Database error: " . $conn->error;
        }
    }
}
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
if (!isset($_SESSION["admin_username"]) && isset($_COOKIE["remember_admin"])) {
    $username = $conn->real_escape_string($_COOKIE["remember_admin"]);
    $sql = "SELECT ID, username FROM admin WHERE username = '$username'";
    $result = $conn->query($sql);
    if ($result) {
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $_SESSION['admin_id'] = $row["ID"];
            $_SESSION['admin_username'] = $row["username"];
            session_regenerate_id(true); 
            header("Location: Dashboard/dashboard.php");
            exit();
        }
    }
}
if (isset($conn)) {
    $conn->close();
}
?>