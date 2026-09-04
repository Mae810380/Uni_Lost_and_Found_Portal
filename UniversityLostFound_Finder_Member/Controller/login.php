<?php
session_start();
require_once "../Model/DatabaseConnection.php";
require_once "../Model/UserModel.php";

$studentId = trim($_POST['student_id'] ?? '');
$password = $_POST['password'] ?? '';

if ($studentId == '' || $password == '') {
    $_SESSION['error'] = 'University ID and password are required.';
    header('Location: ../View/login.php');
    exit();
}

$db = new DatabaseConnection();
$conn = $db->openConnection();
$model = new UserModel();
$user = $model->findUser($conn, $studentId);

if ($user && password_verify($password, $user['password'])) {
    if (strcasecmp(trim($user['role']), 'Staff') !== 0) {
        $_SESSION['error'] = 'This package is for Staff account only.';
        header('Location: ../View/login.php');
        exit();
    }

    $_SESSION['isLoggedIn'] = true;
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['student_id'] = $user['student_id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = 'Staff';
    setcookie('student_id', $user['student_id'], time() + 3600, '/');
    header('Location: ../View/dashboard.php');
    exit();
}

$_SESSION['error'] = 'University ID or password is incorrect.';
header('Location: ../View/login.php');
exit();
?>
