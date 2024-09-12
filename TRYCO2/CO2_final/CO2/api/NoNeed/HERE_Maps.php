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
        <script src="map_js.js"></script>
    </body>
</html>
