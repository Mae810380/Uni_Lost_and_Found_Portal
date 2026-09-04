<?php
session_start();
if ($_SESSION['isLoggedIn'] ?? false) {
    header('Location: dashboard.php');
    exit();
}
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>

<html>
<head>
    <title>Login - AIUB Lost & Found</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-body">
<div class="auth-box">
    <h1>AIUB Lost & Found</h1>
    <p class="small"> ID example: XX-XXXXX-X</p>
    <?php if ($error): ?><p class="error"><?php echo ($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?php echo ($success); ?></p><?php endif; ?>
    <form action="../Controller/login.php" method="post">
        <label>University ID</label>
        <input type="text" name="student_id" placeholder="22-46183-1" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <p>New user? <a href="registration.php">Create account</a></p>
</div>
<script src="../assets/js/app.js"></script>
</body>
</html>
