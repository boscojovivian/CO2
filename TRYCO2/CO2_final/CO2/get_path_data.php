<?php
$host = 'localhost';
$db = 'carbon_emissions';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 獲取最新的路徑資料
$sql = "SELECT path_data FROM paths ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode([]);
}

$conn->close();
?>
