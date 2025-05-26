<?php
$errors = array(); 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["full_name"])) {
        $errors["full_name"] = "Full Name is required";
    }
    if (empty($_POST["email"])) {
        $errors["email"] = "Email is required";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email format";
    }
    if (empty($_POST["phone_number"])) {
        $errors["phone_number"] = "Phone Number is required";
    }
    if (empty($_POST["gender"])) {
        $errors["gender"] = "Gender is required";
    }
    if (empty($_POST["username"])) {
        $errors["username"] = "Username is required";
    }
    if (empty($_POST["password"])) {
        $errors["password"] = "Password is required";
    }
    if (empty($_POST["confirm_password"])) {
        $errors["confirm_password"] = "Confirm Password is required";
    } elseif ($_POST["password"] != $_POST["confirm_password"]) {
        $errors["confirm_password"] = "Passwords do not match";
    }
    if (empty($_POST["role"]) || $_POST["role"] == "default") { 
        $errors["role"] = "Please select a role";
    }
    if (empty($_POST["security_question"])) {
        $errors["security_question"] = "Please select a security question";
    }
    if (empty($_POST["security_answer"])) {
        $errors["security_answer"] = "Security Answer is required";
    }
    if (isset($_FILES["profile_image"])) {
        if ($_FILES["profile_image"]["error"] == UPLOAD_ERR_NO_FILE) { 
            $errors["profile_image"] = "Profile Image is required";
        } elseif ($_FILES["profile_image"]["error"] != UPLOAD_ERR_OK) {
            $errors["profile_image"] = "Error uploading profile image (Code: " . $_FILES["profile_image"]["error"] . ")";
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $profileImageType = $_FILES["profile_image"]["type"];
            $profileImageSize = $_FILES["profile_image"]["size"];
            if (!in_array($profileImageType, $allowedTypes)) {
                $errors["profile_image"] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            }
            if ($profileImageSize > 2 * 1024 * 1024) { 
                $errors["profile_image"] = "File size too large. Maximum size is 2MB.";
            }
        }
    } else {
        $errors["profile_image"] = "Profile Image is required"; 
    }
}
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>