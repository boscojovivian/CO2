<?php
session_start();
include_once("dbcontroller.php");
$dbController = new DBController();

if (isset($_POST['cc_id'])) {
    $cc_id = $_POST['cc_id'];
    $delete_query = "DELETE FROM cm_car WHERE cc_id = '$cc_id'";
    $delete_result = $dbController->executeUpdate($delete_query);
    if ($delete_result) {
        echo json_encode(['status' => 'success', 'message' => '刪除成功！']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '刪除失敗！']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '無效的交通車編號！']);
}
?>