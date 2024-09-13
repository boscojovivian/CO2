<?php
// session_start();
// include_once("dbcontroller.php");
// $dbController = new DBController();

// if (isset($_POST['cc_id'])) {
//     $cc_id = $_POST['cc_id'];
//     $delete_query = "DELETE FROM cm_car WHERE cc_id = '$cc_id'";
//     $delete_result = $dbController->executeUpdate($delete_query);
//     if ($delete_result) {
//         echo json_encode(['status' => 'success', 'message' => '刪除成功！']);
//     } else {
//         echo json_encode(['status' => 'error', 'message' => '刪除失敗！']);
//     }
// } else {
//     echo json_encode(['status' => 'error', 'message' => '無效的交通車編號！']);
// }
?>

<?php
session_start();
include_once("dbcontroller.php");
$dbController = new DBController();

if (isset($_POST['cc_id'])) {
    $cc_id = $_POST['cc_id'];

    // 使用准备语句防止SQL注入
    $delete_query = "DELETE FROM cm_car WHERE cc_id = ?";
    $stmt = $dbController->conn->prepare($delete_query);
    $stmt->bind_param("i", $cc_id);

    if ($stmt->execute()) {
        $response = ['status' => 'success', 'message' => '刪除成功！'];
    } else {
        $response = ['status' => 'error', 'message' => '刪除失敗！'];
    }
    $stmt->close();
} else {
    $response = ['status' => 'error', 'message' => '無效的交通車編號！'];
}

// 设置响应的内容类型为JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
