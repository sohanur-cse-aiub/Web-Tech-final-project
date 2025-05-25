<?php
session_start();
require_once '../Connection/connection.php';
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
        $username = mysqli_real_escape_string($conn, $username); 
        $sql = "SELECT id, username, password FROM employee WHERE username = '$username'";
        $result = $conn->query($sql);
        if ($result) {
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $storedUsername = $row["username"];
                $storedPassword = $row["password"]; 
                $user_id = $row["id"];
                if ($username === $storedUsername && $password === $storedPassword) { 
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    session_regenerate_id(true); 
                    if (isset($_POST['remember_me'])) { 
                        $cookieValue = $username; 
                        $expire = time() + (86400 * 30); 
                        setcookie('remember_user', $cookieValue, $expire, '/');
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
if(!isset($_SESSION["username"]) && isset($_COOKIE["remember_user"])) {
  $username =  mysqli_real_escape_string($conn, $_COOKIE["remember_user"]);
  $sql = "SELECT id, username FROM employee WHERE username = '$username'";
  $result = $conn->query($sql);
  if($result) {
     if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row["id"];
        $_SESSION['username'] = $row["username"];
        session_regenerate_id(true);
        header("Location: Dashboard/dashboard.php");
        exit();
     }
  }
}
$conn->close();
?>