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
$admins = []; 
$errorMessage = '';
$sql_fetch_admins = "SELECT ID, full_name, email, phone_number, dob, gender, profile_image, username, role, security_question, security_answer FROM admin";
$result_fetch_admins = $conn->query($sql_fetch_admins);
if ($result_fetch_admins) {
    if ($result_fetch_admins->num_rows > 0) {
        while ($row = $result_fetch_admins->fetch_assoc()) {
            $admins[] = $row;
        }
    } else {
        $errorMessage = "No admin records found in the database.";
    }
} else {
    $errorMessage = "Error fetching admin data: " . $conn->error;
}
$conn->close(); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Admin</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_dashboard.css">
    <link rel="stylesheet" type="text/css" href="../../CSS/styles_manage.css">
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
                <h2>Manage Admins</h2>
            </div>
            <div class="content-area">
                <?php if (!empty($errorMessage)): ?>
                    <p class="error" style="text-align: center;"><?php echo htmlspecialchars($errorMessage); ?></p>
                <?php endif; ?>
                <?php if (empty($errorMessage) && !empty($admins)): ?>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Profile</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>DOB</th>
                                    <th>Gender</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Security Question</th>
                                    <th>Security Answer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admins as $admin): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo htmlspecialchars($admin['ID']); ?></td>
                                        <td data-label="Profile">
                                            <img src="../../Uploaded_Images/<?php echo htmlspecialchars($admin['profile_image']); ?>" alt="Profile Image">
                                        </td>
                                        <td data-label="Full Name"><?php echo htmlspecialchars($admin['full_name']); ?></td>
                                        <td data-label="Email"><?php echo htmlspecialchars($admin['email']); ?></td>
                                        <td data-label="Phone Number"><?php echo htmlspecialchars($admin['phone_number']); ?></td>
                                        <td data-label="DOB"><?php echo htmlspecialchars($admin['dob']); ?></td>
                                        <td data-label="Gender"><?php echo htmlspecialchars($admin['gender']); ?></td>
                                        <td data-label="Username"><?php echo htmlspecialchars($admin['username']); ?></td>
                                        <td data-label="Role"><?php echo htmlspecialchars($admin['role']); ?></td>
                                        <td data-label="Security Question"><?php echo htmlspecialchars($admin['security_question']); ?></td>
                                        <td data-label="Security Answer"><?php echo htmlspecialchars($admin['security_answer']); ?></td>
                                        <td data-label="Actions" class="action-buttons">
                                            <a href="edit_admin.php?id=<?php echo htmlspecialchars($admin['ID']); ?>" class="edit-btn">Edit</a>
                                            <a href="delete_admin.php?id=<?php echo htmlspecialchars($admin['ID']); ?>" class="delete-btn">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>