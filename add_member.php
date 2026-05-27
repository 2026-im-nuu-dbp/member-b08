<?php
session_start(); //啟動 Session 機制。這樣才能讀取或寫入伺服器上儲存的瀏覽器會話資料（例如登入狀態）
require 'db_config.php'; //引入（載入）名為 db_config.php 的檔案。

if ($_SESSION['is_admin'] != 1) { //檢查當前登入使用者的 Session 變數 is_admin 是否不等於 1（1 通常代表管理員）
    die('你不是管理員');//如果不是管理員，就印出「你不是管理員」這段文字，並立即終止整個程式的執行，
    // 防止非管理員進行後續的操作
}
//檢查瀏覽器發送過來的 HTTP 請求方法是否為 POST。這用來區別
//「第一次打開這個網頁（GET）」與「按下按鈕送出表單（POST）」
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    //使用 trim() 函式去除字串前後不小心的空格，最後存入變數 $username
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    //使用 password_hash() 函式將密碼進行安全的雜湊處理，最後存入變數 $password
    $nickname = trim($_POST['nickname']);
    //使用 trim() 函式去除字串前後不小心的空格，最後存入變數 $nickname
    $is_admin = intval($_POST['is_admin']);
    //使用 intval() 函式將 is_admin 轉換為整數，最後存入變數 $is_admin

    //$pdo->prepare(...)：準備一條 SQL 預處理語句（Prepared Statement）
    $stmt = $pdo->prepare(' 
        INSERT INTO members
        (username, password, nickname, is_admin)
        VALUES (?, ?, ?, ?)
    ');

    $stmt->execute([ //execute(...)：執行之前準備好的 SQL 預處理語句，並傳入對應的參數值
        $username,
        $password,
        $nickname,
        $is_admin
    ]);

    header('Location: admin_members.php');//使用 header() 函式發送一個 HTTP Location 標頭，告訴瀏覽器重定向到 admin_members.php 頁面
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