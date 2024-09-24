<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}

include('dbcontroller.php');
$db_handle = new DBController();
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 設置網頁的字符集與 viewport，方便手機瀏覽 -->
    <title>個人首頁</title>
    <link rel="shortcut icon" href="img\logo.png">
    <!-- <link href="css.css" rel="stylesheet"> -->
    <link href="em_index.css" rel="stylesheet"> <!-- 引入外部 CSS 文件 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- 引入 Bootstrap 框架的 CSS 文件 -->
</head>

<body>
    <!-- 導入導覽列 -->
    <?php include('nav/em_nav.php') ?>

    <div class="custom-bg-position">
        <div class="custom-bg">
            <div class="text-white text-center p-5 row justify-content-md-center">
                <h1 class="fw-bold title">碳探你的路</h1>
                <div class="knowledge-box mt-5 custom-width col col-lg-12 shadow">
                    <h3 class="mt-2">環保小知識</h3>
                    <p>你知道嗎？..................................<a href="#">閱讀更多</a></p>
                </div>
            </div>
        </div>
    </div>


    <div class="gray-bg text-center row justify-content-md-center">
        <div class="col-md-6">
            <h1 class="fw-bold gray-bg-word">個人首頁</h1>
            <div class="row align-items-center p-4 mt-4">
                <div class="col-auto"> <!-- 設置標籤占據一小部分空間 -->
                    <label for="address" class="col-form-label fs-5">預設居家地址 :</label>
                </div>
                <div class="col"> <!-- 設置輸入框占據較大部分空間 -->
                    <div class="input-group">
                        <?php
                        $link = mysqli_connect('localhost', 'root', '')
                            or die("無法開啟 MySQL 資料庫連結!<br>");
                        mysqli_select_db($link, "carbon_emissions");
                        $em_id = $_SESSION['em_id'];

                        $sql = "SELECT area.area_name, city.city_name, em_address.ea_address_detial
                                    FROM em_address
                                    join area on em_address.ea_address_area = area.area_id
                                    join city on em_address.ea_address_city = city.city_id
                                    where em_address.em_id = $em_id
                                    and ea_default = 1";

                        mysqli_query($link, "SET NAMES utf8");
                        $result = mysqli_query($link, $sql);
                        $fields = mysqli_num_fields($result); //取得欄位數
                        $rows = mysqli_num_rows($result); //取得記錄數
                        ?>
                        <?php
                        $rows = mysqli_fetch_array($result);
                        echo '<input type="text" class="form-control" id="address" value="' . $rows[1] . $rows[0] . $rows[2] . '" readonly>';
                        ?>
                        <!-- <input type="text" class="form-control" id="address" value="台中市中區中華路一段" readonly> 地址輸入框 -->
                        <button class="btn btn-outline-secondary" type="button">+</button> <!--新增按鈕-->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8"> <!-- 設置寬度占比，例如占據 8/12 的寬度 -->
            <h3 class="mt-6 text-center">2024年6月出勤記錄</h3>
            <div class="d-flex justify-content-center mb-3 mt-4">
                <button class="btn btn-custom">&lt;&lt;上週</button>
                <button class="btn btn-custom">下週&gt;&gt;</button>
                <button class="btn btn-custom">進階查詢</button>
                <button class="btn btn-new">新增</button>
            </div>

            <table class="table records-table text-center gowork-table p-4 mb-5"> <!-- 出勤記錄表格 -->
                <thead>
                    <tr>
                        <th>日期</th>
                        <th>上下班</th>
                        <th>地址</th>
                        <th>交通工具</th>
                        <th>碳排量</th>
                        <th>編輯</th>
                    </tr>
                </thead>
                <tbody> <!-- 表格內資料 -->

                    <?php
                        $sql = "SELECT em_co2.*, em_address.ea_name
                            from em_co2
                            join em_address on em_co2.ea_id = em_address.ea_id
                            where em_co2.em_id = $em_id
                            order by eCO2_date desc
                            limit 7";
                        mysqli_query($link, "SET NAMES utf8");
                        $result = mysqli_query($link, $sql);

                        while ($rows = mysqli_fetch_array($result)) {
                            echo "<tr>";
                            echo "<td>$rows[1]</td>";
                            echo '<td>' . ($rows[2] == "go" ? "上班" : "下班") . '</td>';
                            echo "<td>$rows[8]</td>";
                            echo '<td>' . ($rows[6] == "car" ? "汽車" : ($rows[6] == "bicycle" ? "機車" : "大眾運輸")) . '</td>';
                            echo "<td>$rows[3]kg</td>";
                            echo "<td>
                                        <form action='em_edit_CO2.php' method='GET'>
                                            <button style='z-index: index 1;' name='edit_CO2' class='btn btn-sm btn-outline-secondary' value='" . $rows[0] . "'>編輯</button>
                                        </form>
                                    </td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="plaid-bg d-flex justify-content-center align-items-center" style="z-index: 99">
        <div class="col-lg-8 col-md-8"> <!-- 設置寬度比例 -->
            <h2 class="text-center mt-6">個人碳排記錄</h2>
            <div class="p-5">
                <canvas class="p-4 pink-bg mt-4" id="carbonChart"></canvas> <!-- 碳排記錄的圖表區 -->
            </div>
        </div>
    </div>
    
    <!-- 抓個人碳排資料 -->
    <?php
        
        $sql = "SELECT YEAR(eCO2_date) AS year, MONTH(eCO2_date) AS month, SUM(eCO2_carbon) AS total_carbon
                FROM em_co2
                WHERE em_id = $em_id
                GROUP BY 
                    YEAR(eCO2_date), MONTH(eCO2_date)
                ORDER BY 
                    YEAR(eCO2_date), MONTH(eCO2_date)";
        
        mysqli_query($link, "SET NAMES utf8");
        $result = mysqli_query($link, $sql);
        
        $carbonData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $year = $row['year'];
            $month = $row['month'];
            $total_carbon = $row['total_carbon'];
        
            // 將資料整理到以年份和月份分組的陣列中
            if (!isset($data[$year])) {
                $data[$year] = [];
            }
            $data[$year][$month] = $total_carbon;
        }
        
        $jsonData = json_encode($data);
        // 回傳格式如下(參考)
        // {
        //     "2022": { "1": 120, "2": 130, "3": 140 },
        //     "2023": { "1": 110, "2": 115, "3": 150 }
        // }
        
        
    ?>
    <!-- 引入 Bootstrap JS（包含 Popper.js） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- 引入 Chart.js 用於繪製圖表 -->
    <script>
        const carbonJsonData = <?php echo $jsonData; ?>;
        console.log(carbonJsonData)
        const monthLabels = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];

        // 長條圖的顏色列表(顏色你們挑喜歡的，我是gpt用幫我生顏色)
        const colorList = [
            '#FF6633', '#FF33FF', '#00B3E6', '#E6B333', '#3366E6', '#B34D4D', 
            '#6680B3', '#66991A', '#FF9933', '#FF1A66', '#33FFCC', '#FFB399',
            '#B3B31A', '#80B300', '#809900', '#999966', '#66E64D', '#4D80CC'
        ];

        // 將資料按照格式放好
        // 每年分配不同顏色
        const datasets = Object.keys(carbonJsonData).map((year, index) => {
            const yearData = [];
            // 用for迴圈去跑那12個月的碳排資料
            for (let i = 1; i <= 12; i++) {
                yearData.push(carbonJsonData[year][i] || 0);  // 沒有碳排的月份就填0
            }
            // Json格式的資料內有幾個year就是會有幾個set
            return {
                label: `${year}年`, // 資料內的key
                data: yearData, // 該key後面帶的data
                backgroundColor: colorList[index % colorList.length]  // 循環使用顏色
            };
        });

        // 繪製圖表
        const ctx = document.getElementById('carbonChart').getContext('2d');
        const carbonChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: datasets
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>