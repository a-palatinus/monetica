<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php') . '?newsletter=greska');
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO newsletter (email) VALUES (?)");
    $stmt->execute([$email]);
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php') . '?newsletter=uspjeh');
} catch (PDOException $e) {
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php') . '?newsletter=vec_pretplacen');
}
exit;
