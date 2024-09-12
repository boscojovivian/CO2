<?php
session_start();
// 检查用户是否已登录
if (!isset($_SESSION['em_id'])) {
    // 如果未登录，重定向到登录页面
    header("Location: Sign_in.php");
    exit();
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>新增交通車</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css1.css" type="text/css">
    <link rel="shortcut icon" href="img\logo.png">
    <script src="js.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

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
                    window.location.href = 'cm_manage_car.php';
                });
            }
            const showAlert_success = (title) => {
                Swal.fire({
                    title: title,
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'cm_manage_car.php';
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
            <a href="cm_index.php"><img src="img\logo.png" class="logo"></a>
            <ul class="drop-down-menu">
                <li>
                    <a class="li1" href="cm_index.php" id="置中">
                        <img src="img\home.png" class="home">&nbsp管理者首頁
                    </a>
                </li>
                <li><a class="li1" href="cm_employee.php" id="置中">員工資料</a>
                </li>
                <li><a class="li1" href="cm_car.php" id="置中">交通車資料</a>
                    <ul>
                        <a href="cm_manage_car.php"><li>管理交通車</li></a>                
                        <!-- <a href="cm_add_car.php"><li>新增交通車</li></a> -->
                    </ul>
                </li>
                <li><a class="li2" id="置中">碳排紀錄</a>
                    <ul>
                        <a href="cm_c_co2.php"><li>交通車碳排紀錄</li></a>                  
                        <a href="cm_e_co2.php"><li>員工碳排紀錄</li></a>
                    </ul>
                </li>
                <li><a href="#" class="li1" onclick="openContactForm()" id="置中">回報問題</a></li>
                <?php
                if(isset($_SESSION['em_name'])){
                    $user_name = $_SESSION['em_name'];
                    echo "<li><a class='li1_user_name' href='#'>" . $user_name . "</a>";
                    echo "<ul>";
                ?>
                <button class="index" onclick="window.location.href='em_index.php'">員工首頁</button>
                <button class="index" onclick="window.location.href='cm_index.php'">管理者首頁</button>
                <?php
                    echo "<form method='post'>";
                    echo "<input type='submit' name='logout' data-style='logout_submit' value='登出'>";
                    echo "</form>"; 
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

    <div class="car">
        <?php
        include("dbcontroller.php");

        $db_handle = new DBController();
        ?>
        <a href="cm_manage_car.php" class="goback_add1"><img src="img\goback.png" class="goback_img"></a>
        
        <h1 class="">新增交通車</h1>

        <div class="car_div">
            <form id="carForm" method="post">
            <label for="car_name">交通車名稱：</label>
            <input type="text" id="car_name" name="car_name" required>
            <br><br>
            <div class="水平靠左">
                <label for="car_type">交通車類型：</label>
                <select class="car_type" id="car_type" name="car_type" required>
                    <option value="" disabled selected>選擇交通車類型</option>
                    <option value="motorcycle">機車</option>
                    <option value="car">汽車</option>    
                    <option value="truck">卡車</option>
                </select>
            </div>
            <br><br>
        </div>

            <input type="submit" name="car_submit" data-style='car_submit' value="新增交通車">

            <br><br><br>

            <?php
            if (isset($_POST["car_submit"])) {
                $car_name = $_POST['car_name'];
                $car_type = $_POST['car_type'];

                // 將新交通車資料插入到資料庫
                $insert_query = "INSERT INTO cm_car (cc_name, cc_type) VALUES ('$car_name', '$car_type')";
                $result = $db_handle->executeUpdate($insert_query);

                if ($result) {
                    echo '<script>showAlert_success("新增交通車成功！");</script>';
                    // echo '<script>alert("新增交通車成功！");</script>';
                } else {
                    echo '<script>showAlert_fail("新增交通車失敗！");</script>';
                    // echo '<script>alert("新增交通車失敗！");</script>';
                }
            }
            ?>
        </form>
        
    </div>

</body>
</html>
