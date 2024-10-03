<?php
// 資料庫連接
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carbon_emissions";

$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 確認是否收到 AJAX 請求的 'mode' 參數
if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];

    // 根據選擇的模式查詢對應的資料
    if ($mode == 'is_cm_car') {
        $sql = 'SELECT cc_id AS id, cc_name AS name, cc_type FROM cm_car';
    } elseif ($mode == 'not_cm_car') {
        $sql = 'SELECT id AS id, type AS name, num FROM transportation';
    }

    // 執行查詢並生成選項
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo '<option value="">請選擇車輛</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
        }
    } else {
        echo '<option value="">沒有可用的車輛</option>';
    }
}

// 關閉資料庫連接
$conn->close();
?>
