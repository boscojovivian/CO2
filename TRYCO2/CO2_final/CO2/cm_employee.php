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
        <title>員工資料</title>
        <link rel="stylesheet" href="./css/employee.css" type="text/css">
        <link rel="shortcut icon" href="img/logo.png" >
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    </head>
    <body>
        <!-- 導入導覽列 -->
        <?php include('nav.php') ?>

        <div class="container">
            <!-- 表格 -->
            <table class="table table-hover caption-top">
                <caption>員工資料</caption>
                <thead class="table">
                    <tr class="active">
                        <th>員工編號</th>
                        <th>姓名</th>
                        <th>電子信箱</th>
                        <th>管理員狀態</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        include_once("dbcontroller.php");
                        $dbController = new DBController();
                        $query = "SELECT em_id, em_name, em_email, flag FROM employee";
                        $result = $dbController->runQuery($query);

                        if ($result) {
                            foreach ($result as $row) {
                                echo "<tr>";
                                echo "<td>".$row["em_id"]."</td>";
                                echo "<td>".$row["em_name"]."</td>";
                                echo "<td>".$row["em_email"]."</td>";
                                echo "<td>";
                                
                                if ($row["em_id"] != 1) {
                                    if ($row["flag"] == 0) {
                                        echo "<button type='button' class='add-button btn btn-success' onclick='setAdmin(".$row["em_id"].")'>新增</button>";
                                    } elseif ($row["flag"] == 1) {
                                        echo "<button type='button' class='add-button btn btn-danger' onclick='deleteAdmin(".$row["em_id"].")'>刪除</button>";
                                    }
                                }
                                
                                echo "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>0 results</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>

        

        

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    </body>
</html>