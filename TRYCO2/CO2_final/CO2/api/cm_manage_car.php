_<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}
function translateCarType($type) {
    switch ($type) {
        case 'motorcycle':
            return '機車';
        case 'car':
            return '汽車';
        case 'truck':
            return '卡車';
        default:
            return '未知';
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>交通車資料</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css1.css" type="text/css">
        <link rel="shortcut icon" href="img\logo.png" >
        <script src="js.js"></script>    
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
    </body>
        <div class="information3" id="置中">
            <h1 class="p_information">交通車資料</h1>
            <a href="cm_add_car.php"><button class="commute">新增交通車</button></a>
        </div>
        <div class="information1" id="置中">
        <table>
            <thead>
                <tr>
                    <th>交通車編號</th>
                    <th>交通車名稱</th>
                    <th>交通車類型</th>
                    <th>編輯交通車</th>
                </tr>
            </thead>
            <tbody id="car-table-body">
                <?php
                include_once("dbcontroller.php");
                $dbController = new DBController();
                $query = "SELECT * FROM cm_car";
                $result = $dbController->runQuery($query);

                if ($result) {
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>".$row["cc_id"]."</td>";
                        echo "<td>".$row["cc_name"]."</td>";
                        echo "<td>".translateCarType($row["cc_type"])."</td>";
                        echo "<td>";
                        echo "<form action='cm_edit_car.php' method='GET'>";
                        echo "<input type='hidden' name='cc_id' value='".$row["cc_id"]."'>";
                        echo "<button type='submit' class='edit-button' name='edit_button'>編輯</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>0 results</td></tr>";
                }
                ?>
        </tbody>
    </table>
</div>
<script>
    function deleteCar(cc_id) {
        if (confirm('確定要刪除這輛交通車嗎？')) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    alert(response.message);
                    if (response.status === 'success') {
                        // 刪除成功後重新導向到 cm_manage_car.php
                        window.location.href = 'cm_manage_car.php';
                    }
                }
            };
            xhttp.open("POST", "delete_car.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("cc_id=" + cc_id);
        }
    }

</script>
     
</html>
