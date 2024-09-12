<?php
include_once("dbcontroller.php");

// 檢查是否設定了 em_id
if(isset($_GET['em_id'])) {
    $em_id = $_GET['em_id'];

    // 建立資料庫連線
    $dbController = new DBController();
    $conn = $dbController->connectDB();

    // 檢查連線是否成功
    if (!$conn) {
        die("資料庫連線失敗：" . mysqli_connect_error());
    }

    // 準備更新 SQL 查詢語句（使用參數化查詢）
    $sql = "UPDATE employee SET flag = 1 WHERE em_id = ?";

    // 建立預備語句
    $stmt = $conn->prepare($sql);

    // 綁定參數
    $stmt->bind_param("i", $em_id);

    // 執行查詢
    if ($stmt->execute()) {
        echo "已成功添加為管理員";
    } else {
        echo "更新失敗：" . $conn->error;
    }

    // 關閉語句和連線
    $stmt->close();
    $conn->close();
} else {
    echo "未提供 em_id 參數";
}
?>
