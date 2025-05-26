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
$loggedInUsername = $_SESSION['admin_username']; 
$admin_details = null;
$admin_id_for_update = null;
$sql_fetch = "SELECT ID, full_name, email, phone_number, gender, ProfileImage, username, role, security_question, security_answer FROM admin WHERE username = '" . $conn->real_escape_string($loggedInUsername) . "'";
$result_fetch = $conn->query($sql_fetch);
if ($result_fetch && $result_fetch->num_rows > 0) {
    $admin_details = $result_fetch->fetch_assoc();
    $admin_id_for_update = $admin_details['ID'];
} else {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Admin account details not found. Please log in again.'];
    header("Location: ../index.php");
    exit();
}
$show_update_form = isset($_GET['action']) && $_GET['action'] === 'edit';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile']) && $admin_id_for_update) {
    $fullname = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $username = $conn->real_escape_string($_POST['username']);
    $role = $conn->real_escape_string($_POST['role']);
    $security_question = $conn->real_escape_string($_POST['security_question']);
    $security_answer = $conn->real_escape_string($_POST['security_answer']);
    $update_sql = "UPDATE admin SET
                    full_name = '$fullname',
                    email = '$email',
                    phone_number = '$phone_number',
                    gender = '$gender',
                    username = '$username',
                    role = '$role',
                    security_question = '$security_question',
                    security_answer = '$security_answer'
                    WHERE ID = " . $admin_id_for_update;
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
    <title>Admin Account Details</title>
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
                <li><a href="../Dashboard/dashboard.php"> Dashboard</a></li>
                <li><a href="account_details.php" class="active"> Admin Account Details</a></li>
                <li><a href="?logout=true"> Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h2>Admin Account Details</h2>
            </div>
            <div class="content-area">
                <?php
                if (isset($_SESSION['message'])) {
                    echo '<div class="message ' . htmlspecialchars($_SESSION['message']['type']) . '">' . htmlspecialchars($_SESSION['message']['text']) . '</div>';
                    unset($_SESSION['message']); 
                }
                ?>
                <?php if ($show_update_form && $admin_details): ?>
                    <div id="updateForm" class="update-form-container">
                        <form action="account_details.php" method="POST">
                            <input type="hidden" name="update_profile" value="1">
                            <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($admin_details['ID']); ?>">
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
                            <div class="action-buttons-container">
                                <button type="submit" class="update-button-submit">Save Changes</button>
                                <a href="account_details.php" class="cancel-button">Cancel</a>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div id="accountDetailsDisplay" class="account-details-card">
                        <?php if ($admin_details): ?>
                             <div class="profile-image-container">
                                <img src="../../Uploaded_Images/<?php echo htmlspecialchars($admin_details['ProfileImage']); ?>" alt="Profile Image">
                            </div>
                             <div class="detail-item">
                                <label>Full Name:</label>
                                <span><?php echo htmlspecialchars($admin_details['full_name']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Email:</label>
                                <span><?php echo htmlspecialchars($admin_details['email']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Phone Number:</label>
                                <span><?php echo htmlspecialchars($admin_details['phone_number']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Gender:</label>
                                <span><?php echo htmlspecialchars($admin_details['gender']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Username:</label>
                                <span><?php echo htmlspecialchars($admin_details['username']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Role:</label>
                                <span><?php echo htmlspecialchars($admin_details['role']); ?></span>
                            </div>
                            <div class="action-buttons-container">
                                <a href="account_details.php?action=edit" class="update-button">Update Info</a>
                            </div>
                        <?php else: ?>
                            <p>No admin account details found for the logged-in user.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>