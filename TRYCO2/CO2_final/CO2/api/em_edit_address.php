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
        <title>修改地址</title>
        <meta charset="utf-8"></meta>
        <link rel="stylesheet" href="css.css" type="text/css">
        <link rel="shortcut icon" href="img\logo.png">
        <script src="js.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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
                    fetch('inc/em_delete_address_inc.php', {
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
                                title: '地址刪除成功!',
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
        $editAddress_id = $_GET['edit_address'];
        $_SESSION['editAddress_id'] = $editAddress_id;

        $sql = "SELECT a.ea_name, 
                    (SELECT city_id FROM city AS b WHERE a.ea_address_city=b.city_id) AS city_id, 
                    (SELECT city_name FROM city AS b WHERE a.ea_address_city=b.city_id) AS city_name, 
                    (SELECT area_id FROM area AS c WHERE a.ea_address_area=c.area_id) AS area_id, 
                    (SELECT area_name FROM area AS c WHERE a.ea_address_area=c.area_id) AS area_name, 
                    a.ea_address_detial
                FROM em_address AS a
                WHERE a.ea_id = " . $editAddress_id;
        mysqli_query($link, "SET NAMES utf8");

        $result = mysqli_query($link, $sql);
        $fields = mysqli_num_fields($result); //取得欄位數
        $rows = mysqli_num_rows($result); //取得記錄數
        ?>
        
        <!-- 修改地址 -->
        <div class="address">
            <a href="em_index.php" class="goback_add"><img src="img\goback.png" class="goback_img"></a>
            <h1 class="">修改地址</h1>
            <form id="edit_addressForm" method="post">
                <div class="address_div">

                    <?php
                    while ($rows = mysqli_fetch_array($result)){
                    ?>

                    <label for="address_name">地址代名：</label>
                    <input type="text" id="address_name" name="address_name" value="<?php echo $rows[0]; ?>" required>

                    <br><br>

                    <div class="水平靠左">

                    <label for='address_city'>城市：</label>
                    <select class='address_city' id="city_list" name="city" onChange='getArea(this.value);' required>
                        <option value disabled selected>請選擇城市</option>
                        <?php
                        foreach($results as $city){
                            $selected = ($city["city_name"] == $rows[2]) ? "selected" : "";
                        ?>
                        <option value="<?php echo $city["city_id"]; ?>" <?php echo $selected; ?>><?php echo $city["city_name"]; ?></option>
                        <?php
                        }
                        ?>
                    </select>

                    &nbsp&nbsp

                    <label for='address_area'>鄉鎮區：</label>
                    <select class='address_city' id="area_list" name="area" required>
                        <option value="">請選擇鄉鎮區</option>
                        <option value="<?php echo $rows[3]; ?>" selected><?php echo $rows[4]; ?></option>
                    </select>

                    </div>

                    <br>

                    <label for="address_detail">詳細地址：</label>
                    <input type="text" id="address_detail" name="address_detail" value="<?php echo $rows[5]; ?>" required>

                    <?php } ?>

                    <br><br>
                </div>

                <div id="文字置中">
                    <a class="default_checkbox_a"><input type="checkbox" name="default_checkbox" class="default_checkbox" value="default">&nbsp&nbsp設為預設地址</a>
                </div>
                    
                <input type="submit" name="address_submit" data-style='address_submit' value="修改地址">
                <br>
                <!-- <input type="submit" name="delete_address" data-style='delete_address' value="刪除地址"> -->
                <!-- <a class="delete_address" onclick="deleteAddress()">刪除地址</a> -->
                <button type="button" class='delete_gowork_CO2_submit' onclick="deleteRecord()">刪除紀錄</button>

                <br><br>
                        
                <?php
                    if (isset($_POST["address_submit"])) {
                        include_once('inc\em_edit_address.inc');
                    }
                    // if (isset($_POST["delete_address"])) {
                    //     include_once('inc\em_delete_address.inc');
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
    </body>
</html>    