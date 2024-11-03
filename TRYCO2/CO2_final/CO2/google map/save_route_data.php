<?php
include_once("dropdown_list/dbcontroller.php");
$db_handle = new DBController();

// 取得 POST 的 JSON 資料
$data = json_decode(file_get_contents('php://input'), true);

// 檢查是否成功解析 JSON
if (!$data) {
    die("無效的 JSON 資料");
}

// 接收 AJAX 傳送的數據並防止 SQL 注入
$start_date = mysqli_real_escape_string($db_handle->conn, $data['start_date']);
$start_time = mysqli_real_escape_string($db_handle->conn, $data['start_time']);
$end_date = mysqli_real_escape_string($db_handle->conn, $data['end_date']);
$end_time = mysqli_real_escape_string($db_handle->conn, $data['end_time']);
$total_time = mysqli_real_escape_string($db_handle->conn, $data['total_time']);
$distance = mysqli_real_escape_string($db_handle->conn, $data['distance']);
$car = mysqli_real_escape_string($db_handle->conn, $data['car']);
$type = mysqli_real_escape_string($db_handle->conn, $data['vehicleType']);
$employee_id = mysqli_real_escape_string($db_handle->conn, $data['employee_id']);

// 檢查並創建 path_files 目錄，若不存在則創建
$path_dir = 'path_files';
if (!is_dir($path_dir)) {
    if (!mkdir($path_dir, 0777, true)) {
        die('無法創建目錄');
    }
}

// 儲存路徑資料為包含經緯度座標物件格式的 JSON 檔案
$path_data = json_encode($data['path'], JSON_PRETTY_PRINT);
$filename = 'path_data_' . time() . '.json';
$file_path = $path_dir . '/' . $filename;

if (file_put_contents($file_path, $path_data) === false) {
    die('無法儲存 JSON 路徑檔案');
}

// 準備要插入的資料
$insertData = [
    'start_date' => $start_date,
    'start_time' => $start_time,
    'end_date' => $end_date,
    'end_time' => $end_time,
    'total_time' => $total_time,
    'distance' => $distance,
    'file' => $filename,
    'car' => $car,
    'type' => $type,
    'employee_id' => $employee_id
];

// 插入資料並取得最後插入的 ID
if ($db_handle->insert('route_tracker', $insertData)) {
    $last_id = $db_handle->getLastInsertId();
    echo '<script>console.log("資料已成功存入資料庫。");</script>';

    // 將剛新增的 ID 傳遞給 count_carbon.php
    $carbon_type = 3;
    $type_id = $last_id; // 設定變數以供 count_carbon.php 使用
    include_once("../count_carbon/count_carbon.php");
} else {
    echo '錯誤: 無法將資料插入資料庫';
}

// 關閉資料庫連線
$db_handle->close();
?>
