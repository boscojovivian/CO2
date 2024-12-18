<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}

// 確定當前頁數和每頁顯示的記錄數
$records_per_page = 20;
$pages = isset($_GET['pages']) && is_numeric($_GET['pages']) ? intval($_GET['pages']) : 1;
$offset = ($pages - 1) * $records_per_page;
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>出勤紀錄</title>
        <link rel="shortcut icon" href="img/logo.png">
        <link rel="stylesheet" href="css/get_route_back.css" type="text/css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- 引入 Bootstrap 框架的 CSS 文件 -->
    </head>
    <body>
        <?php include('nav/cm_nav.php') ?>

        <!-- <div id="map" style="height: 500px;"></div> -->

        <div class="container">
            <div class="title1 mt-1">出勤紀錄</div>
           <!-- 篩選列 -->
<form action="" method="post" class="row g-3 d-flex justify-content-center align-items-center filter-form">
    <div class="row w-100">
        <!-- 篩選日期 -->
        <div class="col-lg-10 d-flex align-items-center">
            <label for="date_range">篩選日期：</label>
            <input type="date" id="start_date_display" name="start_date_display" class="date-range-picker col-5 ms-4" placeholder="開始日期" value="<?php echo isset($_POST['start_date_display']) ? htmlspecialchars($_POST['start_date_display']) : ''; ?>">
            <input type="date" id="end_date_display" name="end_date_display" class="date-range-picker col-5 ms-4" placeholder="結束日期" value="<?php echo isset($_POST['end_date_display']) ? htmlspecialchars($_POST['end_date_display']) : ''; ?>">
        </div>
        
 <!-- 篩選類別 -->
 <div class="col-lg-10 d-flex align-items-center">
                        <label for="co2_type">篩選類別：</label>
                        <select id="co2_type" name="co2_type">
                            <option value="">請選擇</option>
                            <?php
                            include_once("dropdown_list/dbcontroller.php");
                            $db_handle = new DBController();

                            // 查詢資料庫中的 `car` 欄位值
                            $sql = "SELECT DISTINCT car FROM route_tracker";
                            $result = $db_handle->runQuery($sql);

                            // 根據 `car` 欄位值顯示對應的選項名稱
                            if (!empty($result)) {
                                foreach ($result as $row) {
                                    $car_value = $row['car'];
                                    // 設定顯示名稱
                                    $display_name = ($car_value == 'is_cm_car') ? '類別一' : (($car_value == 'not_cm_car') ? '類別三' : '');
                                    
                                    if ($display_name) {
                                        echo "<option value='$car_value'>$display_name</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>




        <!-- 確認篩選按鈕 --> 
        <div class="col-lg-2 d-flex align-items-center">
            <button type="submit" class="btn btn-success btn-lg" name="apply_filter" data-style="apply_filter">確認篩選</button>
        </div>
    </div>
</form>

            <div class="information">
            <?php
                $pages = isset($_GET['pages']) ? (int)$_GET['pages'] : 1;
                $records_per_page = 10; 
                $offset = ($pages - 1) * $records_per_page;

                // 建立查詢語句
                $query = "
                    SELECT 
                        cCO2_id,
                        employee_cm AS name,
                        start_date,
                        start_time,
                        total_time,
                        distance,
                        car, -- 對應碳排放類別
                        type, -- 對應交通工具
                        tatol_carboon AS carbon,
                        carboon_money,
                        place
                    FROM cm_thtf
                    WHERE 1=1
                ";

                // 加入篩選條件
                if (isset($_POST['apply_filter'])) {
                    $start_date_display = $_POST['start_date_display'];
                    $end_date_display = $_POST['end_date_display'];
                    $co2_type = $_POST['co2_type'];

                    if (!empty($start_date_display)) {
                        $query .= " AND DATE(start_date) >= '$start_date_display'";
                    }
                    if (!empty($end_date_display)) {
                        $query .= " AND DATE(start_date) <= '$end_date_display'";
                    }
                    if (!empty($co2_type)) {
                        $query .= " AND type = '$co2_type'"; // 修改篩選條件對應交通工具
                    }
                }

                // 查詢總記錄數
                $total_records_query = "SELECT COUNT(*) as total FROM ($query) as temp_table";
                $total_records_result = $db_handle->runQuery($total_records_query);
                $total_records = $total_records_result[0]['total'] ?? 0;
                $total_pages = ceil($total_records / $records_per_page);

                // 加入排序和分頁
                $query .= " ORDER BY start_date DESC, start_time DESC LIMIT $offset, $records_per_page";

                // 執行查詢
                $result = $db_handle->runQuery($query);
                ?>

                <div class="information">
                    <table class="table table-bordered table-hover">
                        <thead class="table">
                            <tr>
                                <th>員工</th>
                                <th>日期</th>
                                <th>時間</th>
                                <th>總時長</th>
                                <th>總距離</th>
                                <th>碳排放類別</th>
                                <th>交通工具</th>
                                <th>碳排量</th>
                                <th>碳費</th>
                                <th>顯示路徑</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                if (!empty($result)) {
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['total_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['distance']) . " 公里</td>";
                        
                        // 碳排放類別（來自 car 欄位）
                        echo "<td>" . ($row['car'] == 'is_cm_car' ? '類別一' : '類別三') . "</td>";
                        
                        // 交通工具（直接顯示 type 欄位值）
                        echo "<td>" . htmlspecialchars($row['type']) . "</td>";

                        echo "<td>" . htmlspecialchars($row['carbon']) . "</td>";
                        echo "<td>" . number_format($row['carboon_money'], 4) . "</td>";
                        echo "<td>
                                <form action='get_route_back_show.php' method='GET'>
                                    <button class='show_route_btn m-1' type='submit' name='get_route_back_show' value='" . htmlspecialchars($row['cCO2_id']) . "'>顯示路徑</button>
                                </form>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center'>未找到符合條件的紀錄</td></tr>";
                }
                ?>
</tbody>




                </table>
            </div>

            <!-- 分頁按鈕 -->
            <div class="turn_pages_div">
                <?php
                if ($pages > 1) {
                    echo "<a class='turn_pages' href='?pages=1'><<</a> ";
                    echo "<a class='turn_pages' href='?pages=" . ($pages - 1) . "'><</a> ";
                } else {
                    echo "<a class='Noturn_pages'><<</a> ";
                    echo "<a class='Noturn_pages'><</a> ";
                }

                $start_page = max(1, $pages - 4);
                $end_page = min($total_pages, $pages + 4);

                if ($start_page > 1) {
                    echo "<a class='turn_pages_more'> ...</a>";
                }

                for ($i = $start_page; $i <= $end_page; $i++) {
                    if ($i == $pages) {
                        echo "<a class='Noturn_pages'>" . $i . " </a>";
                    } else {
                        echo "<a class='turn_pages' href='?pages=" . $i . "'>" . $i . " </a>";
                    }
                }

                if ($end_page < $total_pages) {
                    echo "<a class='turn_pages_more'> ...</a>";
                }

                if ($pages < $total_pages) {
                    echo "<a class='turn_pages' href='?pages=" . ($pages + 1) . "'>></a> ";
                    echo "<a class='turn_pages' href='?pages=" . $total_pages . "'>>></a>";
                } else {
                    echo "<a class='Noturn_pages'>></a> ";
                    echo "<a class='Noturn_pages'>>></a>";
                }
                ?>
            </div>


        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
        <script>
            i// 初始化所有带有 'data-tooltip' 的元素的 Tooltip
            document.addEventListener('DOMContentLoaded', function () {
                var tooltipElements = document.querySelectorAll('[data-tooltip]');
                tooltipElements.forEach(function (element) {
                    if (!element.tooltipInitialized) { // 確保初始化一次
                        new bootstrap.Tooltip(element, {
                            title: element.getAttribute('data-tooltip'),
                            placement: 'top'
                        });
                        element.tooltipInitialized = true; // 標記為已初始化
                    }
                });
            });

        </script>


    </body>
</html>