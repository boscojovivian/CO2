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
        <title>加油紀錄</title>
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
                        <span>加油紀錄</span>
                    </div>
                </caption>               
                <thead class="table">
                    <tr>
                        <th>公司車名稱</th>
                        <th>加油日期</th>
                        <th>汽油種類</th>
                        <th>油量(公升)</th>
                        <th>價格</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        include_once("dropdown_list/dbcontroller.php");
                        $dbController = new DBController();
                        $query = "SELECT a.oil_date, a.type, a.liter, a.price, b.cc_name
                            FROM cm_car_oil AS a
                            JOIN cm_car AS b ON a.car_id = b.cc_id;";
                        $result = $dbController->runQuery($query);

                        if ($result) {
                            foreach ($result as $row) {
                                echo "<tr>";
                                echo "<td>".$row["cc_name"]."</td>";
                                echo "<td>".$row["oil_date"]."</td>";
                                echo "<td>".$row["type"]."</td>";
                                echo "<td>".$row["liter"]."</td>";
                                echo "<td>".$row["price"]."</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>0 results</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
            
        </div>
    </body>
</html>