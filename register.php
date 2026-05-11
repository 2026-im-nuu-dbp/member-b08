<?php
session_start();
require 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $nickname = trim($_POST['nickname']);

    if ($username && $password && $nickname) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            'INSERT INTO members (username, password, nickname) VALUES (?, ?, ?)'
        );
        $stmt->execute([$username, $hash, $nickname]);

        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="utf-8">
<title>會員註冊</title>
</head>
<body>
<h2>會員註冊</h2>

<form method="post">
    帳號：<input type="text" name="username" required><br><br>
    密碼：<input type="password" name="password" required><br><br>
    暱稱：<input type="text" name="nickname" required><br><br>
    <button type="submit">註冊</button>
</form>

<p><a href="login.php">登入</a></p>
</body>
</html>