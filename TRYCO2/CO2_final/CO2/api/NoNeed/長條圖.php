<!-- no -->

<?php
// 與資料庫建立連接
$servername = "localhost";
$username = "root";
$password = "A12345678";
$dbname = "carbon_emissions";

$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接是否成功
if ($conn->connect_error) {
    die("連接資料庫失敗: " . $conn->connect_error);
}

// 檢查是否從前端接收到日期篩選值
if (isset($_POST['selectedDate'])) {
    $selectedDate = $_POST['selectedDate'];

    // 查詢所選日期的碳排放數據
    $query = "SELECT SUM(cm.cCO2_carbon) AS car_carbon, SUM(em.eCO2_carbon) AS employee_carbon
              FROM cm_co2 cm
              INNER JOIN em_co2 em ON DATE(cm.cCO2_time) = DATE(em.eCO2_time)
              WHERE DATE(cm.cCO2_time) = '$selectedDate'";
    $result = $conn->query($query);

    // 將結果轉換為 JSON 格式並返回給前端
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    echo "未接收到日期篩選值";
}

// 關閉資料庫連接
$conn->close();
?>
