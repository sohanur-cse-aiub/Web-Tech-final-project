<?php
session_start();
if (!isset($_SESSION['admin_username'])) {
    header("Location: ../index.php"); 
    exit();
}
include '../../Connection/connection.php'; 
$admin_id = null;
$admin_details = null;
if (isset($_GET['id'])) {
    $admin_id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT ID, full_name, email, phone_number, dob, gender, ProfileImage, username, role, security_question, security_answer FROM admin WHERE ID = " . $admin_id;
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $admin_details = $result->fetch_assoc();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Admin user not found for update.'];
        header("Location: account_details.php"); 
        exit();
    }
} else {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'No admin ID provided for update.'];
    header("Location: account_details.php"); 
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
    $imagePath = $admin_details['ProfileImage']; 
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../Uploaded_Images/'; 
        $profileImageName = $_FILES["profile_image"]["name"];
        $profileImageTmpName = $_FILES["profile_image"]["tmp_name"];
        $profileImageType = $_FILES["profile_image"]["type"];
        $profileImageSize = $_FILES["profile_image"]["size"];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($profileImageType, $allowedTypes)) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Invalid file type. Only JPG, PNG, and GIF are allowed for profile image.'];
            header("Location: update_info.php?id=" . $admin_id);
            exit();
        }
        if ($profileImageSize > 2 * 1024 * 1024) { 
            $_SESSION['message'] = ['type' => 'error', 'text' => 'File size too large. Maximum size is 2MB for profile image.'];
            header("Location: update_info.php?id=" . $admin_id);
            exit();
        }
        $fileExtension = pathinfo($profileImageName, PATHINFO_EXTENSION);
        $uniqueName = $admin_id . "_" . uniqid() . "." . $fileExtension;
        $destination = $uploadDir . $uniqueName;
        if (move_uploaded_file($profileImageTmpName, $destination)) {
            $imagePath = $uniqueName; 
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Failed to upload new profile image.'];
            header("Location: update_info.php?id=" . $admin_id);
            exit();
        }
    }
    $update_sql = "UPDATE admin SET
                    full_name = '$fullname',
                    email = '$email',
                    phone_number = '$phone_number',
                    dob = '$dob',
                    gender = '$gender',
                    username = '$username',
                    password = '$password',
                    role = '$role',
                    security_question = '$security_question',
                    security_answer = '$security_answer',
                    profile_image = '$imagePath'
                    WHERE ID = " . $admin_id;
    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Admin account details updated successfully!'];
        $_SESSION['admin_username'] = $username;
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
<html>
<head>
    <title>Update Admin Account Details</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_dashboard.css">
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_account_details.css">
    <link rel="stylesheet" href="https:
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
            </div>
            <ul class="sidebar-nav">
                <li><a href="../Dashboard/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="account_details.php" class="active"><i class="fas fa-user-circle"></i> Admin Account Details</a></li>
                <li><a href="?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h2>Update Admin Account Details</h2>
            </div>
            <div class="content-area">
                <?php
                if (isset($_SESSION['message'])) {
                    echo '<div class="message ' . htmlspecialchars($_SESSION['message']['type']) . '">' . htmlspecialchars($_SESSION['message']['text']) . '</div>';
                    unset($_SESSION['message']); 
                }
                ?>
                <?php if ($admin_details): ?>
                    <div id="updateForm" class="update-form-container">
                        <form action="update_info.php?id=<?php echo htmlspecialchars($admin_details['ID']); ?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="form-group">
                                <label for="full_name">Full Name:</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($admin_details['full_name']); ?>" >
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin_details['email']); ?>" >
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
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($admin_details['username']); ?>" >
                            </div>
							<div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($admin_details['password']); ?>" >
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
                                <input type="text" id="security_answer" name="security_answer" value="<?php echo htmlspecialchars($admin_details['security_answer']); ?>" >
                            </div>
                            <div class="form-group">
                                <label for="profile_image">Profile Image:</label>
                                <input type="file" id="profile_image" name="profile_image" accept="image/*">
                                <?php if (!empty($admin_details['ProfileImage'])): ?>
                                    <p style="font-size: 0.9em; color: 
                                <?php endif; ?>
                            </div>
                            <div class="action-buttons-container">
                                <button type="submit" class="update-button-submit">Save Changes</button>
                                <a href="account_details.php" class="cancel-button">Cancel</a>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <p>No admin account details found for update.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>