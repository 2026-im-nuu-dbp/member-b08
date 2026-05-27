<?php
// Read and display discussion topics
session_start();  ////啟動 Session 機制。這樣才能讀取或寫入伺服器上儲存的瀏覽器會話資料（例如登入狀態）
header('Content-Type: text/html; charset=utf-8');
require 'db_config.php';  //require 載入名為 db_config.php 的檔案。這個檔案通常包含資料庫連接的設定和初始化程式碼，讓我們可以使用 $pdo 物件來與資料庫進行互動。

// Fetch all news topics with reply count
//這段程式碼使用 PDO 物件 $pdo 執行一條 SQL 查詢，從 news 表中選取討論主題的相關資訊（id、title、created_at）以及發表者的會員資訊（nickname、avatar、color），同時使用 LEFT JOIN 連接 replies 表來計算每個討論主題的回應數量（reply_count）。查詢結果按照 created_at 欄位降序排列，最新的討論主題會顯示在最前面。最後將查詢結果存入 $news 變數中，以便在後續的 HTML 部分進行顯示。
try {
    $stmt = $pdo->query('
        SELECT 
        n.id,
        n.title,
        n.created_at,
        m.nickname,
        m.avatar,
        m.color,
        COUNT(r.id) as reply_count
        FROM news n
        JOIN members m ON n.member_id = m.id
        LEFT JOIN replies r ON n.id = r.news_id
        GROUP BY n.id, n.title, n.created_at, m.nickname
        ORDER BY n.created_at DESC
    ');  //把『新聞（或文章）』、『發表者（會員）』、『留言（回覆）』這三張資料表串聯在一起，並計算每篇文章有幾則留言，最後依照時間從最新到最舊排序。
    $news = $stmt->fetchAll();
    //為什麼用 LEFT JOIN？：因為有些新聞可能一則留言都沒有。如果用一般的 JOIN，沒有留言的新聞就會在畫面上消失；用 LEFT JOIN 可以確保「就算留言數是 0，新聞依然能正常顯示」。
} catch (PDOException $e) {  //PDOException 是在使用 PDO 物件與資料庫互動時可能會拋出的例外類型。如果在執行 SQL 查詢的過程中發生任何錯誤（例如語法錯誤、連接問題等），就會捕捉到這個例外，並將錯誤訊息存入 $error 變數中，以便在後續的 HTML 部分顯示給使用者看。    
    $error = '讀取討論失敗: ' . $e->getMessage();
    $news = [];
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <title>討論區</title>
    <style>
        body {
            font-family: system-ui, -apple-system, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        .form-box {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        button {
            background: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
        .news-list {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .news-item {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        .news-item:last-child {
            border-bottom: none;
        }
        .news-item:hover {
            background: #f9f9f9;
        }
        .news-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .news-title a {
            color: #007bff;
            text-decoration: none;
        }
        .news-title a:hover {
            text-decoration: underline;
        }
        .news-meta {
            font-size: 14px;
            color: #666;
        }
        .reply-count {
            display: inline-block;
            background: #28a745;
            color: #fff;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 10px;
        }
        .empty {
            text-align: center;
            color: #999;
            padding: 40px;
            font-style: italic;
        }
        .error {
            color: #d32f2f;
            padding: 10px;
            background: #ffebee;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 討論區</h1>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
    <p>
        <a href="admin_members.php">會員管理</a>
    </p>
    <?php endif; ?>

        <?php if (isset($_SESSION['member_id'])): ?>
    <p>
        歡迎 <?= escape($_SESSION['nickname']) ?>
        ｜ <a href="logout.php">登出</a>
    </p>
    <?php else: ?>
    <p>
        <a href="login.php">登入</a>
        ｜ 
        <a href="register.php">註冊</a>
    </p>
    <?php endif; ?>  //endif 是 if 條件語句的結束標誌，表示前面的 if 條件判斷結束了。這裡的 if 條件是用來檢查使用者是否已經登入（即是否存在 member_id 的 Session 變數）。如果使用者已經登入，就會顯示歡迎訊息和登出連結；如果使用者尚未登入，就會顯示登入和註冊的連結。最後的 endif; 就是這個條件判斷的結束，告訴 PHP 這裡不再屬於 if 條件內的程式碼了。

        <div class="form-box">
            <h2>發表新討論</h2>
            <form action="post.php" method="post">
                
                <div class="form-group">
                    <label for="title">標題：</label>
                    <input type="text" id="title" name="title" maxlength="200" required>
                </div>

                <div class="form-group">
                    <label for="content">內容：</label>
                    <textarea id="content" name="content" required></textarea>
                </div>

                <button type="submit">發表討論</button>
            </form>
        </div>

        <h2>討論列表</h2>

        <?php if (empty($news)): ?>
            <div class="news-list">
                <p class="empty">目前沒有討論。</p>
            </div>
        <?php else: ?>
            <div class="news-list">
                <?php foreach ($news as $item): ?>  //foreach 是 PHP 中用來遍歷陣列或物件的語法結構。這裡的 foreach ($news as $item) 表示對 $news 陣列中的每一個元素（每一篇討論主題）進行迭代，將當前元素的值賦給變數 $item。在迴圈內部，我們可以使用 $item 來訪問當前討論主題的相關資訊（例如 id、title、nickname 等），並將它們顯示在 HTML 中。最後的 endforeach; 就是這個迴圈的結束標誌，告訴 PHP 這裡不再屬於 foreach 迴圈內的程式碼了。
                    <div class="news-item">
                        <div class="news-title">
                            <a href="show_news.php?id=<?= $item['id'] ?>">  //href=這是一個超連結，當使用者點擊這個連結時，瀏覽器會導向 show_news.php 頁面，並且在 URL 中帶上 id 參數，值為該討論主題的 id。這樣 show_news.php 就可以根據這個 id 來查詢並顯示對應的討論內容和回覆。
                                <?= escape($item['title']) ?>
                            </a>
                            <?php if ($item['reply_count'] > 0): ?>
                                <span class="reply-count">
                                    <?= $item['reply_count'] ?> 則回應
                                </span>
                            <?php endif; ?>  //endif 是 if 條件語句的結束標誌，表示前面的 if 條件判斷結束了。這裡的 if 條件是用來檢查該討論主題的回應數量（reply_count）是否大於 0。如果回應數量大於 0，就會顯示一個綠色的標籤，裡面寫著「X 則回應」，其中 X 是實際的回應數量。最後的 endif; 就是這個條件判斷的結束，告訴 PHP 這裡不再屬於 if 條件內的程式碼了。
                        </div>
                        <div class="news-meta"
                            style="
                            background: <?= escape($item['color']) ?>;
                            padding:10px;
                            border-radius:10px;
                        ">

                        <strong>
                            <?= escape($item['nickname']) ?>  //escape() 是一個自定義的函式，通常用來對輸出到 HTML 的資料進行轉義處理，以防止 XSS（跨站腳本攻擊）等安全問題。這裡的 escape($item['nickname']) 表示將該討論主題的發表者暱稱進行轉義後輸出，確保即使暱稱中包含特殊字元（例如 <、>、& 等），也不會被瀏覽器解讀為 HTML 標籤或程式碼，而是以純文字的形式顯示在頁面上。
                        </strong>

                        <img src="<?= escape($item['avatar']) ?>"
                            width="40"
                            height="40"
                            style="
                                border-radius:50%;
                                vertical-align:middle;
                                margin-left:8px;
                                object-fit:cover;
                            ">

                        <br>

                        發表於 <?= escape($item['created_at']) ?>

                    </div>
                    </div>
                <?php endforeach; ?>  //endforeach 是 foreach 迴圈的結束標誌，表示前面的 foreach 條件判斷結束了。這裡的 foreach 條件是用來遍歷 $news 陣列中的每一個討論主題，並將當前討論主題的資訊賦值給 $item 變數。在迴圈內部，我們使用 $item 來顯示每個討論主題的標題、發表者資訊、發表時間等。最後的 endforeach; 就是這個迴圈的結束，告訴 PHP 這裡不再屬於 foreach 迴圈內的程式碼了。
            </div>
        <?php endif; ?>
        
    </div>
</body>
</html>
