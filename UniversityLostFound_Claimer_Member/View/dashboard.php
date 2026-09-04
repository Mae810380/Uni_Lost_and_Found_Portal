<?php
session_start();
require_once "../Model/DatabaseConnection.php";
require_once "../Model/ItemModel.php";
require_once "../Model/ClaimModel.php";
require_once "../Model/UserModel.php";

if (!($_SESSION['isLoggedIn'] ?? false) || ($_SESSION['role'] ?? '') !== 'Claimer') {
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
    <title>Claimer Dashboard - AIUB Lost & Found</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard-body">
<header class="topbar">
    <div><strong>AIUB Lost & Found</strong></div>
    <div><?php echo htmlspecialchars($_SESSION['name']); ?> | Claimer | <a href="../Controller/logout.php">Logout</a></div>
</header>

<main class="container">
    <section class="welcome">
        <h1>Claimer Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
        <p>University ID: <strong><?php echo htmlspecialchars($_SESSION['student_id']); ?></strong></p>
    </section>

    <?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>

    <section class="card">
        <h2>Claimer Features</h2>
        <ol>
                <li>Report Lost Item</li>
                <li>Search Found Items using AJAX</li>
                <li>Submit Claim Request</li>
                <li>Track Own Claim Status</li>

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

    <section class="card" id="report">
        <h2>Report a Lost Item</h2>
        <form action="../Controller/addItem.php" method="post">
            <input type="hidden" name="item_type" value="Lost">
            <label>Item Name</label><input type="text" name="item_name" placeholder="Black Wallet" required>
            <label>Category</label>
            <select name="category" required><option value="">Select</option><option>Electronics</option><option>Wallet</option><option>Bag</option><option>Books</option><option>Keys</option><option>Other</option></select>
            <label>Description</label><textarea name="description" placeholder="Write simple details about the item" required></textarea>
            <label>Campus Location</label><input type="text" name="location" placeholder="Campus 4, Library" required>
            <label>Date</label><input type="date" name="item_date" required>
            <button type="submit">Submit Lost Report</button>
        </form>
    </section>
    <section class="card">
        <h2>Find a Found Item (AJAX Search)</h2>
        <input type="text" id="searchBox" placeholder="Search item, category or location">
        <div id="searchResults" class="item-list"></div>
    </section>
    <section class="card">
        <h2>My Claim Requests</h2>
        <?php if ($myClaims->num_rows == 0): ?><p class="small">No claim requests yet.</p><?php endif; ?>
        <?php while ($claim = $myClaims->fetch_assoc()): ?>
            <div class="list-row"><strong><?php echo htmlspecialchars($claim['item_name']); ?></strong> - <?php echo htmlspecialchars($claim['status']); ?><br><span class="small"><?php echo htmlspecialchars($claim['message']); ?></span></div>
        <?php endwhile; ?>
    </section>
    <section class="card">
        <h2>My Lost Reports</h2>
        <?php if ($myItems->num_rows == 0): ?><p class="small">No reports yet.</p><?php endif; ?>
        <?php while ($item = $myItems->fetch_assoc()): ?>
            <div class="list-row"><strong><?php echo htmlspecialchars($item['item_name']); ?></strong> - <?php echo htmlspecialchars($item['status']); ?><br><span class="small"><?php echo htmlspecialchars($item['location']); ?></span></div>
        <?php endwhile; ?>
    </section>

</main>
<script src="../assets/js/app.js"></script>
</body>
</html>
