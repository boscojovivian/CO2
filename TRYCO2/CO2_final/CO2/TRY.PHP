<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 設置網頁的字符集與 viewport，方便手機瀏覽 -->
    <title>個人首頁1</title>
    <link href="styles.css" rel="stylesheet"> <!-- 引入外部 CSS 文件 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- 引入 Bootstrap 框架的 CSS 文件 -->
</head>
<body>

<!-- 導航欄 -->
<div class="nav">
    <nav class="navbar navbar-expand-lg navbar-custom"> <!-- 使用 Bootstrap 的 navbar 類別，並使用自定義樣式 -->
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
        <div>
            <img src="api\img\logo.png" class="logo" alt="Logo">
        </div> <!-- 網頁 Logo 圖片 -->
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span> <!-- 手機版的菜單按鈕 -->
        </button>
        <div class="workingspace_word">
            <div class="collapse navbar-collapse" id="navbarNav"> <!-- 可展開/收起的導航連結 -->
            
                <ul class="navbar-nav"> <!-- 導航連結列表 -->
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">個人首頁</a>
                    </li>
                    <!-- 顯示於大螢幕的額外連結 -->
                    <li class="nav-item d-none d-lg-block"> <!-- Bootstrap d-none 隱藏於小螢幕，d-lg-block 在大螢幕顯示 -->
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
            

                <!-- 手機版的菜單連結 -->
                <ul class="navbar-nav ms-auto d-lg-none"> <!-- ms-auto: 將連結置於右側，d-lg-none 隱藏於大螢幕，顯示於小螢幕 -->
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
            <div class="member">
                <div class="navbar-text">Jo</div> <!-- 導航欄右側的文字 -->
            </div>
        <!-- </div> -->
    </div>
    </nav>
</div>


<div class="container-fluid content"> <!-- 主內容區，使用 Bootstrap 的容器類別，並使用自定義的 content 樣式 -->
    <div>
        <img src="api\img\background_car.png" alt="" class="background_car">
        <a>碳探你的路</a>
    </div>
    <h1>個人首頁</h1>

    <div class="row my-4"> <!-- 使用 Bootstrap 的 row 類別來創建一行，my-4 設定上下邊距 -->
        <div class="col-md-8"> <!-- 佔據 8 格寬度的列，顯示地址信息 -->
            <label for="address">預設居家地址</label>
            <div class="input-group">
                <input type="text" class="form-control" id="address" value="台中市中區中華路一段" readonly> <!-- 地址輸入框 -->
                <button class="btn btn-outline-secondary" type="button">+</button> <!-- 新增按鈕 -->
            </div>
        </div>
        <div class="col-md-4 knowledge-box"> <!-- 佔據 4 格寬度的列，顯示環保知識區塊 -->
            <h3>環保小知識</h3>
            <p>你知道嗎？..................................<a href="#">閱讀更多</a></p>
        </div>
    </div>

<div class="row"> <!-- 另一行，用來放置表格和圖表 -->
    <div class="col-lg-6 left-section"> <!-- 左側佔據 6 格寬度，顯示出勤記錄 -->
        <h2>2024年6月出勤記錄</h2>
        <div class="d-flex justify-content-start mb-3">
            <button class="btn btn-custom">&lt;&lt;上週</button>
            <button class="btn btn-custom">下週&gt;&gt;</button>
            <button class="btn btn-custom">進階查詢</button>
            <button class="btn btn-new">新增</button>
        </div>
       
        <table class="table records-table"> <!-- 出勤記錄表格 -->
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
    <div class="col-lg-6 right-section"> <!-- 右側佔據 6 格寬度，顯示個人碳排記錄的圖表 -->
        <h2>個人碳排記錄：</h2>
        <canvas id="carbonChart"></canvas> <!-- 碳排記錄的圖表區 -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- 引入 Bootstrap JS -->
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
