<?php
// Database configuration

$host = 'localhost';
$db = 'test_db';
$user = 'root';
$password = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    //setAttribute(...)：設置 PDO 物件的屬性。這裡的 PDO::ATTR_ERRMODE 是一個常量，表示要設置的屬性是錯誤模式；PDO::ERRMODE_EXCEPTION 是另一個常量，表示當發生錯誤時要拋出一個 PDOException 異常。通過這行程式碼，我們告訴 PDO 在執行 SQL 查詢或其他資料庫操作時，如果遇到任何錯誤，都應該拋出一個異常，這樣我們就可以在 catch 區塊中捕獲並處理這些錯誤了。
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('資料庫連線失敗: ' . $e->getMessage());   //getMessage()：用於從捕獲到的 PDOException 異常對象中獲取錯誤訊息。當在 try 區塊中執行資料庫連接或操作時，如果發生任何錯誤，會拋出一個 PDOException 異常，這個異常對象包含了有關錯誤的詳細信息。通過調用 $e->getMessage() 方法，我們可以獲取這些錯誤訊息，並將它們顯示給使用者或記錄到日誌中，以便進行調試和問題排查。
}

function escape($value)   //escape() 是一個自定義的函式，通常用來對輸出到 HTML 的資料進行轉義處理，以防止 XSS（跨站腳本攻擊）等安全問題。這裡的 escape($value) 表示將傳入的 $value 變數進行轉義後返回，確保即使 $value 中包含特殊字元（例如 <、>、& 等），也不會被瀏覽器解讀為 HTML 標籤或程式碼，而是以純文字的形式顯示在頁面上。
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');   //htmlspecialchars() 是 PHP 中用來將特殊字元轉換為 HTML 實體的函式。這裡的 htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') 表示將傳入的 $value 變數先強制轉換為字串類型，然後使用 ENT_QUOTES 參數將單引號和雙引號都轉換為 HTML 實體，最後指定字符編碼為 UTF-8。這樣做的目的是確保在輸出到 HTML 頁面時，任何包含特殊字元的資料都能被安全地顯示，而不會被瀏覽器解讀為 HTML 標籤或程式碼，從而防止 XSS 攻擊等安全問題。
}
