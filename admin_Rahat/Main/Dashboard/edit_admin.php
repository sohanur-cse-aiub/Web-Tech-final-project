<?php
session_start();
if (!isset($_SESSION['admin_username'])) {
    header("Location: ../index.php"); 
    exit();
}
if (isset($_GET['logout'])) {
    session_destroy(); 
    $_SESSION = array(); 
    setcookie('remember_admin', '', time() - 3600, '/'); 
    header("Location: ../index.php"); 
    exit();
}
include '../../Connection/connection.php'; 
$admin_id = null;
$admin_details = null;
$new_password = $confirm_new_password = "";
$password_error = "";
$password_updated_flag = false; 
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $admin_id = $conn->real_escape_string($_GET['id']);
    $sql_fetch = "SELECT ID, full_name, email, phone_number, dob, gender, profile_image, username, role, security_question, security_answer FROM admin WHERE ID = " . $admin_id;
    $result_fetch = $conn->query($sql_fetch);
    if ($result_fetch && $result_fetch->num_rows > 0) {
        $admin_details = $result_fetch->fetch_assoc();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Admin not found for editing.'];
        header("Location: manage_admins.php"); 
        exit();
    }
} else {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'No admin ID provided for editing.'];
    header("Location: manage_admins.php"); 
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $username = $conn->real_escape_string($_POST['username']);
    $role = $conn->real_escape_string($_POST['role']);
    $security_question = $conn->real_escape_string($_POST['security_question']);
    $security_answer = $conn->real_escape_string($_POST['security_answer']);
    $imagePath = $admin_details['profile_image']; 
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';
    if (!empty($new_password) || !empty($confirm_new_password)) {
        if (empty($new_password)) {
            $password_error = "New password is required.";
        } elseif (strlen($new_password) < 6) {
            $password_error = "New password must be at least 6 characters long.";
        } elseif ($new_password !== $confirm_new_password) {
            $password_error = "New password and confirm password do not match.";
        }
    }
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../Uploaded_Images/'; 
        $profileImageName = $_FILES["profile_image"]["name"];
        $profileImageTmpName = $_FILES["profile_image"]["tmp_name"];
        $profileImageType = $_FILES["profile_image"]["type"];
        $profileImageSize = $_FILES["profile_image"]["size"];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($profileImageType, $allowedTypes)) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Invalid file type. Only JPG, PNG, and GIF are allowed for profile image.'];
            header("Location: edit_admin.php?id=" . $admin_id);
            exit();
        }
        if ($profileImageSize > 2 * 1024 * 1024) { 
            $_SESSION['message'] = ['type' => 'error', 'text' => 'File size too large. Maximum size is 2MB for profile image.'];
            header("Location: edit_admin.php?id=" . $admin_id);
            exit();
        }
        $fileExtension = pathinfo($profileImageName, PATHINFO_EXTENSION);
        $originalFilenameWithoutExt = pathinfo($profileImageName, PATHINFO_FILENAME);
        $uniqueName = $admin_id . "_" . $originalFilenameWithoutExt . "_" . uniqid() . "." . $fileExtension;
        $destination = $uploadDir . $uniqueName;
        if (move_uploaded_file($profileImageTmpName, $destination)) {
            $imagePath = $uniqueName; 
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Failed to upload new profile image.'];
            header("Location: edit_admin.php?id=" . $admin_id);
            exit();
        }
    }
    if (empty($password_error)) {
        $update_sql = "UPDATE admin SET
                        full_name = '$fullname',
                        email = '$email',
                        phone_number = '$phone_number',
                        dob = '$dob',
                        gender = '$gender',
                        username = '$username',
                        role = '$role',
                        security_question = '$security_question',
                        security_answer = '$security_answer',
                        profile_image = '$imagePath'";
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql .= ", password = '$hashed_password'";
            $password_updated_flag = true;
        }
        $update_sql .= " WHERE ID = " . $admin_id;
        if ($conn->query($update_sql) === TRUE) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Admin details updated successfully!' . ($password_updated_flag ? ' Password also updated.' : '')];
            if ($admin_id == $_SESSION['admin_id']) {
                $_SESSION['admin_username'] = $username;
            }
            header("Location: manage_admins.php"); 
            exit();
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Error updating admin record: ' . $conn->error];
            header("Location: edit_admin.php?id=" . $admin_id);
            exit();
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => $password_error];
        header("Location: edit_admin.php?id=" . $admin_id);
        exit();
    }
}
$conn->close(); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Admin</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_dashboard.css">
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_account_details.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
            </div>
            <ul class="sidebar-nav">
                <li><a href="dashboard.php"> Dashboard</a></li>
                <li><a href="manage_employees.php"> Manage Employees</a></li>
                <li><a href="manage_admins.php" class="active"> Manage Admins</a></li>
                <li><a href="?logout=true"> Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h2>Edit Admin Details</h2>
            </div>
            <div class="content-area">
                <?php
                if (isset($_SESSION['message'])) {
                    echo '<div class="message ' . htmlspecialchars($_SESSION['message']['type']) . '">' . htmlspecialchars($_SESSION['message']['text']) . '</div>';
                    unset($_SESSION['message']); 
                }
                ?>
                <?php if ($admin_details): ?>
                    <div id="editAdminForm" class="update-form-container">
                        <form action="edit_admin.php?id=<?php echo htmlspecialchars($admin_details['ID']); ?>" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="full_name">Full Name:</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($admin_details['full_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin_details['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone_number">Phone Number:</label>
                                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($admin_details['phone_number']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="dob">Date of Birth:</label>
                                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($admin_details['dob']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select id="gender" name="gender">
                                    <option value="Male" <?php if($admin_details['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                    <option value="Female" <?php if($admin_details['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                    <option value="Other" <?php if($admin_details['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($admin_details['username']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="role">Role:</label>
                                <select name="role" id="role">
                                    <option value="Super Admin" <?php echo ($admin_details['role'] == 'Super Admin') ? 'selected' : ''; ?>>Super Admin</option>
                                    <option value="Admin" <?php echo ($admin_details['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="security_question">Security Question:</label>
                                <select id="security_question" name="security_question">
                                    <option value="pet" <?php if($admin_details['security_question'] == 'pet') echo 'selected'; ?>>What is your pet's name?</option>
                                    <option value="mother" <?php if($admin_details['security_question'] == 'mother') echo 'selected'; ?>>What is your mother's maiden name?</option>
                                    <option value="city" <?php if($admin_details['security_question'] == 'city') echo 'selected'; ?>>In which city were you born?</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="security_answer">Security Answer:</label>
                                <input type="text" id="security_answer" name="security_answer" value="<?php echo htmlspecialchars($admin_details['security_answer']); ?>" required>
                            </div>
                            <fieldset class="password-fields-fieldset">
                                <legend>Change Password (Optional)</legend>
                                <div class="form-group">
                                    <label for="new_password">New Password:</label>
                                    <input type="password" id="new_password" name="new_password" placeholder="Leave blank to keep current password">
                                </div>
                                <div class="form-group">
                                    <label for="confirm_new_password">Confirm New Password:</label>
                                    <input type="password" id="confirm_new_password" name="confirm_new_password">
                                </div>
                            </fieldset>
                            <div class="form-group">
                                <label for="profile_image">Profile Image:</label>
                                <input type="file" id="profile_image" name="profile_image" accept="image/*">
                                <?php if (!empty($admin_details['profile_image'])): ?>
                                    <p style="font-size: 0.9em; color: 
                                <?php endif; ?>
                            </div>
                            <div class="action-buttons-container">
                                <button type="submit" class="update-button-submit">Save Changes</button>
                                <a href="manage_admins.php" class="cancel-button">Cancel</a>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <p>Admin details not found for editing.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>