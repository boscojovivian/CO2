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

<?php
include("dbcontroller.php");

$db_handle = new DBController();    //將DBController類別實體化為物件，透過new這個關鍵字來初始化

$query = "SELECT * FROM city";

$results = $db_handle->runQuery($query);
?>


<!DOCTYPE html>
<html>
    <head>
        <title>新增地址</title>
        <meta charset="utf-8"></meta>
        <link rel="stylesheet" href="css.css" type="text/css">
        <link rel="shortcut icon" href="img\logo.png">
        <script src="js.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

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
        
        <!-- 新增地址 -->
        <div class="address" >
            <a href="em_index.php" class="goback_add"><img src="img\goback.png" class="goback_img"></a>
            <h1 class="">新增地址</h1>
            <form id="addressForm" method="post" onsubmit="handleFormSubmission(event)">
                <div class="address_div">
                    <label for="address_name">地址代名：</label>
                    <input type="text" id="address_name" name="address_name" required>

                    <br><br>

                    <div class="水平靠左">

                    <label for='address_city'>城市：</label>
                    <select class='address_city' id="city_list" name="city" onChange='getArea(this.value);' required>
                        <option value disabled selected>請選擇城市</option>
                        <?php
                        foreach($results as $city){
                        ?>
                        <option value = "<?php echo $city["city_id"]; ?>"><?php echo $city["city_name"]; ?></option>
                        <?php
                        }
                        ?>
                    </select>

                    &nbsp&nbsp

                    <label for='address_area'>鄉鎮區：</label>
                    <select class='address_city' id="area_list" name="area" required>
                        <option value="">請選擇鄉鎮區</option>
                    </select>

                    </div>

                    <br>

                    <label for="address_detail">詳細地址：</label>
                    <input type="text" id="address_detail" name="address_detail" required>

                    

                    <br><br>
                </div>
                    
                <input type="submit" name="address_submit" data-style='address_submit' value="新增地址">

                <br><br><br>
                        
                <?php
                    // if (isset($_POST["address_submit"])) {
                    //     include_once('inc\em_add_address.inc');
                    // }
                ?>
            </form>
        </div>

        <script type="text/javascript">
            function getArea(val){
                $.ajax({
                    type : "POST",   //請求資料的方式
                    url : "getArea.php",    //要請求資料的網址
                    //當某個city被選擇時把國家的id POST到後端(getArea)
                    data : "city_id=" + val,    //使用SQL語法到一料庫抓states資料表的資料

                    success : function(data){   //接收成功時執行
                        $("#area_list").html(data);     //取得從getArea.php回傳的資料
                    }
                })
            }
        </script>
        <script>
            // 初始化 HERE 平台
            const platform = new H.service.Platform({
                apikey: 'vLOV0OZxoNgUvE2m00AvrNTQzGhZtOPuCSwU9_BFcBg'
            });

            // 地理编码函数
            function geocode(address, callback) {
                const geocoder = platform.getSearchService();
                const geocodingParameters = { q: address };

                geocoder.geocode(geocodingParameters, (result) => {
                    const locations = result.items;
                    if (locations.length > 0) {
                        callback(true);
                    } else {
                        alert('未找到地址，請輸入正確地址');
                        callback(false);
                    }
                }, (error) => {
                    alert('地理編碼失敗');
                    callback(false);
                });
            }

            // 处理表单提交
            function handleFormSubmission(event) {
                event.preventDefault();

                const address_name = document.getElementById('address_name').value;
                const city = document.getElementById('city_list').options[document.getElementById('city_list').selectedIndex].text;
                const area = document.getElementById('area_list').options[document.getElementById('area_list').selectedIndex].text;
                const address_detail = document.getElementById('address_detail').value;

                // 组合地址进行地理编码
                const address = city + area + address_detail;

                geocode(address, (isValid) => {
                    if (isValid) {
                        // 如果地址有效，通过 AJAX 提交表单数据
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', 'em_add_address_inc.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function () {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                // 处理服务器的响应
                                alert(xhr.responseText);
                                if (xhr.responseText.includes('地址新增成功')) {
                                    window.location.href = 'em_index.php';
                                }
                            }
                        };
                        const params = `address_name=${address_name}&city=${city}&area=${area}&address_detail=${address_detail}`;
                        xhr.send(params);
                    }
                });
            }
        </script>
    </body>
</html>    