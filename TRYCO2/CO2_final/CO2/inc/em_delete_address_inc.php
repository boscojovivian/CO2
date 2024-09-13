<?php
session_start();

// // 檢查用戶是否已登入
// if (!isset($_SESSION['em_id'])) {
//     // 如果未登入，重定向到登入頁面
//     header("Location: Sign_in.php");
//     exit();
// }

// // 檢查editAddress_id是否存在
// if (!isset($_SESSION['editAddress_id'])) {
//     echo json_encode(array('status' => 'error', 'message' => '無效的地址ID'));
//     exit();
// }

$link = mysqli_connect("localhost", "root", "A12345678") 
    or die(json_encode(array('status' => 'error', 'message' => '無法開啟 MySQL 資料庫連結!')));
mysqli_select_db($link, "carbon_emissions");

$ea_id = $_SESSION['editAddress_id'];

$sql = "DELETE FROM em_address WHERE ea_id = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $ea_id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo json_encode(array('status' => 'success', 'message' => '地址已成功刪除'));
} else {
    echo json_encode(array('status' => 'error', 'message' => '刪除地址時出錯: ' . mysqli_error($link)));
}

mysqli_stmt_close($stmt);
mysqli_close($link);
?>
