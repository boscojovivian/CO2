<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}

// 分頁參數
$records_per_page = 20;
$pages = isset($_GET['pages']) && is_numeric($_GET['pages']) ? intval($_GET['pages']) : 1;
$offset = ($pages - 1) * $records_per_page;
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>員工碳排紀錄</title>
        <link rel="stylesheet" href="css/cm_c_co2.css" type="text/css">
        <link rel="shortcut icon" href="img/logo.png" >
        <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr@4.6.3/dist/l10n/zh-tw.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    </head>
    <body>
        <!-- 回到最頂端 -->
        <button onclick="topFunction()" class="topBtn" id="myBtn" title="GOtop">TOP</button>

        <!-- 導入導覽列 -->
        <?php include('nav/cm_nav.php') ?>

        <div class="container-fluid row">
            <!-- 左區塊 -->
            <div class="col-6 left">
                <div id="filteredBarChart" class="filteredBarChart d-flex justify-content-center align-items-center"></div>
            </div>
            <!-- 右區塊 -->
            <div class="col-6 right">
                <div class="row">
                    <!-- 篩選列 -->
                    <form action="" method="post" class="row g-3 d-flex align-items-center filter-form">
                        <!-- 篩選日期 -->
                        <div class="col-12 col-xl-5 d-flex justify-content-center align-items-center">
                            <input type="hidden" name="filter_date" id="filter_date" value="">
                            <label for="date_range">篩選日期：</label>
                            <input type="text" id="date_range" name="date_range" class="date-range-picker" placeholder="選擇日期">
                        </div>

                        <!-- 篩選員工 -->
                        <div class="col-12 col-xl-5 d-flex justify-content-center align-items-center">
                            <label for="filter_employee">篩選員工：</label>
                            <select id="filter_employee" name="filter_employee">
                            <option value="">請選擇</option>
                            <?php
                            // 包含資料庫連線的程式碼
                            include_once("dropdown_list/dbcontroller.php");
                            $db_handle = new DBController();

                            // 從資料庫中獲取所有的 em_name 資料
                            $sql = "SELECT em_name FROM employee";
                            $result = $db_handle->runQuery($sql);

                            // 如果有資料，則建立下拉式選單
                            if (!empty($result)) {
                                foreach ($result as $row) {
                                    echo "<option value='" . $row['em_name'] . "'>" . $row['em_name'] . "</option>";
                                }
                            } else {
                                echo  "<option value=''>沒有資料</option>";
                            }
                            ?>
                        </select>
                        </div>

                        <!-- 確認篩選按鈕 --> 
                        <div class="col-12 col-xl-2 d-flex justify-content-center align-items-end">
                            <!-- type="submit"設定提交表單 -->
                            <button type="submit" class="btn btn-success btn-lg" name="apply_filter" data-style="apply_filter">確認篩選</button>
                        </div>
                    </form>

                    <!-- 碳排表格 -->
                    <div class="information">
                        <table class="table table-bordered table-hover">
                            <thead class="table">
                                <tr>
                                    <th>員工姓名</th>
                                    <th>產生碳排日期</th>
                                    <th>上下班</th>
                                    <th>產生的碳排量</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include_once("dropdown_list/dbcontroller.php");
                                $db_handle = new DBController();
                                
                                if (isset($_POST['apply_filter'])) {
                                    $date_range = $_POST['date_range'];  // 从隐藏字段获取日期范围
                                    $filter_employee = $_POST['filter_employee'];
                                    
                                    $query = "SELECT employee.em_id, employee.em_name, em_co2.eCO2_date, em_co2.eCO2_carbon, em_co2.eCO2_commute
                                                FROM em_co2 
                                                INNER JOIN employee ON em_co2.em_id = employee.em_id
                                                WHERE 1=1";
                                    
                                    if (!empty($date_range)) {
                                        if (strpos($date_range, " 至 ") !== false) {
                                            list($filter_start_date, $filter_end_date) = explode(" 至 ", $date_range);
                                        } else {
                                            // 如果没有分隔符，将开始日期和结束日期都设置为同一日期
                                            $filter_start_date = $filter_end_date = $date_range;
                                        }
                                        $query .= " AND em_co2.eCO2_date BETWEEN '$filter_start_date' AND '$filter_end_date'";
                                    }
                                    
                                    if (!empty($filter_employee)) {
                                        $query .= " AND employee.em_name = '$filter_employee'";
                                    }
                                } else {
                                    $query = "SELECT employee.em_id, employee.em_name, em_co2.eCO2_date, em_co2.eCO2_carbon, em_co2.eCO2_commute
                                                FROM em_co2 
                                                INNER JOIN employee ON em_co2.em_id = employee.em_id
                                                WHERE 1=1";
                                }

                                // 獲取總記錄數
                                $total_records_query = "SELECT COUNT(*) as total FROM ($query) as temp_table";
                                $total_records_result = $db_handle->runQuery($total_records_query);
                                $total_records = $total_records_result[0]['total'];
                                $total_pages = ceil($total_records / $records_per_page);
                                // 添加LIMIT子句到查詢
                                $query .= " ORDER BY em_co2.eCO2_date DESC LIMIT $offset, $records_per_page";
                                $result = $db_handle->runQuery($query);

                                if (!empty($result)) {
                                    foreach ($result as $record) {
                                        echo "<tr>";
                                        // echo "<td>" . $record['em_id'] . "</td>";
                                        echo "<td>" . $record['em_name'] . "</td>";
                                        echo "<td>" . $record['eCO2_date'] . "</td>";
                                        // 替換 eCO2_commute 的值
                                        $commute = $record['eCO2_commute'];
                                        $commute = str_replace('go', '上班', $commute);
                                        $commute = str_replace('back', '下班', $commute);
                                        echo "<td>" . $commute . "</td>";
                                        echo "<td>" . $record['eCO2_carbon'] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>沒有資料</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- 切換頁面按鈕 -->
                    <?php
                    echo "<div class='turn_pages_div' id='置中'>";

                    // 顯示最前面一頁和上一頁
                    if($pages > 1){
                        echo "<a class='turn_pages' href='cm_e_co2.php?pages=1'><<</a> ";
                        echo "<a class='turn_pages' href='cm_e_co2.php?pages=" . ($pages-1) . "'><</a> ";
                    }
                    else{
                        echo "<a class='Noturn_pages'><<</a> ";
                        echo "<a class='Noturn_pages'><</a> ";
                    }

                    // 確定分頁範圍
                    if ($total_pages <= 5) {
                        $start_page = 1;
                        $end_page = $total_pages;
                    } else {
                        if ($pages <= 3) {
                            $start_page = 1;
                            $end_page = 5;
                        } elseif ($pages > $total_pages - 3) {
                            $start_page = $total_pages - 4;
                            $end_page = $total_pages;
                        } else {
                            $start_page = $pages - 2;
                            $end_page = $pages + 2;
                        }
                    }

                    // 確保正確的分頁範圍
                    $start_page = max(1, $start_page);
                    $end_page = min($total_pages, $end_page);

                    if ($start_page > 1) {
                        echo "<a class='turn_pages_more'> ...<a>";
                    }

                    for ($i = $start_page; $i <= $end_page; $i++){
                        if($i != $pages){
                            echo "<a class='turn_pages' href='cm_e_co2.php?pages=" . $i . "' onclick='fetchData()'>" . $i . " </a>";
                        }
                        else{
                            echo "<a class='Noturn_pages'>" . $i . " </a>";
                        }
                    }

                    if ($end_page < $total_pages) {
                        echo "<a class='turn_pages_more'> ...<a>";
                    }

                    // 顯示下一頁和最後面一頁
                    if ($pages < $total_pages){
                        echo "<a class='turn_pages' href='cm_e_co2.php?pages=" . ($pages+1) . "'>></a> ";
                        echo "<a class='turn_pages' href='cm_e_co2.php?pages=" . $total_pages . "'>>></a>";
                    }
                    else{
                        echo "<a class='Noturn_pages'>></a> ";
                        echo "<a class='Noturn_pages'>>></a>";
                    }

                    echo "</div>";
                    ?>
                </div>
            </div>
        </div>

        <?php
        if (!empty($filter_start_date) && !empty($filter_end_date) && empty($filter_employee)) {
            $dateDiff = (strtotime($filter_end_date) - strtotime($filter_start_date)) / (60 * 60 * 24);
            
            if ($dateDiff > 30) {
                $chartQuery = "SELECT 
                                    DATE_FORMAT(em_co2.eCO2_date, '%Y-%m-%d') AS eCO2_date, 
                                    SUM(em_co2.eCO2_carbon) AS total_carbon
                                FROM em_co2
                                WHERE em_co2.eCO2_date BETWEEN '$filter_start_date' AND '$filter_end_date'
                                GROUP BY DATE_FORMAT(em_co2.eCO2_date, '%Y-%u')"; // 按每周分组
            } else {
                $chartQuery = "SELECT em_co2.eCO2_date, SUM(em_co2.eCO2_carbon) AS total_carbon
                                FROM em_co2
                                WHERE em_co2.eCO2_date BETWEEN '$filter_start_date' AND '$filter_end_date'
                                GROUP BY em_co2.eCO2_date";
            }
            
            $chartResults = $db_handle->runQuery($chartQuery);
            $chartData = [
                'dates' => [],
                'carbons' => []
            ];
                            
            if (!empty($chartResults)) {
                foreach ($chartResults as $row) {
                    $chartData['dates'][] = $row['eCO2_date'];
                    $chartData['carbons'][] = $row['total_carbon'];
                }
            }
                            
            echo "<script>
            var dates = " . json_encode($chartData['dates']) . ";
            var carbons = " . json_encode($chartData['carbons']) . ";
                            
            var data = [{
                x: dates,
                y: carbons,
                type: 'scatter',
                mode: 'lines+markers',
                name: '碳排總和',
                line: {
                    color: '#FF5733',
                    width: 2
                },
                marker: {
                    color: '#FF5733',
                    size: 6
                }
            }];
                            
            var layout = {
                title: '$filter_start_date 至 $filter_end_date 每日碳排量',
                xaxis: {
                    title: '日期',
                    gridcolor: '#67776d' // 设置x轴网格线的颜色
                },
                
                yaxis: {
                    title: '碳排量 (kg)',
                    gridcolor: '#67776d' // 设置y轴网格线的颜色
                },
                // 设置绘图区域背景颜色
                plot_bgcolor: '#e2f7ea',
                // 设置整个图表背景颜色
                paper_bgcolor: '#e2f7ea',
            };
                            
            Plotly.newPlot('filteredBarChart', data, layout);
        </script>";
        }



        // 篩選員工
        if (empty($filter_date) && !empty($filter_employee)) {
            $currentYear = date('Y');
            $chartQuery = "SELECT YEAR(em_co2.eCO2_date) AS year, MONTH(em_co2.eCO2_date) AS month, SUM(em_co2.eCO2_carbon) AS total_carbon
                        FROM em_co2
                        INNER JOIN employee ON em_co2.em_id = employee.em_id
                        WHERE employee.em_name = '$filter_employee' AND YEAR(em_co2.eCO2_date) = '$currentYear'
                        GROUP BY year, month";
            $chartResults = $db_handle->runQuery($chartQuery);
            $chartData = [
                'months' => [],
                'carbons' => []
            ];

            if (!empty($chartResults)) {
                foreach ($chartResults as $row) {
                    // 拼接月份和年份
                    $chartData['months'][] = $row['year'] . '-' . $row['month'];
                    $chartData['carbons'][] = $row['total_carbon'];
                }
            }

            echo "<script>
                var months = " . json_encode($chartData['months']) . ";
                var carbons = " . json_encode($chartData['carbons']) . ";

                var data = [{
                    x: months,
                    y: carbons,
                    type: 'scatter',
                    mode: 'lines+markers',
                    name: '碳排量 (g)',
                    line: {
                        color: '#FF5733',
                        width: 2
                    },
                    marker: {
                        color: '#FF5733',
                        size: 6
                    }
                }];

                var layout = {
                    title: '員工 $filter_employee ' + $currentYear + ' 年度碳排量',
                    xaxis: {
                        title: '月份',
                        type: 'category',
                        gridcolor: '#67776d' // 设置x轴网格线的颜色
                    },
                    yaxis: {
                        title: '碳排量 (g)',
                        gridcolor: '#67776d' // 设置y轴网格线的颜色
                    },
                    // 设置绘图区域背景颜色
                    plot_bgcolor: '#e2f7ea',
                    // 设置整个图表背景颜色
                    paper_bgcolor: '#e2f7ea',
                    
                    barmode: 'stack',
                };

                Plotly.newPlot('filteredBarChart', data, layout);
            </script>";
        }

        // 如果篩選日期、篩選員工
        if (!empty($filter_start_date) && !empty($filter_end_date) && !empty($filter_employee)) {
            $chartQuery = "SELECT em_co2.eCO2_date, em_co2.eCO2_commute, SUM(em_co2.eCO2_carbon) AS total_carbon
                        FROM em_co2
                        INNER JOIN employee ON em_co2.em_id = employee.em_id
                        WHERE employee.em_name = '$filter_employee' 
                        AND em_co2.eCO2_date BETWEEN '$filter_start_date' AND '$filter_end_date'
                        GROUP BY em_co2.eCO2_date, em_co2.eCO2_commute";
            $chartResults = $db_handle->runQuery($chartQuery);
            $chartData = [
                'dates' => [],
                'commutes' => [],
                'carbons' => []
            ];

            if (!empty($chartResults)) {
                foreach ($chartResults as $row) {
                    $chartData['dates'][] = $row['eCO2_date'];
                    $chartData['commutes'][] = $row['eCO2_commute'];
                    $chartData['carbons'][] = $row['total_carbon'];
                }
            }

            echo "<script>
                var dates = " . json_encode($chartData['dates']) . ";
                var commutes = " . json_encode($chartData['commutes']) . ";
                var carbons = " . json_encode($chartData['carbons']) . ";

                var data = [];
                var groupedData = {};
                var totalCarbons = {};
                var colors = {
                    '0': '#63ff87', // 上班
                    '1': '#ff8263'  // 下班
                };

                for (var i = 0; i < dates.length; i++) {
                    var commute = commutes[i];
                    if (!groupedData[commute]) {
                        groupedData[commute] = { x: [], y: [] };
                    }
                    groupedData[commute].x.push(dates[i]);
                    groupedData[commute].y.push(carbons[i]);

                    if (!totalCarbons[dates[i]]) {
                        totalCarbons[dates[i]] = 0;
                    }
                    totalCarbons[dates[i]] += parseFloat(carbons[i]);
                }

                for (var commute in groupedData) {
                    data.push({
                        x: groupedData[commute].x,
                        y: groupedData[commute].y,
                        type: 'bar',
                        name: commute === '0' ? '上班' : '下班',
                        marker: {
                            color: colors[commute]
                        }
                    });
                }

                var totalDates = Object.keys(totalCarbons).sort();
                var totalCarbonsValues = totalDates.map(date => totalCarbons[date]);

                data.push({
                    x: totalDates,
                    y: totalCarbonsValues,
                    type: 'scatter',
                    mode: 'lines+markers',
                    name: '碳排總和',
                    line: {
                        color: '#FF5733',
                        width: 2
                    },
                    marker: {
                        color: '#FF5733',
                        size: 6
                    }
                });

                var layout = {
                    title: '$filter_start_date 至 $filter_end_date 員工 $filter_employee 的碳排量',
                    xaxis: {
                        title: '日期',
                        gridcolor: '#67776d' // 设置x轴网格线的颜色
                    },
                    yaxis: {
                        title: '碳排量 (kg)',
                        gridcolor: '#67776d' // 设置y轴网格线的颜色
                    },
                    barmode: 'stack',
                    // 设置绘图区域背景颜色
                    plot_bgcolor: '#e2f7ea',
                    // 设置整个图表背景颜色
                    paper_bgcolor: '#e2f7ea',
                    };

                Plotly.newPlot('filteredBarChart', data, layout);
            </script>";
        }
        ?>

        <?php if (isset($_POST['apply_filter']) && !empty($result)): ?>
            <script>
            
                // 获取 PHP 生成的日期、碳排量和车辆数据
                var dates = <?php echo json_encode(array_column($result, 'eCO2_date')); ?>;
                var carbons = <?php echo json_encode(array_column($result, 'eCO2_carbon')); ?>;
                var cars = <?php echo json_encode(array_column($result, 'ec_name')); ?>;

                // 调用渲染图表的函数
                renderChart(dates, carbons, cars, "<?php echo $filter_start_date; ?>", "<?php echo $filter_end_date; ?>");

                function renderChart(dates, carbons, cars, startDate, endDate) {
                    var data = [];
                    var groupedData = {};
                    var colors = ['#ff8263', '#ffd063']; // 你可以添加更多颜色
                    var totalCarbons = {};

                    for (var i = 0; i < dates.length; i++) {
                        if (!groupedData[cars[i]]) {
                            groupedData[cars[i]] = { x: [], y: [] };
                        }
                        groupedData[cars[i]].x.push(dates[i]);
                        groupedData[cars[i]].y.push(carbons[i]);

                        if (!totalCarbons[dates[i]]) {
                            totalCarbons[dates[i]] = 0;
                        }
                        totalCarbons[dates[i]] += parseFloat(carbons[i]);
                    }

                    var colorIndex = 0;
                    for (var car in groupedData) {
                        data.push({
                            x: groupedData[car].x,
                            y: groupedData[car].y,
                            type: 'bar',
                            name: car,
                            marker: {
                                color: colors[colorIndex % colors.length]
                            }
                        });
                        colorIndex++;
                    }

                    var totalDates = Object.keys(totalCarbons).sort();
                    var totalCarbonsValues = totalDates.map(date => totalCarbons[date]);

                    data.push({
                        x: totalDates,
                        y: totalCarbonsValues,
                        type: 'scatter',
                        mode: 'lines+markers',
                        name: '碳排總和',
                        line: {
                            color: '#FF5733',
                            width: 2
                        },
                        marker: {
                            color: '#FF5733',
                            size: 6
                        }
                    });

                    var layout = {
                        title: startDate + ' 至 ' + endDate + ' 交通車碳排量',
                        xaxis: {
                            title: '日期'
                        },
                        yaxis: {
                            title: '碳排量 (kg)'
                        },
                        barmode: 'stack'
                    },
                    barmode: 'stack'
                    
                    Plotly.newPlot('filteredBarChart', data, layout);
                };
        </script>
        <?php endif; ?>

        <script>
            // 回到最頂端
            window.onscroll = scrollFunction; //每當畫面捲動觸發一次

            // 網頁捲動超過200px => 顯示，反之 => 隱藏
            function scrollFunction() {
                if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
                    // 顯示
                    document.getElementById("myBtn").style.display = "block";
                } else {
                    // 隱藏
                    document.getElementById("myBtn").style.display = "none";
                }
            }

            // 重置變數
            function topFunction() {
                // 不同瀏覽器
                document.body.scrollTop = 0; // Safari
                document.documentElement.scrollTop = 0; // Chrome、Firefox、 IE、Opera
            }

            // 日期選擇器(區間)
            flatpickr("#date_range", {
                mode: "range",
                dateFormat: "Y-m-d",
                locale: "zh_tw",
                onReady: function(selectedDates, dateStr, instance) {
                    const container = instance.calendarContainer;
                    const weekButton = document.createElement("button");
                    weekButton.textContent = "本周";
                    weekButton.type = "button";
                    weekButton.classList.add("flatpickr_week_button");
                    weekButton.addEventListener("click", function() {
                        instance.setDate(getWeekRange());
                    });

                    const monthButton = document.createElement("button");
                    monthButton.textContent = "本月";
                    monthButton.type = "button";
                    monthButton.classList.add("flatpickr_week_button");
                    monthButton.addEventListener("click", function() {
                        instance.setDate(getMonthRange());
                    });

                    container.appendChild(weekButton);
                    container.appendChild(monthButton);
                }
            });

            function getWeekRange() {
                const now = new Date();
                const dayOfWeek = now.getDay();
                const start = new Date(now);
                const end = new Date(now);

                start.setDate(now.getDate() - dayOfWeek + 1);
                end.setDate(now.getDate() + (7 - dayOfWeek));

                return [start, end];
            }

            function getMonthRange() {
                const now = new Date();
                const start = new Date(now.getFullYear(), now.getMonth(), 1);
                const end = new Date(now.getFullYear(), now.getMonth() + 1, 0);

                return [start, end];
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    </body>
</html>