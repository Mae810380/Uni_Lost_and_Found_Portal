<?php
session_start();
require_once "../Model/DatabaseConnection.php";
require_once "../Model/UserModel.php";

$studentId = trim($_POST['student_id'] ?? '');
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'Claimer';

$_SESSION['old_student_id'] = $studentId;
$_SESSION['old_name'] = $name;
$_SESSION['old_email'] = $email;

if (!preg_match('/^\d{2}-\d{5}-\d$/', $studentId)) {
    $_SESSION['error'] = 'University ID must look like 22-46183-1.';
} elseif ($name == '' || $email == '' || $password == '') {
    $_SESSION['error'] = 'Please fill in all fields.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email address.';
} elseif (!in_array($role, ['Claimer', 'Finder'])) {
    $_SESSION['error'] = 'Please select a valid user type.';
} else {
    $db = new DatabaseConnection();
    $conn = $db->openConnection();
    $model = new UserModel();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($model->registerUser($conn, $studentId, $name, $email, $hashedPassword, $role)) {
        $_SESSION['success'] = 'Registration successful. Please login.';
        unset($_SESSION['old_student_id'], $_SESSION['old_name'], $_SESSION['old_email']);
        header('Location: ../View/login.php');
        exit();
    } else {
        $_SESSION['error'] = 'Registration failed. University ID or email may already exist.';
    }
}

header('Location: ../View/registration.php');
exit();
?>
