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
        <!-- 回到最頂端 -->
        <!-- <button onclick="topFunction()" class="topBtn" id="myBtn" title="GOtop">TOP</button> -->

        <!-- 導入導覽列 -->
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
                        <input type="date" id="start_date_display" name="start_date_display" class="date-range-picker col-5 ms-4" placeholder="開始日期">
                        <input type="date" id="end_date_display" name="end_date_display" class="date-range-picker col-5 ms-4" placeholder="結束日期">
                    </div>

                    <!-- 確認篩選按鈕 --> 
                    <div class="col-lg-2 d-flex align-items-center">
                        <!-- type="submit"設定提交表單 -->
                        <button type="submit" class="btn btn-success btn-lg" name="apply_filter" data-style="apply_filter">確認篩選</button>
                    </div>
                </div>
            </form>
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
                            <th>顯示路徑</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include_once("dropdown_list/dbcontroller.php");
                        $db_handle = new DBController();

                        if (isset($_POST['apply_filter'])) {
                            $start_date_display = $_POST['start_date_display'];
                            $end_date_display = $_POST['end_date_display'];

                            $query = "SELECT DISTINCT a.id, a.start_date, a.start_time, a.end_date, a.end_time, a.total_time, a.distance, a.file, a.car, a.type, (SELECT b.em_name FROM employee AS b WHERE a.employee_id = b.em_id) AS name
                                        FROM route_tracker AS a
                                        WHERE 1=1";

                            if (!empty($start_date_display || $end_date_display)) {
                                $filter_start_date = $start_date_display;
                                $filter_end_date = $end_date_display;
                                $query .= "AND (a.start_date BETWEEN '$filter_start_date' AND '$filter_end_date') OR (a.end_date BETWEEN '$filter_start_date' AND '$filter_end_date')";
                            }
                        }
                        else {
                            $query = "SELECT DISTINCT a.id, a.start_date, a.start_time, a.end_date, a.end_time, a.total_time, a.distance, a.file, a.car, a.type, (SELECT b.em_name FROM employee AS b WHERE a.employee_id = b.em_id) AS name
                                        FROM route_tracker AS a";
                        }

                        // 獲取總記錄數
                        $total_records_query = "SELECT COUNT(*) as total FROM ($query) as temp_table";
                        $total_records_result = $db_handle->runQuery($total_records_query);
                        $total_records = $total_records_result[0]['total'];
                        $total_pages = ceil($total_records / $records_per_page);

                        // 添加LIMIT子句到查詢
                        $query .= " ORDER BY a.start_date DESC LIMIT $offset, $records_per_page";
                        $result = $db_handle->runQuery($query);

                        if (!empty($result)) {
                            foreach ($result as $row) {
                                echo "<tr>";
                                echo "<td>" . $row['name'] . "</td>";
                                if ($row["start_date"] == $row["end_date"]){
                                    $date = $row["start_date"];
                                }
                                else {
                                    $date = $row["start_date"] . " ~ " . $row["end_date"];
                                }
                                echo "<td>" . $date . "</td>";
                                echo "<td>" . $row['start_time'] . " ~ " . $row['end_time'] . "</td>";
                                echo "<td>" . $row['total_time'] . "</td>";
                                echo "<td>" . $row['distance'] . "</td>";
                                if ($row["car"] == "is_cm_car"){
                                    $car = "類別一";
                                }
                                else {
                                    $car = "類別三";
                                }
                                echo "<td>" . $car . "</td>";
                                echo "<td>" . $row['type'] . "</td>";
                                echo "<td>
                                        <form action='get_route_back_show.php' method='GET'>
                                            <button type='submit' name='get_route_back_show' value='" . $row['id'] . "'>顯示路徑</button>
                                        </form>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>沒有資料</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        


    </body>
</html>