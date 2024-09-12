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

<!DOCTYPE html>
<html>
    <head>
        <title>員工首頁</title>
        <meta charset="utf-8"></meta>
        <link rel="stylesheet" href="css.css" type="text/css">
        <link rel="shortcut icon" href="img\logo.png" >
        <script src="js.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr@4.6.3/dist/l10n/zh-tw.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Oswald&display=swap" rel="stylesheet">
    </head>

    <body class="body1">
        <a href="#" class="back-to-top">︽</a>

        <!-- 上方工作列 -->
        <header id="置中">
            <a href="em_index.php"><img src="img\logo.png" class="logo"></a>
            <ul class="menu">
                <?php
                // if($_SESSION['flag'] == 1){
                //     echo "<li><a href='cm_index.php' class='li1'>管理者首頁</a></li>";
                // }
                // else{

                // }
                ?>
                <li>
                    <a class="li1" href="em_index.php" id="置中">
                        <img src="img\home.png" class="home">&nbsp個人首頁
                    </a>
                </li>
                <li><a class="li1" href="em_work.php" id="置中">交通車出勤紀錄</a></li>
                <li><a href="#" class="li1" onclick="openContactForm()" id="置中">回報問題</a></li>
                <?php
                if(isset($_SESSION['em_name'])){
                    $user_name = $_SESSION['em_name'];
                    echo "<li><a class='li1_user_name'>" . $user_name . "</a>";
                    echo "<ul>";
                ?>
                <li><button class="index" onclick="window.location.href='em_index.php'">員工首頁</button></li>
                <li><button class="index" onclick="window.location.href='cm_index.php'">管理者首頁</button></li>
                <?php
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
        

        <!-- 個人資訊 -->
        <div class="information" id="置中">
            <h1 class="p_information">個人資訊</h1>
            <a href="em_add_address.php"><button class="commute">新增地址</button></a>
            <a href="em_gowork.php"><button class="commute">新增上下班資訊</button></a>
        </div>
        <div class="my_information" id="文字置中">
            
            <!-- 我的地址 -->
            <div class="em_index_left">
                <a class="em_index_title_left">我的地址：</a>

                <?php
                    $link = mysqli_connect("localhost", "root", "A12345678") 
                    or die("無法開啟 MySQL 資料庫連結!<br>");
                    mysqli_select_db($link, "carbon_emissions");
        
                    $em_id = $_SESSION['em_id'];
        
                    $sql = "SELECT a.ea_id, a.ea_name, 
                                (SELECT city_name FROM city AS b WHERE a.ea_address_city=b.city_id) AS city, 
                                (SELECT area_name FROM area AS c WHERE a.ea_address_area=c.area_id) AS area,
                                a.ea_address_detial, a.ea_default
                            FROM em_address AS a
                            WHERE a.em_id = " . $em_id . "
                            ORDER BY a.ea_default DESC";
                    mysqli_query($link, "SET NAMES utf8");
        
                    $result = mysqli_query($link, $sql);
                    $fields = mysqli_num_fields($result); //取得欄位數
                    $rows = mysqli_num_rows($result); //取得記錄數
        
                    while ($rows = mysqli_fetch_array($result)) {
                        echo "<table class='address_table'>";
                        echo "<tr>";
                        echo "<td colspan='3' id='文字靠左'><a class='ea_name'>&nbsp&nbsp" . $rows['ea_name'];
                        if ($rows['ea_default'] == 1) {
                            echo "<a class='default_address_a'>(預設地址)</a>";
                        }
                        echo "</a></td>";
                        echo "<td class='edit_address'>
                                <form action='em_edit_address.php' method='GET'>
                                    <button type='submit' name='edit_address' class='edit_button' value='" . $rows['ea_id'] . "'>編輯地址</button>
                                </form>
                            </td>";
                        echo "</tr>";
                        echo "<tr class='address_tr'>";
                        echo "<td>&nbsp&nbsp</td>";
                        echo "<td>" . $rows['city'] . "</td>";
                        echo "<td>" . $rows['area'] . "</td>";
                        echo "<td>" . $rows['ea_address_detial'] . "</td>";
                        echo "</tr>";
                        echo "</table>";
        
                        if (isset($_GET['edit_address'])) {
                            $_SESSION['editAddress_id'] = $_GET['edit_address'];
                        }
                    }
                ?>
            </div>
            
            <!-- 我的上下班資訊 -->
            <div class="em_index_right">
                <a class="em_index_title_right">我的上下班資訊：</a>
                <input class="choose_date_rang" type="text" id="startDate" name="startDate" placeholder="選擇日期" required>
                <script>
                    // 初始化日期選擇器，添加跳到今天的快捷鍵
                    flatpickr("#startDate", {
                        dateFormat: "Y-m-d", // 指定日期格式
                        mode: "range",
                        "locale": "zh_tw", // 设置为中文本地化
                    });
                </script>

                <div class="wrap">
                    <table class="my_gowork_table">
                        <tr>
                            <th class="my_gowork_th">日期</th>
                            <th class="my_gowork_th">上下班</th>
                            <th class="my_gowork_th">地址</th>
                            <th class="my_gowork_th">交通工具</th>
                            <th class="my_gowork_th">碳排量</th>
                            <th class="my_gowork_th">編輯</th>
                        </tr>
                        <?php
                        $link = mysqli_connect("localhost", "root", "A12345678") 
                        or die("無法開啟 MySQL 資料庫連結!<br>");
                        mysqli_select_db($link, "carbon_emissions");
            
                        $em_id = $_SESSION['em_id'];

                        $CO2_sql = "SELECT a.eCO2_date, a.eCO2_commute, a.ea_id, a.ec_type, a.eCO2_carbon, a.eCO2_id 
                                FROM em_co2 AS a
                                WHERE a.em_id = " . $em_id . "
                                ORDER BY a.eCO2_date DESC";
                        mysqli_query($link, "SET NAMES utf8");

                        $CO2_result = mysqli_query($link, $CO2_sql);
                        $CO2_fields = mysqli_num_fields($CO2_result); //取得欄位數
                        $CO2_rows = mysqli_num_rows($CO2_result); //取得記錄數

                        while ($CO2_rows = mysqli_fetch_array($CO2_result)){
                            $eCO2_date = $CO2_rows[0];
                            $eCO2_commute = $CO2_rows[1];
                            $ea_id = $CO2_rows[2];
                            $ec_type = $CO2_rows[3];
                            $eCO2_carbon = $CO2_rows[4];
                            $eCO2_id = $CO2_rows[5];

                            // echo "<tr>";
                            echo "<tr class='my_gowork_tr'>";

                            // 日期
                            echo "<td class='my_gowork_td'>" . $eCO2_date . "</td>";

                            // 上下班
                            if($eCO2_commute == "go"){
                                echo "<td class='my_gowork_td'>上班</td>";
                            }else{
                                echo "<td class='my_gowork_td'>下班</td>";
                            }

                            // 地址
                            $ea_id_sql = "SELECT ea_name
                                          FROM em_address
                                          WHERE ea_id = " . $ea_id;
                            $ea_id_result = mysqli_query($link, $ea_id_sql);
                            while ($ea_id_rows = mysqli_fetch_array($ea_id_result)){
                                echo "<td class='my_gowork_td'>" . $ea_id_rows[0] . "</td>";
                            }

                            // 交通工具
                            if($ec_type == "car"){
                                echo "<td class='my_gowork_td'>汽車</td>";
                            }
                            elseif($ec_type == "bicycle"){
                                echo "<td class='my_gowork_td'>機車</td>";
                            }
                            else{
                                echo "<td class='my_gowork_td'>大眾運輸</td>";
                            }

                            // 碳排量
                            echo "<td class='my_gowork_td'>" . $eCO2_carbon . " kg</td>";

                            // 編輯
                            echo "<td class='my_gowork_td'>
                                <form action='em_edit_CO2.php' method='GET'>
                                    <button type='submit' name='edit_CO2' class='edit_CO2' value='" . $eCO2_id . "'>編輯</button>
                                </form>
                            </td>";

                            echo "</tr>";
                        }?>
                        
                    </table>

                    <?php
                        if (isset($_GET['edit_CO2'])) {
                            $_SESSION['edit_CO2_id'] = $_GET['edit_CO2'];
                        }
                    ?>
                </div>
            </div>
        </div>

        <div class="bottom_space"></div>
        




        <script>
            document.addEventListener("DOMContentLoaded", function () {
                flatpickr("#startDate", {
                    dateFormat: "Y-m-d", // 指定日期格式
                    mode: "range",
                    "locale": "zh_tw", // 设置为中文本地化
                    onChange: function (selectedDates, dateStr, instance) {
                        // 將選擇的日期範圍傳遞到後端以獲取相應的數據
                        fetchTableData(dateStr);
                    }
                });

                function fetchTableData(dateStr) {
                    // 發送 AJAX 請求以獲取表格數據
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            // 將返回的數據用於更新表格
                            document.getElementById("my_gowork_table").innerHTML = this.responseText;
                        }
                    };
                    xhttp.open("GET", "em_index_get_table_data.php?date=" + dateStr, true);
                    xhttp.send();
                }
            });
        </script>

    </body>
</html>    