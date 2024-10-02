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
            <title>交通車碳排紀錄</title>
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
                        <form action="" method="post" class="g-3 d-flex justify-content-center align-items-center filter-form">
                            <div class="row w-100">
                                <!-- 篩選日期 -->
                                <div class="col-lg-12 d-flex justify-content-start align-items-center">
                                    <label for="date_range">篩選日期：</label>
                                    <input type="date" id="start_date_display" name="start_date_display" class="date-range-picker col-5 me-2" placeholder="開始日期">
                                    <input type="date" id="end_date_display" name="end_date_display" class="date-range-picker col-5" placeholder="結束日期">
                                </div>


                                <!-- 篩選交通車 -->
                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                    <label for="filter_car">交通車：</label>
                                    <select id="filter_car" name="filter_car">
                                        <option value="">請選擇</option>
                                        <?php
                                        include_once("dropdown_list/dbcontroller.php");
                                        $db_handle = new DBController();

                                        $sql = "SELECT cc_id, cc_name FROM cm_car";
                                        $result = $db_handle->runQuery($sql);

                                        if (!empty($result)) {
                                            foreach ($result as $row) {
                                                echo "<option value='" . $row['cc_id'] . "'>" . $row['cc_name'] . "</option>";
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
                                        $sql = "SELECT em_id, em_name FROM employee";
                                        $result = $db_handle->runQuery($sql);

                                        if (!empty($result)) {
                                            foreach ($result as $row) {
                                                echo "<option value='" . $row['em_id'] . "'>" . $row['em_name'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>沒有資料</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- 確認篩選按鈕 --> 
                                <div class="col-lg-4 d-flex justify-content-center align-items-center">
                                    <!-- type="submit"設定提交表單 -->
                                    <button type="submit" class="btn btn-success btn-lg" name="apply_filter" data-style="apply_filter">確認篩選</button>
                                </div>
                            </div>
                        </form>

                        <!-- 碳排表格 -->
                        <div class="information">
                            <table class="table table-bordered table-hover">
                                <thead class="table">
                                    <tr>
                                        <th>交通車名稱</th>
                                        <th>產生碳排日期</th>
                                        <th>產生的碳排量</th>
                                        <th>員工姓名</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        include_once("dropdown_list/dbcontroller.php");
                                        $db_handle = new DBController();

                                        if (isset($_POST['apply_filter'])) {
                                            $start_date_display = $_POST['start_date_display'];
                                            $end_date_display = $_POST['end_date_display'];
                                            $filter_car = $_POST['filter_car'];
                                            $filter_employee = $_POST['filter_employee'];
                                            $strSrh = '';
                                            // 只篩日期
                                            if((!empty($start_date_display) && !empty($end_date_display)) && empty($filter_car) && empty($filter_employee)){
                                                $strSrh = "WHERE cCO2_date BETWEEN '$start_date_display' AND '$end_date_display'";
                                            }else if((empty($start_date_display) && empty($end_date_display)) && !empty($filter_car) && empty($filter_employee)){ // 只篩車子
                                                $strSrh = "WHERE cm_co2.cc_id = '$filter_car'";
                                            }else if((empty($start_date_display) && empty($end_date_display)) && empty($filter_car) && !empty($filter_employee)){ // 只篩員工
                                                $strSrh = "WHERE cm_co2.em_id = '$filter_employee'";
                                            }else if((!empty($start_date_display) && !empty($end_date_display)) && !empty($filter_car) && empty($filter_employee)){ // 日期車子
                                                $strSrh = "WHERE cm_co2.cCO2_date BETWEEN '$start_date_display' AND '$end_date_display' AND cm_co2.cc_id = '$filter_car'";
                                            }else if((empty($start_date_display) && empty($end_date_display)) && !empty($filter_car) && !empty($filter_employee)){ // 車子員工
                                                $strSrh = "WHERE cm_co2.cc_id = '$filter_car' AND cm_co2.em_id = '$filter_employee'";
                                            }else if((!empty($start_date_display) && !empty($end_date_display)) && empty($filter_car) && !empty($filter_employee)){ // 員工日期
                                                $strSrh = "WHERE cm_co2.cCO2_date BETWEEN '$start_date_display' AND '$end_date_display' AND cm_co2.em_id = '$filter_employee'";
                                            }else if((!empty($start_date_display) && !empty($end_date_display)) && !empty($filter_car) && !empty($filter_employee)){ // 全部
                                                $strSrh = "WHERE cm_co2.cCO2_date BETWEEN '$start_date_display' AND '$end_date_display' AND cm_co2.cc_id = '$filter_car' AND cm_co2.em_id = '$filter_employee'";
                                            }else{
                                                ;
                                            }
                                            
                                            $query = "SELECT cm_co2.cc_id, cm_car.cc_name, cm_co2.cCO2_date, cm_co2.cCO2_start_time, cm_co2.cCO2_end_time, cm_co2.cCO2_carbon, cm_car.cc_type, employee.em_id, employee.em_name, cm_co2.cCO2_address
                                                    FROM cm_co2
                                                    INNER JOIN employee ON cm_co2.em_id = employee.em_id
                                                    INNER JOIN cm_car ON cm_co2.cc_id = cm_car.cc_id
                                                    ".$strSrh;
                                            echo "</br>" . $query;
                                            
                                        } else {
                                            // 預設查詢
                                            $query = "SELECT cm_co2.cc_id, cm_car.cc_name, cm_co2.cCO2_date, cm_co2.cCO2_start_time, cm_co2.cCO2_end_time, cm_co2.cCO2_carbon, cm_car.cc_type, employee.em_id, employee.em_name, cm_co2.cCO2_address
                                                    FROM cm_co2
                                                    INNER JOIN employee ON cm_co2.em_id = employee.em_id
                                                    INNER JOIN cm_car ON cm_co2.cc_id = cm_car.cc_id";
                                        }

                                        // 獲取總記錄數
                                        $total_records_query = "SELECT COUNT(*) as total FROM ($query) as temp_table";
                                        $total_records_result = $db_handle->runQuery($total_records_query);
                                        $total_records = $total_records_result[0]['total'];
                                        $total_pages = ceil($total_records / $records_per_page);
                                        // 添加LIMIT子句到查詢
                                        $query .= " ORDER BY cm_co2.cCO2_date DESC LIMIT $offset, $records_per_page";
                                        $result = $db_handle->runQuery($query);

                                        if (!empty($result)) {
                                            foreach ($result as $row) {
                                                $address = htmlspecialchars($row['cCO2_address'], ENT_QUOTES, 'UTF-8');
                                                $address = str_replace(',', "\n", $address);
                                                echo "<tr>";
                                                echo "<td>" . $row['cc_name'] . "</td>";
                                                echo "<td>" . $row['cCO2_date'] . "</td>";
                                                echo "<td>" . $row['cCO2_carbon'] . "公克</td>";
                                                echo "<td>" . $row['em_name'] . "</td>";
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
                                echo "<a class='turn_pages' href='cm_c_co2.php?pages=1'><<</a> ";
                                echo "<a class='turn_pages' href='cm_c_co2.php?pages=" . ($pages-1) . "'><</a> ";
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
                                    echo "<a class='turn_pages' href='cm_c_co2.php?pages=" . $i . "' onclick='fetchData()'>" . $i . " </a>";
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
                                echo "<a class='turn_pages' href='cm_c_co2.php?pages=" . ($pages+1) . "'>></a> ";
                                echo "<a class='turn_pages' href='cm_c_co2.php?pages=" . $total_pages . "'>>></a>";
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
            // 篩選日期範圍
            if ((!empty($start_date_display) && !empty($end_date_display)) && empty($filter_car) && empty($filter_employee)) {

                $start_date = $start_date_display;
                $end_date = $end_date_display;

                echo "<script>console.log($start_date, $end_date);</script>";
                
                $chartQuery = "SELECT cCO2_date, cm_car.cc_name, SUM(cCO2_carbon) AS total_carbon
                        FROM cm_co2
                        INNER JOIN cm_car ON cm_co2.cc_id = cm_car.cc_id
                        WHERE cCO2_date BETWEEN '$start_date' AND '$end_date'
                        GROUP BY cCO2_date, cm_car.cc_name";
                $chartResults = $db_handle->runQuery($chartQuery);
                $chartData = [
                    'dates' => [],
                    'carbons' => [],
                    'cars' => []
                ];

                if (!empty($chartResults)) {
                    foreach ($chartResults as $row) {
                        $chartData['dates'][] = $row['cCO2_date'];
                        $chartData['carbons'][] = $row['total_carbon'];
                        $chartData['cars'][] = $row['cc_name'];
                    }
                }

                echo "<script>
                    var dates = " . json_encode($chartData['dates']) . ";
                    var carbons = " . json_encode($chartData['carbons']) . ";
                    var cars = " . json_encode($chartData['cars']) . ";

                    var data = [];

                    var groupedData = {};
                    for (var i = 0; i < dates.length; i++) {
                        if (!groupedData[cars[i]]) {
                            groupedData[cars[i]] = { x: [], y: [] };
                        }
                        groupedData[cars[i]].x.push(dates[i]);
                        groupedData[cars[i]].y.push(carbons[i]);
                    }

                    for (var car in groupedData) {
                        data.push({
                            x: groupedData[car].x,
                            y: groupedData[car].y,
                            type: 'bar',
                            name: car
                        });
                    }

                    var layout = {
                        title: '" . $start_date."至".$end_date. " 交通車碳排量',
                        xaxis: {
                            title: '日期',
                            gridcolor: '#67776d'
                        },
                        yaxis: {
                            title: '碳排量 (kg)',
                            gridcolor: '#67776d'
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


            // 篩選員工
            if ((empty($start_date_display) && empty($end_date_display)) && empty($filter_car) && !empty($filter_employee)) {
                echo "<script>console.log($filter_employee)</script>";
                
                // 查詢月份、碳排量和員工姓名
                $chartQuery = "SELECT MONTH(cCO2_date) AS month, SUM(cCO2_carbon) AS total_carbon, employee.em_name
                            FROM cm_co2
                            INNER JOIN employee ON cm_co2.em_id = employee.em_id
                            WHERE cm_co2.em_id = '$filter_employee'
                            GROUP BY month, employee.em_name";
                            
                $chartResults = $db_handle->runQuery($chartQuery);
                
                // 預設圖表資料
                $chartData = [
                    'months' => [],
                    'carbons' => []
                ];
                
                // 員工姓名變數
                $employeeName = '';

                // 檢查查詢結果
                if (!empty($chartResults)) {
                    foreach ($chartResults as $row) {
                        $chartData['months'][] = $row['month'];
                        $chartData['carbons'][] = $row['total_carbon'];
                        $employeeName = $row['em_name']; // 獲取員工姓名
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
                        title: '$employeeName 的每年碳排量', // 使用員工姓名作為圖表標題
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


            // 篩選交通車
            if ((empty($start_date_display) && empty($end_date_display)) && !empty($filter_car) && empty($filter_employee)) {
                // 查詢數據並檢索交通車名稱
                $chartQuery = "SELECT MONTH(cCO2_date) AS month, SUM(cCO2_carbon) AS total_carbon, cm_car.cc_name
                            FROM cm_co2
                            INNER JOIN cm_car ON cm_co2.cc_id = cm_car.cc_id
                            WHERE cm_co2.cc_id = '$filter_car'
                            GROUP BY month, cm_car.cc_name";
                            
                $chartResults = $db_handle->runQuery($chartQuery);
                
                // 準備圖表數據
                $chartData = [
                    'months' => [],
                    'carbons' => []
                ];

                // 初始化交通車名稱變數
                $carName = '';

                // 處理查詢結果
                if (!empty($chartResults)) {
                    foreach ($chartResults as $row) {
                        $chartData['months'][] = $row['month'];
                        $chartData['carbons'][] = $row['total_carbon'];
                        $carName = $row['cc_name']; // 提取交通車名稱
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
                        title: '交通車 $carName 每年碳排量', // 使用交通車名稱替代 ID
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


        // 篩選日期、篩選交通車
        if ((!empty($start_date_display) && !empty($end_date_display)) && !empty($filter_car) && empty($filter_employee)) {
            $start_date = $start_date_display;
            $end_date = $end_date_display;
            
            // 查詢數據並檢索交通車名稱
            $chartQuery = "SELECT MONTH(cCO2_date) AS month, SUM(cCO2_carbon) AS total_carbon, cm_car.cc_name
                        FROM cm_co2
                        INNER JOIN cm_car ON cm_co2.cc_id = cm_car.cc_id
                        WHERE cm_co2.cc_id = '$filter_car' AND cCO2_date BETWEEN '$start_date' AND '$end_date'
                        GROUP BY month, cm_car.cc_name";

            $chartResults = $db_handle->runQuery($chartQuery);
            
            // 準備圖表數據
            $chartData = [
                'months' => [],
                'carbons' => []
            ];

            // 初始化交通車名稱變數
            $carName = '';

            // 處理查詢結果
            if (!empty($chartResults)) {
                foreach ($chartResults as $row) {
                    $chartData['months'][] = $row['month'];
                    $chartData['carbons'][] = $row['total_carbon'];
                    $carName = $row['cc_name']; // 提取交通車名稱
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
                    title: '$start_date 至 $end_date 交通車 $carName 碳排量', // 使用交通車名稱替代 ID
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
        if ((!empty($start_date_display) && !empty($end_date_display)) && empty($filter_car) && !empty($filter_employee)) {
            $start_date = $start_date_display;
            $end_date = $end_date_display;
            
            // 查詢數據並檢索員工姓名
            $chartQuery = "SELECT cCO2_date, cm_car.cc_name, SUM(cCO2_carbon) AS total_carbon, employee.em_name
                        FROM cm_co2
                        INNER JOIN cm_car ON cm_co2.cc_id = cm_car.cc_id
                        INNER JOIN employee ON cm_co2.em_id = employee.em_id
                        WHERE cCO2_date BETWEEN '$start_date' AND '$end_date' AND cm_co2.em_id = '$filter_employee'
                        GROUP BY cCO2_date, cm_car.cc_name, employee.em_name";
                        
            $chartResults = $db_handle->runQuery($chartQuery);
            
            // 準備圖表數據
            $chartData = [
                'dates' => [],
                'carbons' => [],
                'cars' => []
            ];

            // 初始化員工姓名變數
            $employeeName = '';

            // 處理查詢結果
            if (!empty($chartResults)) {
                foreach ($chartResults as $row) {
                    $chartData['dates'][] = $row['cCO2_date'];
                    $chartData['carbons'][] = $row['total_carbon'];
                    $chartData['cars'][] = $row['cc_name'];
                    $employeeName = $row['em_name']; // 提取員工姓名
                }
            }

            // 渲染圖表
            echo "<script>
                var dates = " . json_encode($chartData['dates']) . ";
                var carbons = " . json_encode($chartData['carbons']) . ";
                var cars = " . json_encode($chartData['cars']) . ";

                var data = [{
                    x: cars,
                    y: carbons,
                    type: 'bar',
                    name: '碳排量 (kg)',
                    marker: {
                        color: '#FF6384'
                    },
                    text: dates,
                    hoverinfo: 'x+text+y'
                }];

                var layout = {
                    title: '$start_date 至 $end_date 員工 $employeeName 的碳排量', // 使用員工姓名替代 ID
                    xaxis: {
                        title: '交通車',
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


            // 篩選交通車、篩選員工
            if ((empty($start_date_display) && empty($end_date_display)) && !empty($filter_car) && !empty($filter_employee)) {
            
                // 查询按月份汇总的碳排量
                $chartQuery = "SELECT MONTH(cCO2_date) AS month, SUM(cCO2_carbon) AS total_carbon
                            FROM cm_co2
                            WHERE cm_co2.cc_id = '$filter_car' AND cm_co2.em_id = '$filter_employee'
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
                        title: '交通車 $filter_car 員工 $filter_employee 的碳排量',
                        xaxis: {
                            title: '月份',
                            gridcolor: '#67776d'
                        },
                        yaxis: {
                            title: '碳排量 (kg)',
                            gridcolor: '#67776d'
                        },
                        // 设置绘图区域背景颜色
                        plot_bgcolor: '#e2f7ea',
                        // 设置整个图表背景颜色
                        paper_bgcolor: '#e2f7ea',
                    };

                    Plotly.newPlot('filteredBarChart', data, layout);
                </script>";
            }
                
            

          // 篩選日期、篩選交通車、篩選員工
            if ((!empty($start_date_display) && !empty($end_date_display)) && !empty($filter_car) && !empty($filter_employee)) {
                $start_date = $start_date_display;
                $end_date = $end_date_display;

                // 修改查詢以包括交通車名稱和員工名稱
                $chartQuery = "SELECT cCO2_date, SUM(cCO2_carbon) AS total_carbon, 
                            cm_car.cc_name, employee.em_name 
                            FROM cm_co2 
                            INNER JOIN cm_car ON cm_co2.cc_id = cm_car.cc_id 
                            INNER JOIN employee ON cm_co2.em_id = employee.em_id 
                            WHERE cCO2_date BETWEEN '$start_date' AND '$end_date' 
                            AND cm_co2.cc_id = '$filter_car' 
                            AND cm_co2.em_id = '$filter_employee' 
                            GROUP BY cCO2_date";

                // 調試輸出
                echo "<script>console.log('$chartQuery')</script>";
                
                $chartResults = $db_handle->runQuery($chartQuery);
                $chartData = [
                    'dates' => [],
                    'carbons' => []
                ];

                // 準備數據
                if (!empty($chartResults)) {
                    foreach ($chartResults as $row) {
                        $chartData['dates'][] = $row['cCO2_date'];
                        $chartData['carbons'][] = $row['total_carbon'];
                        // 取得交通車名稱和員工名稱
                        $car_name = $row['cc_name'];
                        $employee_name = $row['em_name'];
                    }
                }

                // 設定圖表資料
                echo "<script>
                    var dates = " . json_encode($chartData['dates']) . ";
                    var carbons = " . json_encode($chartData['carbons']) . ";

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
                        title: '" . $start_date . " 至 " . $end_date . " 交通車 " . $car_name . " 員工 " . $employee_name . " 的碳排量',
                        xaxis: {
                            title: '日期',
                            gridcolor: '#67776d'
                        },
                        yaxis: {
                            title: '碳排量 (kg)',
                            gridcolor: '#67776d'
                        },
                        // 设置绘图区域背景颜色
                        plot_bgcolor: '#e2f7ea',
                        // 设置整个图表背景颜色
                        paper_bgcolor: '#e2f7ea',
                    };

                    Plotly.newPlot('filteredBarChart', data, layout);
                </script>";
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
            </script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
        </body>
    </html>