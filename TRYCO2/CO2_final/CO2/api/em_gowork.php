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

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr@4.6.3/dist/l10n/zh-tw.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
        <script>
            const showAlert_logo = (title) => {
                Swal.fire({
                    title: title,
                    imageUrl: 'img/logo.png',
                    imageWidth: 150,
                    imageHeight: 100,
                    imageAlt: 'Custom image'
                }).then(() => {
                    window.location.href = 'em_index.php';
                });
            }
            const showAlert_success = (title) => {
                Swal.fire({
                    title: title,
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'em_index.php';
                });
            }
            const showAlert_fail = (title) => {
                Swal.fire({
                    title: title,
                    icon: 'error'
                })
            }
        </script>
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
                <div id="水平靠左">
                    <div class="gowork_date_address" id="文字靠左">
                        <label class="gowork_word" for="gowork_date">選擇日期：</label><br>
                        <input class="gowork_date" type="text" id="gowork_date" name="gowork_date" placeholder="選擇日期">
                        <input type="hidden" id="dateStr" name="dateStr" value="">

                        <script>
                            // 初始化日期選擇器
                            const dateInput = document.getElementById("gowork_date");

                            function gettodayhRange() {
                                const now = new Date();
                                
                                return [now];
                            }

                            flatpickr("#gowork_date", {
                                dateFormat: "Y-m-d", // 指定日期格式
                                "locale": "zh_tw", // 设置为中文本地化
                                onChange: function(selectedDates, dateStr, instance) {
                                    // 在日期選擇後觸發
                                    console.log("選定的日期: ", dateStr); // 獲取選擇的日期字符串
                                    document.getElementById("dateStr").value = dateStr; // 將選定的日期設置到輸入框中
                                },
                                onReady: function(selectedDates, dateStr, instance) {
                                    const container = instance.calendarContainer;
                                    const todayButton = document.createElement("button");
                                    todayButton.textContent = "今天";
                                    todayButton.type = "button";
                                    todayButton.classList.add("flatpickr_today_button");
                                    todayButton.addEventListener("click", function() {
                                        instance.setDate(gettodayhRange());
                                    });
                                    container.appendChild(todayButton);
                                },
                                
                            });
                        </script>
                    </div>
                
                    <div class="gowork_date_address" id="文字靠左">
                        <label class="gowork_word" for="gowork_address">通勤地址：</label><br>
                        <?php
                        $link = mysqli_connect("localhost", "root", "A12345678") 
                        or die("無法開啟 MySQL 資料庫連結!<br>");
                        mysqli_select_db($link, "carbon_emissions");

                        $em_id = $_SESSION['em_id'];

                        $sql_default = "SELECT a.ea_id, a.ea_name, b.ea_address_city_name, b.ea_address_area_name, b.ea_address_detial
                                        FROM (SELECT ea_id, ea_name
                                                FROM em_address
                                                WHERE ea_default = 1 AND em_id = " . $em_id . ") AS a,
                                            (SELECT c.ea_id,
                                                    (SELECT city_name FROM city WHERE city.city_id = c.ea_address_city) AS ea_address_city_name, 
                                                    (SELECT area_name FROM area WHERE area.area_id = c.ea_address_area) AS ea_address_area_name,
                                                    ea_address_detial
                                                    FROM em_address AS c) AS b
                                        WHERE a.ea_id = b.ea_id";

                        $sql = "SELECT a.ea_id, a.ea_name, b.ea_address_city_name, b.ea_address_area_name, b.ea_address_detial
                                FROM (SELECT ea_id, ea_name
                                        FROM em_address
                                        WHERE ea_default != 1 AND em_id = " . $em_id . ") AS a,
                                    (SELECT c.ea_id,
                                            (SELECT city_name FROM city WHERE city.city_id = c.ea_address_city) AS ea_address_city_name, 
                                            (SELECT area_name FROM area WHERE area.area_id = c.ea_address_area) AS ea_address_area_name,
                                            ea_address_detial
                                            FROM em_address AS c) AS b
                                WHERE a.ea_id = b.ea_id";

                        mysqli_query($link, "SET NAMES utf8");

                        $result_default = mysqli_query($link, $sql_default);
                        $result = mysqli_query($link, $sql);

                        // $result_default = mysqli_query($link, $sql_default);
                        // $fields_default = mysqli_num_fields($result_default); //取得欄位數
                        // $rows_default = mysqli_num_rows($result_default); //取得記錄數

                        // $result = mysqli_query($link, $sql);
                        // $fields = mysqli_num_fields($result); //取得欄位數
                        // $rows = mysqli_num_rows($result); //取得記錄數

                        echo "<select class='gowork_address' id='gowork_address' name='gowork_address' required>";
                        while ($rows_default = mysqli_fetch_array($result_default)){
                            $gowork_address_name = $rows_default['ea_address_city_name'] . $rows_default['ea_address_area_name'] . $rows_default['ea_address_detial'];
                            echo "<option value='" . $rows_default['ea_id'] . ":" . $gowork_address_name ."'>" . $rows_default['ea_name'] . "<a class='default_address_a'>&nbsp&nbsp(預設地址)</a></option>";
                        }
                        while ($rows = mysqli_fetch_array($result)){
                            $gowork_address_name = $rows['ea_address_city_name'] . $rows['ea_address_area_name'] . $rows['ea_address_detial'];
                            echo "<option value='" . $rows['ea_id'] . ":" . $gowork_address_name ."'>" . $rows['ea_name'] . "</option>";
                        }
                        echo "</select>";

                        mysqli_close($link);
                        ?>
                    </div>
                </div>


                <div class="chosse_go_back">
                    <div id="水平靠左">
                        <label class="gowork_word" for="go_back">上班還是下班：</label>
                        <a class="gowork_word_2">(可複選)</a>
                    </div>
                    
                    <div id="水平靠左">
                        &nbsp&nbsp&nbsp&nbsp
                        <p><input type="checkbox" name="go" class="checkbox" value="go">&nbsp&nbsp上班</p>
                        <p><input type="checkbox" name="back" class="checkbox" value="back">&nbsp&nbsp下班</p>
                    </div>
                </div>
                

                <div class="chosse_car">
                    <label class="gowork_word" for="gowork_car">通勤工具：</label>
                    <div id="水平靠左">
                        <p><input type="radio" name="gowork_car" class="radio" value="bicycle">&nbsp機車</p>
                        <p><input type="radio" name="gowork_car" class="radio" value="car">&nbsp汽車</p>
                        <p><input type="radio" name="gowork_car" class="radio" value="public">&nbsp大眾運輸</p>
                    </div>
                </div>
            </div>

                <input type="hidden" id="gowork_date" name="gowork_date" value="<?php echo $gowork_date; ?>">  
                <input type="hidden" id="gowork_km" name="gowork_km" value="">

                <input type="submit" name="gowork" data-style='gowork_submit' value="新增通勤紀錄">

                <br><br>
                        
                <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        // echo "<script>console.log('表單提交成功');</script>";
                        include_once('inc\em_gowork.inc');
                    }
                ?>
            </form>
        </div>















        <script>

            document.getElementById("goworkForm").addEventListener("submit", function(event) {
                    event.preventDefault(); // 阻止表单提交

                    const addressSelect = document.getElementById("gowork_address");
                    const selectedOption = addressSelect.options[addressSelect.selectedIndex].value;
                    const selectedCar = document.querySelector('input[name="gowork_car"]:checked');
                    let selectedCarValue;

                    console.log("選擇的地址是：" + selectedOption);
                    
                    if (selectedCar) {
                        selectedCarValue = selectedCar.value;
                        console.log("選擇的通勤工具是：" + selectedCarValue);
                    } else {
                        console.log("請選擇一個通勤工具");
                        showAlert_fail('請選擇一個通勤工具');
                        // alert("請選擇一個通勤工具");
                        return;
                    }
                    // const transportMode = document.getElementById("gowork_car").value;

                    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                    // const radioButtons = document.querySelectorAll('input[type="radio"]');
                    let isChecked = false;

                    checkboxes.forEach((checkbox) => {
                        if (checkbox.checked) {
                            isChecked = true;
                        }
                    });

                    if (!isChecked) {
                        showAlert_fail('請至少選擇一個上班或下班選項');
                        // alert("請至少選擇一個上班或下班選項");
                        // event.preventDefault(); // 阻止表单提交
                        return;
                    }

                    geocode(selectedOption, selectedCarValue);
                });
            // });
            

            // 初始化
            const platform = new H.service.Platform({
                apikey: 'vLOV0OZxoNgUvE2m00AvrNTQzGhZtOPuCSwU9_BFcBg'
            });

            // 創建地圖上的標記(公司)
            const companyLat = 24.149878365016026;
            const companyLng = 120.68366751085637;

            function geocode(address, transportMode) {
                const geocoder = platform.getSearchService();
                const geocodingParameters = { q: address };

                geocoder.geocode(geocodingParameters, (result) => {
                    const locations = result.items;
                    if (locations.length > 0) {
                        const location = locations[0].position;
                        // const destinationLat = locations[0].position.lat;
                        // const destinationLng = locations[0].position.lng;
                        if(transportMode == 'public'){
                            document.getElementById('gowork_km').value = 0;
                            document.getElementById("goworkForm").submit(); // 提交表单
                        }else{
                            calculateRoute(location, transportMode);
                        }
                    } else {
                        showAlert_fail('未找到地址，請輸入正確地址');
                        // alert('未找到地址，請輸入正確地址');
                    }
                }, (error) => {
                    showAlert_fail('地理編碼失敗');
                    // alert('地理編碼失敗');
                });
            }

            function calculateRoute(location, transportMode) {
                const router = platform.getRoutingService(null, 8);
                const routeRequestParams = {
                    routingMode: 'fast',
                    transportMode: transportMode,
                    origin: `${companyLat},${companyLng}`,
                    // destination: location,
                    destination: `${location.lat},${location.lng}`,
                    return: 'travelSummary'
                };

                router.calculateRoute(routeRequestParams, (result) => {
                    if (result.routes.length > 0) {
                        const route = result.routes[0];
                        addSummaryToPanel(route);
                    } else {
                        showAlert_fail('未找到路線');
                        // alert('未找到路線');
                    }
                }, (error) => {
                    showAlert_fail('計算路線失敗');
                    // alert('計算路線失敗');
                });
            }
            function addSummaryToPanel(route) {
                let distance = 0;
                // let duration = 0;

                    route.sections.forEach((section) => {
                        distance += section.travelSummary.length;
                        // duration += section.travelSummary.duration;
                    });

                distance = (distance / 1000).toFixed(2); // 将距离转换为公里并保留两位小数
                // var total_km = distance;
                // var total_time = toHHMMSS(duration);
                console.log("公里數:" + distance);

                document.getElementById('gowork_km').value = distance;
                document.getElementById("goworkForm").submit(); // 提交表单

            }
        </script>
    </body>
</html>    