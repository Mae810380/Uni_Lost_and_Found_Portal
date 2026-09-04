<?php
session_start();
require_once "../Model/DatabaseConnection.php";
require_once "../Model/ItemModel.php";
require_once "../Model/ClaimModel.php";
require_once "../Model/UserModel.php";

if (!($_SESSION['isLoggedIn'] ?? false) || ($_SESSION['role'] ?? '') !== 'Finder') {
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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Finder Dashboard - AIUB Lost & Found</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard-body">
<header class="topbar">
    <div><strong>AIUB Lost & Found</strong></div>
    <div><?php echo ($_SESSION['name']); ?> | Finder | <a href="../Controller/logout.php">Logout</a></div>
</header>

<main class="container">
    <section class="welcome">
        <h1>Finder Dashboard</h1>
        <p>Welcome, <?php echo ($_SESSION['name']); ?>!</p>
        <p>University ID: <strong><?php echo ($_SESSION['student_id']); ?></strong></p>
    </section>

    <?php if ($error): ?><p class="error"><?php echo ($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?php echo ($success); ?></p><?php endif; ?>

    <section class="card">
        <h2>Finder Features</h2>
        <ol>
                <li>Post Found Item</li>
                <li>Record Exact Found Location</li>
                <li>View Own Found-Item History</li>

        </ol>
    </section>

    <section class="card">
        <h2>Profile Update</h2>
        <form action="../Controller/profile.php" method="post">
            <input type="text" name="name" value="<?php echo ($_SESSION['name']); ?>" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="New password (optional)">
            <button type="submit">Update Profile</button>
        </form>
    </section>

    <section class="card" id="report">
        <h2>Post a Found Item</h2>
        <form action="../Controller/addItem.php" method="post">
            <input type="hidden" name="item_type" value="Found">
            <label>Item Name</label><input type="text" name="item_name" placeholder="Black Wallet" required>
            <label>Category</label>
            <select name="category" required>
                <option value="">Select</option>
                <option>Electronics</option>
                <option>Wallet</option>
                <option>Bag</option>
                <option>Books</option>
                <option>Keys</option>
                <option>Other</option>
            </select>
            <label>Description</label>
            <textarea name="description" placeholder="Write simple details about the item" required></textarea>
            <label>Exact Found Location</label>
            <input type="text" name="location" placeholder="Campus 4, Library" required>
            <label>Date Found</label>
            <input type="date" name="item_date" required>
            <button type="submit">Submit Found Item</button>
        </form>
    </section>
    <section class="card">
        <h2>My Found Item History</h2>
        <?php if ($myItems->num_rows == 0): ?><p class="small">No found items posted yet.</p><?php endif; ?>
        <?php while ($item = $myItems->fetch_assoc()): ?>
            <div class="list-row"><strong><?php echo ($item['item_name']); ?></strong> - <?php echo ($item['status']); ?><br><span class="small">Found at: <?php echo htmlspecialchars($item['location']); ?></span></div>
        <?php endwhile; ?>
    </section>

</main>
<script src="../assets/js/app.js"></script>
</body>
</html>
