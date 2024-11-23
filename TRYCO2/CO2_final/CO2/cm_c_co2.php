<?php
    session_start();
    // 檢查用戶是否已登入
    if (!isset($_SESSION['em_id'])) {
        // 如果未登入，重定向到登入頁面
        header("Location: Sign_in.php");
        exit();
    }
    function translateCarType($type)
    {
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

    // 分頁參數
    $records_per_page = 20;
    $pages = isset($_GET['pages']) && is_numeric($_GET['pages']) ? intval($_GET['pages']) : 1;
    $offset = ($pages - 1) * $records_per_page;
    // 設一個狀態用來檢查是否有使用篩選功能
    $filter_applied = !empty($_GET['start_date_display']) || !empty($_GET['end_date_display']) || !empty($_GET['filter_car']) || !empty($_GET['filter_employee']);
    ?>


    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>公司車碳排紀錄</title>
        <link rel="stylesheet" href="css/cm_c_co2.css" type="text/css">
        <link rel="shortcut icon" href="img/logo.png">
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
            <?php
            // 連接資料庫
            include_once("dropdown_list/dbcontroller.php");
            $db_handle = new DBController();

            // 查詢 cm_co2_thtf 表，抓取車輛類型 (type) 和對應的總碳排放量
            $pizzaChartQuery = "SELECT type AS car_type, SUM(tatol_carboon) as total_carbon
                                FROM cm_co2_thtf
                                GROUP BY type";
            $pizzaChartResult = $db_handle->runQuery($pizzaChartQuery);

            // 準備資料供 Plotly 使用
            $pizzaChartData = [
                'labels' => [],
                'values' => []
            ];

            if (!empty($pizzaChartResult)) {
                foreach ($pizzaChartResult as $row) {
                    $pizzaChartData['labels'][] = $row['car_type']; // 取車輛類型作為標籤
                    $pizzaChartData['values'][] = $row['total_carbon']; // 取總碳排放量作為值
                }
            }

            // 將資料轉換成 JSON 格式以便 JavaScript 使用
            echo "<script>
                var pizzaLabels = " . json_encode($pizzaChartData['labels']) . ";
                var pizzaValues = " . json_encode($pizzaChartData['values']) . ";
            </script>";
            ?>

            <script>
                // 確認資料是否已載入
                if (pizzaLabels && pizzaValues) {
                    var pieData = [{
                        labels: pizzaLabels,
                        values: pizzaValues,
                        type: 'pie',
                        textinfo: 'label+percent',
                        hoverinfo: 'label+value',
                        hole: 0.3, // 新增這一行，使圖表變成圓環圖
                        marker: {
                            colors: ['#66c2a5', '#fc8d62', '#8da0cb', '#e78ac3', '#a6d854']
                        }
                    }];

                    var pieLayout = {
                        title: '各交通車碳排放分佈',
                        height: 400,
                        plot_bgcolor: '#e2f7ea',
                        paper_bgcolor: '#e2f7ea',
                        margin: { t: 50, b: 50 }
                    };

                    // 繪製圓餅圖
                    Plotly.newPlot('filteredBarChart', pieData, pieLayout);
                }
            </script>
            <!-- 右區塊 -->
            <div class="col-6 right">
                <div class="row">
                   <!-- 篩選列 -->
                    <form action="" method="get" class="g-3 d-flex justify-content-center align-items-center filter-form">
                        <div class="row w-100">
                            <!-- 篩選日期 -->
                            <div class="col-lg-12 d-flex justify-content-start align-items-center">
                                <label for="date_range">篩選日期：</label>
                                <!-- 要保留使用者選的東西優先從url裡面抓出來 -->
                                <input type="date" id="start_date_display" name="start_date_display" class="date-range-picker col-5 me-2" placeholder="開始日期"
                                    value="<?php echo isset($_GET['start_date_display']) ? htmlspecialchars($_GET['start_date_display']) : ''; ?>">
                                <input type="date" id="end_date_display" name="end_date_display" class="date-range-picker col-5" placeholder="結束日期"
                                    value="<?php echo isset($_GET['end_date_display']) ? htmlspecialchars($_GET['end_date_display']) : ''; ?>">
                            </div>

                            <!-- 篩選車輛類型 -->
                            <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                <label for="filter_car">車輛類型：</label>
                                <select id="filter_car" name="filter_car">
                                    <option value="">請選擇</option>
                                    <?php
                                    include_once("dropdown_list/dbcontroller.php");
                                    $db_handle = new DBController();

                                    // 修改為從 cm_co2_thtf 表中提取車輛類型 (type)
                                    $sql = "SELECT DISTINCT type FROM cm_co2_thtf";
                                    $result_car = $db_handle->runQuery($sql);

                                    if (!empty($result_car)) {
                                        foreach ($result_car as $row) {
                                            // 確保選中的選項保持不變
                                            $selected = (isset($_GET['filter_car']) && $_GET['filter_car'] == $row['type']) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($row['type']) . "' $selected>" . htmlspecialchars($row['type']) . "</option>";
                                        }
                                    } else {
                                        echo "<option value=''>沒有資料</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- 篩選員工 -->
                            <div class="col-lg-4 d-flex justify-content-center align-items-center">
                                <label for="filter_employee">員工：</label>
                                <select id="filter_employee" name="filter_employee">
                                    <option value="">請選擇</option>
                                    <?php
                                    // 修改為從 cm_co2_thtf 表中提取員工名稱
                                    $sql = "SELECT DISTINCT em_name FROM cm_co2_thtf";
                                    $result = $db_handle->runQuery($sql);

                                    if (!empty($result)) {
                                        foreach ($result as $row) {
                                            // 確保選中的選項保持不變
                                            $selected = (isset($_GET['filter_employee']) && $_GET['filter_employee'] == $row['em_name']) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($row['em_name']) . "' $selected>" . htmlspecialchars($row['em_name']) . "</option>";
                                        }
                                    } else {
                                        echo "<option value=''>沒有資料</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- 確認篩選按鈕 -->
                            <div class="col-lg-4 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg" name="apply_filter" data-style="apply_filter">確認篩選</button>
                            </div>
                        </div>
                    </form>

                    <!-- 篩選結果顯示區域 -->
                    <div class="filter-results">
                        <?php
                        if ($filter_applied) {
                            $start_date_display = isset($_GET['start_date_display']) ? htmlspecialchars($_GET['start_date_display']) : '';
                            $end_date_display = isset($_GET['end_date_display']) ? htmlspecialchars($_GET['end_date_display']) : '';
                            $filter_car = isset($_GET['filter_car']) ? htmlspecialchars($_GET['filter_car']) : '';
                            $filter_employee = isset($_GET['filter_employee']) ? htmlspecialchars($_GET['filter_employee']) : '';
                            $car_name = !empty($filter_car) ? htmlspecialchars($result_car[array_search($filter_car, array_column($result_car, 'cc_id'))]['type']) : '';
                            $employee_name = !empty($filter_employee) ? htmlspecialchars($result[array_search($filter_employee, array_column($result, 'em_name'))]['em_name']) : '';

                            if (!empty($start_date_display) && !empty($end_date_display) && empty($filter_car) && empty($filter_employee)) {
                                echo "<h4>$start_date_display 到 $end_date_display 的所有碳排記錄</h4>";
                            } elseif (empty($start_date_display) && empty($end_date_display) && !empty($filter_car) && empty($filter_employee)) {
                                echo "<h4>公司車 $car_name 的所有碳排記錄</h4>";
                            } elseif (empty($start_date_display) && empty($end_date_display) && empty($filter_car) && !empty($filter_employee)) {
                                echo "<h4>員工 $employee_name 的所有碳排記錄</h4>";
                            } elseif (!empty($start_date_display) && !empty($end_date_display) && !empty($filter_car) && empty($filter_employee)) {
                                echo "<h4>公司車 $car_name 從 $start_date_display 到 $end_date_display 的碳排記錄</h4>";
                            } elseif (!empty($start_date_display) && !empty($end_date_display) && empty($filter_car) && !empty($filter_employee)) {
                                echo "<h4>員工 $employee_name 從 $start_date_display 到 $end_date_display 的碳排記錄</h4>";
                            } elseif (empty($start_date_display) && empty($end_date_display) && !empty($filter_car) && !empty($filter_employee)) {
                                echo "<h4>員工 $employee_name 使用公司車 $car_name 的所有碳排記錄</h4>";
                            } elseif (!empty($start_date_display) && !empty($end_date_display) && !empty($filter_car) && !empty($filter_employee)) {
                                echo "<h4>員工 $employee_name 使用公司車 $car_name 從 $start_date_display 到 $end_date_display 的碳排記錄</h4>";
                            } else {
                                echo "所有碳排記錄";
                            }
                        }
                        ?>
                    </div>



                    <!-- 碳排表格 -->
                    <div class="information">
                        <table class="table table-bordered table-hover">
                            <thead class="table">
                                <tr>
                                    <?php
                                    if ($filter_applied) {
                                        echo "<th>公司車名稱</th>
                                                    <th>產生碳排日期</th>
                                                    <th>產生的碳排量</th>
                                                    <th>員工姓名</th>";
                                    } else {
                                        echo "<th>公司車名稱</th>
                                            <th>日期區間</th>
                                            <th>總碳排量</th>";
                                    }
                                    ?>

                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                include_once("dropdown_list/dbcontroller.php");
                                $db_handle = new DBController();

                                // 檢查是否有任何篩選參數存在
                                $filter_applied = !empty($_GET['start_date_display']) || !empty($_GET['end_date_display']) || !empty($_GET['filter_car']) || !empty($_GET['filter_employee']);
                                $start_date_display = isset($_GET['start_date_display']) ? $_GET['start_date_display'] : '';
                                $end_date_display = isset($_GET['end_date_display']) ? $_GET['end_date_display'] : '';
                                $filter_car = isset($_GET['filter_car']) ? $_GET['filter_car'] : '';
                                $filter_employee = isset($_GET['filter_employee']) ? $_GET['filter_employee'] : '';

                                $strSrh = '';
                                if ($filter_applied) {
                                    // 只有日期
                                    if ((!empty($start_date_display) && !empty($end_date_display)) && empty($filter_car) && empty($filter_employee)) {
                                        $strSrh = "WHERE cCO2_date BETWEEN '$start_date_display' AND '$end_date_display'";
                                    } elseif ((empty($start_date_display) && empty($end_date_display)) && !empty($filter_car) && empty($filter_employee)) { // 只有車輛類型
                                        $strSrh = "WHERE type = '$filter_car'";
                                    } elseif ((empty($start_date_display) && empty($end_date_display)) && empty($filter_car) && !empty($filter_employee)) { // 只有員工
                                        $strSrh = "WHERE em_name = '$filter_employee'";
                                    } elseif ((!empty($start_date_display) && !empty($end_date_display)) && !empty($filter_car) && empty($filter_employee)) { // 日期、車輛類型
                                        $strSrh = "WHERE cCO2_date BETWEEN '$start_date_display' AND '$end_date_display' AND type = '$filter_car'";
                                    } elseif ((empty($start_date_display) && empty($end_date_display)) && !empty($filter_car) && !empty($filter_employee)) { // 車輛類型、員工
                                        $strSrh = "WHERE type = '$filter_car' AND em_name = '$filter_employee'";
                                    } elseif ((!empty($start_date_display) && !empty($end_date_display)) && empty($filter_car) && !empty($filter_employee)) { // 日期、員工
                                        $strSrh = "WHERE cCO2_date BETWEEN '$start_date_display' AND '$end_date_display' AND em_name = '$filter_employee'";
                                    } elseif ((!empty($start_date_display) && !empty($end_date_display)) && !empty($filter_car) && !empty($filter_employee)) { // 都選
                                        $strSrh = "WHERE cCO2_date BETWEEN '$start_date_display' AND '$end_date_display' AND type = '$filter_car' AND em_name = '$filter_employee'";
                                    }

                                    $query = "SELECT cCO2_date, start_time, end_time, tatol_carboon, em_name, type, place
                                            FROM cm_co2_thtf
                                            $strSrh";
                                    // echo "<br>" . $query;

                                    // 獲取總記錄數
                                    $total_records_query = "SELECT COUNT(*) as total FROM ($query) as temp_table";
                                    $total_records_result = $db_handle->runQuery($total_records_query);
                                    $total_records = $total_records_result[0]['total'];
                                    $total_pages = ceil($total_records / $records_per_page);

                                    // 添加 LIMIT 子句到查詢
                                    $query .= " ORDER BY cCO2_date DESC LIMIT $offset, $records_per_page";
                                    $result = $db_handle->runQuery($query);

                                    if (!empty($result)) {
                                        foreach ($result as $row) {
                                            $place = htmlspecialchars($row['place'], ENT_QUOTES, 'UTF-8');
                                            $place = str_replace(',', "\n", $place);
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['cCO2_date']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['tatol_carboon']) . " 公斤</td>";
                                            echo "<td>" . htmlspecialchars($row['em_name']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>沒有資料</td></tr>";
                                    }
                                } else {
                                    // 預設查詢
                                    $query = "SELECT type, MIN(cCO2_date) as start_date, MAX(cCO2_date) as end_date, ROUND(SUM(tatol_carboon), 2) as tatol_carboon, place
                                            FROM cm_co2_thtf
                                            GROUP BY type";
                                    // echo "<br>" . $query;

                                    // 獲取總記錄數
                                    $total_records_query = "SELECT COUNT(*) as total FROM ($query) as temp_table";
                                    $total_records_result = $db_handle->runQuery($total_records_query);
                                    $total_records = $total_records_result[0]['total'];
                                    $total_pages = ceil($total_records / $records_per_page);

                                    // 添加 LIMIT 子句到查詢
                                    $query .= " ORDER BY start_date DESC LIMIT $offset, $records_per_page";
                                    $result = $db_handle->runQuery($query);

                                    if (!empty($result)) {
                                        foreach ($result as $row) {
                                            $place = htmlspecialchars($row['place'], ENT_QUOTES, 'UTF-8');
                                            $place = str_replace(',', "\n", $place);
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['start_date']) . " 至 " . htmlspecialchars($row['end_date']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['tatol_carboon']) . " 公斤</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>沒有資料</td></tr>";
                                    }
                                }
                            ?>

                            </tbody>
                        </table>
                    </div>
                    <!-- 切換頁面按鈕 -->
                    <?php

                    echo "<div class='turn_pages_div' id='置中'>";

                    // 顯示最前面一頁和上一頁
                    if ($pages > 1) {
                        echo "<a class='turn_pages' href='cm_c_co2.php?pages=1&start_date_display=$start_date_display&end_date_display=$end_date_display&filter_car=$filter_car&filter_employee=$filter_employee'><<</a> ";
                        echo "<a class='turn_pages' href='cm_c_co2.php?pages=" . ($pages - 1) . "&start_date_display=$start_date_display&end_date_display=$end_date_display&filter_car=$filter_car&filter_employee=$filter_employee'><</a> ";
                    } else {
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

                    for ($i = $start_page; $i <= $end_page; $i++) {
                        if ($i != $pages) {
                            echo "<a class='turn_pages' href='cm_c_co2.php?pages=" . $i . "&start_date_display=$start_date_display&end_date_display=$end_date_display&filter_car=$filter_car&filter_employee=$filter_employee' onclick='fetchData()'>" . $i . " </a>";
                        } else {
                            echo "<a class='Noturn_pages'>" . $i . " </a>";
                        }
                    }

                    if ($end_page < $total_pages) {
                        echo "<a class='turn_pages_more'> ...<a>";
                    }

                    // 顯示下一頁和最後面一頁
                    if ($pages < $total_pages) {
                        echo "<a class='turn_pages' href='cm_c_co2.php?pages=" . ($pages + 1) . "&start_date_display=$start_date_display&end_date_display=$end_date_display&filter_car=$filter_car&filter_employee=$filter_employee'>></a> ";
                        echo "<a class='turn_pages' href='cm_c_co2.php?pages=" . $total_pages . "&start_date_display=$start_date_display&end_date_display=$end_date_display&filter_car=$filter_car&filter_employee=$filter_employee'>>></a>";
                    } else {
                        echo "<a class='Noturn_pages'>></a> ";
                        echo "<a class='Noturn_pages'>>></a>";
                    }

                    echo "</div>";
                    ?>

                </div>
            </div>
        </div>

        <?php
            if ($filter_applied) {
                // 篩選日期範圍
                if ((!empty($start_date_display) && !empty($end_date_display)) && empty($filter_car) && empty($filter_employee)) {
                    $start_date = $start_date_display;
                    $end_date = $end_date_display;

                    echo "<script>console.log($start_date, $end_date);</script>";

                    $chartQuery = "SELECT cCO2_date, type, SUM(tatol_carboon) AS total_carbon
                                FROM cm_co2_thtf
                                WHERE cCO2_date BETWEEN '$start_date' AND '$end_date'
                                GROUP BY cCO2_date, type";
                    $chartResults = $db_handle->runQuery($chartQuery);
                    $chartData = [
                        'dates' => [],
                        'carbons' => [],
                        'types' => []
                    ];

                    if (!empty($chartResults)) {
                        foreach ($chartResults as $row) {
                            $chartData['dates'][] = $row['cCO2_date'];
                            $chartData['carbons'][] = $row['total_carbon'];
                            $chartData['types'][] = $row['type'];
                        }
                    }

                    echo "<script>
                            var dates = " . json_encode($chartData['dates']) . ";
                            var carbons = " . json_encode($chartData['carbons']) . ";
                            var types = " . json_encode($chartData['types']) . ";

                            var data = [];

                            var groupedData = {};
                            for (var i = 0; i < dates.length; i++) {
                                if (!groupedData[types[i]]) {
                                    groupedData[types[i]] = { x: [], y: [] };
                                }
                                groupedData[types[i]].x.push(dates[i]);
                                groupedData[types[i]].y.push(carbons[i]);
                            }

                            for (var type in groupedData) {
                                data.push({
                                    x: groupedData[type].x,
                                    y: groupedData[type].y,
                                    type: 'bar',
                                    name: type
                                });
                            }

                            var layout = {
                                title: '" . $start_date . "至" . $end_date . " 公司車碳排量',
                                xaxis: {
                                    title: '日期',
                                    gridcolor: '#67776d'
                                },
                                yaxis: {
                                    title: '碳排量 (kg)',
                                    gridcolor: '#67776d'
                                },
                                barmode: 'stack',
                                plot_bgcolor: '#e2f7ea',
                                paper_bgcolor: '#e2f7ea',
                            };

                            Plotly.newPlot('filteredBarChart', data, layout);
                        </script>";
                }
                // 篩選員工
                else if ((empty($start_date_display) && empty($end_date_display)) && empty($filter_car) && !empty($filter_employee)) {
                    echo "<script>console.log($filter_employee)</script>";

                    $chartQuery = "SELECT MONTH(cCO2_date) AS month, SUM(tatol_carboon) AS total_carbon, em_name
                                FROM cm_co2_thtf
                                WHERE em_name = '$filter_employee'
                                GROUP BY month, em_name";

                    $chartResults = $db_handle->runQuery($chartQuery);

                    $chartData = [
                        'months' => [],
                        'carbons' => []
                    ];

                    $employeeName = '';

                    if (!empty($chartResults)) {
                        foreach ($chartResults as $row) {
                            $chartData['months'][] = $row['month'];
                            $chartData['carbons'][] = $row['total_carbon'];
                            $employeeName = $row['em_name'];
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
                                name: '碳排量 (kg)',
                                line: {
                                    color: '#FF6384'
                                }
                            }];

                            var layout = {
                                title: '$employeeName 的每年碳排量',
                                xaxis: {
                                    title: '月份',
                                    gridcolor: '#67776d'
                                },
                                yaxis: {
                                    title: '碳排量 (kg)',
                                    gridcolor: '#67776d'
                                },
                                plot_bgcolor: '#e2f7ea',
                                paper_bgcolor: '#e2f7ea',
                            };

                            Plotly.newPlot('filteredBarChart', data, layout);
                        </script>";
                }
                // 篩選公司車類型
                else if ((empty($start_date_display) && empty($end_date_display)) && !empty($filter_car) && empty($filter_employee)) {
                    $chartQuery = "SELECT MONTH(cCO2_date) AS month, SUM(tatol_carboon) AS total_carbon, type
                                FROM cm_co2_thtf
                                WHERE type = '$filter_car'
                                GROUP BY month, type";

                    $chartResults = $db_handle->runQuery($chartQuery);

                    $chartData = [
                        'months' => [],
                        'carbons' => []
                    ];

                    $carType = '';

                    if (!empty($chartResults)) {
                        foreach ($chartResults as $row) {
                            $chartData['months'][] = $row['month'];
                            $chartData['carbons'][] = $row['total_carbon'];
                            $carType = $row['type'];
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
                                name: '碳排量 (kg)',
                                line: {
                                    color: '#FF6384'
                                }
                            }];

                            var layout = {
                                title: '公司車類型 $carType 每年碳排量',
                                xaxis: {
                                    title: '月份',
                                    gridcolor: '#67776d'
                                },
                                yaxis: {
                                    title: '碳排量 (kg)',
                                    gridcolor: '#67776d'
                                },
                                plot_bgcolor: '#e2f7ea',
                                paper_bgcolor: '#e2f7ea',
                            };

                            Plotly.newPlot('filteredBarChart', data, layout);
                        </script>";
                }
           // 篩選日期、篩選公司車
            else if ((!empty($start_date_display) && !empty($end_date_display)) && !empty($filter_car) && empty($filter_employee)) {
                $start_date = $start_date_display;
                $end_date = $end_date_display;

                // 更新查詢以匹配新表結構
                $chartQuery = "SELECT MONTH(cCO2_date) AS month, SUM(tatol_carboon) AS total_carbon, type 
                            FROM cm_co2_thtf 
                            WHERE type = '$filter_car' AND cCO2_date BETWEEN '$start_date' AND '$end_date'
                            GROUP BY month, type";

                $chartResults = $db_handle->runQuery($chartQuery);

                // 準備圖表數據
                $chartData = [
                    'months' => [],
                    'carbons' => []
                ];

                // 初始化公司車名稱變數
                $carName = '';

                // 處理查詢結果
                if (!empty($chartResults)) {
                    foreach ($chartResults as $row) {
                        $chartData['months'][] = $row['month'];
                        $chartData['carbons'][] = $row['total_carbon'];
                        $carName = $row['type']; // 提取公司車類型
                    }
                }

                // 渲染圖表
                echo "<script>
                    var months = " . json_encode($chartData['months']) . ";
                    var carbons = " . json_encode($chartData['carbons']) . ";

                    var data = [{
                        x: months,
                        y: carbons,
                        type: 'scatter',
                        mode: 'lines+markers',
                        name: '碳排量 (kg)',
                        line: {
                            color: '#FF6384'
                        }
                    }];

                    var layout = {
                        title: '$start_date 至 $end_date 公司車 $carName 碳排量',
                        xaxis: {
                            title: '月份',
                            gridcolor: '#67776d'
                        },
                        yaxis: {
                            title: '碳排量 (kg)',
                            gridcolor: '#67776d'
                        },
                        plot_bgcolor: '#e2f7ea',
                        paper_bgcolor: '#e2f7ea',
                    };

                    Plotly.newPlot('filteredBarChart', data, layout);
                </script>";
            }
            // 篩選日期、篩選員工
            else if ((!empty($start_date_display) && !empty($end_date_display)) && empty($filter_car) && !empty($filter_employee)) {
                $start_date = $start_date_display;
                $end_date = $end_date_display;

                // 更新查詢以匹配新表結構
                $chartQuery = "SELECT cCO2_date, SUM(tatol_carboon) AS total_carbon, em_name
                            FROM cm_co2_thtf
                            WHERE em_name = '$filter_employee' AND cCO2_date BETWEEN '$start_date' AND '$end_date'
                            GROUP BY cCO2_date, em_name";

                $chartResults = $db_handle->runQuery($chartQuery);

                // 準備圖表數據
                $chartData = [
                    'dates' => [],
                    'carbons' => []
                ];

                // 初始化員工姓名變數
                $employeeName = '';

                // 處理查詢結果
                if (!empty($chartResults)) {
                    foreach ($chartResults as $row) {
                        $chartData['dates'][] = $row['cCO2_date'];
                        $chartData['carbons'][] = $row['total_carbon'];
                        $employeeName = $row['em_name']; // 提取員工姓名
                    }
                }

                // 渲染圖表
                echo "<script>
                    var dates = " . json_encode($chartData['dates']) . ";
                    var carbons = " . json_encode($chartData['carbons']) . ";

                    var data = [{
                        x: dates,
                        y: carbons,
                        type: 'bar',
                        name: '碳排量 (kg)',
                        marker: {
                            color: '#FF6384'
                        }
                    }];

                    var layout = {
                        title: '$start_date 至 $end_date 員工 $employeeName 的碳排量',
                        xaxis: {
                            title: '日期',
                            gridcolor: '#67776d'
                        },
                        yaxis: {
                            title: '碳排量 (kg)',
                            gridcolor: '#67776d'
                        },
                        plot_bgcolor: '#e2f7ea',
                        paper_bgcolor: '#e2f7ea',
                    };

                    Plotly.newPlot('filteredBarChart', data, layout);
                </script>";
            }
            // 篩選公司車、篩選員工
            else if ((empty($start_date_display) && empty($end_date_display)) && !empty($filter_car) && !empty($filter_employee)) {
                $chartQuery = "SELECT MONTH(cCO2_date) AS month, SUM(tatol_carboon) AS total_carbon
                            FROM cm_co2_thtf
                            WHERE type = '$filter_car' AND em_name = '$filter_employee'
                            GROUP BY month";

                $chartResults = $db_handle->runQuery($chartQuery);

                $chartData = [
                    'months' => [],
                    'carbons' => []
                ];

                if (!empty($chartResults)) {
                    foreach ($chartResults as $row) {
                        $chartData['months'][] = $row['month'];
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
                        name: '碳排量 (kg)',
                        line: {
                            color: '#FF6384'
                        }
                    }];

                    var layout = {
                        title: '公司車 $filter_car 員工 $filter_employee 的碳排量',
                        xaxis: {
                            title: '月份',
                            gridcolor: '#67776d'
                        },
                        yaxis: {
                            title: '碳排量 (kg)',
                            gridcolor: '#67776d'
                        },
                        plot_bgcolor: '#e2f7ea',
                        paper_bgcolor: '#e2f7ea',
                    };

                    Plotly.newPlot('filteredBarChart', data, layout);
                </script>";
            }

            // 篩選日期、篩選公司車、篩選員工
else if ((!empty($start_date_display) && !empty($end_date_display)) && !empty($filter_car) && !empty($filter_employee)) {
    $start_date = $start_date_display;
    $end_date = $end_date_display;

    // 修改查詢以包括公司車名稱和員工名稱
    $chartQuery = "SELECT cCO2_date, SUM(total_carbon) AS total_carbon, 
                    type AS car_type, em_name AS employee_name 
                    FROM cm_co2_thtf 
                    WHERE cCO2_date BETWEEN '$start_date' AND '$end_date' 
                    AND type = '$filter_car' 
                    AND em_name = '$filter_employee' 
                    GROUP BY cCO2_date";

    // 調試輸出 SQL 查詢語句
    echo "<script>console.log('SQL Query: $chartQuery');</script>";

    $chartResults = $db_handle->runQuery($chartQuery);
    $chartData = [
        'dates' => [],
        'carbons' => []
    ];

    $car_name = ""; // 預設空字串，避免未定義錯誤
    $employee_name = ""; // 預設空字串

    // 準備數據
    if (!empty($chartResults)) {
        foreach ($chartResults as $row) {
            $chartData['dates'][] = $row['cCO2_date'];
            $chartData['carbons'][] = $row['total_carbon'];
            $car_name = $row['type']; // 從查詢中提取公司車類型
            $employee_name = $row['em_name']; // 提取員工名稱
        }
    } else {
        // 如果查詢結果為空，提示用戶
        echo "<script>alert('沒有符合條件的數據，請檢查篩選條件或數據庫內容！');</script>";
    }

    // 調試輸出數據
    echo "<script>console.log('Dates: " . json_encode($chartData['dates']) . "');</script>";
    echo "<script>console.log('Carbons: " . json_encode($chartData['carbons']) . "');</script>";

    // 設定圖表資料
    echo "<script>
            var dates = " . json_encode($chartData['dates']) . ";
            var carbons = " . json_encode($chartData['carbons']) . ";

            if (dates.length > 0 && carbons.length > 0) {
                var data = [{
                    x: dates,
                    y: carbons,
                    type: 'bar',
                    name: '" . $start_date . " 至 " . $end_date . " " . $car_name . " " . $employee_name . " 的碳排量',
                    marker: {
                        color: '#FF6384'
                    }
                }];

                var layout = {
                    title: '" . $start_date . " 至 " . $end_date . " 公司車 " . $car_name . " 員工 " . $employee_name . " 的碳排量',
                    xaxis: {
                        title: '日期',
                        gridcolor: '#67776d'
                    },
                    yaxis: {
                        title: '碳排量 (kg)',
                        gridcolor: '#67776d'
                    },
                    // 設置繪圖區域背景顏色
                    plot_bgcolor: '#e2f7ea',
                    // 設置整個圖表背景顏色
                    paper_bgcolor: '#e2f7ea',
                };

                Plotly.newPlot('filteredBarChart', data, layout);
            } else {
                console.log('圖表數據為空，未能生成圖表！');
            }
        </script>";

                        }
    }else{

            }


?>



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


            document.querySelector('.filter-form').addEventListener('submit', function(event) {
                var startDate = document.getElementById('start_date_display').value;
                var endDate = document.getElementById('end_date_display').value;
                var car = document.getElementById('filter_car').value;
                var employee = document.getElementById('filter_employee').value;

                var errorMessage = '';

                // 檢查是否所有條件為空
                if (!startDate && !endDate && !car && !employee) {
                    errorMessage = '請至少選擇一個篩選條件。';
                }
                // 檢查是否篩選日期不完整
                else if ((startDate && !endDate) || (!startDate && endDate)) {
                    errorMessage = '請填寫完整的日期範圍。';
                }

                // 如果有錯誤訊息，阻止提交並顯示訊息
                if (errorMessage) {
                    alert(errorMessage); // 你可以改成顯示在頁面上
                    event.preventDefault(); // 阻止表單提交
                }
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    </body>

    </html>