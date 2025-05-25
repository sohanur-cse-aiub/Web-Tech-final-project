<?php
$uploadDir = '../Uploaded_Images/';
$profileImageErr = "";
function uploadImage($employeeId, $profileImageName, $profileImageTmpName, $uploadDir) {
    $fileExtension = pathinfo($profileImageName, PATHINFO_EXTENSION);
    $uniqueName = $employeeId . "_" . uniqid() . "." . $fileExtension;
    $destination = $uploadDir . $uniqueName;
    if (move_uploaded_file($profileImageTmpName, $destination)) {
        return $uniqueName; 
    } else {
        return false; 
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullNameErr = "";
    $emailErr = "";
    $phoneErr = "";
    $dobErr = "";
    $genderErr = "";
    $addressErr = "";
    $employeeIdErr = "";
    $departmentErr = "";
    $designationErr = "";
    $joiningDateErr = "";
    $salaryErr = "";
    $usernameErr = "";
    $passwordErr = "";
    $confirmPasswordErr = "";
    $fullName = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    $employeeId = $_POST['employee_id'] ?? '';
    $department = $_POST['department'] ?? '';
    $designation = $_POST['designation'] ?? '';
    $joiningDate = $_POST['joining_date'] ?? '';
    $salary = $_POST['salary'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $imagePath = ""; 
    if (empty($_FILES["profile_image"]["name"])) {
        $profileImageErr = "Profile Image is required";
    } else {
        $profileImageName = $_FILES["profile_image"]["name"];
        $profileImageTmpName = $_FILES["profile_image"]["tmp_name"];
        $profileImageType = $_FILES["profile_image"]["type"];
        $profileImageSize = $_FILES["profile_image"]["size"];
        $profileImageError = $_FILES["profile_image"]["error"];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($profileImageType, $allowedTypes)) {
            $profileImageErr = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        }
        if ($profileImageSize > 2 * 1024 * 1024) {
            $profileImageErr = "File size too large. Maximum size is 2MB.";
        }
        if ($profileImageError !== UPLOAD_ERR_OK) {
             $profileImageErr = "File upload failed with error code: " . $profileImageError;
        }
        if (empty($profileImageErr)) {
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $uniqueName = uploadImage($employeeId, $profileImageName, $profileImageTmpName, $uploadDir);
            if ($uniqueName) {
                $imagePath = $uploadDir . $uniqueName; 
            } else {
                $profileImageErr = "Failed to upload image.";
            }
        }
    }
    require_once '../Connection/connection.php';
    if (empty($fullNameErr) && empty($emailErr) && empty($phoneErr) && empty($dobErr) && empty($genderErr) && empty($addressErr) &&
        empty($employeeIdErr) && empty($departmentErr) && empty($designationErr) && empty($joiningDateErr) && empty($salaryErr) &&
        empty($usernameErr) && empty($passwordErr) && empty($confirmPasswordErr) && empty($profileImageErr)) {
            $sql = "INSERT INTO employee (FULLNAME, Email, PHONE, DOB, Gender, Address, EmployeeId, Department, Designation, JoiningDate, Salary, Username, Password, ProfileImage)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                 $result = $stmt->execute([$fullName, $email, $phone, $dob, $gender, $address, $employeeId, $department, $designation, $joiningDate, $salary, $username, $password, $imagePath]);
                if ($result) {
                    echo "Registration successful!";
					header("Location: index.php");
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error: " . $conn->error;
            }
        }
        if (isset($conn)) {
            $conn->close();
        }
    } else {
        if (isset($conn)) {
            $conn->close();
        }
    }
?>