<?php
session_start();
require 'db_config.php';  //require

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //這行程式碼是 PHP 開發中極其重要的「門神」，它的主要作用是判斷當前的請求是否為 POST 提交
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare('SELECT * FROM members WHERE username = ?');
    $stmt->execute([$username]);
    $member = $stmt->fetch();

    if ($member && password_verify($password, $member['password'])) {
        $_SESSION['member_id'] = $member['id'];
        $_SESSION['nickname'] = $member['nickname'];
        $_SESSION['is_admin'] = $member['is_admin'];

        header('Location: index.php');
        exit;
    } else {
        $error = '帳號或密碼錯誤';
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="utf-8">
<title>會員登入</title>
</head>
<body>
<h2>會員登入</h2>

<?php if ($error): ?>
<p><?= escape($error) ?></p>
<?php endif; ?>

<form method="post">
    帳號：<input type="text" name="username" required><br><br>
    密碼：<input type="password" name="password" required><br><br>
    <button type="submit">登入</button>
</form>

<p><a href="register.php">註冊</a></p>
</body>
</html>