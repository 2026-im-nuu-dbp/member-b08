<?php
session_start();
require 'db_config.php';

if ($_SESSION['is_admin'] != 1) {
    die('你不是管理員');
}

$id = intval($_GET['id']);  //intval(...)：將變數轉換為整數類型。這裡的 $_GET['id'] 是從 URL 中獲取的值，可能是字串類型。通過使用 intval() 函式，我們可以將這個值轉換為整數，以確保在後續的程式碼中使用 $id 變數時，它是一個有效的整數值。
//轉成整數的目的：確保 $id 變數中存儲的是一個有效的整數值，這樣在後續的 SQL 查詢中使用 $id 作為條件時，可以避免 SQL 注入攻擊等安全問題，並且確保查詢能夠正確地匹配到資料庫中的記錄。
$stmt = $pdo->prepare('DELETE FROM members WHERE id = ?');
$stmt->execute([$id]); 

header('Location: admin_members.php');   //header(...)：發送一個 HTTP 標頭到瀏覽器。這裡的 'Location: admin_members.php' 是一個重定向標頭，告訴瀏覽器跳轉到指定的 URL（在這裡是 admin_members.php）。當瀏覽器接收到這個標頭後，會自動導航到新的頁面，從而實現了在成功刪除會員後返回會員管理頁面的功能。
exit;