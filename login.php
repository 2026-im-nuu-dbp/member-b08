<?php
session_start();
require 'db_config.php';  //require

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //這行程式碼是 PHP 開發中極其重要的「門神」，它的主要作用是判斷當前的請求是否為 POST 提交
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare('SELECT * FROM members WHERE username = ?');
    $stmt->execute([$username]);
    $member = $stmt->fetch();  //fetch()：從 PDOStatement 對象中獲取下一行資料。這裡的 $stmt->fetch() 表示從之前執行的 SQL 查詢結果中獲取一行資料，並將其存入 $member 變數中。由於我們使用了 PDO::FETCH_ASSOC 模式，所以 $member 會是一個關聯陣列，其中的鍵是資料表中的欄位名稱，值則是對應欄位的資料值。如果查詢結果中沒有更多的資料行可供獲取，fetch() 方法會返回 false。
 
    if ($member && password_verify($password, $member['password'])) {  //password_verify()：用於驗證使用者輸入的密碼是否與資料庫中存儲的雜湊密碼相匹配。這裡的 $password 是使用者從登入表單中輸入的明文密碼，而 $member['password'] 是從資料庫中獲取的已經過雜湊處理的密碼。password_verify() 函式會將明文密碼進行相同的雜湊處理，然後與資料庫中的雜湊密碼進行比較，如果兩者匹配則返回 true，表示驗證成功；如果不匹配則返回 false，表示驗證失敗。
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