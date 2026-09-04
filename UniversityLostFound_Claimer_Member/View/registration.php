<?php
session_start();
if ($_SESSION['isLoggedIn'] ?? false) {
    header('Location: dashboard.php');
    exit();
}
$error = $_SESSION['error'] ?? '';
$studentId = $_SESSION['old_student_id'] ?? '';
$name = $_SESSION['old_name'] ?? '';
$email = $_SESSION['old_email'] ?? '';
unset($_SESSION['error'], $_SESSION['old_student_id'], $_SESSION['old_name'], $_SESSION['old_email']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration - AIUB Lost & Found</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-body">
<div class="auth-box">
    <h1>Create Account</h1>
    <p class="small">Use your university-style ID, for example 22-46183-1.</p>
    <?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <form action="../Controller/register.php" method="post">
        <label>University ID</label>
        <input type="text" name="student_id" value="<?php echo htmlspecialchars($studentId); ?>" placeholder="22-46183-1" required>
        <label>Full Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        <label>AIUB Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="you@example.com" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <label>User Type</label>
        <select name="role">
            <option value="Claimer">Claimer</option>
            <option value="Finder">Finder</option>
        </select>
        <button type="submit">Register</button>
    </form>
    <p>Already registered? <a href="login.php">Login here</a></p>
</div>
<script src="../assets/js/app.js"></script>
</body>
</html>
