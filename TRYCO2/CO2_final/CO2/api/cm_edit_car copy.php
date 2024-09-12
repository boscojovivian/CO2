<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    header("Location: Sign_in.php");
    exit();
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>編輯交通車資料</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css1.css" type="text/css">
    <link rel="shortcut icon" href="img/logo.png">

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
        
        const showAlert = (title, icon = 'info', callback = null) => {
            Swal.fire({
                title: title,
                icon: icon
            }).then(() => {
                if (callback) callback();
            });
        }

        const showAlertWithImage = (title, callback = null) => {
            Swal.fire({
                title: title,
                imageUrl: 'img/logo.png',
                imageWidth: 150,
                imageHeight: 100,
                imageAlt: 'Custom image',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed && callback) callback();
            });
        };

        const deleteRecord = (cc_id) => {
            showAlertWithImage('確定要刪除這個紀錄嗎？', () => {
                fetch('delete_car.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ cc_id: cc_id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('紀錄刪除成功!', 'success', () => {
                            window.location.href = 'cm_manage_car.php';
                        });
                    } else {
                        showAlert('刪除失敗: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showAlert('刪除失敗: 請稍後再試', 'error');
                });
            });
        }
    </script>
</head>
<body class="body1">
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
        include_once("dbcontroller1.php");
        $dbController = new DBController();
        
        if (isset($_GET['cc_id'])) {
            $cc_id = $_GET['cc_id'];
            $query = "SELECT * FROM cm_car WHERE cc_id = ?";
            $params = array($cc_id);
            $car = $dbController->runQuery($query, "i", $params);
        
            if (!$car) {
                echo "找不到該交通車資料。";
                exit();
            }
            $car = $car[0];
        } else {
            echo "沒有提供交通車編號。";
            exit();
        }
        
        if (isset($_POST['update'])) {
            $cc_name = $_POST['cc_name'];
            $cc_type = $_POST['cc_type'];
        
            $updateQuery = "UPDATE cm_car SET cc_name = ?, cc_type = ? WHERE cc_id = ?";
            $updateParams = array($cc_name, $cc_type, $cc_id);
            $updateResult = $dbController->updateQuery($updateQuery, "ssi", $updateParams);
        
            if ($updateResult) {
                echo '<script>showAlert_success("交通車資料更新成功！");</script>';
                // echo '<script>alert("交通車資料更新成功！");</script>';
                // echo "<script>window.location.href = 'cm_manage_car.php';</script>";
                exit();
            } else {
                echo '<script>showAlert_fail("更新交通車失敗！");</script>';
                // echo '<script>alert("更新交通車失敗！");</script>';
            }
            
        }
        ?>
        <a href="cm_manage_car.php" class="goback_add1"><img src="img\goback.png" class="goback_img"></a>
        <h1>編輯交通車資料</h1>
        <div class="car_div">
        <form method="post">
            <label for="cc_name">交通車名稱：</label>
            <input type="text" id="cc_name" name="cc_name" value="<?php echo htmlspecialchars($car['cc_name']); ?>" required>
            <br><br>
            <div class="水平靠左">
            <label for="cc_type">交通車類型：</label>
            <select class="car_type" id="cc_type" name="cc_type" required>
                <option value="" disabled <?php echo empty($car['cc_type']) ? 'selected' : ''; ?>>選擇交通車類型</option>
                <option value="motorcycle" <?php echo $car['cc_type'] == 'motorcycle' ? 'selected' : ''; ?>>機車</option>
                <option value="car" <?php echo $car['cc_type'] == 'car' ? 'selected' : ''; ?>>汽車</option>    
                <option value="truck" <?php echo $car['cc_type'] == 'truck' ? 'selected' : ''; ?>>卡車</option>
            </select>
            </div>
            <br><br>
            </div>
            <input type="submit" name="update" data-style='car_submit' value="更新交通車">
            <br>
            <!-- <input type="button" onclick="deleteCar(<?php echo $car['cc_id']; ?>)" data-style='car_delete' value="刪除"> -->
            <input type="button" onclick="deleteRecord('<?php echo $cc_id; ?>')" data-style='car_delete' value="刪除">
            <br><br>
        </form>
        
 
</div>
</body>
<script>
    function deleteCar(cc_id) {
        if (confirm('確定要刪除這輛交通車嗎？')) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    alert(response.message);
                    if (response.status === 'success') {
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
