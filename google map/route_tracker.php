<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>路線追蹤器</title>

    <link href="route_tracker.css" rel="stylesheet"> <!-- 引入外部 CSS 文件 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- 引入 Bootstrap 框架的 CSS 文件 -->
</head>
<body>
    <div class="container text-center mt-4">
        <div class="row justify-content-md-center">
            <div class="col-12 col-md-8 title_text align-items-center">
                <h1 class="fw-bold">路線追蹤器</h1>
            </div>
        </div>
        <div class="row justify-content-md-center mt-2">
            <div class="col-12 col-md-8">
                <div id="dateTime">現在時間：--:--:--</div>
            </div>
        </div>
        <div class="row justify-content-md-center">
            <div class="col-12">
                <div class="map m-3 mt-2" id="map"></div>
            </div>
        </div>

        <?php
        $link = mysqli_connect("localhost", "root", "A12345678") 
        or die("無法開啟 MySQL 資料庫連結!<br>");
        mysqli_select_db($link, "carbon_emissions");

        // 選擇員工
        $sql_employee = "SELECT em_id, em_name FROM employee";
        mysqli_query($link, "SET NAMES utf8");

        $result_employee = mysqli_query($link, $sql_employee);
        $fields_employee = mysqli_num_fields($result_employee); //取得欄位數
        $rows_employee = mysqli_num_rows($result_employee); //取得記錄數


        // 選擇交通車
        $sql_car = "SELECT cc_name, cc_type FROM cm_car";
        mysqli_query($link, "SET NAMES utf8");

        $result_car = mysqli_query($link, $sql_car);
        $fields_car = mysqli_num_fields($result_car); //取得欄位數
        $rows_car = mysqli_num_rows($result_car); //取得記錄數
        ?>
                
        <div class="row justify-content-md-center">
            <!-- 選擇員工 -->
            <div class="col-11 col-md-5">
                <div class="m-2">
                    <label class="label_box fs-5" for="transportMode">出勤員工：</label>
                    &nbsp
                    <select class="select_box" id="transportMode" name="transportMode" required>
                        <option value="">選擇員工</option>
                        <?php
                        while ($rows_employee = mysqli_fetch_array(result: $result_employee)){
                            echo "<option value='" . $rows_employee[0] . "'>" . $rows_employee[1] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- 選擇交通車 -->
            <div class="col-11 col-md-5">
                <div class="m-2">
                    <label class="label_box fs-5" for="transportMode">出勤交通車：</label>
                    &nbsp
                    <select class="select_box" id="transportMode" name="transportMode" required>
                        <option value="">選擇交通車</option>
                        <?php
                        while ($rows_car = mysqli_fetch_array($result_car)){
                            echo "<option value='" . $rows_car[1] . "'>" . $rows_car[0] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <!-- 選擇交通車 -->
            <div class="col-11 col-md-5">
                <div class="m-2">
                    <label class="label_box fs-5" for="transportMode">出勤交通車：</label>
                    &nbsp
                    <select class="select_box" id="transportMode" name="transportMode" required>
                        <option value="">選擇交通車</option>
                        <?php
                        while ($rows_car = mysqli_fetch_array($result_car)){
                            echo "<option value='" . $rows_car[1] . "'>" . $rows_car[0] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row justify-content-md-center text-center fs-5 mt-3 distance_and_time">
            <div class="col-5 col-md-5">
                <div>
                    距離：<span id="distance">0</span>
                </div>
            </div>
            <div class="col-5 col-md-5">
                <div>
                    時間：<span id="time">0</span>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div>
                <button class="button_box" id="startBtn">開始</button>
                <button class="button_box" id="stopBtn">結束</button>
                <button class="button_box" id="exportBtn">匯出</button>
            </div>
        </div>
        
    </div>
    

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBE5jpz-2b9N-tE1DIKCtneSgZ9nXn3jxM&callback=initMap" async defer></script>
    <script src="route_tracker.js"></script>
    <!-- 引入 Bootstrap JS（包含 Popper.js） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
