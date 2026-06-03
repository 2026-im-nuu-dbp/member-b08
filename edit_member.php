<?php
session_start();
require 'db_config.php';

if ($_SESSION['is_admin'] != 1) {
    die('你不是管理員');
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare('SELECT * FROM members WHERE id = ?');
$stmt->execute([$id]);

$member = $stmt->fetch(); //fetch()：從 PDOStatement 對象中獲取下一行資料。這裡的 $stmt->fetch() 表示從之前執行的 SQL 查詢結果中獲取一行資料，並將其存入 $member 變數中。由於我們使用了 PDO::FETCH_ASSOC 模式，所以 $member 會是一個關聯陣列，其中的鍵是資料表中的欄位名稱，值則是對應欄位的資料值。如果查詢結果中沒有更多的資料行可供獲取，fetch() 方法會返回 false。

if (!$member) {
    die('找不到會員');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nickname = trim($_POST['nickname']);
    $is_admin = intval($_POST['is_admin']);

    $stmt = $pdo->prepare('
        UPDATE members
        SET nickname = ?, is_admin = ?
        WHERE id = ?
    ');

    $stmt->execute([
        $nickname,
        $is_admin,
        $id
    ]);

    header('Location: admin_members.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="utf-8">
<title>修改會員</title>
</head>
<body>

<h1>修改會員</h1>

<form method="post">

帳號：
<?= escape($member['username']) ?>

<br><br>

暱稱：
<input type="text"
       name="nickname"
       value="<?= escape($member['nickname']) ?>">

<br><br>

管理員：
<select name="is_admin">

<option value="0"
<?= $member['is_admin']==0 ? 'selected' : '' ?>>
否
</option>

<option value="1"
<?= $member['is_admin']==1 ? 'selected' : '' ?>>
是
</option>

</select>

<br><br>

<button type="submit">修改</button>

</form>

</body>
</html>