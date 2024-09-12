<?php
session_start();

$link = mysqli_connect("localhost", "root", "A12345678") or die(json_encode(array('status' => 'error', 'message' => '無法開啟 MySQL 資料庫連結!')));
mysqli_select_db($link, "carbon_emissions");

$eCO2_id = $_SESSION['edit_CO2_id'];

if (isset($eCO2_id)) {
    $sql = "DELETE FROM em_co2 WHERE eCO2_id = " . intval($eCO2_id);
    mysqli_query($link, "SET NAMES utf8");
    
    if (mysqli_query($link, $sql)) {
        echo json_encode(array('status' => 'success', 'message' => '地址已成功刪除'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => '刪除地址時出錯: ' . mysqli_error($link)));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => '無效的紀錄ID'));
}

mysqli_close($link);
?>
