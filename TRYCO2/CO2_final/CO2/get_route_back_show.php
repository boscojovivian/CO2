<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>查看出勤紀錄</title>
        <link rel="shortcut icon" href="img/logo.png">
        <link rel="stylesheet" href="css/get_route_back_show.css" type="text/css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- 引入 Bootstrap 框架的 CSS 文件 -->
    </head>
    <body>
        <!-- 導入導覽列 -->
        <?php include('nav/cm_nav.php') ?>

        

        <div class="container text-center mt-3">
            <div class="row justify-content-md-center info_box">
                <div class="text-start">
                    <a href="get_route_back.php" class="m-3"><img src="img/goback.png" class="goback_img"></a>
                </div>
                <div class="col-6 col-md-6 title_text align-items-center">
                    <div class="map mt-3" id="map"></div>
                </div>
                <?php
                $link = mysqli_connect("localhost", "root", "") 
                or die("無法開啟 MySQL 資料庫連結!<br>");
                mysqli_select_db($link, "carbon_emissions");
        
                $route_id = $_GET['get_route_back_show'];

                $sql = "SELECT a.id, a.start_date, a.start_time, a.end_date, a.end_time, a.total_time, a.distance, a.file, a.car, a.type, (SELECT b.em_name FROM employee AS b WHERE a.employee_id = b.em_id) AS name
                        FROM route_tracker AS a
                        WHERE a.id = " . $route_id;

                mysqli_query($link, "SET NAMES utf8");

                $result = mysqli_query($link, $sql);
                $fields = mysqli_num_fields($result); //取得欄位數
                $row = mysqli_num_rows($result); //取得記錄數

                while ($row = mysqli_fetch_array($result)) {
                    $date = $row["start_date"] == $row["end_date"] ? $row["start_date"] : $row["start_date"] . " ~ " . $row["end_date"];
                    $time = $row["start_time"] . " ~ " . $row["end_time"];
                    $total_time = $row["total_time"];
                    $hours = floor($total_time / 60);  // 計算小時
                    $minutes = $total_time % 60;       // 計算剩餘分鐘
                    $total_time_formatted = $hours . ' 小時 ' . $minutes . ' 分鐘';  // 格式化顯示
                    $distance = $row["distance"];
                    $file = $row["file"];
                    $car = $row["car"] == "is_cm_car" ? "類別一" : "類別三";
                    $type = $row["type"];
                    $name = $row["name"];
                }


                // 讀取儲存路徑的 .json 檔案
                $jsonFilePath = "google map/path_files/" . $file;
                $jsonData = file_get_contents($jsonFilePath);
                // 第一次解碼
                $coordinates = json_decode($jsonData, true);
                // 第二次解碼
                // $decoded_data = json_decode($coordinates, true);
                ?>
                <div class="col-6 col-md-6 title_text align-items-center">
                    <div>
                        <table>
                            <tr class="car_tr" rowspan="2">
                                <th><?php echo $car; ?></th>
                            </tr>
                            <tr>
                                <th class="w-50">員工姓名 : </th>
                                <th><?php echo $name; ?></th>
                            </tr>
                            <tr>
                                <th>交通工具 : </th>
                                <th><?php echo $type; ?></th>
                            </tr>
                            <tr>
                                <th>日期 : </th>
                                <th><?php echo $date; ?></th>
                            </tr>
                            <tr>
                                <th>時間 : </th>
                                <th><?php echo $time; ?></th>
                            </tr>
                            <tr>
                                <th>總時長 : </th>
                                <th><?php echo $total_time_formatted; ?></th>
                            </tr>
                            <tr>
                                <th>總距離 : </th>
                                <th><?php echo $distance; ?></th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        


        
        <!-- google map api -->
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBE5jpz-2b9N-tE1DIKCtneSgZ9nXn3jxM&callback=initMap" async defer></script>

        <!-- 引入 Bootstrap JS（包含 Popper.js） -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            var routeCoordinates = <?php echo json_encode($coordinates); ?>;

            function initMap() {
                // 設置地圖中心為路徑的第一個座標
                var mapCenter = routeCoordinates.length > 0 ? { lat: parseFloat(routeCoordinates[0].lat), lng: parseFloat(routeCoordinates[0].lng) } : { lat: 24.149878365016026, lng: 120.68366751085637 };

                var map = new google.maps.Map(document.getElementById('map'), {
                    center: mapCenter,
                    zoom: 15
                });

                // 將座標轉換為地圖用的經緯度座標對象
                var pathCoordinates = routeCoordinates.map(function(coord) {
                    return { lat: parseFloat(coord.lat), lng: parseFloat(coord.lng) };
                });

                // 繪製用戶路徑
                var userPath = new google.maps.Polyline({
                    path: pathCoordinates,
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                });
                userPath.setMap(map);

                // 標記起點
                var startMarker = new google.maps.Marker({
                    position: pathCoordinates[0],
                    map: map,
                    label: "起點",  // 可替換成圖示
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: "green",
                        fillOpacity: 1,
                        strokeWeight: 2,
                        strokeColor: "white"
                    }
                });

                // 標記終點
                var endMarker = new google.maps.Marker({
                    position: pathCoordinates[pathCoordinates.length - 1],
                    map: map,
                    label: "終點",  // 可替換成圖示
                    icon: {
                        path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
                        scale: 8,
                        fillColor: "red",
                        fillOpacity: 1,
                        strokeWeight: 2,
                        strokeColor: "white"
                    }
                });
            }
        </script>
    </body>
</html>