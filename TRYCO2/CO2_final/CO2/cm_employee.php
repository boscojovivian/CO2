<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>管理者資料</title>
        <link rel="stylesheet" href="css/cm_employee.css" type="text/css">
        <link rel="shortcut icon" href="img/logo.png" >
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    </head>
    <body>
        <!-- 導入導覽列 -->
        <?php include('nav/cm_nav.php') ?>

        <div class="container">
            <!-- 表格 -->
            <table class="table table-hover caption-top">
                <caption>管理者資料</caption>
                <thead class="table">
                    <tr>
                        <th>管理者編號</th>
                        <th>姓名</th>
                        <th>電子信箱</th>
                        <th colspan="2">設定管理者狀態</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        include_once("dropdown_list/dbcontroller.php");
                        $dbController = new DBController();
                        $query = "SELECT em_id, em_name, em_email, flag FROM employee";
                        $result = $dbController->runQuery($query);

                        if ($result) {
                            foreach ($result as $row) {
                                echo "<tr>";
                                echo "<td>".$row["em_id"]."</td>";
                                echo "<td>".$row["em_name"]."</td>";
                                echo "<td>".$row["em_email"]."</td>";
                                if($row["em_id"] == $_SESSION["em_id"]){
                                    echo "<td>" . ($row['flag'] == 1 ? "管理員" : "員工") . "</td>";
                                }else{
                                    if($row['flag'] == 1){
                                        echo "<td>
                                            <select class='form-select status-select' data-id='".$row["em_id"]."'>
                                                <option value='設定' selected='selected'>管理者</option>
                                                <option value='解除'>員工</option>
                                            </select>
                                        </td>";
                                    }else{
                                        echo "<td>
                                            <select class='form-select status-select' data-id='".$row["em_id"]."'>
                                                <option value='設定'>管理者</option>
                                                <option value='解除' selected='selected'>員工</option>
                                            </select>
                                        </td>";
                                    }
                                } 
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>0 results</td></tr>";
                        }
                    ?>
                </tbody>
            </table>

            <!-- 隱藏的按鈕 -->
            <div id="save-button-container" style="display:none;">
                <button id="save-button" class="btn btn-primary">儲存變更</button>
            </div>
        </div>

        <script>
            // 儲存變更的數據
            var changes = [];

            document.querySelectorAll('.status-select').forEach(function(selectElement) {
                selectElement.addEventListener('change', function() {
                    // 顯示儲存按鈕
                    document.getElementById('save-button-container').style.display = 'block';

                    // 紀錄變更
                    var userId = this.getAttribute('data-id');
                    var newStatus = this.value === '設定' ? 1 : 0;

                    // 更新變更的數據
                    var existingChange = changes.find(change => change.id === userId);
                    if (existingChange) {
                        existingChange.status = newStatus;
                    } else {
                        changes.push({ id: userId, status: newStatus });
                    }
                });
            });

            // 當按下儲存按鈕
            document.getElementById('save-button').addEventListener('click', function() {
                // 發送變更到後端處理
                fetch('update_user_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(changes)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('權限已成功更新');
                        // 隱藏儲存按鈕
                        document.getElementById('save-button-container').style.display = 'none';
                        changes = [];  // 清空變更紀錄
                        // 根據資料是否更新當前使用者 session，重新載入頁面
                        if (data.updateSession) {
                            window.location.reload();  // 更新後重新載入頁面
                        }
                    } else {
                        alert('更新失敗');
                    }
                })
                .catch(error => {
                    console.error('發生錯誤:', error);
                });
            });
        </script>



        

        

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    </body>
</html>