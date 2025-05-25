<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../Connection/connection.php';
$user_id = null;
$user_details = null;
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql = "SELECT ID, FULLNAME, Email, PHONE, DOB, Gender, Address, ProfileImage, EmployeeId, Department, Designation, JoiningDate, Salary, Username FROM employee WHERE ID = " . $user_id;
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $user_details = $result->fetch_assoc();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'User not found.'];
        header("Location: account_details.php"); 
        exit();
    }
} else {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'No user ID provided.'];
    header("Location: account_details.php"); 
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $employeeId = $_POST['employeeId'];
    $department = $_POST['department'];
    $designation = $_POST['designation'];
    $joiningDate = $_POST['joiningDate'];
    $salary = $_POST['salary'];
    $username = $_POST['username'];
    $new_password = $_POST['new_password']; 
    $fullname = $conn->real_escape_string($fullname);
    $email = $conn->real_escape_string($email);
    $phone = $conn->real_escape_string($phone);
    $dob = $conn->real_escape_string($dob);
    $gender = $conn->real_escape_string($gender);
    $address = $conn->real_escape_string($address);
    $employeeId = $conn->real_escape_string($employeeId);
    $department = $conn->real_escape_string($department);
    $designation = $conn->real_escape_string($designation);
    $joiningDate = $conn->real_escape_string($joiningDate);
    $salary = $conn->real_escape_string($salary);
    $username = $conn->real_escape_string($username);
    $password_update_sql = "";
    if (!empty($new_password)) {
        $new_password = $conn->real_escape_string($new_password); 
        $password_update_sql = ", Password = '$new_password'"; 
    }
    $update_sql = "UPDATE employee SET
                    FULLNAME = '$fullname',
                    Email = '$email',
                    PHONE = '$phone',
                    DOB = '$dob',
                    Gender = '$gender',
                    Address = '$address',
                    EmployeeId = '$employeeId',
                    Department = '$department',
                    Designation = '$designation',
                    JoiningDate = '$joiningDate',
                    Salary = '$salary',
                    Username = '$username'
                    $password_update_sql
                    WHERE ID = " . $user_id;
    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Account details updated successfully!'];
        header("Location: account_details.php");
        exit();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Error updating record: ' . $conn->error];
        header("Location: account_details.php");
        exit();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<head>
    <title>Update Account Details</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_dashboard.css">
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_account_details.css">
</head>
<body>
    </body>
</html>