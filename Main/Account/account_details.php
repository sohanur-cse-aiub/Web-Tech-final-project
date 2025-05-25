<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}
if (isset($_GET['logout'])) {
    session_destroy();
    $_SESSION = array();
    setcookie('remember_user', '', time() - 3600, '/');
    header("Location: ../index.php");
    exit();
}
require_once '../../Connection/connection.php';
$loggedInUsername = $_SESSION['username'];
$user_details = null;
$user_id_for_update = null;
$sql_fetch = "SELECT ID, FULLNAME, Email, PHONE, DOB, Gender, Address, ProfileImage, EmployeeId, Department, Designation, JoiningDate, Salary, Username FROM employee WHERE Username = '" . $loggedInUsername . "'";
$result_fetch = $conn->query($sql_fetch);
if ($result_fetch && $result_fetch->num_rows > 0) {
    $user_details = $result_fetch->fetch_assoc();
    $user_id_for_update = $user_details['ID']; 
} else {
    echo "<p>No account details found for the logged-in user. Please log in again or contact support.</p>";
    $conn->close();
    exit();
}
$show_update_form = isset($_GET['action']) && $_GET['action'] === 'edit';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile']) && $user_id_for_update) {
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
                    WHERE ID = " . $user_id_for_update;
    if ($conn->query($update_sql) === TRUE) {
        header("Location: account_details.php?status=success");
        exit();
    } else {
        header("Location: account_details.php?action=edit&status=error&message=" . urlencode($conn->error));
        exit();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<head>
    <title>Account Details</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_dashboard.css">
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_account_details.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>My Account</h3>
            </div>
            <ul class="sidebar-nav">
                <li><a href="../Dashboard/dashboard.php"> Dashboard</a></li>
                <li><a href="account_details.php" class="active"> Account Details</a></li>
                <li><a href="?logout=true"> Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h2>Account Details</h2>
            </div>
            <div class="content-area">
                <?php
                if (isset($_GET['status'])) {
                    if ($_GET['status'] === 'success') {
                        echo '<div class="message success">Account details updated successfully!</div>';
                    } elseif ($_GET['status'] === 'error') {
                        $errorMessage = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : "An unknown error occurred.";
                        echo '<div class="message error">Error updating record: ' . $errorMessage . '</div>';
                    }
                }
                ?>
                <?php if ($show_update_form && $user_details): ?>
                    <div id="updateForm" class="update-form-container">
                        <form action="account_details.php" method="POST">
                            <input type="hidden" name="update_profile" value="1">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_details['ID']); ?>">
                            <div class="form-group">
                                <label for="fullname">Full Name:</label>
                                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user_details['FULLNAME']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_details['Email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone:</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user_details['PHONE']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="dob">Date of Birth:</label>
                                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user_details['DOB']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select id="gender" name="gender">
                                    <option value="Male" <?php if($user_details['Gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                    <option value="Female" <?php if($user_details['Gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                    <option value="Other" <?php if($user_details['Gender'] == 'Other') echo 'selected'; ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <textarea id="address" name="address"><?php echo htmlspecialchars($user_details['Address']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="employeeId">Employee ID:</label>
                                <input type="text" id="employeeId" name="employeeId" value="<?php echo htmlspecialchars($user_details['EmployeeId']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="department">Department:</label>
                                <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($user_details['Department']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="designation">Designation:</label>
                                <input type="text" id="designation" name="designation" value="<?php echo htmlspecialchars($user_details['Designation']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="joiningDate">Joining Date:</label>
                                <input type="date" id="joiningDate" name="joiningDate" value="<?php echo htmlspecialchars($user_details['JoiningDate']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="salary">Salary:</label>
                                <input type="text" id="salary" name="salary" value="<?php echo htmlspecialchars($user_details['Salary']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_details['Username']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password (leave blank if not changing):</label>
                                <input type="password" id="new_password" name="new_password">
                            </div>
                            <div class="action-buttons-container">
                                <button type="submit" class="update-button-submit">Save Changes</button>
                                <a href="account_details.php" class="cancel-button">Cancel</a>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div id="accountDetailsDisplay" class="account-details-card">
                        <?php if ($user_details): ?>
                             <div class="detail-item">
                                <label>Full Name:</label>
                                <span><?php echo htmlspecialchars($user_details['FULLNAME']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Email:</label>
                                <span><?php echo htmlspecialchars($user_details['Email']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Phone:</label>
                                <span><?php echo htmlspecialchars($user_details['PHONE']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Date of Birth:</label>
                                <span><?php echo htmlspecialchars($user_details['DOB']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Gender:</label>
                                <span><?php echo htmlspecialchars($user_details['Gender']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Address:</label>
                                <span><?php echo htmlspecialchars($user_details['Address']); ?></span>
                            </div>
                             <div class="detail-item">
                                <label>Employee ID:</label>
                                <span><?php echo htmlspecialchars($user_details['EmployeeId']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Department:</label>
                                <span><?php echo htmlspecialchars($user_details['Department']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Designation:</label>
                                <span><?php echo htmlspecialchars($user_details['Designation']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Joining Date:</label>
                                <span><?php echo htmlspecialchars($user_details['JoiningDate']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Salary:</label>
                                <span><?php echo htmlspecialchars($user_details['Salary']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Username:</label>
                                <span><?php echo htmlspecialchars($user_details['Username']); ?></span>
                            </div>
                            <div class="action-buttons-container">
                                <a href="account_details.php?action=edit" class="update-button">Update Info</a>
                                <a href="resignation.php" class="resign-button">Resign</a>
                            </div>
                        <?php else: ?>
                            <p>No account details found for the logged-in user.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>