<?php
require 'db_config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $color = $_POST['color'];
    $nickname = $_POST['nickname'];

    // ===== 圖片上傳 =====
    $avatarName = $_FILES['avatar']['name'];
    $tmp = $_FILES['avatar']['tmp_name'];

    $newName = time() . "_" . $avatarName;
    $path = "upload/" . $newName;

    move_uploaded_file($tmp, $path);

    // ===== PDO 寫入 =====
    $sql = "INSERT INTO members(username, password, nickname, color, avatar)
            VALUES(:username, :password, :nickname, :color, :avatar)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username' => $username,
        ':password' => $password,
        ':nickname' => $nickname,
        ':color' => $color,
        ':avatar' => $path
    ]);

    echo "註冊成功";
    header("refresh:3;url=login.php");
    exit;
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

<form action="register.php" method="post" enctype="multipart/form-data">

    帳號：<input type="text" name="username"><br>
    密碼：<input type="password" name="password"><br>

    喜歡的顏色：
    <input type="color" name="color" value="#ffffff"><br>

    大頭貼：
    <input type="file" name="avatar"><br>

    暱稱：
    <input type="text" name="nickname"><br>

    <button type="submit">註冊</button>

</form>

</body>
</html>