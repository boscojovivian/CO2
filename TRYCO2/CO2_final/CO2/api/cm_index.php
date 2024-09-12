<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>管理者首頁</title>
    <meta charset="utf-8"></meta>
    <link rel="stylesheet" href="css1.css" type="text/css">
    <link rel="shortcut icon" href="img\logo.png" >
    <script src="js.js"></script>    
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
  </head>
  <body class="body1">
  <a href="#" class="back-to-top">︽</a>
        <!-- 上方工作列 -->
        <header id="置中">
            <a href="cm_index.php"><img src="img\logo.png" class="logo"></a>
            <ul class="drop-down-menu">
                <li>
                    <a class="li1" href="cm_index.php" id="置中">
                        <img src="img\home.png" class="home">&nbsp管理者首頁
                    </a>
                </li>
                <li><a class="li1" href="cm_employee.php" id="置中">員工資料</a>
                </li>
                <li><a class="li1" href="cm_car.php" id="置中">交通車資料</a>
                    <ul>
                        <a href="cm_manage_car.php"><li>管理交通車</li></a>                
                        <!-- <a href="cm_add_car.php"><li>新增交通車</li></a> -->
                    </ul>
                </li>
                <li><a class="li2" id="置中">碳排紀錄</a>
                    <ul>
                        <a href="cm_c_co2.php"><li>交通車碳排紀錄</li></a>                  
                        <a href="cm_e_co2.php"><li>員工碳排紀錄</li></a>
                    </ul>
                </li>
                <li><a href="#" class="li1" onclick="openContactForm()" id="置中">回報問題</a></li>
                <?php
                if(isset($_SESSION['em_name'])){
                    $user_name = $_SESSION['em_name'];
                    echo "<li><a class='li1_user_name' href='#'>" . $user_name . "</a>";
                    echo "<ul>";
                ?>
                <button class="index" onclick="window.location.href='em_index.php'">員工首頁</button>
                <button class="index" onclick="window.location.href='cm_index.php'">管理者首頁</button>
                <?php
                    echo "<form method='post'>";
                    echo "<input type='submit' name='logout' data-style='logout_submit' value='登出'>";
                    echo "</form>"; 
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
                <a class="contact_title">回報問題</a>
                <hr class="contact_hr">
                <form id="form" method="post" onsubmit="return ContactFormSuccess();">
                <!-- <div class="contactForm_div"> -->
                    <label class="contactForm_label" for="sender">電子信箱：</label>
                    <?php
                    echo "<a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp" . $_SESSION['em_email'] . "</>";
                    ?>

                    <label class="contactForm_label" for="message">新增留言：</label>
                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                    <textarea class="contactForm_message" id="message" name="message" rows="4" required></textarea>
                    <br>

                    <div id="置中">
                        <input type="submit" name="contact" data-style='submit1' value="送出">
                    </div>
                    
                    <?php
                    try {
                        if (isset($_POST["contact"])) {
                            include_once('inc\message.inc');
                        }
                    } catch (Exception $e) {
                        // 
                    }
                    ?>
                <!-- </div> -->
                    
                </form>
            </div>
        </header>
        <div class="top-banner2">
            <div class="left-side1">
                <!-- 圓餅圖 -->
                <div id="pieChart" style="width: 100%;"></div>
                <?php
                require_once("dbcontroller.php");
                $db_handle = new DBController();

                // 查詢圓餅圖數據
                $pieDataQuery = "SELECT category, total_carbon FROM total_carbon";
                $pieDataResult = $db_handle->runQuery($pieDataQuery);
                if ($pieDataResult === false) {
                    die("Query Failed: " . mysqli_error($db_handle->connectDB()));
                }
                $pieData = array();
                if (!empty($pieDataResult)) {
                    foreach ($pieDataResult as $row) {
                        $pieData[$row['category']] = $row['total_carbon'];
                    }
                }
                ?>
                <script>
                    // 圓餅圖數據
                    var pieData = [{
                        values: <?php echo json_encode(array_values($pieData)); ?>,
                        labels: <?php echo json_encode(array_keys($pieData)); ?>.map(value => value + ' kg'), // 在標籤後面加上 ' kg'
                        type: 'pie',
                        marker: {
                            colors: ["#8cb4bf", "#cbb48e"]
                        }
                    }];
                    var layout = {
                        title: '碳排放量比例',
                        paper_bgcolor: '#e2f7ea', // 整個圖表區域的背景顏色
                        plot_bgcolor: '#e2f7ea'  // 繪圖區域的背景顏色
                    };

                    // 渲染圓餅圖
                    Plotly.newPlot('pieChart', pieData, layout, {
                        responsive: true,
                        title: '碳排放量比例'
                    });
                </script>
            </div>
            <div class="right-side1">
                <a href="cm_c_co2.php"><button class="search-history-button1">交通車碳排紀錄</button></a>
                <a href="cm_e_co2.php"><button class="search-history-button2" style="">員工碳排紀錄</button></a>
            </div>
        </div>      
    </body>   
</html>
