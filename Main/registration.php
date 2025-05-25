<!DOCTYPE html>
<html>
<head>
    <title>Employee Registration</title>
    <link rel="stylesheet" type="text/css" href="../CSS/styles.css">
</head>
<body>
    <?php
    include '../PHP/upload.php';
	include '../PHP/validation.php';
?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend><h2>Employee Registration</h2></legend>
            <fieldset>
                <legend>Personal Details</legend>
                <table>
                    <tr>
                        <td>Full Name:</td>
                        <td><input type="text" name="fullname" value="<?php echo $fullName; ?>">
                            <span class="error"><?php echo $fullNameErr; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><input type="email" name="email" value="<?php echo $email; ?>">
                            <span class="error"><?php echo $emailErr; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Phone Number:</td>
                        <td><input type="number" name="phone" value="<?php echo $phone; ?>">
                            <span class="error"><?php echo $phoneErr; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Date of Birth:</td>
                        <td><input type="date" name="dob" value="<?php echo $dob; ?>">
                            <span class="error"><?php echo $dobErr; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Gender:</td>
                        <td>
                            <input type="radio" name="gender" value="Male" <?php if (isset($gender) && $gender == "Male") echo "checked"; ?>> Male
                            <input type="radio" name="gender" value="Female" <?php if (isset($gender) && $gender == "Female") echo "checked"; ?>> Female
                            <span class="error"><?php echo $genderErr; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Address:</td>
                        <td><textarea name="address" rows="3"><?php echo $address; ?></textarea>
                            <span class="error"><?php echo $addressErr; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Profile Image:</td>
                        <td><input type="file" name="profile_image" accept="image/*">
                            <span class="error"><?php echo $profileImageErr; ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset>
                <legend>Job Details</legend>
                <table>
                    <tr>
                        <td>Employee ID:</td>
                        <td><input type="text" name="employee_id" value="<?php echo $employeeId; ?>">
                            <span class="error"><?php echo $employeeIdErr; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Department:</td>
                        <td>
                            <select name="department">
                                <option value="">Select Department</option>
                                <option value="HR" <?php if (isset($department) && $department == "HR") echo "selected"; ?>>HR</option>
                                <option value="IT" <?php if (isset($department) && $department == "IT") echo "selected"; ?>>IT</option>
                                <option value="Finance" <?php if (isset($department) && $department == "Finance") echo "selected"; ?>>Finance</option>
                                <option value="Marketing" <?php if (isset($department) && $department == "Marketing") echo "selected"; ?>>Marketing</option>
                            </select>
                            <span class="error"><?php echo $departmentErr; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Designation:</td>
                        <td><input type="text" name="designation" value="<?php echo $designation; ?>">
                            <span class="error"><?php echo $designationErr; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Date of Joining:</td>
                        <td><input type="date" name="joining_date" value="<?php echo $joiningDate; ?>">
                            <span class="error"><?php echo $joiningDateErr; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Salary:</td>
                        <td><input type="number" name="salary" value="<?php echo $salary; ?>">
                            <span class="error"><?php echo $salaryErr; ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset>
                <legend>Login Information</legend>
                <table>
                    <tr>
                        <td>Username:</td>
                        <td><input type="text" name="username" value="<?php echo $username; ?>">
                            <span class="error"><?php echo $usernameErr; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input type="password" name="password" value="<?php echo $password; ?>">
                            <span class="error"><?php echo $passwordErr; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Confirm Password:</td>
                        <td><input type="password" name="confirm_password" value="<?php echo $confirmPassword; ?>">
                            <span class="error"><?php echo $confirmPasswordErr; ?></span>
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