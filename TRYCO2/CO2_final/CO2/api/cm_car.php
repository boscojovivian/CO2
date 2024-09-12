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

<!DOCTYPE html>
<html>
<head>
    <title>交通車資料</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css1.css" type="text/css">
    <link rel="shortcut icon" href="img/logo.png">
    <script src="js.js"></script>    

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
    </body> 
    <div class="table-container2">
        <form method="post" class="filter-form">
            <label for="date_range">篩選日期：</label>
            <input type="text" id="date_range" name="date_range" class="date-range-picker" placeholder="選擇日期">

            <label for="filter_car">篩選交通車：</label>
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
            <input type="submit" name="apply_filter" value="確認篩選" data-style="apply_filter">
        </form>

        <!-- 碳排表格 -->
        <div class="information">
            <table>
                <thead>
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
                    include_once("dbcontroller.php");
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

        <br>
        
            <!-- 切換頁面按鈕 -->
            <?php
            echo "<div class='turn_pages_div' id='置中'>";

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
    </div>

    <br><br><br>

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
</html>
