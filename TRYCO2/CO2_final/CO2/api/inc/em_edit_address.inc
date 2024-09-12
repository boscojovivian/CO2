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
$editAddress_id = $_SESSION['editAddress_id'];
$isDefault = mysqli_real_escape_string($link, $_POST['default_checkbox']); // 获取复选框值

if($isDefault == 1){
    $isDefault_sql = "SELECT * FROM em_address WHERE ea_default = '$isDefault' AND ea_id = $editAddress_id";
    $isDefault_result = mysqli_query($link, $isDefault_sql);

    if (mysqli_num_rows($isDefault_result) == 0) {
        $ea_default = $isDefault;
    }else{
        $chang_Default_sql = "UPDATE em_address
                            SET ea_default = 0
                            WHERE ea_id = " . $editAddress_id . " AND ea_default = 1";
        mysqli_query($link, $chang_Default_sql);
        $ea_default = $isDefault;
    }  
}else{
    $ea_default = 0;
}

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 构建插入数据的 SQL 语句
    $sql = "UPDATE em_address 
            SET ea_name = '$address_name', 
                ea_address_city = '$city', 
                ea_address_area = '$area', 
                ea_address_detial = '$address_detail',
                ea_default = '$ea_default'
            WHERE ea_id = " . intval($editAddress_id);

    // 执行 SQL 语句
    $result = mysqli_query($link, $sql);

    // echo "<script>alert('地址修改成功');</script>";
    // echo "<script>showAlert_edit_address();</script>";
    // echo "<script>window.location.href = 'em_index.php';</script>";
    echo "<script>showAlert_edit_CO2('地址修改成功!');</script>";

    // 检查插入是否成功
    if (!$result) {
        die("插入失敗：" . mysqli_error($link));
    }
}
else{
    echo "<script>showAlert_edit_CO2('地址修改失敗');</script>";
}   

// 關閉 MySQL 資料庫連結
mysqli_close($link);



