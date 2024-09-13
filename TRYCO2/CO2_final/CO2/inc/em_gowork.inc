<?php

// 建立 MySQL 資料庫連結
$link = mysqli_connect("localhost", "root", "A12345678") 
or die("無法開啟 MySQL 資料庫連結!<br>");
mysqli_select_db($link, "carbon_emissions"); // 選擇 feedback 資料庫

// 送出 UTF8 編碼的 MySQL 指令
mysqli_query($link, "SET NAMES utf8");

// 获取表单数据
$gowork_date = $_POST['dateStr'];
// $gowork_date = date("Y-m-d", strtotime($gowork_date));  // 将日期转换为YYYY-MM-DD格式
// $gowork_date = date("Y-m-d");
$gowork_id_address = $_POST['gowork_address'];
$gowork_address_arr = explode(":", $gowork_id_address);
$gowork_address_id = $gowork_address_arr[0];
$gowork_address = $gowork_address_arr[1];


$em_id = $_SESSION['em_id'];
// $gowork_address_id = $_POST['gowork_address_name'];

echo "<script>console.log('gowork_id_address:" . $gowork_id_address . "');</script>";
// echo "<script>console.log('gowork_address_arr:" . $gowork_address_arr . "');</script>";
echo "<script>console.log('gowork_address_id:" . $gowork_address_id . "');</script>";
echo "<script>console.log('gowork_address:" . $gowork_address . "');</script>";
echo "<script>console.log('gowork_date:" . $gowork_date . "');</script>";



$go_checked = isset($_POST['go']) ? true : false;
$back_checked = isset($_POST['back']) ? true : false;
$go = $go_checked ? $_POST['go'] : null;
$back = $back_checked ? $_POST['back'] : null;


$em_id = $_SESSION['em_id'];
$em_name_sql = "SELECT em_name FROM employee WHERE em_id = " . $em_id;
$em_name_result = mysqli_query($link, $em_name_sql);
$em_name = mysqli_fetch_assoc($em_name_result)['em_name'];

$gowork_car = $_POST['gowork_car'];
$gowork_km = floatval($_POST['gowork_km']);

echo "<script>console.log('" . $gowork_km . "');</script>";

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

echo "<script>console.log('" . $eCO2_carbon . "');</script>";

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ((!empty($go) || !empty($back)) && !empty($gowork_car)) {
        if (!empty($go) && !empty($gowork_car)) {

            $check = "SELECT * FROM em_co2 
                        WHERE eCO2_date = '$gowork_date' AND em_id = $em_id AND eCO2_commute = '$go'";

            if (mysqli_num_rows(mysqli_query($link, $check)) == 0) {
                $sql = "INSERT INTO em_co2 (eCO2_date, eCO2_commute, eCO2_carbon, em_id, em_name, ec_type, ea_id) 
                        VALUES ('$gowork_date', '$go', '$eCO2_carbon', '$em_id', '$em_name', '$gowork_car', '$gowork_address_id')";
                // $message = "通勤記錄新增成功!";
            }
            else {
                $sql = "UPDATE em_co2 
                        SET eCO2_carbon = '$eCO2_carbon', 
                            em_name = '$em_name', 
                            ec_type = '$gowork_car', 
                            ea_id = '$gowork_address_id' 
                        WHERE em_id = '$em_id' AND eCO2_date = '$gowork_date' AND eCO2_commute = '$go'";
                // $message = "通勤記錄修改成功!";
            }
            $result = mysqli_query($link, $sql);
            if (!$result) {
                die("插入失敗：" . mysqli_error($link));
            }
        }
        if (!empty($back) && !empty($gowork_car)) {

            $check = "SELECT * FROM em_co2 
                        WHERE eCO2_date = '$gowork_date' AND em_id = $em_id AND eCO2_commute = '$go'";

            if (mysqli_num_rows(mysqli_query($link, $check)) == 0) {
                $sql = "INSERT INTO em_co2 (eCO2_date, eCO2_commute, eCO2_carbon, em_id, em_name, ec_type, ea_id) 
                    VALUES ('$gowork_date', '$back', '$eCO2_carbon', '$em_id', '$em_name', '$gowork_car', '$gowork_address_id')";
            }
            else {
                $sql = "UPDATE em_co2 
                        SET eCO2_carbon = '$eCO2_carbon', 
                            em_name = '$em_name', 
                            ec_type = '$gowork_car', 
                            ea_id = '$gowork_address_id' 
                        WHERE em_id = '$em_id' AND eCO2_date = '$gowork_date' AND eCO2_commute = '$back'";
            }
            $result = mysqli_query($link, $sql);
            if (!$result) {
                die("插入失敗：" . mysqli_error($link));
            }
        }
        echo "<script>showAlert_success('通勤記錄新增成功!');</script>";
        // echo "<script>alert('通勤記錄新增成功!');</script>";
        // echo "<script>window.location.href = 'em_index.php';</script>";
    }
    else{
        echo "<script>showAlert_fail('有選項尚未選擇!');</script>";
        // echo "<script>alert('有選項尚未選擇');</script>";
    } 
}
else{
    echo "<script>console.log('表單未提交成功');</script>";
}

// 關閉 MySQL 資料庫連結
mysqli_close($link);
