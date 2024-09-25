<?php
session_start();
$em_id = $_SESSION['em_id']; // 從 Session 中取得員工 ID
include('dropdown_list/dbcontroller.php');

// Database connection
$link = mysqli_connect('localhost', 'root', '', 'carbon_emissions')
    or die("無法開啟 MySQL 資料庫連結!");
mysqli_set_charset($link, "utf8");

// 獲取今天的日期
$today = new DateTime();

// 計算當週的星期一和星期日
$monday = clone $today;
$monday->modify('this week Monday');
$sunday = clone $today;
$sunday->modify('this week Sunday');

// 將日期格式化為 YYYY-MM-DD
$startDate = $monday->format('Y-m-d');
$endDate = $sunday->format('Y-m-d');

// 檢查是否有進階查詢的日期篩選
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];
}

// 用於查詢的 SQL 語句
$sql = "SELECT em_co2.*, em_address.ea_name
        FROM em_co2
        JOIN em_address ON em_co2.ea_id = em_address.ea_id
        WHERE em_co2.em_id = $em_id
        AND eCO2_date BETWEEN '$startDate' AND '$endDate'
        ORDER BY eCO2_date DESC";

$result = mysqli_query($link, $sql);

$attendanceRecords = [];
while ($rows = mysqli_fetch_array($result)) {
    $attendanceRecords[] = $rows;
}

mysqli_close($link);

// 返回 JSON 格式的數據
header('Content-Type: application/json');
echo json_encode($attendanceRecords);
?>
