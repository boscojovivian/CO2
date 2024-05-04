<?php

session_start();

// 建立 MySQL 資料庫連結
$link = mysqli_connect("localhost", "root", "A12345678") 
or die("無法開啟 MySQL 資料庫連結!<br>");
mysqli_select_db($link, "carbon_emissions"); // 選擇 feedback 資料庫

// 設定 MySQL 查詢字串
$sql = "";

// 送出 UTF8 編碼的 MySQL 指令
mysqli_query($link, "SET NAMES utf8");

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据
    $sender = $_SESSION['em_email'];
    $message = mysqli_real_escape_string($link, $_POST["message"]);

    // 构建插入数据的 SQL 语句
    $sql = "INSERT INTO message (mes_email, mes_message) VALUES ('$sender', '$message')";

    // 执行 SQL 语句
    $result = mysqli_query($link, $sql);

    // 检查插入是否成功
    if (!$result) {
        die("插入失敗：" . mysqli_error($link));
    }
}

// 關閉 MySQL 資料庫連結
mysqli_close($link);



?>
