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


<!DOCTYPE html>
<html>
<head>
    <title>交通車碳排紀錄</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css1.css" type="text/css">
    <link rel="shortcut icon" href="img/logo.png">
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr@4.6.3/dist/l10n/zh-tw.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
   
</head>
<body class="body1">
    <a href="#" class="back-to-top">︽</a>
    <!-- 上方工作列 -->
    <header id="置中">
            <a href="cm_index.php"><img src="img\logo.png" class="logo"></a>
            <ul class="drop-down-menu">
                <li>
                    <a class="li1" href="cm_index.php" id="置中">
                        <img src="img\home.png" class="home">&nbsp管理者首頁
                    </a>
                </li>
                <li><a class="li1" href="cm_employee.php" id="置中">員工資料</a>
                </li>
                <li><a class="li1" href="cm_car.php" id="置中">交通車資料</a>
                    <ul>
                        <a href="cm_manage_car.php"><li>管理交通車</li></a>                
                        <!-- <a href="cm_add_car.php"><li>新增交通車</li></a> -->
                    </ul>
                </li>
                <li><a class="li2" id="置中">碳排紀錄</a>
                    <ul>
                        <a href="cm_c_co2.php"><li>交通車碳排紀錄</li></a>                  
                        <a href="cm_e_co2.php"><li>員工碳排紀錄</li></a>
                    </ul>
                </li>
                <li><a href="#" class="li1" onclick="openContactForm()" id="置中">回報問題</a></li>
                <?php
                if(isset($_SESSION['em_name'])){
                    $user_name = $_SESSION['em_name'];
                    echo "<li><a class='li1_user_name' href='#'>" . $user_name . "</a>";
                    echo "<ul>";
                ?>
                <button class="index" onclick="window.location.href='em_index.php'">員工首頁</button>
                <button class="index" onclick="window.location.href='cm_index.php'">管理者首頁</button>
                <?php
                    echo "<form method='post'>";
                    echo "<input type='submit' name='logout' data-style='logout_submit' value='登出'>";
                    echo "</form>"; 
                    echo "</ul></li>";
                }
                else{
                    echo "<li><a>XXX</a></li>";
                }

                if (isset($_POST["logout"])) {
                    include_once('inc\log_out.inc');
                }
                ?>
            </ul>

            <!-- 回報問題視窗 -->
            <div id="contactForm" class="contact-form" style="display: none;">
                <span class="close-btn" onclick="closeContactForm()">&times;</span>
                <a class="contact_title">回報問題</a>
                <hr class="contact_hr">
                <form id="form" method="post" onsubmit="return ContactFormSuccess();">
                <!-- <div class="contactForm_div"> -->
                    <label class="contactForm_label" for="sender">電子信箱：</label>
                    <?php
                    echo "<a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp" . $_SESSION['em_email'] . "</>";
                    ?>

                    <label class="contactForm_label" for="message">新增留言：</label>
                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                    <textarea class="contactForm_message" id="message" name="message" rows="4" required></textarea>
                    <br>

                    <div id="置中">
                        <input type="submit" name="contact" data-style='submit1' value="送出">
                    </div>
                    
                    <?php
                    try {
                        if (isset($_POST["contact"])) {
                            include_once('inc\message.inc');
                        }
                    } catch (Exception $e) {
                        // 
                    }
                    ?>
                <!-- </div> -->
                    
                </form>
            </div>
        </header>

    <div class="top-banner2">
        <div class="c_left_side">
            <div id="filteredBarChart" class="filteredBarChart"></div>
        </div>

        <div class="right-side">
            <div class="table-container2">
                <form method="post" class="filter-form">
                    <!-- 筛选日期范围隐藏字段 -->
                    <!-- <input type="hidden" name="filter_date" id="filter_date" value=""> -->
                    <label for="date_range">日期：</label>
                    <input type="text" id="date_range" name="date_range" class="date-range-picker" placeholder="選擇日期">

                    <label for="filter_car">交通車：</label>
                    <select id="filter_car" name="filter_car">
                        <option value="">請選擇</option>
                        <?php
                        include_once("dbcontroller.php");
                        $db_handle = new DBController();

                        $sql = "SELECT cc_id, cc_name FROM cm_car";
                        $result = $db_handle->runQuery($sql);

                        if (!empty($result)) {
                            foreach ($result as $row) {
                                echo "<option value='" . $row['cc_name'] . "'>" . $row['cc_name'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>沒有資料</option>";
                        }
                        ?>
                    </select>

                    <label for="filter_employee">員工：</label>
                    <select id="filter_employee" name="filter_employee">
                        <option value="">請選擇</option>
                        <?php
                        $sql = "SELECT em_id, em_name FROM employee";
                        $result = $db_handle->runQuery($sql);

                        if (!empty($result)) {
                            foreach ($result as $row) {
                                echo "<option value='" . $row['em_name'] . "'>" . $row['em_name'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>沒有資料</option>";
                        }
                        ?>
                    </select>
                    
                    <input type="submit" name="apply_filter" value="確認篩選" data-style="apply_filter">
                </form>
            </div>

            <div class="information" id="文字置中">
                <!-- 新的长条图容器 -->
                <table>
                    <thead>
                        <tr>
                            <th>交通車名稱</th>
                            <th>產生碳排日期</th>
                            <th>產生的碳排量</th>
                            <th>員工姓名</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
    include_once("dbcontroller.php");
    $db_handle = new DBController();

    if (isset($_POST['apply_filter'])) {
        $date_range = $_POST['date_range'];  // 从隐藏字段获取日期范围
        $filter_car = $_POST['filter_car'];
        $filter_employee = $_POST['filter_employee'];

        $query = "SELECT DISTINCT cm_co2.cc_id, cm_car.cc_name, cm_co2.cCO2_date, cm_co2.cCO2_start_time, cm_co2.cCO2_end_time, cm_co2.cCO2_carbon, cm_car.cc_type, employee.em_id, employee.em_name, cm_co2.cCO2_address
                  FROM cm_co2
                  INNER JOIN employee ON cm_co2.em_id = employee.em_id
                  INNER JOIN cm_car ON cm_co2.cc_id = cm_car.cc_id
                  WHERE 1=1";

        if (!empty($date_range)) {
            if (strpos($date_range, " 至 ") !== false) {
                list($filter_start_date, $filter_end_date) = explode(" 至 ", $date_range);
            } else {
                // 如果没有分隔符，将开始日期和结束日期都设置为同一日期
                $filter_start_date = $filter_end_date = $date_range;
            }
            $query .= " AND cm_co2.cCO2_date BETWEEN '$filter_start_date' AND '$filter_end_date'";
        }

        if (!empty($filter_car)) {
            $sql = "SELECT cc_id FROM cm_car WHERE cc_name = '$filter_car'";
            $result = $db_handle->runQuery($sql);

            if (!empty($result)) {
                $filter_car_id = $result[0]['cc_id'];
                $query .= " AND cm_co2.cc_id = '$filter_car_id'";
            }
        }

        if (!empty($filter_employee)) {
            $sql = "SELECT em_id FROM employee WHERE em_name = '$filter_employee'";
            $result = $db_handle->runQuery($sql);

            if (!empty($result)) {
                $filter_employee_id = $result[0]['em_id'];
                $query .= " AND cm_co2.em_id = '$filter_employee_id'";
            }
        }
    } else {
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
                <br>
                <div class="pagination">
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

                    <br><br><br><br>
            </div>
        </div>
    </div>
        

                        
                  
<?php        
// 篩選日期範圍
if (!empty($date_range) && empty($filter_car) && empty($filter_employee)) {

    list($start_date, $end_date) = explode(' 至 ', $date_range);

    echo "<script>console.log($date_range);</script>";
    
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
            title: '" . $date_range . " 交通車碳排量',
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

//篩選員工
if (empty($date_range) && empty($filter_car) && !empty($filter_employee)) {
    $chartQuery = "SELECT MONTH(cCO2_date) AS month, SUM(cCO2_carbon) AS total_carbon
                   FROM cm_co2
                   INNER JOIN employee ON cm_co2.em_id = employee.em_id
                   WHERE employee.em_name = '$filter_employee'
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
            title: '$filter_employee 的每年碳排量',
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

//篩選交通車
if (empty($date_range) && !empty($filter_car) && empty($filter_employee)) {
    $chartQuery = "SELECT MONTH(cCO2_date) AS month, SUM(cCO2_carbon) AS total_carbon
                   FROM cm_co2
                   WHERE cc_id = '$filter_car_id'
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
            title: '交通車 $filter_car 每年碳排量',
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

// 篩選日期、篩選交通車
if (!empty($date_range) && !empty($filter_car) && empty($filter_employee)) {
    list($start_date, $end_date) = explode(' 至 ', $date_range);
    $chartQuery = "SELECT MONTH(cCO2_date) AS month, SUM(cCO2_carbon) AS total_carbon
                   FROM cm_co2
                   WHERE cc_id = '$filter_car_id' AND cCO2_date BETWEEN '$start_date' AND '$end_date'
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
            title: '$start_date 交通車 $filter_car 碳排量',
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

// 篩選日期、篩選員工
if (!empty($date_range) && empty($filter_car) && !empty($filter_employee)) {
    list($start_date, $end_date) = explode(' 至 ', $date_range);
    $chartQuery = "SELECT cCO2_date, cm_car.cc_name, SUM(cCO2_carbon) AS total_carbon
                   FROM cm_co2
                   INNER JOIN cm_car ON cm_co2.cc_id = cm_car.cc_id
                   INNER JOIN employee ON cm_co2.em_id = employee.em_id
                   WHERE cCO2_date BETWEEN '$start_date' AND '$end_date' AND employee.em_name = '$filter_employee'
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
            title: '$date_range 員工 $filter_employee 的碳排量',
            xaxis: {
                title: '交通車',
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

// 篩選交通車、篩選員工
if (empty($date_range) && !empty($filter_car) && !empty($filter_employee)) {
    // 获取交通车ID
    $sql = "SELECT cc_id FROM cm_car WHERE cc_name = '$filter_car'";
    $result = $db_handle->runQuery($sql);

    if (!empty($result)) {
        $filter_car_id = $result[0]['cc_id'];

        // 获取员工ID
        $sql = "SELECT em_id FROM employee WHERE em_name = '$filter_employee'";
        $result = $db_handle->runQuery($sql);

        if (!empty($result)) {
            $filter_employee_id = $result[0]['em_id'];

            // 查询按月份汇总的碳排量
            $chartQuery = "SELECT MONTH(cCO2_date) AS month, SUM(cCO2_carbon) AS total_carbon
                           FROM cm_co2
                           WHERE cm_co2.cc_id = '$filter_car_id' AND cm_co2.em_id = '$filter_employee_id'
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
    }
}

// 篩選日期、篩選交通車、篩選員工
if (!empty($date_range) && !empty($filter_car) && !empty($filter_employee)) {
    list($start_date, $end_date) = explode(' 至 ', $date_range);

    $chartQuery = "SELECT cCO2_date, SUM(cCO2_carbon) AS total_carbon
                   FROM cm_co2
                   WHERE cCO2_date BETWEEN '$start_date' AND '$end_date' AND cc_id = '$filter_car' AND em_name = '$filter_employee'
                   GROUP BY cCO2_date";
    $chartResults = $db_handle->runQuery($chartQuery);
    $chartData = [
        'dates' => [],
        'carbons' => []
    ];

    if (!empty($chartResults)) {
        foreach ($chartResults as $row) {
            $chartData['dates'][] = $row['cCO2_date'];
            $chartData['carbons'][] = $row['total_carbon'];
        }
    }

    echo "<script>
        var dates = " . json_encode($chartData['dates']) . ";
        var carbons = " . json_encode($chartData['carbons']) . ";

        var data = [{
            x: dates,
            y: carbons,
            type: 'bar',
            name: '" . $date_range . " " . $filter_car . " " . $filter_employee . " 的碳排量',
            marker: {
                color: '#FF6384'
            }
        }];

        var layout = {
            title: '" . $date_range . " 交通車" . $filter_car . " 員工" . $filter_employee . " 的碳排量',
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


<?php if (isset($_POST['apply_filter']) && !empty($result)){ ?>
    <script>
        // 获取 PHP 生成的日期、碳排量和车辆数据
        var dates = <?php echo json_encode(array_column($result, 'cCO2_date')); ?>;
        var carbons = <?php echo json_encode(array_column($result, 'cCO2_carbon')); ?>;
        var cars = <?php echo json_encode(array_column($result, 'cc_name')); ?>;

        // 调用渲染图表的函数
        renderChart(dates, carbons, cars, "<?php echo $filter_start_date; ?>", "<?php echo $filter_end_date; ?>");

        function renderChart(dates, carbons, cars, startDate, endDate) {
            var data = [];
            var groupedData = {};
            var colors = ['#ff8263', '#ffd063', '#f7ff63', '#63ff87', '#63ffea', '#639aff', '#c363ff']; // 你可以添加更多颜色
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
        }
    </script>
<?php } ?>

  
   

    <!-- 日期範圍選擇器腳本 -->
    <script>
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            "locale": "zh_tw", // 设置为中文本地化
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

            start.setDate(now.getDate() - dayOfWeek + 1); // 設定到本周的星期一
            end.setDate(now.getDate() + (7 - dayOfWeek)); // 設定到本周的星期日

            return [start, end];
        }

        function getMonthRange() {
            const now = new Date();
            const start = new Date(now.getFullYear(), now.getMonth(), 1); // 設定到本月的第一天
            const end = new Date(now.getFullYear(), now.getMonth() + 1, 0); // 設定到本月的最後一天

            return [start, end];
        }
    </script>
</body>
</html>