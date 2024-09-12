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
$gowork_id_address = $_POST['edit_gowork_address_name'];
$gowork_address_arr = explode(":", $gowork_id_address);
$gowork_address_id = $gowork_address_arr[0];
$gowork_address = $gowork_address_arr[1];

$gowork_car = $_POST['gowork_car'];
$gowork_km = floatval($_POST['gowork_km']);
$eCO2_id = $_SESSION['edit_CO2_id'];

if ($gowork_car == 'bicycle'){
    $eCO2_carbon = $gowork_km * 0.08;
}
elseif ($gowork_car == 'car'){
    $eCO2_carbon = $gowork_km * 0.15;
}
elseif ($gowork_car == 'public'){
    $eCO2_carbon = 0;
}
else {
    echo "<script>console.log('碳排計算錯誤');</script>";
    // echo "<script>alert('查無交通車形式');</script>";
}

$eCO2_carbon_2 = sprintf("%.2f", round($eCO2_carbon,2));

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 构建插入数据的 SQL 语句
    $sql = "UPDATE em_co2 
            SET ea_id = '$gowork_address_id', 
                ec_type = '$gowork_car', 
                eCO2_carbon = '$eCO2_carbon_2'
            WHERE eCO2_id = " . intval($eCO2_id);

    // 执行 SQL 语句
    $result = mysqli_query($link, $sql);

    // echo "<script>alert('地址修改成功');</script>";
    // echo "<script>showAlert_edit_address();</script>";
    // echo "<script>window.location.href = 'em_index.php';</script>";
    echo "<script>showAlert_edit_CO2('紀錄修改成功!');</script>";

    // 检查插入是否成功
    if (!$result) {
        die("插入失敗：" . mysqli_error($link));
    }
}
else{
    echo "<script>showAlert_edit_CO2('紀錄修改失敗');</script>";
}   

// 關閉 MySQL 資料庫連結
mysqli_close($link);



