<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../Connection/connection.php'; 
include '../php/validation.php'; 
include '../php/upload.php'; 
$fullName = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone_number = $_POST['phone_number'] ?? '';
$dob = $_POST['dob'] ?? '';
$gender = $_POST['gender'] ?? '';
$username = $_POST['username'] ?? ''; 
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$role = $_POST['role'] ?? '';
$security_question = $_POST['security_question'] ?? '';
$security_answer = $_POST['security_answer'] ?? '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($errors)) {
        $errors = [];
    }
    if (empty($fullName)) $errors['full_name'] = "Full Name is required";
    if (empty($email)) $errors['email'] = "Email is required";
    if (empty($phone_number)) $errors['phone_number'] = "Phone Number is required";
    if (empty($dob)) $errors['dob'] = "Date of Birth is required";
    if (empty($gender)) $errors['gender'] = "Gender is required";
    if (empty($username)) $errors['username'] = "Username is required";
    if (empty($password)) $errors['password'] = "Password is required";
    if (empty($confirm_password)) $errors['confirm_password'] = "Confirm Password is required";
    if ($password !== $confirm_password) $errors['confirm_password'] = "Passwords do not match";
    if ($role === 'default' || empty($role)) $errors['role'] = "Please select a role";
    if ($security_question === 'default' || empty($security_question)) $errors['security_question'] = "Please select a security question";
    if (empty($security_answer)) $errors['security_answer'] = "Security Answer is required";
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
    if (empty($errors)) {
        $imagePath = ""; 
        $uniqueImageName = uploadImage($username, $_FILES["profile_image"]["name"], $_FILES["profile_image"]["tmp_name"], $uploadDir);
        if ($uniqueImageName) {
            $imagePath = $uniqueImageName; 
        } else {
            $errors['profile_image'] = "Failed to upload profile image.";
        }
        if (empty($errors)) {
            $plainTextPassword = $password; 
            $sql = "INSERT INTO admin (full_name, email, phone_number, dob, gender, username, password, role, security_question, security_answer, profile_image)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql); 
            if ($stmt) {
                 $result = $stmt->execute([$fullName, $email, $phone_number, $dob, $gender, $username, $plainTextPassword, $role, $security_question, $security_answer, $imagePath]);
                if ($result) {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Admin registration successful! Please login.'];
                    header("Location: index.php");
                    exit();
                } else {
                    $errors['database'] = "Error registering admin: " . $stmt->error;
                }
                $stmt->close(); 
            } else {
                $errors['database'] = "Database error: " . $conn->error;
            }
        }
    }
}
if (isset($conn)) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration for Admin</title>
    <link rel="stylesheet" type="text/css" href="../CSS/styles.css">
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend>
                <h2>Admin Registration</h2>
            </legend>
            <?php if (!empty($errors['database'])) { ?>
                <p class="error"><?php echo htmlspecialchars($errors['database']); ?></p>
            <?php } ?>
            <fieldset>
                <legend>Personal Information</legend>
                <table>
                    <tr>
                        <td>Full Name:</td>
                        <td><input type="text" name="full_name" value="<?php echo htmlspecialchars($fullName); ?>">
                            <span class="error">
                                <?php echo isset($errors['full_name']) ? $errors['full_name'] : ''; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                            <span class="error">
                                <?php echo isset($errors['email']) ? $errors['email'] : ''; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Phone Number:</td>
                        <td><input type="text" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>">
                            <span class="error">
                                <?php echo isset($errors['phone_number']) ? $errors['phone_number'] : ''; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Date of Birth:</td>
                        <td><input type="date" name="dob" value="<?php echo htmlspecialchars($dob); ?>">
                            <span class="error">
                                <?php echo isset($errors['dob']) ? $errors['dob'] : ''; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Gender:</td>
                        <td>
                            <input type="radio" name="gender" value="Male" <?php echo ($gender == 'Male') ? 'checked' : ''; ?>>Male
                            <input type="radio" name="gender" value="Female" <?php echo ($gender == 'Female') ? 'checked' : ''; ?>>Female
                            <span class="error">
                                <?php echo isset($errors['gender']) ? $errors['gender'] : ''; ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset>
                <legend>Account Information</legend>
                <table>
                    <tr>
                        <td>Username:</td>
                        <td><input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
                            <span class="error">
                                <?php echo isset($errors['username']) ? $errors['username'] : ''; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input type="password" name="password">
                            <span class="error">
                                <?php echo isset($errors['password']) ? $errors['password'] : ''; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Confirm Password:</td>
                        <td><input type="password" name="confirm_password">
                            <span class="error">
                                <?php echo isset($errors['confirm_password']) ? $errors['confirm_password'] : ''; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Role:</td>
                        <td>
                            <select name="role" id="role">
                                <option value="default">Select Role</option>
                                <option value="Super Admin" <?php echo ($role == 'Super Admin') ? 'selected' : ''; ?>>
                                    Super Admin</option>
                                <option value="Admin" <?php echo ($role == 'Admin') ? 'selected' : ''; ?>>Admin
                                </option>
                            </select>
                            <span class="error">
                                <?php echo isset($errors['role']) ? $errors['role'] : ''; ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset>
                <legend>Security Information</legend>
                <table>
                    <tr>
                        <td>Security Question:</td>
                        <td>
                            <select name="security_question">
                                <option value="default">Select a Security Question</option>
                                <option value="pet" <?php echo ($security_question == 'pet') ? 'selected' : ''; ?>>
                                    What is your pet's name?</option>
                                <option value="mother" <?php echo ($security_question == 'mother') ? 'selected' : ''; ?>>
                                    What is your mother's maiden name?</option>
                            </select>
                            <span class="error">
                                <?php echo isset($errors['security_question']) ? $errors['security_question'] : ''; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Answer:</td>
                        <td><input type="text" name="security_answer" value="<?php echo htmlspecialchars($security_answer); ?>">
                            <span class="error">
                                <?php echo isset($errors['security_answer']) ? $errors['security_answer'] : ''; ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset>
                <legend>Profile Image</legend>
                <table>
                    <tr>
                        <td>Profile Image:</td>
                        <td>
							<input type="file" name="profile_image" accept="image/*">
                            <span class="error"><?php echo $profileImageErr; ?></span>
                            <span class="error"><?php echo isset($errors['profile_image']) ? $errors['profile_image'] : ''; ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <table align="center">
                <tr>
                    <td>
                        <input type="submit" value="Register">
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</body>
</html>