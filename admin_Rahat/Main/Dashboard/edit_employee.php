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
$employee_id = null;
$employee_details = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $employee_id = $conn->real_escape_string($_GET['id']);
    $sql_fetch = "SELECT ID, FULLNAME, Email, PHONE, DOB, Gender, Address, ProfileImage, EmployeeId, Department, Designation, JoiningDate, Salary, Username FROM employee WHERE ID = " . $employee_id;
    $result_fetch = $conn->query($sql_fetch);
    if ($result_fetch && $result_fetch->num_rows > 0) {
        $employee_details = $result_fetch->fetch_assoc();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Employee not found for editing.'];
        header("Location: manage_employees.php"); 
        exit();
    }
} else {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'No employee ID provided for editing.'];
    header("Location: manage_employees.php"); 
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $address = $conn->real_escape_string($_POST['address']);
    $employeeId = $conn->real_escape_string($_POST['employeeId']);
    $department = $conn->real_escape_string($_POST['department']);
    $designation = $conn->real_escape_string($_POST['designation']);
    $joiningDate = $conn->real_escape_string($_POST['joiningDate']);
    $salary = $conn->real_escape_string($_POST['salary']);
    $username = $conn->real_escape_string($_POST['username']);
    $imagePath = $employee_details['ProfileImage']; 
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../Uploaded_Images/'; 
        $profileImageName = $_FILES["profile_image"]["name"];
        $profileImageTmpName = $_FILES["profile_image"]["tmp_name"];
        $profileImageType = $_FILES["profile_image"]["type"];
        $profileImageSize = $_FILES["profile_image"]["size"];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($profileImageType, $allowedTypes)) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Invalid file type. Only JPG, PNG, and GIF are allowed for profile image.'];
            header("Location: edit_employee.php?id=" . $employee_id);
            exit();
        }
        if ($profileImageSize > 2 * 1024 * 1024) { 
            $_SESSION['message'] = ['type' => 'error', 'text' => 'File size too large. Maximum size is 2MB for profile image.'];
            header("Location: edit_employee.php?id=" . $employee_id);
            exit();
        }
        $fileExtension = pathinfo($profileImageName, PATHINFO_EXTENSION);
        $originalFilenameWithoutExt = pathinfo($profileImageName, PATHINFO_FILENAME);
        $uniqueName = $employeeId . "_" . $originalFilenameWithoutExt . "_" . uniqid() . "." . $fileExtension;
        $destination = $uploadDir . $uniqueName;
        if (move_uploaded_file($profileImageTmpName, $destination)) {
            $imagePath = $uniqueName; 
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Failed to upload new profile image.'];
            header("Location: edit_employee.php?id=" . $employee_id);
            exit();
        }
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
                    Username = '$username',
                    ProfileImage = '$imagePath'
                    WHERE ID = " . $employee_id;
    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Employee details updated successfully!'];
        header("Location: manage_employees.php"); 
        exit();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Error updating employee record: ' . $conn->error];
        header("Location: edit_employee.php?id=" . $employee_id);
        exit();
    }
}
$conn->close(); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Employee</title>
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
                <li><a href="manage_employees.php" class="active"> Manage Employees</a></li>
                <li><a href="manage_admins.php"> Manage Admins</a></li>
                <li><a href="?logout=true"> Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h2>Edit Employee Details</h2>
            </div>
            <div class="content-area">
                <?php
                if (isset($_SESSION['message'])) {
                    echo '<div class="message ' . htmlspecialchars($_SESSION['message']['type']) . '">' . htmlspecialchars($_SESSION['message']['text']) . '</div>';
                    unset($_SESSION['message']); 
                }
                ?>
                <?php if ($employee_details): ?>
                    <div id="editEmployeeForm" class="update-form-container">
                        <form action="edit_employee.php?id=<?php echo htmlspecialchars($employee_details['ID']); ?>" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="fullname">Full Name:</label>
                                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($employee_details['FULLNAME']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($employee_details['Email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone:</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($employee_details['PHONE']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="dob">Date of Birth:</label>
                                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($employee_details['DOB']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select id="gender" name="gender">
                                    <option value="Male" <?php if($employee_details['Gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                    <option value="Female" <?php if($employee_details['Gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                    <option value="Other" <?php if($employee_details['Gender'] == 'Other') echo 'selected'; ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <textarea id="address" name="address"><?php echo htmlspecialchars($employee_details['Address']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="employeeId">Employee ID:</label>
                                <input type="text" id="employeeId" name="employeeId" value="<?php echo htmlspecialchars($employee_details['EmployeeId']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="department">Department:</label>
                                <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($employee_details['Department']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="designation">Designation:</label>
                                <input type="text" id="designation" name="designation" value="<?php echo htmlspecialchars($employee_details['Designation']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="joiningDate">Joining Date:</label>
                                <input type="date" id="joiningDate" name="joiningDate" value="<?php echo htmlspecialchars($employee_details['JoiningDate']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="salary">Salary:</label>
                                <input type="text" id="salary" name="salary" value="<?php echo htmlspecialchars($employee_details['Salary']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($employee_details['Username']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="profile_image">Profile Image:</label>
                                <input type="file" id="profile_image" name="profile_image" accept="image/*">
                                <?php if (!empty($employee_details['ProfileImage'])): ?>
                                    <p style="font-size: 0.9em; color: 
                                <?php endif; ?>
                            </div>
                            <div class="action-buttons-container">
                                <button type="submit" class="update-button-submit">Save Changes</button>
                                <a href="manage_employees.php" class="cancel-button">Cancel</a>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <p>Employee details not found for editing.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>