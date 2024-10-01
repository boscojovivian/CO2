<?php
// 連接資料庫
$link = mysqli_connect("localhost", "root", "", "carbon_emissions");

if (!$link) {
    die("無法連接到資料庫：" . mysqli_connect_error());
}

// 取得 POST 的 JSON 資料
$data = json_decode(file_get_contents('php://input'), true);

// 接收 AJAX 發送的數據
$start_date_time = $_POST['start_date_time'];
$end_date_time = $_POST['end_date_time'];
$total_time = $_POST['total_time'];
$distance = $_POST['distance'];
// $file = $_POST['path'];
$car = $_POST['car'];
$type = $_POST['vehicleType'];
$employee_id = $_POST['employee_id'];

// 路徑資料可存儲為 JSON 檔案並存入
$path = json_encode($_POST['path']);
$filename = 'path_data_' . time() . '.json';
file_put_contents('path_files/' . $filename, $path);

// 插入資料到 transportation 表
$sql = "INSERT INTO transportation (start_date_time, end_date_time, total_time, distance, file, car, type, employee_id) 
        VALUES ($start_date_time, $start_date_time, $total_time, $distance, $filename, $car, $type, $employee_id)";

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