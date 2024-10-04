<!DOCTYPE html>
<html>
    <head>
        <title>查看出勤紀錄</title>
    </head>
    <body>
        <!-- 回到最頂端 -->
        <button onclick="topFunction()" class="topBtn" id="myBtn" title="GOtop">TOP</button>

        <!-- 導入導覽列 -->
        <?php include('nav/cm_nav.php') ?>

        <div id="map" style="height: 500px;"></div>

        <div class="information">
            <table class="table table-bordered table-hover">
                <thead class="table">
                    <tr>
                        <th>員工</th>
                        <th>日期</th>
                        <th>時間</th>
                        <th>總時長</th>
                        <th>總距離</th>
                        <th>是否開公司車</th>
                        <th>交通工具</th>
                        <th>顯示路徑</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>



        <!-- 加入 Google map -->
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBE5jpz-2b9N-tE1DIKCtneSgZ9nXn3jxM&callback=initMap" async defer></script>


        <script>
            // 初始化地圖
        var map = L.map('map').setView([24.149878365016026, 120.68366751085637], 13); // 可設定預設位置與縮放層級

        // 添加地圖圖層
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // 從伺服器獲取路徑 JSON 資料
        fetch('get_path_data.php')
            .then(response => response.json())
            .then(data => {
                // 將路徑轉換成經緯度陣列
                var pathCoordinates = JSON.parse(data.path_data);
                
                // 在地圖上繪製路徑
                var polyline = L.polyline(pathCoordinates, {color: 'red'}).addTo(map);
                
                // 調整地圖視角以適應路徑
                map.fitBounds(polyline.getBounds());
            });
        </script>
    </body>
</html>