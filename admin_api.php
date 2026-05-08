<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

require_once 'db.php';

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT AccountType FROM Users WHERE Username = ?");
$stmt->execute([$username]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || $row['AccountType'] !== 'ADMIN') {
    echo json_encode(['success' => false, 'error' => 'Admin access required']);
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

if ($action === 'get_all_users') {
    $stmt = $conn->prepare("SELECT FirstName, LastName, Username, Email, ContactNumber, AccountType FROM Users ORDER BY Username");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'users' => $users]);

} elseif ($action === 'update_account_type') {
    $targetUsername = isset($_POST['username']) ? $_POST['username'] : null;
    $newAccountType = isset($_POST['account_type']) ? $_POST['account_type'] : null;

    if (!$targetUsername || !$newAccountType) {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        exit();
    }
    if ($targetUsername === $username && $newAccountType !== 'ADMIN') {
        echo json_encode(['success' => false, 'error' => 'Cannot remove your own admin status']);
        exit();
    }

    $stmt = $conn->prepare("UPDATE Users SET AccountType = ? WHERE Username = ?");
    if ($stmt->execute([$newAccountType, $targetUsername])) {
        echo json_encode(['success' => true, 'message' => 'Account type updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update account type']);
    }

} elseif ($action === 'delete_user') {
    $targetUsername = isset($_POST['username']) ? $_POST['username'] : null;

    if (!$targetUsername) {
        echo json_encode(['success' => false, 'error' => 'Username required']);
        exit();
    }
    if ($targetUsername === $username) {
        echo json_encode(['success' => false, 'error' => 'Cannot delete your own account']);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM Users WHERE Username = ?");
    if ($stmt->execute([$targetUsername])) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete user']);
    }

} elseif ($action === 'reset_password') {
    $targetUsername = isset($_POST['username']) ? $_POST['username'] : null;
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : null;

    if (!$targetUsername || !$newPassword) {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        exit();
    }
    if (strlen($newPassword) < 4) {
        echo json_encode(['success' => false, 'error' => 'Password must be at least 4 characters']);
        exit();
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE Users SET Password = ? WHERE Username = ?");
    if ($stmt->execute([$newHash, $targetUsername])) {
        echo json_encode(['success' => true, 'message' => 'Password reset successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to reset password']);
    }

} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
