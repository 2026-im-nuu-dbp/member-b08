<?php
session_start();
require 'db_config.php';

if ($_SESSION['is_admin'] != 1) {
    die('你不是管理員');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nickname = trim($_POST['nickname']);
    $is_admin = intval($_POST['is_admin']);

    $stmt = $pdo->prepare('
        INSERT INTO members
        (username, password, nickname, is_admin)
        VALUES (?, ?, ?, ?)
    ');

    $stmt->execute([
        $username,
        $password,
        $nickname,
        $is_admin
    ]);

    header('Location: admin_members.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="utf-8">
<title>新增會員</title>
</head>
<body>

<h1>新增會員</h1>

<form method="post">

帳號：
<input type="text" name="username" required>
<br><br>

密碼：
<input type="password" name="password" required>
<br><br>

暱稱：
<input type="text" name="nickname" required>
<br><br>

管理員：
<select name="is_admin">
    <option value="0">否</option>
    <option value="1">是</option>
</select>

<br><br>

<button type="submit">新增</button>

</form>

</body>
</html>