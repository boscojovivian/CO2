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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增交通車</title>
    <link rel="stylesheet" href="css1.css" type="text/css">
    <link rel="shortcut icon" href="img/logo.png">
    <script src="js.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">

    <style>
        /* 放大表單區域字體 */
        .car_div input,
        .car_div select {
            font-size: 1.5em;
            padding: 10px;
            width: 100%; /* 保證下拉框寬度 100% */
            box-sizing: border-box;
            margin-bottom: 20px;
        }

        .car_type {
        width: 100%; /* 保證下拉選單寬度填滿容器 */
        padding: 10px; /* 增加內邊距確保顯示完整 */
        font-size: 1.2em; /* 調整字體大小 */
        box-sizing: border-box; /* 防止 padding 和邊框影響寬度 */
        border-radius: 5px; /* 添加圓角邊框美化 */
        border: 1px solid #ccc; /* 邊框顏色 */
        height: 60px; /* 增加高度 */
         }

        /* 控制內部區塊內容平分 */
        .car.container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            padding: 20px;
        }

        .car_item {
            flex: 1 1 45%; /* 每個項目寬度占45%，自動調整 */
            min-width: 250px; /* 確保在小螢幕下項目不會變得太小 */
            margin-bottom: 20px;
        }

        .car_item label {
            font-size: 1.2em;
        }

        /* 美化表單提交按鈕，並加上 RWD 支援 */
        .car_div input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            padding: 12px 20px;
            font-size: 1.2em;
            width: 100%; /* 保證按鈕寬度適應外部容器 */
            box-sizing: bo  rder-box; /* 確保 padding 不影響寬度 */
        }

        .car_div input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* 改善區塊內間距 */
        /* 修改 car_div 區塊顏色 */
        .car_div {
            padding: 20px;
            border-radius: 8px;
            background-color:#81b595; /* 背景顏色，例如：淡灰色 */
            color: #333; /* 字體顏色，例如：深灰色 */
            
            font-size: 1.2em;
        }

        /* RWD 支援 - 在小螢幕下調整顯示 */
        @media (max-width: 768px) {
            .car_item {
                flex: 1 1 100%; /* 小螢幕下每個項目占滿一行 */
            }

            .car_div input[type="submit"] {
                font-size: 1.1em;
                padding: 10px;
            }
        }
        
        .input[type="submit"][data-style="car_submit"]{
            border: 2px solid #93CDA9; 
            outline: none; 
            background-color: #e2f7ea; 
            color: #3b7752; 
            cursor: pointer; 
            padding: 10px 15px 15px 15px; 
            border-radius: 10px; 

            font-size: 20px;
            font-weight: bold; 
            width: 40%;
            height: 50px;
        }

        .input[type="submit"][data-style="car_submit"]:hover {
            background-color: #3b7752;
            color: #e2f7ea;
        }

    </style>
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
    
    <div class="car container">
        <div class="car_item">
            <a href="cm_manage_car.php" class="goback_add1"><img src="img/goback.png" class="goback_img"></a>

            <h1>新增交通車</h1>

            <div class="car_div">
                <form id="carForm" method="post">
                    <label for="car_name">交通車名稱：</label>
                    <input type="text" id="car_name" name="car_name" required>

                    <label for="car_type">交通車類型：</label>
                    <select class="car_type" id="car_type" name="car_type" required>
                        <option value="" disabled selected>選擇交通車類型</option>
                        <option value="motorcycle">機車</option>
                        <option value="car">汽車</option>
                        <option value="truck">卡車</option>
                    </select>
                    <br><br>
                    <input type="submit" name="car_submit" data-style='car_submit' value="新增交通車">

               

             
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
        </div>
    </div>

    <!-- 引入 Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
</body>

</html>
