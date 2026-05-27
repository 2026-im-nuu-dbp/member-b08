<?php
session_start();
require 'db_config.php';

if ($_SESSION['is_admin'] != 1) {
    die('你不是管理員');
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare('DELETE FROM members WHERE id = ?');
$stmt->execute([$id]);

header('Location: admin_members.php');
exit;