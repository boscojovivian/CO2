<?php
session_start();  // 啟動 session
include_once("dropdown_list/dbcontroller.php");
$dbController = new DBController();

// 獲取前端傳來的數據
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    $updateSession = false;  // 用來檢查是否需要更新 session
    foreach ($data as $change) {
        $userId = $change['id'];
        $newStatus = $change['status'];

        // 使用 update 方法更新資料庫中的 flag 欄位
        $dbController->updateUserPermission('employee', ['flag' => $newStatus], ['em_id' => $userId]);

        // 如果更新的是當前登入的用戶，需要同步更新 session
        if ($_SESSION['em_id'] == $userId) {
            $_SESSION['flag'] = $newStatus;
            $updateSession = true;  // 標記為需要更新 session
        }
    }

    // 回傳成功訊息，並告知是否需要重新載入頁面
    echo json_encode(['success' => true, 'updateSession' => $updateSession]);
    
} else {
    echo json_encode(['success' => false]);
}
?>
