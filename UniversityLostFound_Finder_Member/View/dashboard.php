<?php
session_start();
require_once "../Model/DatabaseConnection.php";
require_once "../Model/ItemModel.php";
require_once "../Model/ClaimModel.php";
require_once "../Model/UserModel.php";

if (!($_SESSION['isLoggedIn'] ?? false) || ($_SESSION['role'] ?? '') !== 'Staff') {
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
$pendingItems = $itemModel->getPendingItems($conn);
$approvedClaimHistory = $claimModel->getApprovedClaimHistory($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Staff Dashboard - AIUB Lost & Found</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard-body">
<header class="topbar">
    <div><strong>AIUB Lost & Found</strong></div>
    <div><?php echo htmlspecialchars($_SESSION['name']); ?> | Staff | <a href="../Controller/logout.php">Logout</a></div>
</header>

<main class="container">
    <section class="welcome">
        <h1>Staff Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
        <p>University ID: <strong><?php echo htmlspecialchars($_SESSION['student_id']); ?></strong></p>
    </section>

    <?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>

    <section class="card">
        <h2>Staff Features</h2>
        <ol>
                <li>Verify/Reject Item Reports</li>
                <li>Review Admin-Approved Claims</li>
                <li>Mark Item as Returned</li>
                <li>View Pending Reports</li>

        </ol>
    </section>

    <section class="card">
        <h2>Common Account Management</h2>
        <form action="../Controller/profile.php" method="post">
            <input type="text" name="name" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="New password (optional)">
            <button type="submit">Update Profile</button>
        </form>
    </section>

    <section class="card">
        <h2>Pending Item Reports</h2>
        <?php if ($pendingItems->num_rows == 0): ?><p class="small">No pending reports.</p><?php endif; ?>
        <?php while ($item = $pendingItems->fetch_assoc()): ?>
            <div class="list-row">
                <strong><?php echo htmlspecialchars($item['item_name']); ?></strong> (<?php echo htmlspecialchars($item['item_type']); ?>) - <?php echo htmlspecialchars($item['student_id']); ?>
                <form action="../Controller/staffAction.php" method="post" class="inline-form">
                    <input type="hidden" name="action" value="verify_item"><input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                    <button name="status" value="Approved">Approve Report</button><button name="status" value="Rejected">Reject Report</button>
                </form>
            </div>
        <?php endwhile; ?>
    </section>
    <section class="card">
        <h2>Admin-Approved Claims / Return</h2>
        <p class="small">After Admin approves a claim, Staff completes the handover and marks the item returned.</p>
        <?php if ($approvedClaimHistory->num_rows == 0): ?><p class="small">No approved claims yet.</p><?php endif; ?>
        <?php while ($history = $approvedClaimHistory->fetch_assoc()): ?>
            <div class="list-row">
                <strong><?php echo htmlspecialchars($history['item_name']); ?></strong> - Claimer: <?php echo htmlspecialchars($history['claimer_id']); ?><br>
                Admin Approval: Approved - Item Status: <?php echo htmlspecialchars($history['item_status']); ?>
                <?php if ($history['item_status'] !== 'Returned'): ?>
                <form action="../Controller/staffAction.php" method="post" class="inline-form">
                    <input type="hidden" name="action" value="mark_returned"><input type="hidden" name="item_id" value="<?php echo $history['item_id']; ?>">
                    <button type="submit">Mark as Returned</button>
                </form>
                <?php else: ?><strong> - Returned</strong><?php endif; ?>
            </div>
        <?php endwhile; ?>
    </section>

</main>
<script src="../assets/js/app.js"></script>
</body>
</html>
