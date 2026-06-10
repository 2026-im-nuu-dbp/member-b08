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

    $newName = time() . "_" . $avatarName;//time()：返回當前的 Unix 時間戳（自 1970 年 1 月 1 日 00:00:00 UTC 起的秒數）。這裡的 time() 函式用於生成一個基於當前時間的唯一值，通常用於文件命名，以避免文件名稱衝突。通過將 time() 的返回值與原始文件名稱 $avatarName 結合起來，我們可以創建一個新的文件名稱 $newName，這樣即使多個使用者上傳了同名的文件，也能確保每個文件都有一個獨特的名稱。
    $path = "upload/" . $newName;

    move_uploaded_file($tmp, $path);//move_uploaded_file(...)：將上傳的臨時文件移動到指定的目標位置。這裡的 $tmp 是上傳文件的臨時路徑，而 $path 是我們希望將文件保存到的目標路徑。move_uploaded_file() 函式會檢查 $tmp 是否是一個有效的上傳文件，然後將其移動到 $path 指定的位置。如果移動成功，函式返回 true；如果失敗，則返回 false。這樣可以確保上傳的文件被正確地保存到伺服器上的指定位置。

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