<?php
// 建立 MySQL 資料庫連結
$link = mysqli_connect("localhost", "root", "") 
or die("無法開啟 MySQL 資料庫連結!<br>");
mysqli_select_db($link, "carbon_emissions");

// 設定 MySQL 查詢字串
$sql = "";

// 送出 UTF8 編碼的 MySQL 指令
mysqli_query($link, "SET NAMES utf8");

// 取得 POST 的 JSON 資料
$data = json_decode(file_get_contents('php://input'), true);

// 接收 AJAX 發送的數據並進行轉義，防止 SQL 注入
$start_date_time = mysqli_real_escape_string($link, $data['start_date_time']);
$end_date_time = mysqli_real_escape_string($link, $data['end_date_time']);
$total_time = mysqli_real_escape_string($link, $data['total_time']);
$distance = mysqli_real_escape_string($link, $data['distance']);
$car = mysqli_real_escape_string($link, $data['car']);
$type = mysqli_real_escape_string($link, $data['vehicleType']);
$employee_id = mysqli_real_escape_string($link, $data['employee_id']);

// 路徑資料可存儲為 JSON 檔案並存入
$path = json_encode($data['path']);
$filename = 'path_data_' . time() . '.json';
file_put_contents('path_files/' . $filename, $path);

$ppp = "555";

// 插入資料到 route_tracker 表
$sql = "INSERT INTO route_tracker(start_date_time, end_date_time, total_time, distance, file, car, type, employee_id) 
        VALUES ('$start_date_time', '$end_date_time', '$total_time', '$distance', '$ppp', '$car', '$type', '$employee_id')";

// 預處理 SQL
// $stmt = mysqli_prepare($link, $sql);
// mysqli_stmt_bind_param($stmt, 'sssssssi', $start_date_time, $start_date_time, $total_time, $distance, $file, $car, $type, $employee_id);

// 执行 SQL 语句
$result = mysqli_query($link, $sql);

// 執行 SQL
if ($result) {
    // echo '資料已成功存入資料庫。';
    echo '<script>console.log("資料已成功存入資料庫。");</script>';
} else {
    echo '錯誤: ' . mysqli_error($link);
}

mysqli_close($link);
?>