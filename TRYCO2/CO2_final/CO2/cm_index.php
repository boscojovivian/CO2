<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>管理者首頁</title>
        <meta charset="utf-8"></meta>
        <link rel="stylesheet" href="./css/cm_index.css" type="text/css">
        <link rel="shortcut icon" href="img\logo.png" >
        <script src="js.js"></script>    
        <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include('nav/cm_nav.php') ?>
        <div class="row">
            <div class="col-xl-10 right">
                <!-- 圓餅圖 -->
                <div id="pieChart" ></div>
                <?php
                require_once("dropdown_list/dbcontroller.php");
                $db_handle = new DBController();

                // 查詢圓餅圖數據
                $pieDataQuery = "SELECT category, total_carbon FROM total_carbon";
                $pieDataResult = $db_handle->runQuery($pieDataQuery);
                if ($pieDataResult === false) {
                    die("Query Failed: " . mysqli_error($db_handle->connectDB()));
                }
                $pieData = array();
                if (!empty($pieDataResult)) {
                    foreach ($pieDataResult as $row) {
                        $pieData[$row['category']] = $row['total_carbon'];
                    }
                }
                ?>
                <script>
                    // 圓餅圖數據
                    var pieData = [{
                        values: <?php echo json_encode(array_values($pieData)); ?>,
                        labels: <?php echo json_encode(array_keys($pieData)); ?>.map(value => value + ' kg'), // 在標籤後面加上 ' kg'
                        type: 'pie',
                        marker: {
                            colors: ["#8cb4bf", "#cbb48e"]
                        }
                    }];
                    var layout = {
                        title: '碳排放量比例',
                        //paper_bgcolor: '#e2f7ea', // 整個圖表區域的背景顏色
                        plot_bgcolor: '#e2f7ea',  // 繪圖區域的背景顏色
                    };

                    // 渲染圓餅圖
                    Plotly.newPlot('pieChart', pieData, layout, {
                        responsive: true,
                        title: '碳排放量比例'
                    });
                </script>
            </div>
            <div class="col-xl-2 left">
                <a href="cm_c_co2.php"><button class="search-history-button1">交通車碳排紀錄</button></a><br><br>
                <a href="cm_e_co2.php"><button class="search-history-button2" style="">員工碳排紀錄</button></a>
            </div>
            <div class="total_carbon">
                <?php
                    $link = mysqli_connect("localhost", "root", "", "carbon_emissions")
                        or die("無法開啟 MySQL 資料庫連結!<br>");
                    mysqli_set_charset($link, "utf8");

                    $sql = "SELECT SUM(total_carbon) AS total_carbon
                            FROM total_carbon";

                    mysqli_query($link, "SET NAMES utf8");
                    $result = mysqli_query($link, $sql);

                    while ($rows = mysqli_fetch_array($result)){
                        $total = $rows["total_carbon"];
                        $total_carbon = sprintf("%.2f", round($total,2));
                        echo "<span class='total_carbon_text'>公司總碳排&nbsp:&nbsp</span>";
                        echo "<span class='total_carbon_text2'>" . $total_carbon ."</span>";
                        echo "<span class='total_carbon_text'>&nbspkg</sapan>";
                    }
                ?>
            </div>  
        </div>      
    </body>   
</html>
