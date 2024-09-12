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
$transportMode = $_POST['transportMode'];
$transportName = $_POST['transportName'];
// $startDate = $_POST['startDate'];
// $startTime = $_POST['startTime'];
$chinese_address = $_POST['chinese_address'];
$total_km = $_POST['total_km'];
$total_hr = $_POST['total_hr'];
$total_min = $_POST['total_min'];

$emp_id = $_SESSION['em_id'];
$em_name_sql = "SELECT em_name FROM employee WHERE em_id =" . $emp_id;
$em_name_result = mysqli_query($link, $em_name_sql);
while ($em_name_rows = mysqli_fetch_array($em_name_result)){
    $em_name = $em_name_rows[0];
}


// 检查字段是否存在并获取它们的值
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
$startTime = isset($_POST['startTime']) ? $_POST['startTime'] : null;
// 如果缺少字段，处理错误
if ($startDate === null || $startTime === null) {
    echo "<script>showAlert_chack_km_time();</script>";
    // echo "<script>showAlert_fail('請輸入日期及時間');</script>";
    // echo "<script>alert('請輸入日期及時間');</script>";
}
$startDateTime = new DateTime("$startDate $startTime");  // 创建 DateTime 对象
$interval = new DateInterval("PT{$total_hr}H{$total_min}M");  // 创建一个 DateInterval 对象
$startDateTime->add($interval);  // 添加时间间隔
$endTime = $startDateTime->format("H:i");  // 获取结束时间
$endDateTime = $startDateTime->format("Y-m-d H:i");  // 获取结束日期和时间


// 搜尋資料庫資料
$cc_id_sql = "SELECT cc_id FROM cm_car WHERE cc_name = '$transportName'";
// 執行查詢 
$result = mysqli_query($link, $cc_id_sql);
if ($result) {
    // 從結果中取得一行作為關聯陣列
    $row = mysqli_fetch_assoc($result);
    // 提取出 cc_id
    $cc_id = $row['cc_id'];
    // 釋放結果物件
    mysqli_free_result($result);
} else {
    // 查詢失敗時的處理
    echo "查詢失敗: " . mysqli_error($link);
}


$transportMode_type_sql = "SELECT cc_type FROM cm_car WHERE cc_name = '$transportName'";
$transportMode_type_result = mysqli_query($link, $transportMode_type_sql);
if ($transportMode_type_result) {
    // 從結果中取得一行作為關聯陣列
    $row = mysqli_fetch_assoc($transportMode_type_result);
    // 提取出 cc_id
    $transportMode_type = $row['cc_type'];
    // 釋放結果物件
    mysqli_free_result($transportMode_type_result);
} else {
    // 查詢失敗時的處理
    echo "查詢失敗: " . mysqli_error($link);
}

if ($transportMode_type == 'bicycle'){
    $CO2_emissions = $total_km * 0.08;
}
elseif ($transportMode_type == 'car'){
    $CO2_emissions = $total_km * 0.15;
}
elseif ($transportMode_type == 'truck'){
    $CO2_emissions = $total_km * 0.525;
}
else {
    echo "<script>console.log('查無交通車形式');</script>";
    echo "<script>showAlert_fail('查無交通車形式');</script>";
    // echo "<script>alert('查無交通車形式');</script>";
}

$cCO2_carbon = sprintf("%.2f", round($CO2_emissions,2));


// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 构建插入数据的 SQL 语句
    $sql = "INSERT INTO cm_co2 (cCO2_date, cCO2_start_time, cCO2_end_time, cCO2_carbon, cCO2_address, cc_id, em_id, em_name) 
            VALUES ('$startDate', '$startTime', '$endTime', '$cCO2_carbon', '$chinese_address', '$cc_id', '$emp_id', '$em_name')";

    // 执行 SQL 语句
    $result = mysqli_query($link, $sql);

    // 检查插入是否成功
    if ($result) {
        echo "<script>showAlert_add_success();</script>";
        // echo "<script>showAlert_logo('出勤紀錄新增成功!');</script>";
        // echo "<script>alert('出勤紀錄新增成功');</script>";
    }
    else{
        echo "<script>showAlert_add_fail();</script>";
        // echo "<script>showAlert_fail('出勤紀錄新增失敗');</script>";
        // echo "<script>alert('出勤紀錄新增失敗');</script>";
        die("插入失敗：" . mysqli_error($link));
    }   
}

// 關閉 MySQL 資料庫連結
mysqli_close($link);
?>