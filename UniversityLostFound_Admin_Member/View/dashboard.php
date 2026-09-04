<?php
session_start();
require_once "../Model/DatabaseConnection.php";
require_once "../Model/ItemModel.php";
require_once "../Model/ClaimModel.php";
require_once "../Model/UserModel.php";

if (!($_SESSION['isLoggedIn'] ?? false) || ($_SESSION['role'] ?? '') !== 'Admin') {
    header('Location: login.php');
    exit();
}

$db = new DatabaseConnection();
$conn = $db->openConnection();
$itemModel = new ItemModel();
$claimModel = new ClaimModel();
$userModel = new UserModel();
$myItems = $itemModel->getMyItems($conn, $_SESSION['user_id']);
$myClaims = $claimModel->getMyClaims($conn, $_SESSION['user_id']);
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
$allClaims = $claimModel->getAllClaims($conn);
$stats = $itemModel->getStats($conn);
$allUsers = $userModel->getAllUsers($conn);
$itemsForAdmin = $itemModel->getItems($conn, '');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - AIUB Lost & Found</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard-body">
<header class="topbar">
    <div><strong>AIUB Lost & Found</strong></div>
    <div><?php echo ($_SESSION['name']); ?> | Admin | <a href="../Controller/logout.php">Logout</a></div>
</header>

<main class="container">
    <section class="welcome">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo ($_SESSION['name']); ?>!</p>
        <p>University ID: <strong><?php echo ($_SESSION['student_id']); ?></strong></p>
    </section>

    <?php if ($error): ?><p class="error"><?php echo ($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?php echo ($success); ?></p><?php endif; ?>

    <section class="card">
        <h2>Admin Features</h2>
        <ol>
                <li>Approve/Reject Claims</li>
                <li>View System Statistics</li>
                <li>View Registered Users</li>
                <li>Delete Inappropriate Reports</li>

        </ol>
    </section>

    <section class="card">
        <h2>Common Account Management</h2>
        <form action="../Controller/profile.php" method="post">
            <input type="text" name="name" value="<?php echo ($_SESSION['name']); ?>" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="New password (optional)">
            <button type="submit">Update Profile</button>
        </form>
    </section>

    <section class="card">
        <h2>Claim Approval</h2>
        <?php if ($allClaims->num_rows == 0): ?><p class="small">No claim requests.</p><?php endif; ?>
        <?php while ($claim = $allClaims->fetch_assoc()): ?>
            <div class="list-row"><strong><?php echo ($claim['item_name']); ?></strong> - Claimer: <?php echo ($claim['student_id']); ?> - <?php echo ($claim['status']); ?><br>
            <span class="small"><?php echo ($claim['message']); ?></span>
            <?php if ($claim['status'] === 'Pending'): ?>
            <form action="../Controller/adminAction.php" method="post" class="inline-form"><input type="hidden" name="action" value="claim_status"><input type="hidden" name="claim_id" value="<?php echo $claim['claim_id']; ?>"><button name="status" value="Approved">Approve Claim</button><button name="status" value="Rejected">Reject Claim</button></form>
            <?php endif; ?></div>
        <?php endwhile; ?>
    </section>
    <section class="card">
        <h2>System Statistics</h2>
        <div class="stats"><div><b><?php echo $stats['total']; ?></b><span>Total</span></div><div><b><?php echo $stats['lost']; ?></b><span>Lost</span></div><div><b><?php echo $stats['found']; ?></b><span>Found</span></div><div><b><?php echo $stats['returned']; ?></b><span>Returned</span></div></div>
    </section>
    <section class="card">
        <h2>Registered Users</h2>
        <?php while ($user = $allUsers->fetch_assoc()): ?><div class="list-row"><?php echo ($user['student_id']); ?> - <?php echo ($user['name']); ?> - <?php echo ($user['role']); ?></div><?php endwhile; ?>
    </section>
    <section class="card">
        <h2>Remove Inappropriate Reports</h2>
        <?php while ($item = $itemsForAdmin->fetch_assoc()): ?>
            <div class="list-row"><strong><?php echo ($item['item_name']); ?></strong> - <?php echo ($item['status']); ?>
            <form action="../Controller/adminAction.php" method="post" class="inline-form"><input type="hidden" name="action" value="delete_item"><input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>"><button type="submit" onclick="return confirmDelete('Delete this report?');">Delete</button></form></div>
        <?php endwhile; ?>
    </section>

</main>
<script src="../assets/js/app.js"></script>
</body>
</html>
