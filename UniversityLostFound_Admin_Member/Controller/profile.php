<?php
session_start();
require_once "../Model/DatabaseConnection.php";

if (!($_SESSION['isLoggedIn'] ?? false)) {
    header('Location: ../View/login.php');
    exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($name == '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Name and valid email are required.';
} else {
    $db = new DatabaseConnection();
    $conn = $db->openConnection();
    if ($password != '') {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('UPDATE university_accounts SET name=?, email=?, password=? WHERE user_id=?');
        $stmt->bind_param('sssi', $name, $email, $hashed, $_SESSION['user_id']);
    } else {
        $stmt = $conn->prepare('UPDATE university_accounts SET name=?, email=? WHERE user_id=?');
        $stmt->bind_param('ssi', $name, $email, $_SESSION['user_id']);
    }
    if ($stmt->execute()) {
        $_SESSION['name'] = $name;
        $_SESSION['success'] = 'Profile updated successfully.';
    } else {
        $_SESSION['error'] = 'Could not update profile.';
    }
}
header('Location: ../View/dashboard.php');
exit();
?>
