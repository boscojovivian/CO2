<?php
// 建立 MySQL 資料庫連結
$link = mysqli_connect("localhost", "root", "") 
or die("無法開啟 MySQL 資料庫連結!<br>");
mysqli_select_db($link, "carbon_emissions");

// 送出 UTF8 編碼的 MySQL 指令
mysqli_query($link, "SET NAMES utf8");

// 取得 POST 的 JSON 資料
$data = json_decode(file_get_contents('php://input'), true);

// 檢查是否成功解析 JSON
if (!$data) {
    die("無效的 JSON 資料");
}

// 接收 AJAX 傳送的數據並防止 SQL 注入
$start_date = mysqli_real_escape_string($link, $data['start_date']);
$start_time = mysqli_real_escape_string($link, $data['start_time']);
$end_date = mysqli_real_escape_string($link, $data['end_date']);
$end_time = mysqli_real_escape_string($link, $data['end_time']);
$total_time = mysqli_real_escape_string($link, $data['total_time']);
$distance = mysqli_real_escape_string($link, $data['distance']);
$car = mysqli_real_escape_string($link, $data['car']);
$type = mysqli_real_escape_string($link, $data['vehicleType']);
$employee_id = mysqli_real_escape_string($link, $data['employee_id']);

// 檢查並創建 path_files 目錄，若不存在則創建
$path_dir = 'path_files';
if (!is_dir($path_dir)) {
    if (!mkdir($path_dir, 0777, true)) {
        die('無法創建目錄');
    }
}

// 儲存路徑資料為基本數組格式的 JSON 檔案
$path_data = json_encode($data['path'], JSON_PRETTY_PRINT); // JSON_PRETTY_PRINT 用來讓 JSON 格式更加美觀
$filename = 'path_data_' . time() . '.json';
$file_path = $path_dir . '/' . $filename;

if (file_put_contents($file_path, $path_data) === false) {
    die('無法儲存 JSON 路徑檔案');
}

// 使用 Prepared Statement 防止 SQL 注入
$sql = "INSERT INTO route_tracker (start_date, start_time, end_date, end_time, total_time, distance, file, car, type, employee_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($link, $sql);

// 綁定參數到 Prepared Statement
mysqli_stmt_bind_param($stmt, 'sssssdssss', 
    $start_date, 
    $start_time, 
    $end_date, 
    $end_time, 
    $total_time, 
    $distance, 
    $filename, 
    $car, 
    $type, 
    $employee_id
);

// 執行 Prepared Statement
if (mysqli_stmt_execute($stmt)) {
    echo '<script>console.log("資料已成功存入資料庫。");</script>';
} else {
    echo '錯誤: ' . mysqli_error($link);
}

// 關閉 Prepared Statement 和資料庫連線
mysqli_stmt_close($stmt);
mysqli_close($link);
?>
