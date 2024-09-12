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
        <title>新增上下班資訊</title>
        <meta charset="utf-8"></meta>
        <link rel="stylesheet" href="css.css" type="text/css">
        <link rel="shortcut icon" href="img\logo.png" >
        <script src="js.js"></script>

        <script src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
        <script src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
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
        

        <!-- 新增通勤紀錄 -->
        <div class="gowork">
            <a href="em_index.php" class="goback_gowork"><img src="img\goback.png" class="goback_img"></a>
            <h1>新增上下班資訊</h1>
            <form id="goworkForm" method="post">

            <div class="gowork_div">                   
                    
                <label class="gowork_word" for="gowork_address">通勤地址：</label><br>
                <?php
                $link = mysqli_connect("localhost", "root", "A12345678") 
                or die("無法開啟 MySQL 資料庫連結!<br>");
                mysqli_select_db($link, "carbon_emissions");

                $em_id = $_SESSION['em_id'];

                $sql_default = "SELECT ea_id, ea_name
                                FROM em_address
                                WHERE ea_default = 1 AND em_id = " . $em_id;

                $sql = "SELECT ea_id, ea_name
                        FROM em_address
                        WHERE ea_default != 1 AND em_id = " . $em_id;

                mysqli_query($link, "SET NAMES utf8");

                $result_default = mysqli_query($link, $sql_default);
                $fields_default = mysqli_num_fields($result_default); //取得欄位數
                $rows_default = mysqli_num_rows($result_default); //取得記錄數

                $result = mysqli_query($link, $sql);
                $fields = mysqli_num_fields($result); //取得欄位數
                $rows = mysqli_num_rows($result); //取得記錄數

                echo "<select class='gowork_address' id='gowork_address' name='gowork_address' required>";
                while ($rows_default = mysqli_fetch_array($result_default)){
                    echo "<option value='" . $rows_default['ea_id'] ."'>" . $rows_default['ea_name'] . "</option>";
                }
                while ($rows = mysqli_fetch_array($result)){
                    echo "<option value='" . $rows['ea_id'] ."'>" . $rows['ea_name'] . "</option>";
                }
                echo "</select>";

                mysqli_close($link);
                ?>


                <div class="chosse_go_back" id="水平靠左">
                    <label class="gowork_word" for="go_back">上班還是下班：</label>
                    <a class="gowork_word_2">(可複選)</a>
                </div>
                
                <div id="水平靠左">
                    &nbsp&nbsp&nbsp&nbsp
                    <p><input type="checkbox" name="go" class="checkbox" value="go">&nbsp&nbsp上班</p>
                    <p><input type="checkbox" name="back" class="checkbox" value="back">&nbsp&nbsp下班</p>
                </div>

                <div class="chosse_car">
                    <label class="gowork_word" for="gowork_car">通勤工具：</label>
                    <div id="水平靠左">
                        <p><input type="radio" name="gowork_car" class="radio" value="bicycle">&nbsp機車</p>
                        <p><input type="radio" name="gowork_car" class="radio" value="car">&nbsp汽車</p>
                        <p><input type="radio" name="gowork_car" class="radio" value="public">&nbsp大眾運輸</p>
                    </div>
                </div>

                <input type="hidden" id="gowork_date" name="gowork_date" value="<?php echo $gowork_date; ?>">  
                <input type="hidden" id="gowork_km" name="gowork_km" value="">              

            </div>

                <input type="submit" name="gowork" data-style='gowork_submit' value="新增通勤紀錄">

                <br><br>
                        
                <?php
                    if (isset($_POST["gowork"])) {
                        // $gowork_date = date("Y-m-d");
                        include_once('inc\em_gowork.inc');
                    }
                ?>
            </form>
        </div>















        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const form = document.getElementById("goworkForm");

                form.addEventListener("submit", function(event) {
                    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
                    const radioButtons = form.querySelectorAll('input[type="radio"]');
                    let isChecked = false;

                    checkboxes.forEach((checkbox) => {
                        if (checkbox.checked) {
                            isChecked = true;
                        }
                    });

                    if (!isChecked) {
                        alert("請至少選擇一個上班或下班選項");
                        event.preventDefault(); // 阻止表单提交
                        return false;
                    }

                    let isRadioChecked = false;
                    radioButtons.forEach((radio) => {
                        if (radio.checked) {
                            isRadioChecked = true;
                        }
                    });

                    if (!isRadioChecked) {
                        alert("請選擇一個通勤工具");
                        event.preventDefault(); // 阻止表单提交
                        return false;
                    }

                    event.preventDefault();
                    const selectedOption = form.elements["gowork_address"];
                    // const address = selectedOption.options[selectedOption.selectedIndex].text;
                    const address = <?php echo $gowork_address_name ?>;
                    const transportMode = form.elements["gowork_car"].value;

                    // 使用地理編碼服務將地址轉換為經緯度座標
                    geocode(address, transportMode, form);
                });
            });

            // 初始化
            const platform = new H.service.Platform({
                apikey: 'vLOV0OZxoNgUvE2m00AvrNTQzGhZtOPuCSwU9_BFcBg'
            });

            // 創建地圖上的標記(公司)
            const companyLat = 24.149878365016026;
            const companyLng = 120.68366751085637;

            function geocode(address, transportMode, form) {
                const geocoder = platform.getSearchService();
                const geocodingParameters = { q: address };

                geocoder.geocode(geocodingParameters, (result) => {
                    const locations = result.items;
                    if (locations.length > 0) {
                        const destinationLat = locations[0].position.lat;
                        const destinationLng = locations[0].position.lng;
                        calculateRoute(destinationLat, destinationLng, transportMode);
                    } else {
                        alert('未找到地址，請輸入正確地址');
                    }
                }, (error) => {
                    alert('地理編碼失敗');
                });
            }

            function calculateRoute(destinationLat, destinationLng, transportMode) {
                const router = platform.getRoutingService(null, 8);
                const routeRequestParams = {
                    routingMode: 'fast',
                    transportMode: transportMode,
                    origin: `${companyLat},${companyLng}`,
                    destination: `${destinationLat},${destinationLng}`,
                    return: 'travelSummary'
                };

                router.calculateRoute(routeRequestParams, (result) => {
                    if (result.routes.length > 0) {
                        const route = result.routes[0];
                        const summary = route.sections[0].travelSummary;
                        const distance = (summary.length / 1000).toFixed(2); // 公里
                        const duration = toHHMMSS(summary.duration); // 時間

                        // 儲存結果
                        const routeResult = {
                            distance: distance,
                            duration: duration
                        };

                        let total_km = routeResult.distance,
                            total_time = routeResult.duration;

                        console.log('總距離:', routeResult.distance + ' 公里');
                        console.log('時間:', routeResult.duration);
                        // 這裡可以將結果儲存到伺服器或本地儲存

                        document.getElementById('gowork_km').value = total_km;
                        form.submit(); // 提交表单
                    } else {
                        alert('未找到路線');
                    }
                }, (error) => {
                    alert('計算路線失敗');
                });
            }

            function toHHMMSS(seconds) {
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const remainingSeconds = seconds % 60;
                return `${hours}小時${minutes}分${remainingSeconds}秒`;
            }
        </script>
    </body>
</html>    