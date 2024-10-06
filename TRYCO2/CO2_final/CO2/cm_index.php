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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include('nav/cm_nav.php') ?>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-6 mb-4" style="margin-top: 5%;">
                    <div class="row">
                        <div class="col-12">
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
                        <div class="col-12">
                            <div class="button-container d-flex justify-content-center mt-3">
                                <a href="cm_c_co2.php"><button class="search-history-button1 me-3">交通車碳排紀錄</button></a>
                                <a href="cm_e_co2.php"><button class="search-history-button2">員工碳排紀錄</button></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6" style="margin-top: 5%;">
                    <div class="row">
                        <div class="col-12">
                            <div class="total_carbon text-center">
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
                                        echo "<span class='total_carbon_text2 counter'>" . $total_carbon ."</span>";
                                        echo "<span class='total_carbon_text'>&nbsp公噸</sapan>";
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="col-12" style="margin-top: 5%;">
                            <div class="calculator row">
                                <div class="col-12 mb-3">
                                    <h4 class="small-title-bold-bar">碳排換算價格</h4>
                                </div>
                                <div class="col-12 mb-4 row">
                                    <i class="text">碳費公式=(總排放量-免徵額2.5萬公噸二氧化碳)×費率×碳洩漏風險係數值</i>
                                </div>
                                <form id="carbonCalculator" class="col-12 ">
                                    <div class="mb-3">
                                        <label for="emission" class="form-label">排放量（噸）</label>
                                        <input type="number" class="form-control" id="emission" name="emission" placeholder="請輸入排放量（噸）" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="rate" class="form-label">收費費率（每噸收費）</label>
                                        <input type="number" class="form-control" id="rate" name="rate" placeholder="請輸入收費費率（每噸）" value="500" required>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-lg" id="btn-submit">計算</button>
                                </form>
                                <div class="col-12 mt-3">
                                    <div id="result" class="alert alert-primary alert-custom" style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>   
        </div>

        <script>
            // 公司總碳排動畫
            $(document).ready(function () {
                $(".counter").counterUp({
                    delay: 10,
                    time: 1200
                });
            });

            // 碳排換算價格
            document.getElementById('carbonCalculator').addEventListener("submit", function (event) {
                event.preventDefault(); // 阻止表單默認提交行為

                var emission = parseFloat(document.getElementById('emission').value); // 獲取排放量
                var rate = parseFloat(document.getElementById('rate').value); // 獲取費率
                var deduction = 25000; // 2.5萬公噸的二氧化碳
                var carbon = 1; // 碳洩漏風險係數值

                if(isNaN(emission) || isNaN(rate)) {
                    alert("請輸入有效的排放量和收費費率！");
                    return;
                }

                // 計算應繳碳費
                var fee = (emission - deduction) * rate * carbon;
                fee = fee > 0 ? fee : 0; // 費用不能為負

                // 顯示結果
                var result = document.getElementById('result');
                result.style.display = "block";
                result.textContent = "碳費應繳費額為：" + fee.toFixed(2) + "元"; // 取小數點第二位
            });
        </script>
    </body>   
</html>
