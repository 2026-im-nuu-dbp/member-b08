<?php
// Insert new discussion into database

header('Content-Type: text/html; charset=utf-8');
require 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method.');
}

$member_id = isset($_POST['member_id']) ? trim($_POST['member_id']) : '';
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// Validation
if (empty($member_id) || empty($title) || empty($content)) {
    die('所有欄位都必須填寫。<br><a href="index.php">返回</a>');
}

// Limit input length
$member_id = substr($member_id, 0, 100);
$title = substr($title, 0, 200);
$content = substr($content, 0, 10000);

try {
    $stmt = $pdo->prepare('INSERT INTO news (title, content, member_id) VALUES (?, ?, ?)');
    $stmt->execute([$title, $content, $member_id]);

    // Redirect to homepage
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    die('發表討論失敗: ' . $e->getMessage() . '<br><a href="index.php">返回</a>');
}
