<?php
session_start();
session_destroy();   //session_destroy()：用於終止當前的 Session，這會清除所有與該 Session 相關的資料，並使 Session 失效。當使用者執行登出操作時，我們通常會調用 session_destroy() 函式來確保使用者的登入狀態被清除，從而保護使用者的帳戶安全。
header('Location: index.php');
exit;