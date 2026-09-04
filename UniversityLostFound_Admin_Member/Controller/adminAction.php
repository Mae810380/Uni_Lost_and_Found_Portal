<?php
session_start();
require_once "../Model/DatabaseConnection.php";
require_once "../Model/ClaimModel.php";
require_once "../Model/ItemModel.php";

if (!($_SESSION['isLoggedIn'] ?? false)) {
    header('Location: ../View/login.php');
    exit();
}

if (($_SESSION['role'] ?? '') !== 'Admin') {
    $_SESSION['error'] = 'Only Admin can manage claim requests.';
    header('Location: ../View/dashboard.php');
    exit();
}

$action = $_POST['action'] ?? '';
$db = new DatabaseConnection();
$conn = $db->openConnection();
$claimModel = new ClaimModel();
$itemModel = new ItemModel();

if ($action === 'claim_status') {
    $status = in_array($_POST['status'] ?? '', ['Approved', 'Rejected']) ? $_POST['status'] : 'Pending';
    $claimId = (int)($_POST['claim_id'] ?? 0);

    if ($claimId > 0 && $claimModel->updateClaim($conn, $claimId, $status)) {
        $_SESSION['success'] = 'Claim ' . strtolower($status) . ' by Admin.';
    } else {
        $_SESSION['error'] = 'Could not update the claim.';
    }
} elseif ($action === 'delete_item') {
    $itemId = (int)($_POST['item_id'] ?? 0);
    if ($itemId > 0 && $itemModel->deleteItem($conn, $itemId)) {
        $_SESSION['success'] = 'Item report removed by Admin.';
    } else {
        $_SESSION['error'] = 'Could not remove the item report.';
    }
} else {
    $_SESSION['error'] = 'Unknown Admin action.';
}

header('Location: ../View/dashboard.php');
exit();
?>
