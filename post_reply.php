<?php
// Insert reply into database

session_start();  
header('Content-Type: text/html; charset=utf-8');   //設定 HTTP 響應的內容類型為 text/html，並指定字符編碼為 UTF-8。這樣瀏覽器在接收這個響應時，就會知道如何正確地解析和顯示其中的文字內容，特別是對於包含中文等非 ASCII 字符的情況。
require 'db_config.php';   //引入（載入）名為 db_config.php 的檔案。這個檔案通常包含了資料庫連接的設定，例如主機名稱、使用者名稱、密碼和資料庫名稱等。通過引入這個檔案，當前的 PHP 腳本就可以使用其中定義的資料庫連接資訊來與資料庫進行交互，例如執行 SQL 查詢或插入數據等操作。
//「第一次打開這個網頁（GET）」與「按下按鈕送出表單（POST）」的區別：當使用者第一次打開這個網頁時，瀏覽器會發送一個 GET 請求，這時 $_SERVER['REQUEST_METHOD'] 的值是 'GET'；而當使用者在網頁上填寫表單並按下提交按鈕時，瀏覽器會發送一個 POST 請求，這時 $_SERVER['REQUEST_METHOD'] 的值是 'POST'。通過檢查這個變數的值，我們可以確定當前的請求是來自於哪種操作，從而執行相應的程式碼邏輯。
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method.');
}

$newsId = isset($_POST['news_id']) ? intval($_POST['news_id']) : 0;  //isset是用來檢查變數是否已經被設定並且不是 null 的函式。這裡用來檢查 $_POST['news_id'] 是否存在，如果存在就將其轉換為整數並賦值給 $newsId 變數；如果不存在則將 $newsId 設置為 0。這樣做的目的是確保 $newsId 變數始終有一個有效的整數值，避免在後續的程式碼中出現未定義變數或非整數值的問題。
$member_id = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : '';  //$_SESSION 是一個超全局變數，用於存儲用戶的會話資料。這裡使用 isset() 函式來檢查 $_SESSION['member_id'] 是否已經被設定，如果已經設定就將其值賦給 $member_id 變數；如果沒有設定則將 $member_id 設置為空字串。這樣做的目的是確保 $member_id 變數始終有一個有效的值，避免在後續的程式碼中出現未定義變數或空值的問題。
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// Validation
if ($newsId <= 0) {
    die('無效的討論 ID。<br><a href="index.php">返回</a>');
}

if (empty($member_id) || empty($content)) {
    die('所有欄位都必須填寫。<br><a href="show_news.php?id=' . $newsId . '">返回</a>');
}

// Check if news exists
try {
    $stmt = $pdo->prepare('SELECT id FROM news WHERE id = ?');  //prepare(...)：準備一條 SQL 預處理語句（Prepared Statement）。這裡的 SQL 語句是 'SELECT id FROM news WHERE id = ?'，其中的問號 (?) 是一個佔位符，用於在執行語句時替換成實際的值。這種方式可以幫助防止 SQL 注入攻擊，因為使用者輸入的值不會直接插入到 SQL 語句中，而是作為參數傳遞給資料庫引擎進行處理。
    $stmt->execute([$newsId]);   //execute(...)：執行之前準備好的 SQL 預處理語句，並傳入對應的參數值。這裡傳入了一個包含 $newsId 的陣列，這個值會替換掉 SQL 語句中的問號 (?) 佔位符。資料庫引擎會根據這個值來執行查詢，並返回相應的結果。
    if (!$stmt->fetch()) {  
        die('找不到此討論。<br><a href="index.php">返回首頁</a>');
    }
} catch (PDOException $e) {
    die('驗證失敗: ' . $e->getMessage());
}

// Limit input length
$member_id = substr($member_id, 0, 100);  //substr(...)：從字串中提取子字串。這裡的 $member_id 是要被限制長度的字串，0 是起始位置，100 是要提取的最大長度。這行程式碼的作用是將 $member_id 的長度限制在 100 個字符以內，如果 $member_id 的長度超過 100 個字符，則只保留前 100 個字符，超過部分會被截斷。
$content = substr($content, 0, 10000);

try {
    $stmt = $pdo->prepare('INSERT INTO replies (news_id, content, member_id) VALUES (?, ?, ?)');
    $stmt->execute([$newsId, $content, $member_id]);

    // Redirect back to discussion page
    header('Location: show_news.php?id=' . $newsId);   //header(...)：發送一個 HTTP 標頭到瀏覽器。這裡的 'Location: show_news.php?id=' . $newsId 是一個重定向標頭，告訴瀏覽器跳轉到指定的 URL（在這裡是 show_news.php?id=某個討論 ID）。當瀏覽器接收到這個標頭後，會自動導航到新的頁面，從而實現了在成功發表回應後返回討論頁面的功能。
    exit;
} catch (PDOException $e) {
    die('發表回應失敗: ' . $e->getMessage() . '<br><a href="show_news.php?id=' . $newsId . '">返回</a>');
}
//PDOException是 PHP Data Objects (PDO) 擴展中用於表示資料庫相關錯誤的異常類別。當在使用 PDO 進行資料庫操作時發生錯誤，例如連接失敗、SQL 語法錯誤或執行失敗等，會拋出一個 PDOException 異常。這個異常對象包含了有關錯誤的詳細信息，例如錯誤訊息、錯誤碼和堆疊跟蹤等。通過捕獲 PDOException，可以對資料庫操作中的錯誤進行適當的處理，例如顯示錯誤訊息給使用者或記錄錯誤日誌等。