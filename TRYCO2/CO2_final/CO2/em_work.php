<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}
?>

<?php
include("dbcontroller.php");

$db_handle = new DBController();    //將DBController類別實體化為物件，透過new這個關鍵字來初始化

$query = "SELECT * FROM city";

$results = $db_handle->runQuery($query);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>新增交通車出勤紀錄</title>
        <meta charset="utf-8"></meta>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 設置網頁的字符集與 viewport，方便手機瀏覽 -->
        <link rel="stylesheet" href="css.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
        <link rel="shortcut icon" href="img\logo.png" >
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- 引入 Bootstrap 框架的 CSS 文件 -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr@4.6.3/dist/l10n/zh-tw.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
        <script>
            const showAlert_km_time = (distance, time) => {
                var icon = 'img/logo.png';
                Swal.fire({
                    // icon: icon,
                    title: '路徑計算成功!',
                    text: '總距離 : ' + distance + ' 公里   總時間 : ' + time,
                    imageUrl: icon,
                    imageWidth: 150,
                    imageHeight: 100,
                    imageAlt: 'Custom image'
                });
            }
            const showAlert_chack_km_time = () => {
                var icon = 'img/logo.png';
                Swal.fire({
                    // icon: icon,
                    title: '請輸入日期及時間',
                    icon: 'error'
                });
            }
            const showAlert_add_success = () => {
                var icon = 'img/logo.png';
                Swal.fire({
                    // icon: icon,
                    title: '出勤紀錄新增成功',
                    icon: 'success'
                });
            }
            const showAlert_add_fail = () => {
                var icon = 'img/logo.png';
                Swal.fire({
                    // icon: icon,
                    title: '出勤紀錄新增失敗',
                    icon: 'error'
                });
            }
        </script>
    </head>

    <body class="body1">
        <a href="#" class="back-to-top">︽</a>

        <!-- 導航欄 -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top" style="background-color: rgb(255, 255, 255, 0.5)">
            <div class="container-fluid">
                <!-- logo -->
                <nav class="navbar navbar-brand bg-body-tertiary ms-6">
                    <div class="container">
                        <a class="navbar-brand nav-link active" aria-current="page" href="#">
                        <img src="api/img/logo.png" alt="Bootstrap" width="100px">
                        </a>
                    </div>
                </nav>
                <!-- logo -->

                <!-- 漢堡包按鈕 -->
                <button class="navbar-toggler me-6" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvasLg" aria-controls="navbarOffcanvasLg" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Offcanvas 視窗 -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="navbarOffcanvasLg" aria-labelledby="navbarOffcanvasLgLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title fw-bold me-6 mt-4" id="offcanvasNavbarLabel">個人首頁</h5>
                        <button type="button" class="btn-close mt-4" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body me-6">
                        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                            <li class="nav-item me-2">
                                <a class="nav-link" href="#">交通車出勤</a>
                            </li>
                            <li class="nav-item me-2">
                                <a class="nav-link" href="#">最新消息</a>
                            </li>
                            <li class="nav-item me-2">
                                <a class="nav-link" href="#">環保教室</a>
                            </li>
                            <li class="nav-item me-2">
                                <a class="nav-link" href="#">回報問題</a>
                            </li>
                        </ul>
                        <div class="mt-auto text-center">
                            <a class="nav-link mb-2 fs-5 fw-bold" href="#">Jo</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        

        <!-- 新增交通車出勤紀錄 -->
        <div class="mt-8 text-center container">
            <div class="row justify-content-md-center">
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-5">
                            <div class="map position-fixed" id="map"></div>
                        </div>

                        <div class="col-7">
                            <h1 class="fw-bold m-3">新增交通車出勤紀錄</h1>
                            <div class="mt-5">
                                <form id="routingForm" method="post">
                                    <div id="text-center">
                                        <div id="文字靠左">
                                            <!-- 選擇交通車、出勤日期時間 -->
                                            <div id="水平靠左">
                                                
                                                <!-- 選擇交通車 -->
                                                <?php
                                                $link = mysqli_connect("localhost", "root", "A12345678") 
                                                or die("無法開啟 MySQL 資料庫連結!<br>");
                                                mysqli_select_db($link, "carbon_emissions");
                                    
                                                $em_id = $_SESSION['em_id'];
                                    
                                                $sql = "SELECT cc_name, cc_type FROM cm_car";
                                                mysqli_query($link, "SET NAMES utf8");
                                    
                                                $result = mysqli_query($link, $sql);
                                                $fields = mysqli_num_fields($result); //取得欄位數
                                                $rows = mysqli_num_rows($result); //取得記錄數
                                                ?>

                                                <div class="choose_div">
                                                    <label class="work_word" for="transportMode">出勤交通車：</label>

                                                    &nbsp&nbsp&nbsp&nbsp
                                                    <select class="choose_car" id="transportMode" name="transportMode" required>
                                                        <option value="">選擇交通車</option>
                                                        <?php
                                                        while ($rows = mysqli_fetch_array($result)){
                                                            echo "<option value='" . $rows[1] . "'>" . $rows[0] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <!-- 日期選擇器 -->
                                                <div class="choose_dat_time_div" >
                                                    <label class="work_word" for="choose_date">選擇出勤日期時間：</label>
                                                    <div id="水平均分">
                                                        &nbsp&nbsp
                                                        <input class="choose_date" type="text" id="startDate" name="startDate" placeholder="選擇日期" required>
                                                        <input class="choose_date" type="text" id="startTime" name="startTime" placeholder="選擇時間" required>
                                                    </div>
                        
                                                    <script>
                                                        // 初始化日期選擇器，添加跳到今天的快捷鍵
                                                        flatpickr("#startDate", {
                                                            dateFormat: "Y-m-d", // 指定日期格式
                                                            "locale": "zh_tw", // 设置为中文本地化
                                                        });

                                                        // 初始化時間選擇器，預設為現在的時間
                                                        flatpickr("#startTime", {
                                                            enableTime: true,
                                                            noCalendar: true, // 不顯示日曆，只顯示時間
                                                            dateFormat: "H:i", // 指定時間格式
                                                            time_24hr: true,
                                                            // defaultDate: new Date(), // 預設為現在的時間
                                                            "locale": "zh_tw", // 设置为中文本地化
                                                        });
                                                        // 检查日期和时间是否已选择
                                                        function checkDateTimeSelected() {
                                                            var startDate = document.getElementById('startDate').value;
                                                            var startTime = document.getElementById('startTime').value;
                                                            if (!startDate || !startTime) {
                                                                showAlert_chack_km_time();
                                                                return false;
                                                            }
                                                            return true;
                                                        }
                                                    </script>
                                                </div>
                                            </div>

                                            <!-- 設置起點 -->
                                            <div class="work_city_div" style="background-color: #8cb4bf;">
                                                <a class="work_word">起點：</a>
                                                <br><br>
                                                <div id="水平靠左">
                                                    &nbsp&nbsp&nbsp&nbsp
                                                    <label for='city_list_0'>城市：</label>
                                                    <select class='work_city' style="background-color:#e2ebf7; color:#527c7c; border:2px solid #9bc9ca;" id="city_list_0" name="city[]" onChange='getStartArea(this.value);' required>
                                                        <option value disabled>請選擇城市</option>
                                                        <?php
                                                        foreach($results as $city){
                                                            $selected = $city["city_name"] == "台中市" ? "selected" : "";
                                                        ?>
                                                        <option value="<?php echo $city["city_id"]; ?>" <?php echo $selected; ?>><?php echo $city["city_name"]; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp  
                                                    <label for='area_list_0'>鄉鎮區：</label>
                                                    <select class='work_city' style="background-color:#e2ebf7; color:#527c7c; border:2px solid #9bc9ca;" id="area_list_0" name="area[]" required>
                                                        <option value="">請選擇鄉鎮區</option>
                                                        <!-- 這裡需要動態載入鄉鎮區，假設北區的id為 "100" -->
                                                        <option value="100" selected>北區</option>
                                                    </select>
                                                </div>
                                                <br>
                                                <div id="水平靠左">
                                                    &nbsp&nbsp&nbsp&nbsp
                                                    <label for="address_detail_0">詳細地址：</label>
                                                    &nbsp
                                                    <input class='work_address_detail rounded-3' style="background-color:#e2ebf7; color:#527c7c; border:2px solid #9bc9ca;" type="text" id="address_detail_0" name="address_detail[]" value="三民路三段129號" required>
                                                </div>
                                            </div>

                                            <br><br>

                                            <!-- 設置中途點 -->
                                            <a class="work_word">新增中途點：</a>
                                            <div id="address_container">
                                                <script>
                                                    window.onload = function() {
                                                        addAddress();
                                                    };
                                                </script>
                                            </div>

                                            <!-- 新增中途點按鈕 -->
                                            <div id="文字置中">
                                                <button class="add_work_addAddress m-4 fw-bolder fs-1" type="button" onclick="addAddress()">+</button>
                                            </div>
                                            
                                            <!-- 設置終點 -->
                                            <div class="work_city_div" style="background-color: #cbb48e;">
                                                <a class="work_word">終點：</a>
                                                <br><br>
                                                <div id="水平靠左">
                                                    &nbsp&nbsp&nbsp&nbsp
                                                    <label for='city_list_1'>城市：</label>
                                                    <select class='work_city' style="background-color:#f4eee5; color:#773f3b; border:2px solid #caa79b;" id="city_list_1" name="city[]" onChange='getEndArea(this.value);' required>
                                                        <option value disabled>請選擇城市</option>
                                                        <?php
                                                        foreach($results as $city){
                                                            $selected = $city["city_name"] == "台中市" ? "selected" : "";
                                                        ?>
                                                        <option value="<?php echo $city["city_id"]; ?>" <?php echo $selected; ?>><?php echo $city["city_name"]; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp  
                                                    <label for='area_list_1'>鄉鎮區：</label>
                                                    <select class='work_city' style="background-color:#f4eee5; color:#773f3b; border:2px solid #caa79b;" id="area_list_1" name="area[]" required>
                                                        <option value="">請選擇鄉鎮區</option>
                                                        <!-- 這裡需要動態載入鄉鎮區，假設北區的id為 "100" -->
                                                        <option value="100" selected>北區</option>
                                                    </select>
                                                </div>
                                                <br>
                                                <div id="水平靠左">
                                                    &nbsp&nbsp&nbsp&nbsp
                                                    <label for="address_detail_1">詳細地址：</label>
                                                    &nbsp
                                                    <input class='work_address_detail' style="background-color:#f4eee5; color:#773f3b; border:2px solid #caa79b;" type="text" id="address_detail_1" name="address_detail[]" value="三民路三段129號" required>
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <div id="水平均分">
                                            <input class="add_work_plan_submit" type="submit" name="plan" value="規劃路線">
                                            <input class="add_work_apply_submit" type="submit" name="apply" value="提交申請" disabled>
                                        </div>
                                        <br>

                                        <input type="hidden" id="chinese_address" name="chinese_address" value="">
                                        <input type="hidden" id="total_km" name="total_km" value="">
                                        <input type="hidden" id="total_hr" name="total_hr" value="">
                                        <input type="hidden" id="total_min" name="total_min" value="">
                                        <input type="hidden" id="transportName" name="transportName" value="">
                                    </div>

                                    <?php
                                        if (isset($_POST["apply"])) {
                                            include_once('inc\add_work.inc');
                                        }
                                    ?>
                                </form>
                            </div>
                            
                            
                            <br><br><br>
                        </div>
                    </div>
                    
                </div>
                
            
            
            
                <!-- <div class="col-md-4 m-2">
                    <div class="map" id="map"></div>
                </div> -->
            </div>
        </div>


        <!-- <div id="panel"></div> -->














        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
        <script src="js.js"></script>
        <!-- <script src="map_js.js"></script> -->
        <script type="text/javascript">
            function getStartArea(val){
                $.ajax({
                    type : "POST",   //請求資料的方式
                    url : "getArea.php",    //要請求資料的網址
                    //當某個city被選擇時把國家的id POST到後端(getArea)
                    data : "city_id=" + val,    //使用SQL語法到一料庫抓states資料表的資料

                    success : function(data){   //接收成功時執行
                        $("#area_list_0").html(data);     //取得從getArea.php回傳的資料
                    }
                })
            }
            function getEndArea(val){
                $.ajax({
                    type : "POST",   //請求資料的方式
                    url : "getArea.php",    //要請求資料的網址
                    //當某個city被選擇時把國家的id POST到後端(getArea)
                    data : "city_id=" + val,    //使用SQL語法到一料庫抓states資料表的資料

                    success : function(data){   //接收成功時執行
                        $("#area_list_1").html(data);     //取得從getArea.php回傳的資料
                    }
                })
            }
        </script>
        <script type="text/javascript">
            let addressCount = 2;

            function addAddress() {
                addressCount++;

                const addressContainer = document.getElementById('address_container');
                const addressDiv = document.createElement('div');
                addressDiv.classList.add('work_city_div');

                const midPointLabel = document.createElement('a');
                midPointLabel.classList.add('work_word');
                midPointLabel.innerText = '中途點：';
                addressDiv.appendChild(midPointLabel);

                const deleteButton = document.createElement('button');
                deleteButton.classList.add('work_address_delete');
                deleteButton.setAttribute('type', 'button');
                deleteButton.setAttribute('onclick', 'removeAddress(this);');
                deleteButton.innerText = '刪除';
                addressDiv.appendChild(deleteButton);

                addressDiv.appendChild(document.createElement('br'));
                addressDiv.appendChild(document.createElement('br'));

                const horizontalDiv1 = document.createElement('div');
                horizontalDiv1.id = '水平靠左';

                horizontalDiv1.appendChild(document.createTextNode('\u00A0\u00A0\u00A0\u00A0'));

                const cityLabel = document.createElement('label');
                const cityId = `address_city_${addressCount}`;
                cityLabel.setAttribute('for', cityId);
                cityLabel.innerText = '城市：';
                horizontalDiv1.appendChild(cityLabel);

                const citySelect = document.createElement('select');
                citySelect.classList.add('work_city');
                citySelect.id = cityId;
                citySelect.name = 'city[]';
                citySelect.setAttribute('onChange', 'getArea(this);');
                citySelect.required = true;

                citySelect.innerHTML = `
                    <option value disabled selected>請選擇城市</option>
                    <?php foreach($results as $city): ?>
                    <option value="<?php echo $city['city_id']; ?>"><?php echo $city['city_name']; ?></option>
                    <?php endforeach; ?>
                `;

                horizontalDiv1.appendChild(citySelect);
                horizontalDiv1.appendChild(document.createTextNode('\u00A0\u00A0\u00A0\u00A0\u00A0\u00A0'));

                const areaLabel = document.createElement('label');
                const areaId = `address_area_${addressCount}`;
                areaLabel.setAttribute('for', areaId);
                areaLabel.innerText = '鄉鎮區：';
                horizontalDiv1.appendChild(areaLabel);

                const areaSelect = document.createElement('select');
                areaSelect.classList.add('work_city');
                areaSelect.setAttribute('id', areaId);
                areaSelect.setAttribute('name', 'area[]');
                areaSelect.required = true;
                areaSelect.innerHTML = '<option value="">請選擇鄉鎮區</option>';
                horizontalDiv1.appendChild(areaSelect);

                addressDiv.appendChild(horizontalDiv1);
                addressDiv.appendChild(document.createElement('br'));

                const horizontalDiv2 = document.createElement('div');
                horizontalDiv2.id = '水平靠左';

                horizontalDiv2.appendChild(document.createTextNode('\u00A0\u00A0\u00A0\u00A0'));

                const detailLabel = document.createElement('label');
                const detailId = `address_detail_${addressCount}`;
                detailLabel.setAttribute('for', detailId);
                detailLabel.innerText = '詳細地址：';
                horizontalDiv2.appendChild(detailLabel);

                horizontalDiv2.appendChild(document.createTextNode('\u00A0'));

                const detailInput = document.createElement('input');
                detailInput.classList.add('work_address_detail');
                detailInput.setAttribute('type', 'text');
                detailInput.setAttribute('id', detailId);
                detailInput.setAttribute('name', 'address_detail[]');
                detailInput.required = true;
                horizontalDiv2.appendChild(detailInput);

                addressDiv.appendChild(horizontalDiv2);
                addressContainer.appendChild(addressDiv);
            }

            function removeAddress(button) {
                const addressDiv = button.parentElement;
                addressDiv.remove();
            }

            function getArea(selectElement) {
                const addressDiv = selectElement.closest('.work_city_div');
                const areaSelect = addressDiv.querySelector('select[name="area[]"]');
                const cityId = selectElement.value;

                $.ajax({
                    type: "POST",
                    url: "getArea.php",
                    data: "city_id=" + cityId,
                    success: function(data) {
                        areaSelect.innerHTML = data;
                    }
                });
            }

            //初始化地圖
            const platform = new H.service.Platform({
                apikey: 'vLOV0OZxoNgUvE2m00AvrNTQzGhZtOPuCSwU9_BFcBg'
            });

            //默認地圖圖層物件
            var defaultLayers = platform.createDefaultLayers({
                lg: 'cht'    // 使用中文地圖樣式
            });

            //創建一個地圖實例
            var map = new H.Map(document.getElementById('map'),
                defaultLayers.vector.normal.map, {
                    zoom: 15,    //初始縮放級別
                    center: { lat: 24.149878365016026, lng: 120.68366751085637 },    //初始坐標中心
                    pixelRatio: window.devicePixelRatio || 1
                }
            );
            var routeInstructionsContainer = document.getElementById('panel');

            //事件監聽器，確保地圖在視窗大小改變時能夠自適應
            window.addEventListener('resize', () => map.getViewPort().resize());

            //啟用地圖的事件系統和預設交互行為
            var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

            //創建了地圖的默認 UI 元件
            var ui = H.ui.UI.createDefault(map, defaultLayers, 'zh-CN');

            //創建地圖上的群組
            var group = new H.map.Group();
            map.addObject(group);


            // 使用自定義圖標圖片創建圖標
            var iconUrl = 'img/logo.png';  // 替換為你的圖標圖片 URL
            // 使用自定義圖標圖片創建 H.map.Icon，並設置大小
            var icon = new H.map.Icon(iconUrl, {
                size: { w: 50, h: 35 }  // 設置圖標的寬和高
                });

            // 使用自定義圖標創建標記
            var company = new H.map.Marker(
                { lat: 24.149878365016026, lng: 120.68366751085637 }, 
                { icon: icon }
            );
            group.addObject(company);    //將標記添加到群組


            //向HERE Maps API發送路由請求
            var router = platform.getRoutingService(null, 8);

            // 禁用"提交申請"按钮
            document.querySelector('.add_work_apply_submit').disabled = true;

            //規劃路線
            document.getElementById("routingForm").addEventListener("submit", function(event) {
                var clickedButtonName = event.submitter.name; // 获取被点击的提交按钮的name属性

                if (clickedButtonName === "plan") {
                    event.preventDefault(); // 阻止表单提交

                    // 检查日期和时间是否已选择
                    if (!checkDateTimeSelected()) {
                        return; // 如果没有选择日期和时间，则退出函数
                    }
                    
                    // 处理"規劃路線"的逻辑
                    console.log('Plan route');
                    planRoute(); // 调用规划路线的函数
                }
                // 如果是其他按钮，则不阻止表单提交
            });
            function planRoute() {
                // event.preventDefault(); // 阻止表單提交
                var form = event.target;
                var transportModeElement = document.getElementById('transportMode');
                var transportMode = transportModeElement.value; // 获取所选交通工具的值
                var transportName = transportModeElement.options[transportModeElement.selectedIndex].text;

                var addresses = document.querySelectorAll('.work_city_div');
                var waypoints = [];
                var chinese_address = "";
                var total_km = 0;
                var total_hr = 0;
                var total_min = 0;

                // 清除之前的标记和路线
                if (map) { // 检查地图对象是否存在
                    map.removeObjects(map.getObjects());
                }

                addresses.forEach(addressDiv => {
                    var citySelect = addressDiv.querySelector('select[name="city[]"]');
                    var areaSelect = addressDiv.querySelector('select[name="area[]"]');

                    var city = citySelect.options[citySelect.selectedIndex].text;
                    var area = areaSelect.options[areaSelect.selectedIndex].text;

                    var detail = addressDiv.querySelector('input[name="address_detail[]"]').value;
                    var address = city + area + detail;

                    waypoints.push(address);
                });

                console.log('waypoints:', waypoints);
                chinese_address = waypoints;
                console.log('chinese_address:', chinese_address);

                // 使用地理編碼服務將地址轉換為經緯度座標
                geocodeAddresses(waypoints);

                function geocodeAddresses(addresses) {
                    var geocoder = platform.getSearchService()
                    // var results = [];
                    var results = new Array(addresses.length);  // 创建一个与地址数组相同长度的数组来存储结果
                    var completedRequests = 0;

                    addresses.forEach((address, index) => {
                        var geocodingParameters = {
                            q: address // 地址參數
                        };

                        geocoder.geocode(
                            geocodingParameters,
                            function (result) {
                                if (result.items.length > 0) {

                                    var location = result.items[0];
                                    // results.push(location);
                                    results[index] = location;  // 将结果存储在对应的索引位置
                                } else {
                                    console.error('No results found for address:', address);
                                }
                                checkIfComplete();
                            },
                            function (error) {
                                console.error('Geocoding error for address:', address, error);
                                checkIfComplete();
                            }
                        );
                    });

                    function checkIfComplete() {
                        completedRequests++;
                        if (completedRequests === addresses.length) {
                            console.log('Geocoding complete. Results:', results);
                            // All addresses have been geocoded, proceed with map operations
                            handleGeocodingResults(results);
                        }
                    }
                }

                function handleGeocodingResults(locations) {
                    // 將地理編碼的位置添加到地圖上
                    addLocationsToMap(locations);

                    // 初始化途径点数组
                    var waypoints = locations.map(locations => locations.position.lat + ',' + locations.position.lng);

                    // 起点是第一个位置
                    var origin = waypoints[0];

                    // 终点是最后一个位置
                    var destination = waypoints[waypoints.length - 1];

                    // 如果有多个位置，设置途径点（去掉第一个和最後一個）
                    var viaWaypoints = waypoints.slice(0, -1);

                    var router = platform.getRoutingService(null, 8);

                    // 构建路由请求参数
                    var routeRequestParams = {
                        routingMode: 'fast',    // 计算最快的路径
                        transportMode: transportMode,   // 使用汽车作为交通工具
                        // transportMode: 'car',
                        origin: origin,         // 起点
                        destination: destination, // 终点
                        return: 'polyline,turnByTurnActions,actions,instructions,travelSummary'
                    };

                    // 添加途径点，如果存在多个途径点
                    if (viaWaypoints.length > 0) {
                        routeRequestParams['via'] = new H.service.Url.MultiValueQueryParameter(
                            viaWaypoints.map(waypoint => waypoint)
                        );
                    }

                    // 打印请求参数以进行调试
                    console.log('Route request parameters:', routeRequestParams);

                    console.log('Origin:', origin);
                    viaWaypoints.forEach((waypoint, index) => {
                        console.log('Via ' + (index) + ':',  waypoint);
                    });
                    console.log('Destination:', destination);
                    
                    router.calculateRoute(
                        routeRequestParams,
                        onResult,
                        onError
                    );

                    function onResult(result) {
                        var route = result.routes[0];

                        addRouteShapeToMap(route);
                        // addWaypointsToPanel(route, chinese_address);
                        // addManueversToPanel(route);
                        addSummaryToPanel(route);
                        enableApplyButton(); // 启用“提交申请”按钮
                        postHidden();
                    }

                    //新增路徑到地圖
                    function addRouteShapeToMap(route) {
                        route.sections.forEach((section) => {
                            // decode LineString from the flexible polyline
                            let linestring = H.geo.LineString.fromFlexiblePolyline(section.polyline);

                            // Create a polyline to display the route:
                            let polyline = new H.map.Polyline(linestring, {
                                style: {
                                    lineWidth: 5,
                                    strokeColor: 'rgba(62, 112, 211, 1)'
                                }
                            });

                            // Add the polyline to the map
                            map.addObject(polyline);
                            // And zoom to its bounding rectangle
                            map.getViewModel().setLookAtData({
                                bounds: polyline.getBoundingBox()
                            });
                        });
                    }

                    //從...到...再到...
                    function addWaypointsToPanel(route, chinese_address) {
                        var nodeH3 = document.createElement('h3'),
                            labels = [];

                        route.sections.forEach((section) => {
                            labels.push(
                            section.turnByTurnActions[0].nextRoad.name[0].value)
                            labels.push(
                            section.turnByTurnActions[section.turnByTurnActions.length - 1].currentRoad.name[0].value)
                        });

                        nodeH3.textContent = labels.join(' - ');
                        routeInstructionsContainer.innerHTML = '';
                        routeInstructionsContainer.appendChild(nodeH3);
                    }

                    //路線指示文字
                    function addManueversToPanel(route) {
                        var nodeOL = document.createElement('ol');

                        nodeOL.style.fontSize = 'small';
                        nodeOL.style.marginLeft ='5%';
                        nodeOL.style.marginRight ='5%';
                        nodeOL.className = 'directions';

                        route.sections.forEach((section) => {
                            section.actions.forEach((action, idx) => {
                            var li = document.createElement('li'),
                                spanArrow = document.createElement('span'),
                                spanInstruction = document.createElement('span');

                            spanArrow.className = 'arrow ' + (action.direction || '') + action.action;
                            spanInstruction.innerHTML = section.actions[idx].instruction;
                            li.appendChild(spanArrow);
                            li.appendChild(spanInstruction);

                            nodeOL.appendChild(li);
                            });
                        });

                        routeInstructionsContainer.appendChild(nodeOL);
                    }
                    
                    function addSummaryToPanel(route) {
                        let duration = 0,
                            distance = 0;

                        route.sections.forEach((section) => {
                            distance += section.travelSummary.length; // 距离以米为单位
                            duration += section.travelSummary.duration; // 时间以秒为单位
                        });

                        distance = (distance / 1000).toFixed(2); // 将距离转换为公里并保留两位小数
                        total_km = distance;
                        time = toHHMMSS(duration);

                        showAlert_km_time(distance, time);
                    }

                }

                function onError(error) {
                    console.error('Routing error:', error); // 打印错误信息
                    showAlert_fail('請輸入正確地址');
                    // alert('請輸入正確地址');
                }


                // 使用自定義圖標圖片創建圖標
                var iconStartUrl = 'img/iconStart.png';
                var iconMiddleUrl = 'img/iconMiddle.png';
                var iconEndUrl = 'img/iconEnd.png';

                // 使用自定義圖標圖片創建 H.map.Icon，並設置大小
                var iconStart = new H.map.Icon(iconStartUrl, {
                    size: { w: 50, h: 50 }  // 設置圖標的寬和高
                    });
                var iconMiddle = new H.map.Icon(iconMiddleUrl, {
                    size: { w: 50, h: 50 }  // 設置圖標的寬和高
                    });
                var iconEnd = new H.map.Icon(iconEndUrl, {
                    size: { w: 50, h: 50 }  // 設置圖標的寬和高
                    });
                                
                // 將地理編碼的位置添加到地圖上
                function addLocationsToMap(locations){
                    var group = new H.map.Group();

                    // var group = new H.map.Group(),
                    //     position,
                    //     i;

                    console.log('icon_locations:', locations);

                    for (var i = 0; i < locations.length; i += 1) {
                        let location = locations[i];
                        let icon;

                        if (i === 0) {
                            icon = iconStart;  // 第一个标记
                        } else if (i === locations.length - 1) {
                            icon = iconEnd;    // 最後一個標記
                        } else {
                            icon = iconMiddle; // 中途标记
                        }

                        let marker = new H.map.Marker(location.position, { icon: icon });
                        marker.label = location.address.label;
                        group.addObject(marker);
                    }

                    // 將標記群組添加到地圖上
                    map.addObject(group);
                    map.getViewModel().setLookAtData({
                        bounds: group.getBoundingBox()
                    });
                }
                
                //時間換算
                function toHHMMSS(seconds) {
                    var hours = Math.floor(seconds / 3600);
                    var minutes = Math.floor((seconds % 3600) / 60);
                    var remainingSeconds = seconds % 60;

                    total_hr = hours;
                    total_min = minutes;

                    if(hours === 0){
                        return minutes + "分";
                    } else{
                        return hours + "小時" + minutes + "分"
                    }
                }

                // 启用“提交申请”按钮
                function enableApplyButton() {
                    document.querySelector('.add_work_apply_submit').disabled = false;
                }

                function postHidden() {
                    // 将 JavaScript 变量的值设置为隐藏字段的值
                    document.getElementById("chinese_address").value = chinese_address;
                    document.getElementById("total_km").value = total_km;
                    document.getElementById("total_hr").value = total_hr;
                    document.getElementById("total_min").value = total_min;
                    document.getElementById("transportName").value = transportName;
                }
            }
        </script>

    </body>
</html>