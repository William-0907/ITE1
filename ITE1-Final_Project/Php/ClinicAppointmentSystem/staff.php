<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        echo "<p style='text-align:center;color:red;'>⚠ Please enter both username and password.</p>";
        echo "<p style='text-align:center;'><a href='login.php'>Go back to login</a></p>";
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM auth_user WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['staff_logged_in'] = true;
            $_SESSION['staff_username'] = $username;
            header('Location: dashboard.php'); 
            exit;
        } else {
            echo "<p style='text-align:center;color:red;'>❌ Invalid username or password.</p>";
            echo "<p style='text-align:center;'><a href='login.php'>Go back to login</a></p>";
            exit;
        }

    } catch (PDOException $e) {
        echo "<p style='text-align:center;color:red;'>⚠ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    header('Location: login.php');
    exit;
}
?>
