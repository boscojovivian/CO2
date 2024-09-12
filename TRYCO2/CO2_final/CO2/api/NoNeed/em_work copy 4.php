<!-- no -->

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
        <link rel="stylesheet" href="css.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
        <link rel="shortcut icon" href="img\logo.png" >
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr@4.6.3/dist/l10n/zh-tw.js"></script>
    </head>

    <body class="body1">
        <a href="#" class="back-to-top">︽</a>

        <!-- 上方工作列 -->
        <header id="置中">
            <a href="em_index.php"><img src="img\logo.png" class="logo"></a>
            <ul class="menu">
                <?php
                if($_SESSION['flag'] == 1){
                    echo "<li><a href='cm_index.php' class='li1'>管理者首頁</a></li>";
                }
                else{

                }
                ?>
                <li><a class="li1" href="em_add_address.php">新增地址</a></li>
                <li><a class="li1" href="em_work.php">交通車出勤紀錄</a></li>
                <li><a href="#" class="li1" onclick="openContactForm()">回報問題</a></li>
                <?php
                if(isset($_SESSION['em_name'])){
                    $user_name = $_SESSION['em_name'];
                    echo "<li><a class='li1'>" . $user_name . "</a>";
                    echo "<ul>";
                    echo "<li><form method='post'>";
                    echo "<input type='submit' name='logout' data-style='logout_submit' value='登出'>";
                    echo "</form></li>";
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
                <h2>回報問題</h2>
                <form id="form" method="post" onsubmit="return ContactFormSuccess();">
                    <label for="sender">電子信箱：</label>
                    <?php
                    echo "<a>" . $_SESSION['em_email'] . "</>";
                    ?>

                    <label for="message">新增留言：</label>
                    <textarea id="message" name="message" rows="4" required></textarea>
                    <br>
                    <input type="submit" name="contact" data-style='submit1' value="送出">
                    
                    <?php
                    try {
                        if (isset($_POST["contact"])) {
                            include_once('inc\message.inc');
                        }
                    } catch (Exception $e) {
                        // 
                    }
                    ?>
                </form>
            </div>
        </header>
        

        <!-- 新增交通車出勤紀錄 -->
        <div class="add_work">
            <div class="add_work_left">
                <h1 class="add_work_title">新增交通車出勤紀錄</h1>
                <form id="routingForm" method="post">
                    <div id="文字置中">
                        <div id="文字靠左">

                            <!-- 選擇交通車、出勤日期時間 -->
                            <div id="水平靠左">

                                <!-- 選擇交通車 -->
                                <!-- <div class="choose_div">
                                    <label class="work_word" for="transportMode">出勤交通車：</label>

                                    &nbsp&nbsp&nbsp&nbsp
                                    <select class="choose_car" id="transportMode" name="transportMode">
                                        <option value="car">汽車</option>
                                        <option value="bicycle">機車</option>
                                        <option value="truck">卡車</option>
                                    </select>
                                </div> -->
                                
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
                                <div class="choose_div">
                                    <label class="work_word" for="choose_date">選擇出勤日期時間：</label>
                                    &nbsp&nbsp&nbsp&nbsp
                                    <input class="choose_date" type="text" id="myDatePicker" placeholder="選擇日期" required>
                                    <script>
                                        // 初始化 flatpickr 日期選擇器
                                        flatpickr("#myDatePicker", {
                                            // 配置選項
                                            enableTime: true,
                                            dateFormat: "Y-m-d H:i", // 指定日期格式
                                            minDate: "today", // 指定最小日期為今天
                                            maxDate: new Date().fp_incr(60), // 指定最大日期為今天後 30 天
                                            time_24hr: true,
                                            "locale": "zh_tw", // 设置为中文本地化
                                        });
                                    </script>
                                </div>
                            </div>

                            <!-- 設置起點 -->
                            <div class="work_city_div" style="background-color: #8191B5;">
                                <a class="work_word">起點：</a>
                                <br><br>
                                <div id="水平靠左">
                                    &nbsp&nbsp&nbsp&nbsp
                                    <label for='city_list_0'>城市：</label>
                                    <select class='work_city' style="background-color:#e2ebf7; color:#3b4877; border:2px solid #93abcd;" id="city_list_0" name="city[]" onChange='getStartArea(this.value);' required>
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
                                    <select class='work_city' style="background-color:#e2ebf7; color:#3b4877; border:2px solid #93abcd;" id="area_list_0" name="area[]" required>
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
                                    <input class='work_address_detail' style="background-color:#e2ebf7; color:#3b4877; border:2px solid #93abcd;" type="text" id="address_detail_0" name="address_detail[]" value="三民路三段129號" required>
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
                                <br>
                                <button class="add_work_addAddress" type="button" onclick="addAddress()">+</button>
                                <br><br>
                            </div>
                            
                            <!-- 設置終點 -->
                            <div class="work_city_div" style="background-color: #cd9b93;">
                                <a class="work_word">終點：</a>
                                <br><br>
                                <div id="水平靠左">
                                    &nbsp&nbsp&nbsp&nbsp
                                    <label for='city_list_1'>城市：</label>
                                    <select class='work_city' style="background-color:#f7e4e2; color:#773f3b; border:2px solid #dab4ac;" id="city_list_1" name="city[]" onChange='getEndArea(this.value);' required>
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
                                    <select class='work_city' style="background-color:#f7e4e2; color:#773f3b; border:2px solid #dab4ac;" id="area_list_1" name="area[]" required>
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
                                    <input class='work_address_detail' style="background-color:#f7e4e2; color:#773f3b; border:2px solid #dab4ac;" type="text" id="address_detail_1" name="address_detail[]" value="三民路三段129號" required>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div id="水平均分">
                            <input class="add_work_plan_submit" type="submit" name="plan" value="規劃路線">
                            <input class="add_work_apply_submit" type="submit" name="apply" value="提交申請" disabled>
                        </div>
                        <br><br><br>
                    </div>

                    <?php
                        if (isset($_POST["apply"])) {
                            include_once('inc\add_work.inc');
                        }
                    ?>
                </form>
            </div>
            <div class="add_work_right">
                <div class="map" id="map"></div>
            </div>
        </div>


        <div id="panel"></div>














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

            //設置點擊事件
            group.addEventListener('tap', function(evt){
                var bubble = new H.ui.InfoBubble(evt.target.getGeometry(), {
                    content: evt.target.getData()
                });
                ui.addBubble(bubble);
            }, false);


            // //創建地圖上的標記(公司)
            // var company = new H.map.Marker({ lat: 24.149878365016026, lng: 120.68366751085637 });    //創建地圖上的標記
            // // company.setData("<div><p>Hello!</p></div>");
            // group.addObject(company);    //將標記添加到群組


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
                    // 处理"規劃路線"的逻辑
                    console.log('Plan route');
                    planRoute(); // 调用规划路线的函数
                }
                // 如果是其他按钮，则不阻止表单提交
            });
            function planRoute() {
                // event.preventDefault(); // 阻止表單提交
                var form = event.target;
                var transportMode = form.elements["transportMode"].value; // 獲取所選交通工具的值

                var addresses = document.querySelectorAll('.work_city_div');
                var waypoints = [];

                // 清除之前的标记和路线
                if (map) { // 检查地图对象是否存在
                    map.removeObjects(map.getObjects());
                }
                // routeInstructionsContainer.innerHTML = '';

                // var start_city = document.getElementById('city_list_0').options[document.getElementById('city_list_0').selectedIndex].text;
                // var start_area = document.getElementById('area_list_0').options[document.getElementById('area_list_0').selectedIndex].text;
                // var start_detail = document.getElementById('address_detail_0').value;
                // var end_city = document.getElementById('city_list_9999').options[document.getElementById('city_list_9999').selectedIndex].text;
                // var end_area = document.getElementById('area_list_9999').options[document.getElementById('area_list_9999').selectedIndex].text;
                // var end_detail = document.getElementById('address_detail_9999').value;

                // var start_address = start_city + start_area + start_detail;
                // var geocode_end_address = geocode_StartEndAddresses(start_address);
                // var end_address = end_city + end_area + end_detail;
                // var geocode_end_address = geocode_StartEndAddresses(end_address);

                // function geocode_StartEndAddresses(address) {
                //     var geocoder = platform.getSearchService(),
                //         geocodingParameters = {
                //             q: address // 地址參數
                //         };

                //     geocoder.geocode(
                //         geocodingParameters,
                //         function (result) {
                //             var geocode_end_address = result.items;
                //         },
                //         function (error) {
                //             console.error('Geocoding error for address:', address, error);
                //         }
                //     );
                // }

                addresses.forEach(addressDiv => {
                    var citySelect = addressDiv.querySelector('select[name="city[]"]');
                    var areaSelect = addressDiv.querySelector('select[name="area[]"]');

                    var city = citySelect.options[citySelect.selectedIndex].text;
                    var area = areaSelect.options[areaSelect.selectedIndex].text;


                    // var city = addressDiv.querySelector('select[name="city[]"]').value;
                    // var area = addressDiv.querySelector('select[name="area[]"]').value;
                    var detail = addressDiv.querySelector('input[name="address_detail[]"]').value;
                    var address = city + area + detail;

                    waypoints.push(address);
                });

                console.log('waypoints:', waypoints);

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
                                    // for(i = 0; i < result.items.length; i++){
                                    //     var location = result.items[i];
                                    //     results.push(location);
                                    // }

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

                    // // 初始化途径点数组
                    // var waypoints = locations.map(location => location.position.lat + ',' + location.position.lng);

                    // // 起点是第一个位置
                    // var origin = waypoints[0];

                    // // 终点是最后一个位置
                    // var destination = waypoints[waypoints.length - 1];

                    // // // 终点
                    // // var destination = geocode_end_address;

                    // // 如果有多个位置，设置途径点（去掉第一个和最后一个位置）
                    // var viaWaypoints = waypoints.slice(1, -1);

                    // var router = platform.getRoutingService(null, 8),
                    //     routeRequestParams = {
                    //         routingMode: 'fast',    // 计算最快的路径
                    //         transportMode: transportMode,   // 使用汽车作为交通工具
                    //         origin: origin,         // 起点
                    //         destination: destination, // 终点
                    //         via: viaWaypoints,      // 途径点
                    //         return: 'polyline,turnByTurnActions,actions,instructions,travelSummary'
                    //     };

                    // // 打印请求参数以进行调试
                    // console.log('Route request parameters:', routeRequestParams);
                    
                    // router.calculateRoute(
                    //     routeRequestParams,
                    //     onSuccess,
                    //     onError
                    // );

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
                        // transportMode: transportMode,   // 使用汽车作为交通工具
                        transportMode: 'car',
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

                    // // 添加途径点
                    // for (var i = 0; i <= viaWaypoints.length; i++) {
                    //     routeRequestParams['via' + (i+2)] = new H.service.Url.MultiValueQueryParameter(
                    //         viaWaypoints.map(waypoints => waypoints[i+2])
                    //     );
                    //     console.log('Route request parameters:', routeRequestParams);
                    // }

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
                        //addManueversToMap(route);  //新增轉彎標記圓點
                        addWaypointsToPanel(route);
                        addManueversToPanel(route);
                        addSummaryToPanel(route);
                        enableApplyButton(); // 启用“提交申请”按钮
                    }
                    function addRouteShapeToMap(route) {
                        route.sections.forEach((section) => {
                            // decode LineString from the flexible polyline
                            let linestring = H.geo.LineString.fromFlexiblePolyline(section.polyline);

                            // Create a polyline to display the route:
                            let polyline = new H.map.Polyline(linestring, {
                                style: {
                                    lineWidth: 5,
                                    strokeColor: 'rgba(59, 119, 82, 0.7)'
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

                    //新增轉彎標記圓點
                    function addManueversToMap(route) {
                        var svgMarkup = '<svg width="18" height="18" ' +
                            'xmlns="http://www.w3.org/2000/svg">' +
                            '<circle cx="8" cy="8" r="8" ' +
                            'fill="#1b468d" stroke="white" stroke-width="1" />' +
                            '</svg>',
                            dotIcon = new H.map.Icon(svgMarkup, {anchor: {x:8, y:8}}),
                            group = new H.map.Group(),
                            i,
                            j;

                        route.sections.forEach((section) => {
                            let poly = H.geo.LineString.fromFlexiblePolyline(section.polyline).getLatLngAltArray();

                            let actions = section.actions;
                            // Add a marker for each maneuver
                            for (i = 0; i < actions.length; i += 1) {
                            let action = actions[i];
                            var marker = new H.map.Marker({
                                lat: poly[action.offset * 3],
                                lng: poly[action.offset * 3 + 1]},
                                {icon: dotIcon});
                            marker.instruction = action.instruction;
                            group.addObject(marker);
                            }

                            group.addEventListener('tap', function (evt) {
                            map.setCenter(evt.target.getGeometry());
                            openBubble(evt.target.getGeometry(), evt.target.instruction);
                            }, false);

                            // Add the maneuvers group to the map
                            map.addObject(group);
                        });
                    }

                    //從...到...再到...
                    function addWaypointsToPanel(route) {
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
                    
                    //計算時間距離
                    function addSummaryToPanel(route) {
                        let duration = 0,
                            distance = 0;

                        route.sections.forEach((section) => {
                            distance += section.travelSummary.length; // 距离以米为单位
                            duration += section.travelSummary.duration; // 时间以秒为单位
                        });

                        distance = (distance / 1000).toFixed(2); // 将距离转换为公里并保留两位小数

                        var summaryDiv = document.createElement('div'),
                            content = '<b>Total distance</b>: ' + distance + ' km. <br />' +
                            '<b>Travel Time</b>: ' + toHHMMSS(duration) + ' (in current traffic)';

                        summaryDiv.style.fontSize = 'small';
                        summaryDiv.style.marginLeft = '5%';
                        summaryDiv.style.marginRight = '5%';
                        summaryDiv.innerHTML = content;
                        routeInstructionsContainer.appendChild(summaryDiv);
                    }
                }

                function onError(error) {
                    console.error('Routing error:', error); // 打印错误信息
                    alert('路由計算失敗，請檢查地址和參數是否正確');
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

                    group.addEventListener('tap', function (evt) {
                        map.setCenter(evt.target.getGeometry());
                        openBubble(
                            evt.target.getGeometry(), evt.target.label);
                    }, false);

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
            }
        </script>
    </body>
</html>