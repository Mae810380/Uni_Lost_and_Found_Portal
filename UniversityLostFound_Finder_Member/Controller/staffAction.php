<?php
session_start();
require_once "../Model/DatabaseConnection.php";
require_once "../Model/ItemModel.php";

if (!($_SESSION['isLoggedIn'] ?? false)) {
    header('Location: ../View/login.php');
    exit();
}

$role = $_SESSION['role'] ?? '';
$action = $_POST['action'] ?? '';
$db = new DatabaseConnection();
$conn = $db->openConnection();
$itemModel = new ItemModel();

if ($role === 'Staff') {
    if ($action === 'verify_item') {
        $status = $_POST['status'] === 'Approved' ? 'Approved' : 'Rejected';
        $itemModel->updateStatus($conn, (int)$_POST['item_id'], $status);
        $_SESSION['success'] = 'Item report updated by staff.';
    } elseif ($action === 'mark_returned') {
        $itemModel->updateStatus($conn, (int)$_POST['item_id'], 'Returned');
        $_SESSION['success'] = 'Item marked as returned.';
    }
} else {
    $_SESSION['error'] = 'This controller is only for staff actions.';
}

header('Location: ../View/dashboard.php');
exit();
?>
