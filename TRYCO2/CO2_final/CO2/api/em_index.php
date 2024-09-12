

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
                <li><a class="li1" href="em_work.php" id="置中">最新消息</a></li>
                <li><a class="li1" href="em_work.php" id="置中">環保教室</a></li>
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
            <input class="choose_date_rang" type="text" id="startDate" name="startDate" placeholder="選擇日期">
            <button type="button" class="clearDate_button" id="clearDate">x</button>
            <script>
                // 初始化日期選擇器
                const dateInput = document.getElementById("startDate");
                const clearButton = document.getElementById("clearDate");

                function getWeekRange() {
                    const now = new Date();
                    const dayOfWeek = now.getDay();
                    const start = new Date(now);
                    const end = new Date(now);

                    start.setDate(now.getDate() - dayOfWeek + 1); // 設定到本周的星期一
                    end.setDate(now.getDate() + (7 - dayOfWeek)); // 設定到本周的星期日

                    return [start, end];
                }

                function getMonthRange() {
                    const now = new Date();
                    const start = new Date(now.getFullYear(), now.getMonth(), 1); // 設定到本月的第一天
                    const end = new Date(now.getFullYear(), now.getMonth() + 1, 0); // 設定到本月的最後一天

                    return [start, end];
                }

                flatpickr("#startDate", {
                    dateFormat: "Y-m-d", // 指定日期格式
                    mode: "range",
                    "locale": "zh_tw", // 设置为中文本地化
                    onReady: function(selectedDates, dateStr, instance) {
                        const container = instance.calendarContainer;
                        const weekButton = document.createElement("button");
                        weekButton.textContent = "本周";
                        weekButton.type = "button";
                        weekButton.classList.add("flatpickr_week_button");
                        weekButton.addEventListener("click", function() {
                            instance.setDate(getWeekRange());
                        });

                        const monthButton = document.createElement("button");
                        monthButton.textContent = "本月";
                        monthButton.type = "button";
                        monthButton.classList.add("flatpickr_week_button");
                        monthButton.addEventListener("click", function() {
                            instance.setDate(getMonthRange());
                        });

                        container.appendChild(weekButton);
                        container.appendChild(monthButton);
                    },
                    onClose: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length === 2) {
                            fetchData(selectedDates);
                        }
                    }
                });

                clearButton.addEventListener("click", function() {
                    dateInput._flatpickr.clear(); // 清空日期
                    window.location.href = 'em_index.php';
                });
            </script>

            <div id="my_gowork">
                <div class="wrap">
                    <table class="my_gowork_table">
                        <thead>
                            <tr>
                                <th class="my_gowork_th">日期</th>
                                <th class="my_gowork_th">上下班</th>
                                <th class="my_gowork_th">地址</th>
                                <th class="my_gowork_th">交通工具</th>
                                <th class="my_gowork_th">碳排量</th>
                                <th class="my_gowork_th">編輯</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($CO2_rows = mysqli_fetch_array($CO2_result)) {
                                $eCO2_date = $CO2_rows['eCO2_date'];
                                $eCO2_commute = $CO2_rows['eCO2_commute'];
                                $ea_id = $CO2_rows['ea_id'];
                                $ec_type = $CO2_rows['ec_type'];
                                $eCO2_carbon = $CO2_rows['eCO2_carbon'];
                                $eCO2_id = $CO2_rows['eCO2_id'];

                                echo "<tr class='my_gowork_tr'>";
                                echo "<td class='my_gowork_td'>" . $eCO2_date . "</td>";
                                echo "<td class='my_gowork_td'>" . ($eCO2_commute == "go" ? "上班" : "下班") . "</td>";

                                $ea_id_sql = "SELECT ea_name FROM em_address WHERE ea_id = " . $ea_id;
                                $ea_id_result = mysqli_query($link, $ea_id_sql);
                                $ea_id_rows = mysqli_fetch_array($ea_id_result);
                                echo "<td class='my_gowork_td'>" . $ea_id_rows['ea_name'] . "</td>";

                                echo "<td class='my_gowork_td'>" . ($ec_type == "car" ? "汽車" : ($ec_type == "bicycle" ? "機車" : "大眾運輸")) . "</td>";
                                echo "<td class='my_gowork_td'>" . $eCO2_carbon . " kg</td>";

                                echo "<td class='my_gowork_td'>
                                    <form action='em_edit_CO2.php' method='GET'>
                                        <button type='submit' name='edit_CO2' class='edit_CO2_button' value='" . $eCO2_id . "'>編輯</button>
                                    </form>
                                  </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>


            <!-- 切換頁面按鈕 -->
            <?php
            echo "<div class='turn_pages_div' id='置中'>";

            // 顯示最前面一頁和上一頁
            if($pages > 1){
                echo "<a class='turn_pages' href='em_index.php?Pages=1'><<</a> ";
                echo "<a class='turn_pages' href='em_index.php?Pages=" . ($pages-1) . "'><</a> ";
            }
            else{
                echo "<a class='Noturn_pages'><<</a> ";
                echo "<a class='Noturn_pages'><</a> ";
            }

            // 確定分頁範圍
            if ($total_pages <= 5) {
                $start_page = 1;
                $end_page = $total_pages;
            } else {
                if ($pages <= 3) {
                    $start_page = 1;
                    $end_page = 5;
                } elseif ($pages > $total_pages - 3) {
                    $start_page = $total_pages - 4;
                    $end_page = $total_pages;
                } else {
                    $start_page = $pages - 2;
                    $end_page = $pages + 2;
                }
            }

            // 確保正確的分頁範圍
            $start_page = max(1, $start_page);
            $end_page = min($total_pages, $end_page);

            if ($start_page > 1) {
                echo "<a class='turn_pages_more'> ...<a>";
            }

            for ($i = $start_page; $i <= $end_page; $i++){
                if($i != $pages){
                    echo "<a class='turn_pages' href='em_index.php?Pages=" . $i . "' onclick='fetchData()'>" . $i . " </a>";
                }
                else{
                    echo "<a class='Noturn_pages'>" . $i . " </a>";
                }
            }

            if ($end_page < $total_pages) {
                echo "<a class='turn_pages_more'> ...<a>";
            }

            // 顯示下一頁和最後面一頁
            if ($pages < $total_pages){
                echo "<a class='turn_pages' href='em_index.php?Pages=" . ($pages+1) . "'>></a> ";
                echo "<a class='turn_pages' href='em_index.php?Pages=" . $total_pages . "'>>></a>";
            }
            else{
                echo "<a class='Noturn_pages'>></a> ";
                echo "<a class='Noturn_pages'>>></a>";
            }

            echo "</div>";
            ?>

            </div>
            
            <br><br><br><br>
            <div class="bottom_space"></div>


            
            <script>
                function fetchData() {
                    var startDate = document.getElementById("startDate").value;
                    console.log(startDate);
                    if (startDate === "") {
                        // 如果未選擇日期範圍，則不執行請求
                        return;
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "em_index_get_table_data.php?startDate=" + startDate, true);
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            document.getElementById("my_gowork").innerHTML = xhr.responseText;
                        }
                    };
                    xhr.send();
                }

                document.getElementById("startDate").addEventListener("change", fetchData);
            </script>
        </div>
    </body>
</html>    