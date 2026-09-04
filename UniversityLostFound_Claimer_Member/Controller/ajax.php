<?php
session_start();
header('Content-Type: application/json');
require_once "../Model/DatabaseConnection.php";
require_once "../Model/ItemModel.php";
require_once "../Model/ClaimModel.php";

if (!($_SESSION['isLoggedIn'] ?? false)) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit();
}

$db = new DatabaseConnection();
$conn = $db->openConnection();
$itemModel = new ItemModel();
$claimModel = new ClaimModel();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'search') {
    $keyword = trim($_GET['keyword'] ?? '');
    $result = $itemModel->getItems($conn, $keyword);
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    echo json_encode(['success' => true, 'items' => $items]);
    exit();
}

if ($action === 'claim') {
    $role = trim($_SESSION['role'] ?? '');
    if (strcasecmp($role, 'Student') === 0) { $role = 'Claimer'; }
    if ($role !== 'Claimer') {
        echo json_encode(['success' => false, 'message' => 'Only Claimer can submit a claim.']);
        exit();
    }
    $itemId = (int)($_POST['item_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    if ($itemId <= 0 || $message == '') {
        echo json_encode(['success' => false, 'message' => 'Please enter a short claim message.']);
        exit();
    }
    $ok = $claimModel->createClaim($conn, $itemId, $_SESSION['user_id'], $message);
    echo json_encode(['success' => $ok, 'message' => $ok ? 'Claim request sent.' : 'Could not send claim request.']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Unknown action.']);
?>
