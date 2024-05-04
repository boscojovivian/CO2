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
        <title>我的地址</title>
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
                    echo "<li><a href='cm_index.php' class='li1'>管理者首頁</a></li>";
                }
                else{

                }
                ?>
                <li><a class="li1" href="em_add_address.php">新增地址</a></li>
                <li><a class="li1" href="em_work.php">交通車出勤紀錄</a></li>
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
                    include_once('inc\log_out.inc');
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
                            include_once('inc\message.inc');
                        }
                    } catch (Exception $e) {
                        // 
                    }
                    ?>
                </form>
            </div>
        </header>
        

        <!-- 我的地址 -->
        <div class="information" id="置中">
            <a href="員工首頁.php" class="goback_myaddress"><img src="img\goback.png" class="goback_img"></a>
            <h1 class="p_information">我的地址</h1>
            <a href="員工新增地址.php"><button class="commute">新增地址</button></a>
        </div>
        <div>
            <?php
            $link = mysqli_connect("localhost", "root", "A12345678") 
            or die("無法開啟 MySQL 資料庫連結!<br>");
            mysqli_select_db($link, "carbon_emissions");

            $em_id = $_SESSION['em_id'];

            $sql = "SELECT a.ea_id, a.ea_name, 
                        (SELECT city_name FROM city AS b WHERE a.ea_address_city=b.city_id) AS city, 
                        (SELECT area_name FROM area AS c WHERE a.ea_address_area=c.area_id) AS area,
                        a.ea_address_detial
                    FROM em_address AS a
                    WHERE a.em_id = " . $em_id;
            mysqli_query($link, "SET NAMES utf8");

            $result = mysqli_query($link, $sql);
            $fields = mysqli_num_fields($result); //取得欄位數
            $rows = mysqli_num_rows($result); //取得記錄數

            while ($rows = mysqli_fetch_array($result)){
                echo "<table class='address_table'>";
                echo "<tr>";
                echo "<td></td>";
                echo "<td><a class='ea_name'>" . $rows[1] . "</a></td>";
                echo "<td></td>";
                echo "<td class='edit_address'>
                        <form action='員工修改地址.php' method='GET'>
                            <button type='submit' name='edit_address' class='edit_button' value='" . $rows[0] . "'>編輯地址</button>
                        </form>&nbsp&nbsp&nbsp&nbsp
                    </td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td></td>";
                echo "<td>" . $rows[2] . "</td>";
                echo "<td>" . $rows[3] . "</td>";
                echo "<td>" . $rows[4] . "</td>";
                echo "</tr>";
                echo "</table>";

                if (isset($_GET['edit_address'])) {
                    $_SESSION['editAddress_id'] = $_GET['edit_address'];
                }
            }

            mysqli_close($link);
            ?>
            </div>
    </body>
</html>    