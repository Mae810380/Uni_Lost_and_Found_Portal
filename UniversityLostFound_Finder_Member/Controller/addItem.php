<?php
session_start();
require_once "../Model/DatabaseConnection.php";
require_once "../Model/ItemModel.php";

if (!($_SESSION['isLoggedIn'] ?? false)) {
    header('Location: ../View/login.php');
    exit();
}

$type = $_POST['item_type'] ?? '';
$itemName = trim($_POST['item_name'] ?? '');
$category = trim($_POST['category'] ?? '');
$description = trim($_POST['description'] ?? '');
$location = trim($_POST['location'] ?? '');
$itemDate = $_POST['item_date'] ?? '';

if (!in_array($type, ['Lost', 'Found']) || $itemName == '' || $category == '' || $description == '' || $location == '' || $itemDate == '') {
    $_SESSION['error'] = 'Please complete all item fields.';
    header('Location: ../View/dashboard.php');
    exit();
}

$db = new DatabaseConnection();
$conn = $db->openConnection();
$model = new ItemModel();

if ($model->addItem($conn, $_SESSION['user_id'], $type, $itemName, $category, $description, $location, $itemDate)) {
    $_SESSION['success'] = $type . ' item report added successfully.';
} else {
    $_SESSION['error'] = 'Could not save the item report.';
}

header('Location: ../View/dashboard.php');
exit();
?>
