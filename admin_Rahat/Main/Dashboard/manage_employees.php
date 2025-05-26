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
$employees = []; 
$errorMessage = '';
$sql_fetch_employees = "SELECT ID, FULLNAME, Email, PHONE, DOB, Gender, Address, ProfileImage, EmployeeId, Department, Designation, JoiningDate, Salary, Username FROM employee";
$result_fetch_employees = $conn->query($sql_fetch_employees);
if ($result_fetch_employees) {
    if ($result_fetch_employees->num_rows > 0) {
        while ($row = $result_fetch_employees->fetch_assoc()) {
            $employees[] = $row;
        }
    } else {
        $errorMessage = "No employee records found in the database.";
    }
} else {
    $errorMessage = "Error fetching employee data: " . $conn->error;
}
$conn->close(); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Employee</title>
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
                <li><a href="manage_employees.php" class="active"> Manage Employees</a></li>
                <li><a href="manage_admins.php"> Manage Admins</a></li>
                <li><a href="?logout=true"> Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h2>Manage Employees</h2>
            </div>
            <div class="content-area">
                <?php if (!empty($errorMessage)): ?>
                    <p class="error" style="text-align: center;"><?php echo htmlspecialchars($errorMessage); ?></p>
                <?php endif; ?>
                <?php if (empty($errorMessage) && !empty($employees)): ?>
                    <div class="employee-table-container">
                        <table class="employee-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Profile</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>DOB</th>
                                    <th>Gender</th>
                                    <th>Address</th>
                                    <th>Employee ID</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Joining Date</th>
                                    <th>Salary</th>
                                    <th>Username</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employees as $employee): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo htmlspecialchars($employee['ID']); ?></td>
                                        <td data-label="Profile">
                                            <img src="../../Uploaded_Images/<?php echo htmlspecialchars($employee['ProfileImage']); ?>" alt="Profile Image">
                                        </td>
                                        <td data-label="Full Name"><?php echo htmlspecialchars($employee['FULLNAME']); ?></td>
                                        <td data-label="Email"><?php echo htmlspecialchars($employee['Email']); ?></td>
                                        <td data-label="Phone"><?php echo htmlspecialchars($employee['PHONE']); ?></td>
                                        <td data-label="DOB"><?php echo htmlspecialchars($employee['DOB']); ?></td>
                                        <td data-label="Gender"><?php echo htmlspecialchars($employee['Gender']); ?></td>
                                        <td data-label="Address"><?php echo htmlspecialchars($employee['Address']); ?></td>
                                        <td data-label="Employee ID"><?php echo htmlspecialchars($employee['EmployeeId']); ?></td>
                                        <td data-label="Department"><?php echo htmlspecialchars($employee['Department']); ?></td>
                                        <td data-label="Designation"><?php echo htmlspecialchars($employee['Designation']); ?></td>
                                        <td data-label="Joining Date"><?php echo htmlspecialchars($employee['JoiningDate']); ?></td>
                                        <td data-label="Salary"><?php echo htmlspecialchars($employee['Salary']); ?></td>
                                        <td data-label="Username"><?php echo htmlspecialchars($employee['Username']); ?></td>
                                        <td data-label="Actions" class="action-buttons">
                                            <a href="edit_employee.php?id=<?php echo htmlspecialchars($employee['ID']); ?>" class="edit-btn">Edit</a>
                                            <a href="delete_employee.php?id=<?php echo htmlspecialchars($employee['ID']); ?>" class="delete-btn">Delete</a>
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