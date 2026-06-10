<?php
// Insert new discussion into database

session_start();
header('Content-Type: text/html; charset=utf-8');
require 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method.');
}

if (!isset($_SESSION['member_id'])) {//isset(...)：用於檢查變數是否已經被設定並且不是 null 的函式。這裡用來檢查 $_SESSION['member_id'] 是否存在，如果不存在就表示使用者尚未登入，這時我們會終止程式的執行並顯示「請先登入」的訊息。
    die('請先登入');
}

$member_id = $_SESSION['member_id'];
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// 修改後的驗證：只檢查內容和會員 ID
if (empty($member_id) || empty($content)) {//empty(...)：用於檢查變數是否為空的函式。這裡用來檢查 $member_id 和 $content 是否為空，如果其中任何一個變數為空，就表示使用者沒有提供必要的資訊，這時我們會終止程式的執行並顯示「所有欄位都必須填寫」的訊息。
    die('所有欄位都必須填寫。<br><a href="index.php">返回</a>');
}

// Limit input length
$title = substr($title, 0, 200);//substr(...)：從字串中提取子字串。這裡的 $title 是要被限制長度的字串，0 是起始位置，200 是要提取的最大長度。這行程式碼的作用是將 $title 的長度限制在 200 個字符以內，如果 $title 的長度超過 200 個字符，則只保留前 200 個字符，超過部分會被截斷。
$content = substr($content, 0, 10000);

try {
    $stmt = $pdo->prepare('INSERT INTO news (title, content, member_id) VALUES (?, ?, ?)');
    $stmt->execute([$title, $content, $member_id]);

    // Redirect to homepage
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    die('發表討論失敗: ' . $e->getMessage() . '<br><a href="index.php">返回</a>');//getMessage()：用於從捕獲到的 PDOException 異常對象中獲取錯誤訊息。當在 try 區塊中執行資料庫操作時，如果發生任何錯誤，會拋出一個 PDOException 異常，這個異常對象包含了有關錯誤的詳細信息。通過調用 $e->getMessage() 方法，我們可以獲取這些錯誤訊息，並將它們顯示給使用者或記錄到日誌中，以便進行調試和問題排查。
}
