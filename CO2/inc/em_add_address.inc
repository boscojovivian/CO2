<?php

// 建立 MySQL 資料庫連結
$link = mysqli_connect("localhost", "root", "A12345678") 
or die("無法開啟 MySQL 資料庫連結!<br>");
mysqli_select_db($link, "carbon_emissions"); // 選擇 feedback 資料庫

// 設定 MySQL 查詢字串
$sql = "";

// 送出 UTF8 編碼的 MySQL 指令
mysqli_query($link, "SET NAMES utf8");

// 获取表单数据
$address_name = $_POST['address_name'];
$city = $_POST['city'];
$area = $_POST['area'];
$address_detail = $_POST['address_detail'];
$emp_id = $_SESSION['em_id'];

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //檢查帳號是否重複
    $check = "SELECT * 
            FROM em_address 
            WHERE ea_name='" . $address_name . "' AND em_id=" . $emp_id;

    if (mysqli_num_rows(mysqli_query($link, $check)) == 0) {
        // 构建插入数据的 SQL 语句
        $sql = "INSERT INTO em_address (ea_name, ea_address_city, ea_address_area, ea_address_detial, em_id) 
                VALUES ('$address_name', '$city', '$area', '$address_detail', '$emp_id')";

        // 执行 SQL 语句
        $result = mysqli_query($link, $sql);

        echo "<script>alert('地址新增成功');</script>";

        // 检查插入是否成功
        if (!$result) {
            die("插入失敗：" . mysqli_error($link));
        }
    }
    else{
        echo "<script>alert('該地址代名已用過');</script>";
    }   
}

// 關閉 MySQL 資料庫連結
mysqli_close($link);



?>
