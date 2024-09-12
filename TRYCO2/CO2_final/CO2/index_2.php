<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 設置網頁的字符集與 viewport，方便手機瀏覽 -->
    <title>新增或修改地址</title>
    <link href="css.css" rel="stylesheet"> <!-- 引入外部 CSS 文件 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- 引入 Bootstrap 框架的 CSS 文件 -->
</head>
<body>
    <!-- 導航欄 -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top top-background">
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

    <div class="container">
        <div id="add-section" class="form-section">
            <a href="em_index.php" class="back-button">← 返回</a>
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
    </div>

    <!-- 引入 Bootstrap JS（包含 Popper.js） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>