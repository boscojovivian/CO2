<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}
function translateCarType($type) {
    switch ($type) {
        case 'motorcycle':
            return '機車';
        case 'car':
            return '汽車';
        case 'truck':
            return '卡車';
        default:
            return '未知';
    }
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>管理交通車</title>
        <link rel="stylesheet" href="./css/cm_manage_car.css" type="text/css">
        <link rel="shortcut icon" href="img/logo.png" >
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    </head>
    <body>
        <!-- 導入導覽列 -->
        <?php include('nav/cm_nav.php') ?>

        <div class="container">
            <!-- 表格 -->
            <table class="table table-hover caption-top">
                <caption>
                    <div class="caption-container">
                    <span>管理交通車</span>
                    <a href="cm_add_car.php"><button class="commute">新增交通車</button></a>
                    </div>
                </caption>               
                <thead class="table">
                    <tr>
                        <th>交通車編號</th>
                        <th>交通車名稱</th>
                        <th>交通車類型</th>
                        <th>編輯交通車</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        include_once("dropdown_list/dbcontroller.php");
                        $dbController = new DBController();
                        $query = "SELECT * FROM cm_car";
                        $result = $dbController->runQuery($query);

                        if ($result) {
                            foreach ($result as $row) {
                                echo "<tr>";
                                echo "<td>".$row["cc_id"]."</td>";
                                echo "<td>".$row["cc_name"]."</td>";
                                echo "<td>".translateCarType($row["cc_type"])."</td>";
                                echo "<td>";
                                echo "<form action='cm_edit_car.php' method='GET'>";
                                echo "<input type='hidden' name='cc_id' value='".$row["cc_id"]."'>";
                                echo "<button type='submit' class='edit-button' name='edit_button'>編輯</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>0 results</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
            
        </div>

        

        

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
        <script>
            function deleteCar(cc_id) {
                if (confirm('確定要刪除這輛交通車嗎？')) {
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            var response = JSON.parse(this.responseText);
                            alert(response.message);
                            if (response.status === 'success') {
                                // 刪除成功後重新導向到 cm_manage_car.php
                                window.location.href = 'cm_manage_car.php';
                            }
                        }
                    };
                    xhttp.open("POST", "delete_car.php", true);
                    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhttp.send("cc_id=" + cc_id);
                }
            }

        </script>
    </body>
</html>