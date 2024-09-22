<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 設置網頁的字符集與 viewport，方便手機瀏覽 -->
    <title>個人首頁</title>
    <link rel="shortcut icon" href="img\logo.png" >
    <link href="css.css" rel="stylesheet"> <!-- 引入外部 CSS 文件 -->
    <link href="em_index.css" rel="stylesheet"> <!-- 引入外部 CSS 文件 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- 引入 Bootstrap 框架的 CSS 文件 -->
</head>
<body>
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

    <div class="custom-bg-position">
        <div class="custom-bg">
            <div class="text-white text-center p-5 row justify-content-md-center">
                <h1 class="fw-bold title">碳探你的路</h1>
                <div class="knowledge-box mt-5 custom-width col col-lg-12 shadow">
                    <h3 class="mt-2">環保小知識</h3>
                    <p>你知道嗎？..................................<a href="#">閱讀更多</a></p>
                </div>
            </div>
        </div>
    </div>
    

    <div class="gray-bg text-center row justify-content-md-center">
        <div class="col-md-6">
            <h1 class="fw-bold gray-bg-word">個人首頁</h1>
            <div class="row align-items-center p-4 mt-4">
                <div class="col-auto"> <!-- 設置標籤占據一小部分空間 -->
                    <label for="address" class="col-form-label fs-5">預設居家地址 :</label>
                </div>
                <div class="col"> <!-- 設置輸入框占據較大部分空間 -->
                    <div class="input-group">
                        <input type="text" class="form-control" id="address" value="台中市中區中華路一段" readonly> <!-- 地址輸入框 -->
                        <button class="btn btn-outline-secondary" type="button">+</button> <!-- 新增按鈕 -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center"> <!-- 讓這一行的內容置中 -->
            <div class="col-md-8"> <!-- 設置寬度占比，例如占據 8/12 的寬度 -->
                <h3 class="mt-6 text-center">2024年6月出勤記錄</h3>
                <div class="d-flex justify-content-center mb-3 mt-4">
                    <button class="btn btn-custom">&lt;&lt;上週</button>
                    <button class="btn btn-custom">下週&gt;&gt;</button>
                    <button class="btn btn-custom">進階查詢</button>
                    <button class="btn btn-new">新增</button>
                </div>
        
                <table class="table records-table text-center gowork-table p-4 mb-5"> <!-- 出勤記錄表格 -->
                    <thead>
                        <tr>
                            <th>日期</th>   
                            <th>上下班</th>
                            <th>地址</th>
                            <th>交通工具</th>
                            <th>碳排量</th>
                            <th>編輯</th>
                        </tr>
                    </thead>
                    <tbody> <!-- 表格內資料 -->
                        <tr>
                            <td>2024-06-07</td>
                            <td>上班</td>
                            <td>家</td>
                            <td>機車</td>
                            <td>0.13 kg</td>
                            <td><button class="btn btn-sm btn-outline-secondary">編輯</button></td>
                        </tr>
                        <tr>
                            <td>2024-06-07</td>
                            <td>下班</td>
                            <td>家</td>
                            <td>機車</td>
                            <td>0.13 kg</td>
                            <td><button class="btn btn-sm btn-outline-secondary">編輯</button></td>
                        </tr>
                        <tr>
                            <td>2024-06-06</td>
                            <td>上班</td>
                            <td>家</td>
                            <td>機車</td>
                            <td>0.13 kg</td>
                            <td><button class="btn btn-sm btn-outline-secondary">編輯</button></td>
                        </tr>
                        <tr>
                            <td>2024-06-06</td>
                            <td>下班</td>
                            <td>家</td>
                            <td>機車</td>
                            <td>0.13 kg</td>
                            <td><button class="btn btn-sm btn-outline-secondary">編輯</button></td>
                        </tr>
                        <tr>
                            <td>2024-06-05</td>
                            <td>下班</td>
                            <td>家</td>
                            <td>汽車</td>
                            <td>0.24 kg</td>
                            <td><button class="btn btn-sm btn-outline-secondary">編輯</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="plaid-bg d-flex justify-content-center align-items-center">
        <div class="col-lg-8 col-md-8"> <!-- 設置寬度比例 -->
            <h2 class="text-center mt-6">個人碳排記錄</h2>
            <div class="p-5">
                <canvas class="p-4 pink-bg mt-4" id="carbonChart"></canvas> <!-- 碳排記錄的圖表區 -->
            </div>
        </div>
    </div>

    <!-- 引入 Bootstrap JS（包含 Popper.js） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- 引入 Chart.js 用於繪製圖表 -->
    <script>
        const ctx = document.getElementById('carbonChart').getContext('2d');
        const carbonChart = new Chart(ctx, {
            type: 'bar',
            data: { /* 畫出 2023 和 2024 年每月的碳排量對比柱狀圖 */
                labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
                datasets: [{
                    label: '2023年',
                    data: [3, 4, 3, 5, 4, 6, 5, 7, 4, 6, 7, 8],
                    backgroundColor: '#FF6633'
                }, {
                    label: '2024年',
                    data: [5, 6, 4, 5, 6, 7, 8, 9, 6, 8, 9, 10],
                    backgroundColor: '#3366FF'
                }]
            },
            options: {
                responsive: true, /* 圖表會根據設備大小自動縮放 */
                scales: {
                    y: {
                        beginAtZero: true /* Y 軸從 0 開始 */
                    }
                }
            }
        });
    </script>
</body>
</html>


