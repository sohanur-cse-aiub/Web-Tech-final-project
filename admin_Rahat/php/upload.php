<?php
$uploadDir = '../Uploaded_Images/';
$profileImageErr = "";
function uploadImage($username, $profileImageName, $profileImageTmpName, $uploadDir) {
    $fileExtension = pathinfo($profileImageName, PATHINFO_EXTENSION);
    $originalFilenameWithoutExt = pathinfo($profileImageName, PATHINFO_FILENAME);
    $uniqueName = $username . "_" . $originalFilenameWithoutExt . "." . $fileExtension;
    $destination = $uploadDir . $uniqueName;
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            error_log("Failed to create upload directory: " . $uploadDir);
            return false;
        }
    }
    if (move_uploaded_file($profileImageTmpName, $destination)) {
        return $uniqueName; 
    } else {
        error_log("Failed to move uploaded file from " . $profileImageTmpName . " to " . $destination);
        return false; 
    }
}
?>