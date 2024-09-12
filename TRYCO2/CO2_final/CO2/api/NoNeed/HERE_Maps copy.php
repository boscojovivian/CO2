<!-- no -->

<!DOCTYPE html>
<html>
    <head>
        <title>HERE maps</title>
        <meta charset="utf-8"></meta>
        <link rel="stylesheet" href="css.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
        <link rel="shortcut icon" href="img\logo.png" >

        <style>
            /* Define the map container's size */
            #map {
                width: 400px;
                height: 400px;
                margin-left: 4em;
            }
        </style>
    </head>
    <body>
        <form id="routingForm">
            <!-- <label for="startAddress">起點地址：</label>
            <input type="text" id="startAddress" name="startAddress" placeholder="例如: 台中市北區三民路三段129號"><br><br>
            <label for="endAddress">終點地址：</label>
            <input type="text" id="endAddress" name="endAddress" placeholder="例如: 台北市信義區忠孝東路四段205號"><br><br> -->
            <label for="Address">起點地址：</label>
            <input type="text" id="Address" name="Address" placeholder="例如: 台中市北區三民路三段129號"><br><br>

            <label for="transportMode">選擇交通工具：</label>
            <select id="transportMode" name="transportMode">
                <option value="car">汽車</option>
                <option value="pedestrian">步行</option>
                <option value="truck">卡車</option>
                <!-- 添加其他選項 -->
            </select>

            <input type="submit" value="提交">
        </form>


        <div id="map"></div>
        <div id="panel"></div>

        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
        <script>
            //初始化
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
                    zoom: 7,    //初始縮放級別
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
            // group.addEventListener('tap', function(evt){
            //     var bubble = new H.ui.InfoBubble(evt.target.getGeometry(), {
            //         content: evt.target.getData()
            //     });
            //     ui.addBubble(bubble);
            // }, false);

            //創建地圖上的標記(公司)
            var company = new H.map.Marker({ lat: 24.149878365016026, lng: 120.68366751085637 });    //創建地圖上的標記
            // company.setData("<div><p>Hello!</p></div>");
            group.addObject(company);    //將標記添加到群組

            //向HERE Maps API發送路由請求
            var router = platform.getRoutingService(null, 8);

            
            //規劃路線
            document.getElementById("routingForm").addEventListener("submit", function(event) {
                
                event.preventDefault(); // 阻止表單提交
                var form = event.target;
                var address = form.elements["Address"].value;
                var transportMode = form.elements["transportMode"].value; // 獲取所選交通工具的值

                // 使用地理編碼服務將地址轉換為經緯度座標
                geocode(address);

                function geocode(address) {
                    var geocoder = platform.getSearchService(),
                        geocodingParameters = {
                            q: address // 地址參數
                        };

                    geocoder.geocode(
                        geocodingParameters,
                        onSuccess, // 成功回調函數
                        onError // 失敗回調函數
                    );
                }

                function onSuccess(result) {
                    var locations = result.items;

                    // 將地理編碼的位置添加到地圖上
                    addLocationsToMap(locations);

                    // 將地理編碼結果的第一個位置設為起始點
                    var origin = locations[0].position.lat + ',' + locations[0].position.lng;

                    var router = platform.getRoutingService(null, 8),
                        routeRequestParams = {
                            routingMode: 'fast',    // 計算最快的路徑
                            transportMode: transportMode,   // 使用汽車作為交通工具
                            origin: origin,         // 起點
                            destination: company.getGeometry().lat + ',' + company.getGeometry().lng, // 終點
                            return: 'polyline,turnByTurnActions,actions,instructions,travelSummary'
                        };

                    router.calculateRoute(
                        routeRequestParams,
                        onSuccess,
                        onError
                    );

                    function onSuccess(result) {
                        var route = result.routes[0];

                        addRouteShapeToMap(route);
                        addManueversToMap(route);
                        addWaypointsToPanel(route);
                        addManueversToPanel(route);
                        addSummaryToPanel(route);
                    }

                    function addRouteShapeToMap(route) {
                        route.sections.forEach((section) => {
                            // decode LineString from the flexible polyline
                            let linestring = H.geo.LineString.fromFlexiblePolyline(section.polyline);

                            // Create a polyline to display the route:
                            let polyline = new H.map.Polyline(linestring, {
                            style: {
                                lineWidth: 4,
                                strokeColor: 'rgba(0, 128, 255, 0.7)'
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

                    function addSummaryToPanel(route) {
                        let duration = 0,
                            distance = 0;

                        route.sections.forEach((section) => {
                            distance += section.travelSummary.length;
                            duration += section.travelSummary.duration;
                        });

                        var summaryDiv = document.createElement('div'),
                            content = '<b>Total distance</b>: ' + distance + 'm. <br />' +
                            '<b>Travel Time</b>: ' + toMMSS(duration) + ' (in current traffic)';

                        summaryDiv.style.fontSize = 'small';
                        summaryDiv.style.marginLeft = '5%';
                        summaryDiv.style.marginRight = '5%';
                        summaryDiv.innerHTML = content;
                        routeInstructionsContainer.appendChild(summaryDiv);
                    }

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

                    //處理路由請求的回應
                    var onResult = function(result) {
                        if (result.routes && result.routes.length > 0) {
                            var route = result.routes[0];
                            var distance = route.summary.distance;
                            var travelTime = route.summary.travelTime;
                            
                            console.log("路線總長度：" + distance + "米");
                            console.log("預估行駛時間：" + (travelTime / 60).toFixed(2) + "分鐘");

                            // 处理路线的其他部分
                            result.routes[0].sections.forEach((section) => {
                                // 处理路线的各个路段
                            });
                        } else {
                            console.log("找不到有效的路線摘要信息。");
                        }
                    };

                    // //發送路由請求
                    // router.calculateRoute(routingParameters, onResult, function(error) {
                    //     alert(error.message);    //在路由請求失敗時彈出警告對話框，顯示錯誤信息
                    // });
                }

                function onError(error) {
                    alert('請輸入正確地址'); // 提示無法連接到遠程服務器
                }
                
                // 將地理編碼的位置添加到地圖上
                function addLocationsToMap(locations){

                    // var group = new H.map.Group(),
                    //     position,
                    //     i;

                    // 將每個位置添加標記到地圖上
                    for (i = 0;  i < locations.length; i += 1) {
                        let location = locations[i];
                        marker = new H.map.Marker(location.position);
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
                
                function toMMSS(seconds) {
                    var minutes = Math.floor(seconds / 60);
                    var remainingSeconds = seconds % 60;
                    return minutes + "分" + remainingSeconds + "秒";
                }
            });
        </script>
    </body>
</html>
