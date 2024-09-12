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
        <title>員工資料</title>
        <meta charset="utf-8"></meta>
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
                <li><a href="em_index.php">員工首頁</a>
                </li>
                <li><a href="employee.php">員工資料</a>
                </li>
                <li><a href="#">我的交通車</a>
                    <ul>
                        <a href="#"><li>管理交通車</li></a>                
                        <a href="#"><li>新增交通車</li></a>
                    </ul>
                </li>
                <li><a href="#">碳排紀錄</a>
                    <ul>
                        <a href="cm_c_co2.php"><li>交通車碳排紀錄</li></a>                  
                        <a href="cm_e_co2.php"><li>員工碳排紀錄</li></a>
                    </ul>
                </li>
                <li><a href="#" class="li1" onclick="openContactForm()">回報問題</a></li>
                <?php
                if(isset($_SESSION['em_name'])){
                    $user_name = $_SESSION['em_name'];
                    echo "<li><a class='li1'>" . $user_name . "</a>";
                    echo "<ul>";
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
        <div class="information1">
            <table>
                <thead>
                    <tr>
                        <th>員工編號</th>
                        <th>姓名</th>
                        <th>Email</th>
                        <th>管理員狀態</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include_once("dbcontroller.php");
                    $dbController = new DBController();
                    $query = "SELECT em_id, em_name, em_email, flag FROM employee";
                    $result = $dbController->runQuery($query);

                    if ($result) {
                        foreach ($result as $row) {
                            echo "<tr>";
                            echo "<td>".$row["em_id"]."</td>";
                            echo "<td>".$row["em_name"]."</td>";
                            echo "<td>".$row["em_email"]."</td>";
                            echo "<td>";
                            if ($row["flag"] == 0) {
                                echo "<button class='add-button' onclick='setAdmin(".$row["em_id"].")'>新增</button>";
                            }elseif($row["flag"] == 1) {
                                echo "<button class='delete-button' onclick='deleteAdmin(".$row["em_id"].")'>刪除</button>";
                            }
                            
                            echo "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>0 results</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <script>
            function setAdmin(em_id) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        var response = this.responseText;
                        alert(response); 
                    }
                };
                xhttp.open("GET", "update_flag.php?em_id=" + em_id, true);
                xhttp.send();
                setTimeout(function(){ location.reload(); }, 1000);
            }
            function deleteAdmin(em_id){
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        var response = this.responseText;
                        alert(response); 
                    }
                };
                xhttp.open("GET", "delete_admin.php?em_id=" + em_id, true);
                xhttp.send();
                setTimeout(function(){ location.reload(); }, 1000);
            }
        </script>
    </body> 
</html>