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
        

        <!-- 個人資訊 -->
        <div class="information" id="置中">
            <h1 class="p_information">個人資訊</h1>
            <a href="員工新增地址.php"><button class="commute">新增地址</button></a>
        </div>
    </body>
</html>    