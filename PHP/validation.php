<?php

$fullNameErr = $emailErr = $phoneErr = $dobErr = $genderErr = $addressErr = "";
$employeeIdErr = $departmentErr = $designationErr = $joiningDateErr = $salaryErr = "";
$usernameErr = $passwordErr = $confirmPasswordErr = "";

$fullName = $email = $phone = $dob = $gender = $address = "";
$employeeId = $department = $designation = $joiningDate = $salary = "";
$username = $password = $confirmPassword = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    if (empty($_POST["full_name"])) {
        $fullNameErr = "Full Name is required";
    } else {
        $fullName = test_input($_POST["full_name"]);
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["phone"])) {
        $phoneErr = "Phone Number is required";
    } else {
        $phone = test_input($_POST["phone"]);
        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            $phoneErr = "Invalid phone number format";
        }
    }

    if (empty($_POST["dob"])) {
        $dobErr = "Date of Birth is required";
    } else {
        $dob = test_input($_POST["dob"]);
    }

    if (empty($_POST["gender"])) {
        $genderErr = "Gender is required";
    } else {
        $gender = test_input($_POST["gender"]);
    }

    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
    } else {
        $address = test_input($_POST["address"]);
    }

    if (empty($_POST["employee_id"])) {
        $employeeIdErr = "Employee ID is required";
    } else {
        $employeeId = test_input($_POST["employee_id"]);
    }

    if (empty($_POST["department"])) {
        $departmentErr = "Department is required";
    } else {
        $department = test_input($_POST["department"]);
    }

    if (empty($_POST["designation"])) {
        $designationErr = "Designation is required";
    } else {
        $designation = test_input($_POST["designation"]);
    }

    if (empty($_POST["joining_date"])) {
        $joiningDateErr = "Date of Joining is required";
    } else {
        $joiningDate = test_input($_POST["joining_date"]);
    }

    if (empty($_POST["salary"])) {
        $salaryErr = "Salary is required";
    } else {
        $salary = test_input($_POST["salary"]);
        if (!is_numeric($salary) || $salary <= 0) {
            $salaryErr = "Invalid salary";
        }
    }

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

    if (empty($_POST["confirm_password"])) {
        $confirmPasswordErr = "Confirm Password is required";
    } else {
        $confirmPassword = test_input($_POST["confirm_password"]);
        if ($password != $confirmPassword) {
            $confirmPasswordErr = "Passwords do not match";
        }
    }

    if (empty($fullNameErr) && empty($emailErr) && empty($phoneErr) && empty($dobErr) && empty($genderErr) && empty($addressErr) &&
        empty($employeeIdErr) && empty($departmentErr) && empty($designationErr) && empty($joiningDateErr) && empty($salaryErr) &&
        empty($usernameErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
        echo "<h3>Registration Successful!</h3>";
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>