<?php
session_start();
require 'db_config.php';

if (!isset($_SESSION['member_id'])) {
    die('請先登入');
}

if ($_SESSION['is_admin'] != 1) {
    die('你不是管理員');
}

$stmt = $pdo->query('SELECT * FROM members ORDER BY id DESC');
$members = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="utf-8">
<title>會員管理</title>
<style>
table{
    width:100%;
    border-collapse: collapse;
}
th,td{
    border:1px solid #ccc;
    padding:10px;
}
a{
    text-decoration:none;
}
</style>
</head>
<body>

<h1>會員管理</h1>

<p>
    <a href="index.php">返回首頁</a> |
    <a href="add_member.php">新增會員</a>
</p>

<table>
    <tr>
        <th>ID</th>
        <th>帳號</th>
        <th>暱稱</th>
        <th>管理員</th>
        <th>功能</th>
    </tr>

    <?php foreach($members as $m): ?>
    <tr>
        <td><?= $m['id'] ?></td>
        <td><?= escape($m['username']) ?></td>
        <td><?= escape($m['nickname']) ?></td>
        <td><?= $m['is_admin'] ?></td>
        <td>
            <a href="edit_member.php?id=<?= $m['id'] ?>">修改</a>
            |
            <a href="delete_member.php?id=<?= $m['id'] ?>"
               onclick="return confirm('確定刪除?')">
               刪除
            </a>
        </td>
    </tr>
    <?php endforeach; ?>

</table>

</body>
</html>