<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}

include 'dropdown_list/dbcontroller.php';
$db_handle = new DBController();
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 設置網頁的字符集與 viewport，方便手機瀏覽 -->
    <title>個人首頁</title>
    <link rel="shortcut icon" href="img\logo.png">
    <!-- <link href="css.css" rel="stylesheet"> -->
    <link href="em_index.css" rel="stylesheet"> <!-- 引入外部 CSS 文件 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- 引入 Bootstrap 框架的 CSS 文件 -->
</head>

<body>
    <!-- 導入導覽列 -->
    <?php include 'nav/em_nav.php'?>

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
                        <?php
                            $link = mysqli_connect('localhost', 'root', '')
                            or die("無法開啟 MySQL 資料庫連結!<br>");
                            mysqli_select_db($link, "carbon_emissions");
                            $em_id = $_SESSION['em_id'];

                            $sql = "SELECT area.area_name, city.city_name, em_address.ea_address_detial
                                    FROM em_address
                                    join area on em_address.ea_address_area = area.area_id
                                    join city on em_address.ea_address_city = city.city_id
                                    where em_address.em_id = $em_id
                                    and ea_default = 1";

                            mysqli_query($link, "SET NAMES utf8");
                            $result = mysqli_query($link, $sql);
                            $fields = mysqli_num_fields($result); //取得欄位數
                            $rows = mysqli_num_rows($result); //取得記錄數
                            ?>
                                                                                <?php
                            $rows = mysqli_fetch_array($result);
                            echo '<input type="text" class="form-control" id="address" value="' . $rows[1] . $rows[0] . $rows[2] . '" readonly>';
                        ?>
                        <!-- <input type="text" class="form-control" id="address" value="台中市中區中華路一段" readonly> 地址輸入框 -->
                        <a href="em_add_adrress.php"><button class="btn btn-outline-secondary" type="button">+</button></a> <!--新增按鈕-->
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-8">
            <h3 id="attendance-title" class="mt-6 text-center">出勤記錄</h3>
                <div class="d-flex justify-content-center mb-3 mt-4">
                    <button id="prevWeekBtn" class="btn btn-custom" onclick="changeWeek(-1)">&lt;&lt;上週</button>
                    <button id="nextWeekBtn" class="btn btn-custom" onclick="changeWeek(1)">下週&gt;&gt;</button>
                    <button class="btn btn-custom" onclick="showAdvancedSearch()">進階查詢</button>
                    <a href="em_add_CO2.php"><button class="btn btn-new">新增</button></a>
                </div>
                <!-- 進階查詢彈窗 -->
                <div id="advancedSearchModal" class="modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">進階查詢</h5>
                                <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="searchForm">
                                    <div class="form-group">
                                        <label for="start_date">開始日期</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="end_date">結束日期</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                                    </div>
                                    <button type="button" class="btn btn-primary" onclick="performAdvancedSearch()">查詢</button>
                                    <button type="button" class="btn btn-primary" onclick="resetdate()">清除條件</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table records-table text-center gowork-table p-4 mb-5">
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
                    <tbody id="attendance-table-body">
                        <!-- 這裡的數據將由 JavaScript 填充 -->
                    </tbody>
                </table>
        </div>
    </div>

    <div class="plaid-bg d-flex justify-content-center align-items-center" style="z-index: 99">
        <div class="col-lg-8 col-md-8"> <!-- 設置寬度比例 -->
            <h2 class="text-center mt-6">個人碳排記錄</h2>
            <div class="p-5">
                <canvas class="p-4 pink-bg mt-4" id="carbonChart"></canvas> <!-- 碳排記錄的圖表區 -->
            </div>
        </div>
    </div>

    <!-- 抓個人碳排資料 -->
    <?php
        $sql = "SELECT YEAR(eCO2_date) AS year, MONTH(eCO2_date) AS month, SUM(eCO2_carbon) AS total_carbon
                FROM em_co2
                WHERE em_id = $em_id
                GROUP BY
                    YEAR(eCO2_date), MONTH(eCO2_date)
                ORDER BY
                    YEAR(eCO2_date), MONTH(eCO2_date)";

        mysqli_query($link, "SET NAMES utf8");
        $result = mysqli_query($link, $sql);

        $carbonData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $year = $row['year'];
            $month = $row['month'];
            $total_carbon = $row['total_carbon'];

            // 將資料整理到以年份和月份分組的陣列中
            if (!isset($data[$year])) {
                $data[$year] = [];
            }
            $data[$year][$month] = $total_carbon;
        }

        $jsonData = json_encode($data);
        // 回傳格式如下(參考)
        // {
        //     "2022": { "1": 120, "2": 130, "3": 140 },
        //     "2023": { "1": 110, "2": 115, "3": 150 }
        // }

    ?>
    <!-- 引入 Bootstrap JS（包含 Popper.js） -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- 引入 Chart.js 用於繪製圖表 -->
    <script>
        const carbonJsonData = <?php echo $jsonData; ?>;
        console.log(carbonJsonData)
        const monthLabels = ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'];

        // 長條圖的顏色列表(顏色你們挑喜歡的，我是gpt用幫我生顏色)
        const colorList = [
            '#E36E50', '#E5BF68', '#269B8A', '#254551', '#5375CB', '#EDA29E',
            '#227E86', '#DC5037', '#425893', '#F4D0A0', '#156C8C', '#E3D5C0',
            '#4C79BD', '#D0A159', '#79595B', '#80A0B2', '#F2DE90', '#F8AC76'
        ];

        // 將資料按照格式放好
        // 每年分配不同顏色
        const datasets = Object.keys(carbonJsonData).map((year, index) => {
            const yearData = [];
            // 用for迴圈去跑那12個月的碳排資料
            for (let i = 1; i <= 12; i++) {
                yearData.push(carbonJsonData[year][i] || 0);  // 沒有碳排的月份就填0
            }
            // Json格式的資料內有幾個year就是會有幾個set
            return {
                label: `${year}年`, // 資料內的key
                data: yearData, // 該key後面帶的data
                backgroundColor: colorList[index % colorList.length]  // 循環使用顏色
            };
        });

        // 繪製圖表
        const ctx = document.getElementById('carbonChart').getContext('2d');
        const carbonChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: datasets
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });



        let currentStartDate, currentEndDate; // 宣告當前開始日與結束日
        const prevWeekButton = document.querySelector(".btn-custom:nth-child(1)"); // 上週按鈕
        const nextWeekButton = document.querySelector(".btn-custom:nth-child(2)"); // 下週按鈕
        let isAdvancedSearch = false; // 標記是否進行進階查詢

        // 獲取當前周的開始和結束日期
        function getCurrentWeekDates() {
            const today = new Date(); // 找到今天
            const monday = new Date(today.setDate(today.getDate() - today.getDay() + 1)); // 本週一
            const sunday = new Date(today.setDate(today.getDate() - today.getDay() + 7)); // 本週日
            return {
                start: monday.toISOString().split('T')[0],
                end: sunday.toISOString().split('T')[0]
            };
        }

        // 取得出勤記錄
        function fetchAttendance() {
            const form = document.getElementById("searchForm");
            var startDate;
            var endDate;

            

            // 如果是進階查詢，禁用按鈕
            if (isAdvancedSearch) {
                prevWeekButton.disabled = true;
                nextWeekButton.disabled = true;
                startDate = form.start_date.value;
                endDate = form.end_date.value;
            } else {
                // 確保按鈕在預設查詢後仍然可用
                prevWeekButton.disabled = false;
                nextWeekButton.disabled = false;
                startDate = currentStartDate;
                endDate = currentEndDate;
            }

            // 更新當前日期範圍
            currentStartDate = startDate;
            currentEndDate = endDate;

            fetch(`fetch_attendance.php?start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById("attendance-table-body");
                    // 每次抓新的資料都要把舊的清空
                    tableBody.innerHTML = ""; // 清空表格

                    // 更新標題
                    document.getElementById("attendance-title").innerText = `${currentStartDate} 到 ${currentEndDate} 出勤紀錄`;

                    if (data.length === 0) {
                        // 如果沒有資料，顯示「沒有資料」的字樣
                        const noDataRow = document.createElement("tr");
                        // 產生沒有資料的那一格，一欄跨六格
                        noDataRow.innerHTML = `<td colspan="6">沒有資料</td>`;
                        // 利用子節點的方式插進表格內
                        tableBody.appendChild(noDataRow);
                    } else {
                        data.forEach(row => {
                            const tr = document.createElement("tr");
                            tr.innerHTML = `
                                <td>${row.eCO2_date}</td>
                                <td>${row.eCO2_commute === "go" ? "上班" : "下班"}</td>
                                <td>${row.ea_name}</td>
                                <td>${row.ec_type === "car" ? "汽車" : (row.ec_type === "bicycle" ? "機車" : "大眾運輸")}</td>
                                <td>${row.eCO2_carbon}kg</td>
                                <td>
                                    <form action='em_edit_CO2.php' method='GET'>
                                        <button name='edit_CO2' class='btn btn-sm btn-outline-secondary' value='${row.eCO2_id}'>編輯</button>
                                    </form>
                                </td>
                            `;
                            tableBody.appendChild(tr);
                        });
                    }

                    // 關閉進階查詢視窗
                    closeModal();
                })
                .catch(error => console.error("Error fetching attendance data:", error));
        }

        // 重置日期
        function resetdate() {
            // 清空開始和結束日期輸入框
            document.getElementById("start_date").value = "";
            document.getElementById("end_date").value = "";
            closeModal();
            isAdvancedSearch = false; // 重置進階查詢標記
            loadAttendance(); // 重新加載出勤記錄
        }

        // 獲取上週或下週的出勤記錄
        function loadAttendance() {
            const dates = getCurrentWeekDates();
            currentStartDate = dates.start;
            currentEndDate = dates.end;
            fetchAttendance();

            // 顯示預設的日期範圍
            document.getElementById("attendance-title").innerText = `${dates.start} 到 ${dates.end} 出勤紀錄`;

            // 顯示上週和下週按鈕
            document.querySelector(".btn-custom").style.display = "inline-block"; // 上週按鈕
            document.querySelectorAll(".btn-custom")[1].style.display = "inline-block"; // 下週按鈕
        }

        // 進階查詢觸發的函數
        function performAdvancedSearch() {
            const form = document.getElementById("searchForm");
            var startDate = form.start_date.value;
            var endDate = form.end_date.value;
            
            // 檢查開始日期與結束日期是否都有輸入
            if (startDate === '' || endDate === '') {
                isAdvancedSearch = false;
                alert('請輸入條件範圍');
                return; // 如果他沒設範圍，不給他跑fetch
            }
            
            isAdvancedSearch = true; // 標記為進階查詢
            fetchAttendance(); // 繼續進行資料抓取
            // 隱藏上週和下週按鈕
            document.querySelector(".btn-custom").style.display = "none"; // 上週按鈕
            document.querySelectorAll(".btn-custom")[1].style.display = "none"; // 下週按鈕 
        }


        // 顯示進階查詢彈窗
        function showAdvancedSearch() {
            document.getElementById("advancedSearchModal").style.display = "block";
        }

        // 關閉彈窗
        function closeModal() {
            document.getElementById("advancedSearchModal").style.display = "none";
            isAdvancedSearch = false;
        }

        // 切換週數
        // 會傳入-1或是1，來決定是要下週還是上週
        function changeWeek(offset) {
            const currentDate = new Date(currentStartDate);
            // currentDate.getDate()：取得 currentDate 物件的當前日期（日）。 設今天是9/25
            //currentDate.getDay()：取得當前是星期幾，回傳值是 0（星期日）到 6（星期六）的數字。 設今天星期三，返回3
            //currentDate.getDate() - currentDate.getDay()：用當前的日數減去當前星期幾的數字
            currentDate.setDate(currentDate.getDate() + (offset * 7)); // 根據函數傳入值(1 or -1)調整日期。 以傳入值-1為例，這邊會把currentDate變成9/18
            // 所以這邊就會變成9/18-3(因為是星期三)+1，就會得到上周的星期一是9/16號
            currentStartDate = new Date(currentDate.setDate(currentDate.getDate() - currentDate.getDay() + 1)).toISOString().split('T')[0];
            // 反之則找出9/22為該週的結束日，即為星期天
            currentEndDate = new Date(currentDate.setDate(currentDate.getDate() + 6)).toISOString().split('T')[0];

            // 根據新的日期範圍查詢出勤記錄
            fetchAttendance();
        }
        // 初始化
        loadAttendance();


    </script>
</body>

</html>