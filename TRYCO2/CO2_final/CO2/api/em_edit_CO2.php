<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}
?>

<?php
include("dbcontroller.php");

$db_handle = new DBController();    //將DBController類別實體化為物件，透過new這個關鍵字來初始化

$query = "SELECT * FROM city";

$results = $db_handle->runQuery($query);
?>


<!DOCTYPE html>
<html>
    <head>
        <title>修改上下班資訊</title>
        <meta charset="utf-8"></meta>
        <link rel="stylesheet" href="css.css" type="text/css">
        <link rel="shortcut icon" href="img\logo.png">
        <script src="js.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <script src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
        <script src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
        <script>
            const showAlert_edit_CO2 = (title) => {
                var icon = 'img/logo.png';
                Swal.fire({
                    // icon: icon,
                    title: title,
                    imageUrl: icon,
                    imageWidth: 150,
                    imageHeight: 100,
                    imageAlt: 'Custom image'
                }).then(() => {
                    window.location.href = 'em_index.php';
                });
            }
            const showAlert_delete_CO2 = (title, callback) => {
                var icon = 'img/logo.png';
                Swal.fire({
                    title: title,
                    imageUrl: icon,
                    imageWidth: 150,
                    imageHeight: 100,
                    imageAlt: 'Custom image',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        callback();
                    }
                });
            };
            function deleteRecord() {
                showAlert_delete_CO2('確定要刪除這個紀錄嗎？', () => {
                    fetch('inc/delete_gowork_CO2_inc.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: '紀錄刪除成功!',
                                icon: 'success'
                            }).then(() => {
                                window.location.href = 'em_index.php';
                            });
                        } else {
                            Swal.fire({
                                title: '刪除失敗',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: '刪除失敗',
                            text: '請稍後再試',
                            icon: 'error'
                        });
                    });
                });
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

        <?php
        $link = mysqli_connect("localhost", "root", "A12345678") 
        or die("無法開啟 MySQL 資料庫連結!<br>");
        mysqli_select_db($link, "carbon_emissions");

        $em_id = $_SESSION['em_id'];
        $edit_CO2_id = $_GET['edit_CO2'];
        $_SESSION['edit_CO2_id'] = $edit_CO2_id;

        $sql = "SELECT a.eCO2_date, a.eCO2_commute, a.ea_id, a.ec_type, a.eCO2_carbon
                FROM em_co2 AS a
                WHERE a.eCO2_id = " . $edit_CO2_id;

        // $sql = "SELECT a.eCO2_date, a.eCO2_commute, 
        //             (SELECT b.ea_name FROM em_address AS b WHERE a.ea_id=b.ea_id) AS address_name, 
        //             a.ec_type, a.eCO2_carbon
        //         FROM em_co2 AS a
        //         WHERE a.eCO2_id =" . $editAddress_id;
        mysqli_query($link, "SET NAMES utf8");

        $result = mysqli_query($link, $sql);
        $fields = mysqli_num_fields($result); //取得欄位數
        $rows = mysqli_num_rows($result); //取得記錄數
        ?>
        
        <!-- 修改地址 -->
        <div class="address" id="文字置中">
            <a href="em_index.php" class="goback_add"><img src="img\goback.png" class="goback_img"></a>
            <h1>修改地址</h1>
            <!-- <form id="edit_gowork" method="post"> -->
                <?php
                    while ($rows = mysqli_fetch_array($result)){
                        $eCO2_date = $rows[0];
                        $eCO2_commute = $rows[1];
                        if($eCO2_commute == "go"){
                            $go_back = "上班";
                        }else{
                            $go_back = "下班";
                        }
                        $ea_id = $rows[2];
                        $ec_type = $rows[3];
                        $eCO2_carbon = $rows[4];
                ?>

                <div id="文字置中">
                    <h2>日期 : <?php echo $eCO2_date; ?>&nbsp&nbsp&nbsp<?php echo $go_back; ?></h2>
                </div>

                <form id="edit_gowork_form" method="post">

                <!-- <div class="gowork_div"> -->
                    <div class="gowork_div">
                        <?php
                        $choose_ea_id_sql = "SELECT a.ea_id, a.ea_name,
                                                (SELECT city_name FROM city WHERE city.city_id = a.ea_address_city) AS city_name, 
                                                (SELECT area_name FROM area WHERE area.area_id = a.ea_address_area) AS area_name, 
                                                a.ea_address_detial
                                            FROM em_address AS a
                                            WHERE a.em_id = " . $em_id . " AND a.ea_id = (
                                                SELECT b.ea_id
                                                FROM em_co2 AS b
                                                WHERE b.eCO2_id = " . $edit_CO2_id . "
                                            )";
                        $choose_ea_id_result = mysqli_query($link, $choose_ea_id_sql);

                        $ea_id_sql = "SELECT a.ea_id, a.ea_name,
                                        (SELECT city_name FROM city WHERE city.city_id = a.ea_address_city) AS city_name, 
                                        (SELECT area_name FROM area WHERE area.area_id = a.ea_address_area) AS area_name, 
                                        a.ea_address_detial
                                    FROM em_address AS a
                                    WHERE a.em_id = " . $em_id . " AND a.ea_id NOT IN (
                                        SELECT b.ea_id
                                        FROM em_co2 AS b
                                        WHERE b.eCO2_id = " . $edit_CO2_id . "
                                    )";
                        $ea_id_result = mysqli_query($link, $ea_id_sql);

                        // echo "<div id='文字靠左'>";
                        echo "<label class='gowork_word' for='edit_gowork_address_name'>地址：</label><br>";
                        echo "<select class='edit_gowork_CO2_address' id='edit_gowork_address_name' name='edit_gowork_address_name' required>";
                        while ($choose_ea_id_rows = mysqli_fetch_array($choose_ea_id_result)){
                            $edit_address_name = $choose_ea_id_rows['city_name'] . $choose_ea_id_rows['area_name'] . $choose_ea_id_rows['ea_address_detial'];
                            echo "<option value='" . $choose_ea_id_rows['ea_id'] . ":" . $edit_address_name ."'>" . $choose_ea_id_rows['ea_name'] . "</option>";
                        }
                        while ($ea_id_rows = mysqli_fetch_array($ea_id_result)){
                            $edit_address_name = $ea_id_rows['city_name'] . $ea_id_rows['area_name'] . $ea_id_rows['ea_address_detial'];
                            echo "<option value='" . $ea_id_rows['ea_id'] . ":" . $edit_address_name ."'>" . $ea_id_rows['ea_name'] . "</option>";
                        }
                        echo "</select>";
                        // echo "</div>";
                        ?>
                    
                        <div class="chang_car_div">
                            <label class="gowork_word" for="gowork_car">通勤工具：</label>
                            <div id="水平靠左">
                                <?php
                                if($ec_type == "bicycle"){
                                ?>
                                <p><input type="radio" name="gowork_car" class="radio" value="bicycle" <?php echo ' checked';?>>&nbsp機車</p>
                                <p><input type="radio" name="gowork_car" class="radio" value="car">&nbsp汽車</p>
                                <p><input type="radio" name="gowork_car" class="radio" value="public">&nbsp大眾運輸</p>
                                <?php
                                }elseif($ec_type == "car"){
                                ?>
                                <p><input type="radio" name="gowork_car" class="radio" value="bicycle">&nbsp機車</p>
                                <p><input type="radio" name="gowork_car" class="radio" value="car" <?php echo ' checked';?>>&nbsp汽車</p>
                                <p><input type="radio" name="gowork_car" class="radio" value="public">&nbsp大眾運輸</p>
                                <?php
                                }elseif($ec_type == "public"){
                                ?>
                                <p><input type="radio" name="gowork_car" class="radio" value="bicycle">&nbsp機車</p>
                                <p><input type="radio" name="gowork_car" class="radio" value="car">&nbsp汽車</p>
                                <p><input type="radio" name="gowork_car" class="radio" value="public" <?php echo ' checked';?>>&nbsp大眾運輸</p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                <!-- <input type="hidden" id="eCO2_date" name="eCO2_date" value="<?php echo $eCO2_date; ?>"> -->
                <input type="hidden" id="gowork_km" name="gowork_km" value="">

                <?php } ?>
                    
                <input type="submit" name="edit_gowork_CO2_submit" data-style='address_submit' value="修改紀錄">
                <br>
                <button type="button" class='delete_gowork_CO2_submit' onclick="deleteRecord()">刪除紀錄</button>

                <br><br>

                
                        
                <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        include_once('inc\edit_gowork_CO2.inc');
                    }
                ?>
            </form>
        </div>

        <script>
            document.getElementById("edit_gowork_form").addEventListener("submit", function(event) {
                    event.preventDefault(); // 阻止表单提交

                    const addressSelect = document.getElementById("edit_gowork_address_name");
                    const selectedOption = addressSelect.options[addressSelect.selectedIndex].value;
                    const selectedCar = document.querySelector('input[name="gowork_car"]:checked');
                    let selectedCarValue;

                    console.log("選擇的地址是：" + selectedOption);
                    
                    if (selectedCar) {
                        selectedCarValue = selectedCar.value;
                        console.log("選擇的通勤工具是：" + selectedCarValue);
                    } else {
                        console.log("請選擇一個通勤工具");
                        alert("請選擇一個通勤工具");
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
                            document.getElementById("edit_gowork_form").submit(); // 提交表单
                        }else{
                            calculateRoute(location, transportMode);
                        }
                    } else {
                        alert('未找到地址，請輸入正確地址');
                    }
                }, (error) => {
                    alert('地理編碼失敗');
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
                        alert('未找到路線');
                    }
                }, (error) => {
                    alert('計算路線失敗');
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
                document.getElementById("edit_gowork_form").submit(); // 提交表单

            }
        </script>
    </body>
</html> 