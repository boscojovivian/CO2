<?php
// 與資料庫建立連接
$servername = "localhost";
$username = "root";
$password = "A12345678";
$dbname = "carbon_emissions";

$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接是否成功
if ($conn->connect_error) {
    die("連接資料庫失敗: " . $conn->connect_error);
}

// 查詢圓餅圖數據
$pieDataQuery = "SELECT category, total_carbon FROM total_carbon";
$pieDataResult = $conn->query($pieDataQuery);
$pieData = array();
if ($pieDataResult->num_rows > 0) {
    while ($row = $pieDataResult->fetch_assoc()) {
        $pieData[$row['category']] = $row['total_carbon'];
    }
}

// 查詢長條圖數據
$barDataQuery = "SELECT DATE_FORMAT(cm.cCO2_time, '%Y-%m-%d') AS date,
                        SUM(cm.cCO2_carbon) AS car_carbon,
                        SUM(em.eCO2_carbon) AS employee_carbon
                 FROM cm_co2 cm
                 INNER JOIN em_co2 em ON DATE(cm.cCO2_time) = DATE(em.eCO2_time)
                 GROUP BY DATE_FORMAT(cm.cCO2_time, '%Y-%m-%d')";
$barDataResult = $conn->query($barDataQuery);
$barData = array();
if ($barDataResult->num_rows > 0) {
    while ($row = $barDataResult->fetch_assoc()) {
        $date = $row['date'];
        $barData[$date] = array(
            'car_carbon' => $row['car_carbon'],
            'employee_carbon' => $row['employee_carbon']
        );
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbon Emissions</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- 圓餅圖 -->
    <div style="width:50%;">
        <canvas id="pieChart"></canvas>
    </div>

    <!-- 長條圖 -->
    <div style="width:50%;">
        <canvas id="barChart"></canvas>
    </div>

    <script>
        // 圓餅圖的配置數據
        var pieData = {
            labels: <?php echo json_encode(array_keys($pieData)); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($pieData)); ?>,
                backgroundColor: ["#FF6384", "#36A2EB"]
            }]
        };

        // 長條圖的配置數據
        var barData = {
            labels: <?php echo json_encode(array_keys($barData)); ?>,
            datasets: [{
                label: '交通車碳排放量 (kg)',
                data: <?php echo json_encode(array_map(function($data) { return $data['car_carbon']; }, $barData)); ?>,
                backgroundColor: "#FF6384"
            }, {
                label: '員工碳排放量 (kg)',
                data: <?php echo json_encode(array_map(function($data) { return $data['employee_carbon']; }, $barData)); ?>,
                backgroundColor: "#36A2EB"
            }]
        };

        // 渲染圓餅圖
        var pieCtx = document.getElementById('pieChart').getContext('2d');
        var pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: pieData,
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: '碳排放量比例'
                }
            }
        });

        // 渲染長條圖
        var barCtx = document.getElementById('barChart').getContext('2d');
        var barChart = new Chart(barCtx, {
            type: 'bar',
            data: barData,
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: '每日碳排放量'
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>
</body>
</html>
