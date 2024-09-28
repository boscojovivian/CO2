<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>路線追蹤器</title>

    <link rel="shortcut icon" href="img/logo.png" >
    <link href="route_tracker.css" rel="stylesheet"> <!-- 引入外部 CSS 文件 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- 引入 Bootstrap 框架的 CSS 文件 -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        const message = (message) => {
            var icon = 'img/logo.png';
            Swal.fire({
                // icon: icon,
                title: message,
                imageUrl: icon,
                imageWidth: 150,
                imageHeight: 100,
            });
        }
        </script>
</head>
<body>
    <div class="container text-center mt-3">
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
                <div class="map m-3 mt-1" id="map"></div>
            </div>
        </div>

        <?php
        $link = mysqli_connect("localhost", "root", "") 
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
            <div class="col-11 col-md-7">
                <div class="m-1">
                    <label class="label_box fs-5" for="employeeSelect">出勤員工：</label>
                    &nbsp
                    <select class="select_box" id="employeeSelect" name="employeeSelect" required>
                        <option value="">選擇員工</option>
                        <?php
                        while ($rows_employee = mysqli_fetch_array($result_employee)){
                            echo "<option value='" . $rows_employee[0] . "'>" . $rows_employee[1] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- 是否開公司車 -->
            <div class="col-11 col-md-7">
                <div class="m-1">
                    <label class="label_box fs-5" for="transportMode">是否開公司車：</label>
                    &nbsp
                    <select class="select_box" id="transportMode" name="transportMode" required>
                        <option value="">請選擇</option>
                        <option value="is_cm_car">是</option>
                        <option value="not_cm_car">否</option>
                    </select>
                </div>
            </div>

            <!-- 選擇交通工具 -->
            <div class="col-11 col-md-7">
                <div class="m-1">
                    <label class="label_box fs-5" for="vehicleType">車輛選擇：</label>
                    &nbsp
                    <select class="select_box" id="vehicleType" name="vehicleType" required>
                        <option value="">請先選擇是否開公司車</option>
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
            <div class="col-6 col-md-6">
                <div>
                    時間：<span id="time">0</span>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div>
                <button class="button_box" id="startBtn">開始</button>
                <button class="button_box" id="stopBtn" disabled>結束</button>
                <button class="button_box" id="exportBtn" disabled>完成出勤</button>
            </div>
        </div>
        
    </div>
    

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBE5jpz-2b9N-tE1DIKCtneSgZ9nXn3jxM&callback=initMap" async defer></script>
    <!-- <script src="route_tracker.js"></script> -->

    <!-- 引入 Bootstrap JS（包含 Popper.js） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 連動下拉式選單 -->
    <script>
    // 使用 jQuery 監聽選擇變更事件
    $('#transportMode').on('change', function () {
        const transportMode = $(this).val();
        const vehicleTypeSelect = $('#vehicleType');

        // 清空現有選項並顯示載入中
        vehicleTypeSelect.html('<option value="">載入中...</option>');

        // 發送 AJAX 請求到 PHP 來獲取資料
        $.ajax({
            url: 'get_vehicle_data.php',
            type: 'GET',
            data: { mode: transportMode },
            success: function (response) {
                vehicleTypeSelect.html(response);  // 用 PHP 返回的選項更新下拉選單
            },
            error: function () {
                vehicleTypeSelect.html('<option value="">資料載入失敗</option>');
            }
        });
    });
    </script>

    <!-- 路線追蹤 -->
    <script>
    let map;
    let userPath;
    let marker;
    let startTime;
    let watchId;
    let totalDistance = 0;
    let positions = []; // 用來存儲所有位置點和時間戳
    let loca = [];
    let calculatedRoute = null; // 全局變數用於存儲計算的路線

    function initMap() {
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer();

        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 0, lng: 0},
            zoom: 15
        });

        // 創建一條用來表示路徑的折線
        userPath = new google.maps.Polyline({
            path: [],
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
        });

        userPath.setMap(map);
        directionsRenderer.setMap(map);

        // 檢查瀏覽器是否支持Geolocation API
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const initialPosition = {lat: position.coords.latitude, lng: position.coords.longitude};
                map.setCenter(initialPosition);

                // 創建並顯示用戶位置的標記
                marker = new google.maps.Marker({
                    position: initialPosition,
                    map: map,
                    title: '你的當前位置'
                });
            });
        } else {
            alert('此瀏覽器不支援地理定位功能。');
        }

        // 綁定按鈕事件
        document.getElementById('startBtn').addEventListener('click', function() {
            // 檢查三個下拉式選單是否都有選取
            const employeeSelect = document.getElementById('employeeSelect').value;
            const transportMode = document.getElementById('transportMode').value;
            const vehicleType = document.getElementById('vehicleType').value;

            // 如果有任何一個下拉式選單沒有選取，顯示提示並終止執行
            if (!employeeSelect || !transportMode || !vehicleType) {
                Swal.fire({
                    title: '請完成所有選項',
                    text: '請確保員工、是否開公司車和車輛選擇都有選取！',
                    icon: 'warning',
                    confirmButtonText: '確定'
                });
                return; // 終止執行
            }

            console.log("開始追蹤");
            startTracking();
        });
        document.getElementById('stopBtn').addEventListener('click', function() {
            console.log("結束追蹤");
            stopTracking();
        });
        document.getElementById('exportBtn').addEventListener('click', function() {
            console.log("匯出資料");
            exportData();
        });

        // 設定日期時間的自動更新
        updateDateTime();
        setInterval(updateDateTime, 1000); // 每秒更新一次
    }

    function updateDateTime() {
        const now = new Date();
        const formattedDateTime = now.toLocaleDateString('zh-TW', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            timeZone: 'Asia/Taipei' // 設定台灣時間
        });

        document.getElementById('dateTime').innerText = `現在時間：${formattedDateTime}`;
    }

    function startTracking() {
        // 開啟 "結束" 按鈕
        document.getElementById('stopBtn').disabled = false;

        // 禁用三個下拉式選單
        document.getElementById('employeeSelect').disabled = true;
        document.getElementById('transportMode').disabled = true;
        document.getElementById('vehicleType').disabled = true;

        message('開始路線追蹤!');

        if (navigator.geolocation) {

            startTime = new Date().getTime();

            // 使用 watchPosition 只在第一次要求權限，之後自動監測位置變化
            watchId = navigator.geolocation.watchPosition(position => {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                    timestamp: new Date().toLocaleString('zh-TW', { timeZone: 'Asia/Taipei' })  // 記錄當前台灣時間
                };

                positions.push(pos); // 存儲位置和時間戳

                const path = userPath.getPath();
                path.push(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));

                if (loca.length === 0) {
                    // 第一次獲取位置時，將位置作為起點
                    loca.push(new google.maps.LatLng(pos.lat, pos.lng));
                }
                // loca.push(new google.maps.LatLng(pos.lat, pos.lng));

                if (path.getLength() > 1) {
                    const prevPos = path.getAt(path.getLength() - 2);
                    const currPos = path.getAt(path.getLength() - 1);
                    const distance = google.maps.geometry.spherical.computeDistanceBetween(prevPos, currPos);
                    totalDistance += distance;

                    const currentTime = new Date().getTime();
                    const elapsedTime = (currentTime - startTime) / 1000; // 以秒為單位

                    // const speed = (totalDistance / 1000) / (elapsedTime / 3600); // 速度以公里/小時為單位

                    // 將距離轉換為公里
                    const distanceInKm = totalDistance / 1000;

                    // 將時間轉換為小時和分鐘
                    const hours = Math.floor(elapsedTime / 3600);
                    const minutes = Math.floor((elapsedTime % 3600) / 60);

                    document.getElementById('distance').innerText = distanceInKm.toFixed(2) + ' 公里';
                    document.getElementById('time').innerText = hours + ' 小時 ' + minutes + ' 分鐘';
                    // document.getElementById('speed').innerText = speed.toFixed(2) + ' 公里/小時';
                }

                // 更新地圖中心位置和標記位置
                map.setCenter(pos);
                marker.setPosition(pos);

            }, error => {
                // 錯誤處理部分
                handleGeolocationError(error);
            }, {
                enableHighAccuracy: true,
                timeout: 3000, // 將超時設置為 1 秒
                maximumAge: 0 // 設定最大允許的定位信息年齡為0
            });

            // watchId = navigator.geolocation.watchPosition(successCallback, errorCallback, options);
        } else {
            alert('此瀏覽器不支援地理定位功能。');
        }
    }


    function stopTracking() {
        // 禁用 "結束" 按鈕
        document.getElementById('stopBtn').disabled = true;
        // 啟用 "匯出" 按鈕
        document.getElementById('exportBtn').disabled = false;

        if (watchId) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;

            // 停止追蹤時將最後的位置添加到 loca 作為終點
            if (positions.length > 0) {
                const lastPos = positions[positions.length - 1];
                loca.push(new google.maps.LatLng(lastPos.lat, lastPos.lng));
            }

            // alert('路線追蹤已停止。');
            message('路線追蹤已停止');
        }

        // calculateAndDisplayRoute(directionsService, directionsRenderer);

        // const onChangeHandler = function () {
        //     calculateAndDisplayRoute(directionsService, directionsRenderer);
        // };

        if (loca.length >= 2) {
            calculateAndDisplayRoute(directionsService, directionsRenderer);
        } else {
            alert('路徑數據不足以計算路線。');
        }
    }

    function exportData() {
        let elapsedTime = 0;

        if (watchId) {
            elapsedTime = (new Date().getTime() - startTime) / 3600000; // 以小時為單位
        } else if (positions.length > 0) {
            const lastPositionTime = new Date(positions[positions.length - 1].timestamp).getTime();
            if (!isNaN(lastPositionTime)) {
                elapsedTime = (lastPositionTime - startTime) / 3600000;
            } else {
                console.error("Invalid timestamp in positions array.");
                elapsedTime = 0;  // 或者处理方式根据业务需求调整
            }
        } else {
            console.warn("No positions recorded, cannot calculate elapsed time.");
        }

        if (!isNaN(elapsedTime)) {
            const data = {
                distance: (totalDistance / 1000).toFixed(2) + ' 公里',  // 以公里為單位
                time: elapsedTime.toFixed(2) + ' 小時',  // 以小時為單位
                path: positions,  // 使用完整的路徑數據，包括時間戳
                calculatedRoute: calculatedRoute || []  // 計算的路線，如果尚未計算則為空數組
            };

            const blob = new Blob([JSON.stringify(data, null, 2)], {type: 'application/json'});
            const url = URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = 'route-data.json';
            a.click();
            URL.revokeObjectURL(url);
        } else {
            console.error("Elapsed time is NaN, data export aborted.");
        }
    }




    // 路線
    function calculateAndDisplayRoute(directionsService, directionsRenderer) {
        if (loca.length < 2) {
            alert("路徑數據不足，無法計算路線。");
            return;
        }

        directionsService.route({
            origin: loca[0],
            destination: loca[loca.length - 1],
            travelMode: google.maps.TravelMode.DRIVING,
        })
        .then((response) => {
            directionsRenderer.setDirections(response);

            // 將計算的路線存儲到全局變數
            calculatedRoute = response.routes[0].overview_path.map(point => ({
                lat: point.lat(),
                lng: point.lng()
            }));

            console.log('Calculated Route:', calculatedRoute);
        })
        .catch((e) => window.alert("Directions request failed due to " + e.message));
    }


    // 錯誤處理部分
    function handleGeolocationError(error) {
        switch (error.code) {
            case 1:
                alert('未授權獲取位置信息。請允許網站訪問您的位置信息。');
                break;
            case 2:
                alert('無法獲取位置信息。請檢查您的網絡或 GPS 設置。');
                break;
            case 3:
                alert('獲取位置信息超時。請稍後再試。');
                break;
            default:
                alert('發生未知錯誤。錯誤代碼：' + error.code);
                break;
        }
    }


    window.initMap = initMap;

    </script>
</body>
</html>
