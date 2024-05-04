<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: 登入.php");
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
    </head>

    <body class="body1">
        <a href="#" class="back-to-top">︽</a>

        <!-- 上方工作列 -->
        <header id="置中">
            <a href="員工首頁.php"><img src="img\logo.png" class="logo"></a>
            <ul class="menu">
                <?php
                if($_SESSION['flag'] == 1){
                    echo "<li><a href='管理者首頁.php' class='li1'>管理者首頁</a></li>";
                }
                else{

                }
                ?>
                <li><a class="li1" href="員工新增出勤紀錄.php">交通車出勤紀錄</a></li>
                <li><a href="#" class="li1" onclick="openContactForm()">回報問題</a></li>
                <?php
                if(isset($_SESSION['em_name'])){
                    $user_name = $_SESSION['em_name'];
                    echo "<li><a class='li1'>" . $user_name . "</a>";
                    echo "<ul>";
                    echo "<li><form method='post'>";
                    echo "<input type='submit' name='logout' data-style='logout_submit' value='登出'>";
                    echo "</form></li>";
                    echo "</ul></li>";
                }
                else{
                    echo "<li><a>XXX</a></li>";
                }

                if (isset($_POST["logout"])) {
                    include_once('inc\員工登出.inc');
                }
                ?>
            </ul>

            <!-- 回報問題視窗 -->
            <div id="contactForm" class="contact-form" style="display: none;">
                <span class="close-btn" onclick="closeContactForm()">&times;</span>
                <h2>回報問題</h2>
                <form id="form" method="post" onsubmit="return ContactFormSuccess();">
                    <label for="sender">電子信箱：</label>
                    <?php
                    echo "<a>" . $_SESSION['em_email'] . "</>";
                    ?>

                    <label for="message">新增留言：</label>
                    <textarea id="message" name="message" rows="4" required></textarea>
                    <br>
                    <input type="submit" name="contact" data-style='submit1' value="送出">
                    
                    <?php
                    try {
                        if (isset($_POST["contact"])) {
                            include_once('inc\回報問題.inc');
                        }
                    } catch (Exception $e) {
                        // 
                    }
                    ?>
                </form>
            </div>
        </header>
        

        <!-- 新增通勤紀錄 -->
        <div class="gowork">
            <h1>新增上下班資訊</h1>
            <form id="goworkForm" method="post">

            <div class="gowork_div">
                <label for="go_back">上班還是下班：</label>
                <table class="gowork_table">
                <tr>
                    <td><p><input type="radio" name="go_back" class="radio" value="go"> 上班</p></td>
                    <td><p><input type="radio" name="go_back" class="radio" value="back"> 下班</p></td>
                    </tr>
                </table>

                <label for="gowork_car">通勤工具：</label>
                <table class="gowork_table">
                <tr>
                    <td><p><input type="radio" name="gowork_car" class="radio" value="scooter"> 機車</p></td>
                    <td><p><input type="radio" name="gowork_car" class="radio" value="car"> 小客車</p></td>
                    <td><p><input type="radio" name="gowork_car" class="radio" value="small_truck"> 小貨車</p></td>
                </tr>
                <tr>
                    <td><p><input type="radio" name="gowork_car" class="radio" value="big_truck"> 大貨車</p></td>
                    <td><p><input type="radio" name="gowork_car" class="radio" value="general_bus"> 一般公車客運</p></td>
                    <td><p><input type="radio" name="gowork_car" class="radio" value="highway_bus"> 國道客運/遊覽車</p></td>
                </tr>
                </table>

                <label for="gowork_address">通勤地址：</label><br>
                <?php
                $link = mysqli_connect("localhost", "root", "A12345678") 
                or die("無法開啟 MySQL 資料庫連結!<br>");
                mysqli_select_db($link, "carbon_emissions");

                $em_id = $_SESSION['em_id'];

                $sql = "SELECT ea_name
                        FROM em_address
                        WHERE em_id = " . $em_id;
                mysqli_query($link, "SET NAMES utf8");

                $result = mysqli_query($link, $sql);
                $fields = mysqli_num_fields($result); //取得欄位數
                $rows = mysqli_num_rows($result); //取得記錄數

                echo "<select class='gowork_address' id='gowork_address' name='gowork_address' required>";
                echo "<option>請選擇通勤地址</option>";
                while ($rows = mysqli_fetch_array($result)){
                    echo "<option value='" . $rows[0] ."'>" . $rows[0] . "</option>";
                }
                echo "</select>";

                mysqli_close($link);
                ?>

            </div>

                <br><br>

                <input type="submit" name="gowork" data-style='address_submit' value="新增">

                <br><br>
                        
                <?php
                    if (isset($_POST["gowork"])) {
                        include_once('inc\.inc');
                    }
                ?>
            </form>
        </div>
    </body>
</html>    