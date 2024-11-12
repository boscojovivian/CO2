

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
    <meta charset="utf-8">
    <link rel="stylesheet" href="css.css" type="text/css">
    <link rel="shortcut icon" href="img/logo.png">
    <script src="js.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
      <script>
            const showAlert_success = (title) => {
                // var icon = 'img/logo.png';
                Swal.fire({
                    title: title,
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'em_index.php';
                });
            }
            const showAlert_fail = (title) => {
                // var icon = 'img/logo.png';
                Swal.fire({
                    title: title,
                    icon: 'error'
                })
            }
    </script>


<style>
       
        /* 修改表單中標籤和輸入框的字體 */
        form label, form input, form select {
            font-size: 30px; /* 調整字體大小 */
        }

        /* 修改按鈕字體 */
        input[type="submit"], input[type="button"] {
            font-size: 40px; /* 調整按鈕字體大小 */
        }
    </style>
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
    <div class="address container">
    <div class="address_item">
        
            <!--加一個div row-->
        <a href="em_index.php" class="goback_add"><img src="img/goback.png" class="goback_img"></a>
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
                    <option value="<?php echo $city["city_id"]; ?>"><?php echo $city["city_name"]; ?></option>
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

                <label for="address_detail" class="add_address_detail">詳細地址：</label>
                <input type="text" id="address_detail" name="address_detail" required>

                <br><br>
            </div>

            <div id="文字置中">
                <a class="default_checkbox_a"><input type="checkbox" name="default_checkbox" class="default_checkbox" value="default">&nbsp&nbsp設為預設地址</a>
            </div>
                
            <input type="submit" name="address_submit" data-style='address_submit' value="新增地址">

            <br><br><br>
                    
            <?php
                // if (isset($_POST["address_submit"])) {
                //     include_once('inc/em_add_address.inc');
                // }
            ?>
        </form>
    </div>

    <script type="text/javascript">
        function getArea(val){
            $.ajax({
                type : "POST",
                url : "getArea.php",
                data : "city_id=" + val,

                success : function(data){
                    $("#area_list").html(data);
                }
            })
        }

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
                    // alert('未找到地址，請輸入正確地址');
                    showAlert_fail('未找到地址，請輸入正確地址');
                    callback(false);
                }
            }, (error) => {
                showAlert_fail('地理編碼失敗');
                // alert('地理編碼失敗');
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
            const isDefault = document.querySelector('.default_checkbox').checked ? 1 : 0;

            // 组合地址进行地理编码
            const address = city + area + address_detail;

            console.log(address);

            geocode(address, (isValid) => {
                if (isValid) {
                    // 如果地址有效，通过 AJAX 提交表单数据
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'em_add_address_inc.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            // 处理服务器的响应
                            console.log(isDefault);
                            console.log(xhr.responseText);
                            if (xhr.responseText.includes('地址新增成功')) {
                                // alert('地址新增成功');
                                showAlert_success('地址新增成功!');
                                // window.location.href = 'em_index.php';
                            }
                            else if(xhr.responseText.includes('該地址代名已存在')) {
                                showAlert_fail('該地址代名已存在');
                            }
                        }
                    };
                    const params = `address_name=${address_name}&city=${city}&area=${area}&address_detail=${address_detail}&default_checkbox=${isDefault}`;
                    console.log(params);
                    xhr.send(params);
                }
            });
        }
    </script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
</body>
</html>
