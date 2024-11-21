<?php
// 資料庫連線設定
$host = "localhost";
$username = "root";
$password = "";
$dbname = "carbon_emissions";

try {
    // 建立 PDO 連線
    $db_handle = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("資料庫連線失敗：" . $e->getMessage());
}
?>
