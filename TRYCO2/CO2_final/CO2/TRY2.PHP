<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增或修改地址</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D3E7F1;
        }

        .container {
            margin-top: 50px;
            max-width: 700px; /* 增加容器的寬度 */
        }

        .navbar {
            background-color: #A0D1A5;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-section {
            padding: 30px; /* 增加內邊距 */
            border-radius: 10px;
            position: relative;
            font-size: 18px; /* 增加字體大小 */
        }

        #add-section {
            background-color: #A0D1A5;
        }

        #edit-section {
            background-color: #E8A0A0;
        }

        .toggle-button {
            width: 50%;
            padding: 15px; /* 增加按鈕的內邊距 */
            border-radius: 10px;
            text-align: center;
            color: white;
            font-weight: bold;
            cursor: pointer;
            font-size: 20px; /* 增加按鈕的字體大小 */
        }

        #add-button {
            background-color: #8FC1B4;
        }

        #edit-button {
            background-color: #D18B8B;
        }

        .back-button {
            position: absolute;
            top: 15px; /* 調整位置 */
            left: 15px; /* 調整位置 */
            font-size: 20px; /* 增加字體大小 */
            font-weight: bold;
            color: #000;
            cursor: pointer;
            text-decoration: none;
        }

        h3 {
            font-size: 26px; /* 增加標題的字體大小 */
        }

        .form-control, .form-select {
            font-size: 18px; /* 增加表單元素的字體大小 */
            padding: 10px; /* 增加表單元素的內邊距 */
        }

        .form-check-label {
            font-size: 18px; /* 增加複選框標籤的字體大小 */
        }

        .btn {
            font-size: 20px; /* 增加按鈕的字體大小 */
            padding: 10px 20px; /* 增加按鈕的內邊距 */
        }
    </style>
</head>

<body>
    <!-- 導航欄 -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <a href="index.php"><img src=".\api\img\logo.png" class="logo"></a>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">個人首頁</a>
                    </li>
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" href="#">交通車出勤</a>
                    </li>
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" href="#">最新消息</a>
                    </li>
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" href="#">環保教室</a>
                    </li>
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" href="#">回報問題</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto d-lg-none">
                    <li class="nav-item">
                        <a class="nav-link" href="#">交通車出勤</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">最新消息</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">環保教室</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">回報問題</a>
                    </li>
                </ul>
            </div>
            <div class="navbar-text">Jo</div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-center mb-3">
            <div id="add-button" class="toggle-button" onclick="showAddAddress()">新增地址</div>
            <div id="edit-button" class="toggle-button" onclick="showEditAddress()">修改地址</div>
        </div>

        <!-- 新增地址表單 -->
        <div id="add-section" class="form-section">
            <a href="index.php" class="back-button">← 返回</a>
            <br>
            <h3>新增地址</h3>
            <form id="add-form">
                <div class="mb-3">
                    <label for="address-alias" class="form-label">地址代名:</label>
                    <input type="text" class="form-control" id="address-alias">
                </div>
                <div class="mb-3">
                    <label for="city" class="form-label">城市:</label>
                    <select class="form-select" id="city">
                        <option>台中市</option>
                        <option>台北市</option>
                        <!-- 其他城市選項 -->
                    </select>
                </div>
                <div class="mb-3">
                    <label for="district" class="form-label">鄉鎮區:</label>
                    <select class="form-select" id="district">
                        <option>東區</option>
                        <option>西區</option>
                        <!-- 其他鄉鎮區選項 -->
                    </select>
                </div>
                <div class="mb-3">
                    <label for="detailed-address" class="form-label">詳細地址:</label>
                    <input type="text" class="form-control" id="detailed-address">
                </div>
                <div class="mb-3">
                    <label for="transport" class="form-label">預設交通工具:</label><br>
                    <input type="radio" name="transport" id="transport-scooter" value="scooter"> 機車
                    <input type="radio" name="transport" id="transport-car" value="car"> 汽車
                    <input type="radio" name="transport" id="transport-mass" value="mass"> 大眾運輸
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="default-check">
                    <label class="form-check-label" for="default-check">設為預設地址</label>
                </div>
                <button type="submit" class="btn btn-primary">新增地址</button>
            </form>
        </div>

        <!-- 修改地址表單 -->
        <div id="edit-section" class="form-section" style="display: none;">
            <a href="index.php" class="back-button">← 返回</a>
            <br>
            <h3>修改地址</h3>
            <form id="edit-form">
                <div class="mb-3">
                    <label for="edit-address-alias" class="form-label">地址代名:</label>
                    <input type="text" class="form-control" id="edit-address-alias" value="家_2">
                </div>
                <div class="mb-3">
                    <label for="edit-city" class="form-label">城市:</label>
                    <select class="form-select" id="edit-city">
                        <option>台中市</option>
                        <option>台北市</option>
                        <!-- 其他城市選項 -->
                    </select>
                </div>
                <div class="mb-3">
                    <label for="edit-district" class="form-label">鄉鎮區:</label>
                    <select class="form-select" id="edit-district">
                        <option>東區</option>
                        <option>西區</option>
                        <!-- 其他鄉鎮區選項 -->
                    </select>
                </div>
                <div class="mb-3">
                    <label for="edit-detailed-address" class="form-label">詳細地址:</label>
                    <input type="text" class="form-control" id="edit-detailed-address" value="復興路4段">
                </div>
                <div class="mb-3">
                    <label for="edit-transport" class="form-label">預設交通工具:</label><br>
                    <input type="radio" name="edit-transport" id="edit-transport-scooter" value="scooter"> 機車
                    <input type="radio" name="edit-transport" id="edit-transport-car" value="car"> 汽車
                    <input type="radio" name="edit-transport" id="edit-transport-mass" value="mass"> 大眾運輸
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="edit-default-check">
                    <label class="form-check-label" for="edit-default-check">設為預設地址</label>
                </div>
                <button type="submit" class="btn btn-primary">修改地址</button>
            </form>
        </div>
    </div>

    <script>
        function showAddAddress() {
            document.getElementById('add-section').style.display = 'block';
            document.getElementById('edit-section').style.display = 'none';
        }

        function showEditAddress() {
            document.getElementById('add-section').style.display = 'none';
            document.getElementById('edit-section').style.display = 'block';
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
