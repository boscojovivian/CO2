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

// 確定當前頁數和每頁顯示的記錄數
$records_per_page = 20;
$pages = isset($_GET['pages']) && is_numeric($_GET['pages']) ? intval($_GET['pages']) : 1;
$offset = ($pages - 1) * $records_per_page;
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>交通車資料</title>
        <link rel="stylesheet" href="css/cm_car.css" type="text/css">
        <link rel="shortcut icon" href="img/logo.png" >
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

        <div class="container">
            <!-- 篩選列 -->
            <form action="" method="post" class="row g-3 d-flex align-items-center filter-form">
                <!-- 篩選日期 -->
                <div class="col-12 col-lg-6 col-xl-4 d-flex align-items-center">
                    <label for="date_range">篩選日期：</label>
                    <input type="text" id="date_range" name="date_range" class="date-range-picker" placeholder="選擇日期">
                </div>

                <!-- 篩選交通車 -->
                <div class="col-12 col-lg-6 col-xl-3 d-flex justify-content-center align-items-center">
                    <label for="filter_car">篩選交通車：</label>
                    <select id="filter_car" name="filter_car">
                        <option value="">請選擇</option>
                        <?php
                        include_once("dropdown_list/dbcontroller.php");
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
                </div>

                <!-- 篩選員工 -->
                <div class="col-12 col-lg-6 col-xl-3 d-flex justify-content-center align-items-center">
                    <label for="filter_employee">篩選員工：</label>
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
                </div>

                <!-- 確認篩選按鈕 --> 
                <div class="col-12 col-lg-6 col-xl-2 d-flex justify-content-center align-items-end">
                    <!-- type="submit"設定提交表單 -->
                    <button type="submit" class="btn btn-success btn-lg" name="apply_filter" data-style="apply_filter">確認篩選</button>
                </div>
            </form>

            <!-- 碳排表格 -->
            <div class="information">
                <table class="table table-bordered table-hover">
                    <thead class="table">
                        <tr>
                            <th>交通車名稱</th>
                            <th>車子的類型</th>
                            <th>員工姓名</th>
                            <th>產生碳排日期</th>
                            <th>產生碳排時間</th>
                            <th>產生的碳排量</th>
                            <th>路程</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include_once("dropdown_list/dbcontroller.php");
                        $db_handle = new DBController();

                        if (isset($_POST['apply_filter'])) {
                            $date_range = $_POST['date_range'];
                            $filter_car = $_POST['filter_car'];
                            $filter_employee = $_POST['filter_employee'];

                            $query = "SELECT DISTINCT cm_co2.cc_id, cm_car.cc_name, cm_co2.cCO2_date, cm_co2.cCO2_start_time, cm_co2.cCO2_end_time, cm_co2.cCO2_carbon, cm_car.cc_type, employee.em_id, employee.em_name, cm_co2.cCO2_address
                                    FROM cm_co2
                                    INNER JOIN employee ON cm_co2.em_id = employee.em_id
                                    INNER JOIN cm_car ON cm_co2.cc_id = cm_car.cc_id
                                    WHERE 1=1";

                            if (!empty($date_range)) {
                                list($filter_start_date, $filter_end_date) = explode(" 至 ", $date_range);
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
                            $query = "SELECT DISTINCT cm_co2.cc_id, cm_car.cc_name, cm_co2.cCO2_date, cm_co2.cCO2_start_time, cm_co2.cCO2_end_time, cm_co2.cCO2_carbon, cm_car.cc_type, employee.em_id, employee.em_name, cm_co2.cCO2_address
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
                                echo "<td>" . translateCarType($row["cc_type"]) . "</td>";
                                echo "<td>" . $row['em_name'] . "</td>";
                                echo "<td>" . $row['cCO2_date'] . "</td>";
                                echo "<td>" . $row['cCO2_start_time'] . " ~ " . $row['cCO2_end_time'] . "</td>";
                                echo "<td>" . $row['cCO2_carbon'] . "公克</td>";
                                echo "<td class='show-tooltip' data-tooltip='" . $address . "'>查看路程</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>沒有資料</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- 切換頁面按鈕 -->
            <?php
            echo "<div class='turn_pages_div'>";

            // 顯示最前面一頁和上一頁
            if ($pages > 1) {
                echo "<a class='turn_pages' href='cm_car.php?pages=1'><<</a> ";
                echo "<a class='turn_pages' href='cm_car.php?pages=" . ($pages - 1) . "'><</a> ";
            } else {
                echo "<a class='Noturn_pages'><<</a> ";
                echo "<a class='Noturn_pages'><</a> ";
            }

            // 確定分頁範圍
            $start_page = max(1, $pages - 4);
            $end_page = min($total_pages, $start_page + 8);

            if ($start_page > 1) {
                echo "<a class='turn_pages_more'> ...<a>";
            }

            for ($i = $start_page; $i <= $end_page; $i++) {
                if ($i != $pages) {
                    echo "<a class='turn_pages' href='cm_car.php?pages=" . $i . "' onclick='fetchData()'>" . $i . " </a>";
                } else {
                    echo "<a class='Noturn_pages'>" . $i . " </a>";
                }
            }

            if ($end_page < $total_pages) {
                echo "<a class='turn_pages_more'> ...<a>";
            }

            // 顯示下一頁和最後面一頁
            if ($pages < $total_pages) {
                echo "<a class='turn_pages' href='cm_car.php?pages=" . ($pages + 1) . "'>></a> ";
                echo "<a class='turn_pages' href='cm_car.php?pages=" . $total_pages . "'>>></a>";
            } else {
                echo "<a class='Noturn_pages'>></a> ";
                echo "<a class='Noturn_pages'>>></a>";
            }

            echo "</div>";
            ?>
        </div>

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
                "locale": "zh_tw", 
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    </body>
</html>