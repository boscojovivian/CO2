<!-- no -->

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
        <title>新增交通車出勤紀錄</title>
        <meta charset="utf-8"></meta>
        <link rel="stylesheet" href="css.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
        <link rel="shortcut icon" href="img\logo.png" >
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>

    <body class="body1">
        <a href="#" class="back-to-top">︽</a>

        <!-- 上方工作列 -->
        <header id="置中">
            <a href="em_index.php"><img src="img\logo.png" class="logo"></a>
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
        

        <!-- 新增交通車出勤紀錄 -->
        <div class="add_work">
            <div class="add_work_left">
                <h1 class="add_work_title">新增交通車出勤紀錄</h1>
                <form id="routingForm" method="post">
                    <div id="文字置中">
                        <div id="文字靠左">
                            <label class="work_word" for="transportMode">出勤交通車：</label>
                            <br>
                            &nbsp&nbsp&nbsp&nbsp
                            <select class="choose_car" id="transportMode" name="transportMode">
                                <option value="car">汽車</option>
                                <option value="pedestrian">步行</option>
                                <option value="truck">卡車</option>
                            </select>

                            <br><br>

                            <a class="work_word">新增中途點：</a>
                            

                            <div id="address_container">
                                <script>
                                    window.onload = function() {
                                        addAddress();
                                    };
                                </script>
                            </div>
                        </div>
                        <br>
                        <button class="add_work_addAddress" type="button" onclick="addAddress()">+</button>
                        <br><br>
                        <input class="add_work_submit" type="submit" value="提交">
                    </div>
                </form>
            </div>
            <div class="add_work_right">
                <div class="map" id="map"></div>
            </div>
        </div>

        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
        <script src="js.js"></script>
        <script src="map_js.js"></script>
        <script type="text/javascript">
            let addressCount = 0;

            function addAddress() {
                addressCount++;

                const addressContainer = document.getElementById('address_container');
                const addressDiv = document.createElement('div');
                addressDiv.classList.add('work_city_div');

                const midPointLabel = document.createElement('a');
                midPointLabel.classList.add('work_word');
                midPointLabel.innerText = '中途點：';
                addressDiv.appendChild(midPointLabel);

                const deleteButton = document.createElement('button');
                deleteButton.classList.add('work_address_delete');
                deleteButton.setAttribute('type', 'button');
                deleteButton.setAttribute('onclick', 'removeAddress(this);');
                deleteButton.innerText = '刪除';
                addressDiv.appendChild(deleteButton);

                addressDiv.appendChild(document.createElement('br'));
                addressDiv.appendChild(document.createElement('br'));

                const horizontalDiv1 = document.createElement('div');
                horizontalDiv1.id = '水平靠左';

                horizontalDiv1.appendChild(document.createTextNode('\u00A0\u00A0\u00A0\u00A0'));

                const cityLabel = document.createElement('label');
                const cityId = `address_city_${addressCount}`;
                cityLabel.setAttribute('for', cityId);
                cityLabel.innerText = '城市：';
                horizontalDiv1.appendChild(cityLabel);

                const citySelect = document.createElement('select');
                citySelect.classList.add('work_city');
                citySelect.setAttribute('id', cityId);
                citySelect.setAttribute('name', 'city[]');
                citySelect.setAttribute('onChange', 'getArea(this);');
                citySelect.required = true;

                citySelect.innerHTML = `
                    <option value disabled selected>請選擇城市</option>
                    <?php foreach($results as $city): ?>
                    <option value="<?php echo $city['city_id']; ?>"><?php echo $city['city_name']; ?></option>
                    <?php endforeach; ?>
                `;

                horizontalDiv1.appendChild(citySelect);
                horizontalDiv1.appendChild(document.createTextNode('\u00A0\u00A0\u00A0\u00A0\u00A0\u00A0'));

                const areaLabel = document.createElement('label');
                const areaId = `address_area_${addressCount}`;
                areaLabel.setAttribute('for', areaId);
                areaLabel.innerText = '鄉鎮區：';
                horizontalDiv1.appendChild(areaLabel);

                const areaSelect = document.createElement('select');
                areaSelect.classList.add('work_city');
                areaSelect.setAttribute('id', areaId);
                areaSelect.setAttribute('name', 'area[]');
                areaSelect.required = true;
                areaSelect.innerHTML = '<option value="">請選擇鄉鎮區</option>';
                horizontalDiv1.appendChild(areaSelect);

                addressDiv.appendChild(horizontalDiv1);
                addressDiv.appendChild(document.createElement('br'));

                const horizontalDiv2 = document.createElement('div');
                horizontalDiv2.id = '水平靠左';

                horizontalDiv2.appendChild(document.createTextNode('\u00A0\u00A0\u00A0\u00A0'));

                const detailLabel = document.createElement('label');
                const detailId = `address_detail_${addressCount}`;
                detailLabel.setAttribute('for', detailId);
                detailLabel.innerText = '詳細地址：';
                horizontalDiv2.appendChild(detailLabel);

                horizontalDiv2.appendChild(document.createTextNode('\u00A0'));

                const detailInput = document.createElement('input');
                detailInput.classList.add('work_address_detail');
                detailInput.setAttribute('type', 'text');
                detailInput.setAttribute('id', detailId);
                detailInput.setAttribute('name', 'address_detail[]');
                detailInput.required = true;
                horizontalDiv2.appendChild(detailInput);

                addressDiv.appendChild(horizontalDiv2);
                addressContainer.appendChild(addressDiv);
            }

            function removeAddress(button) {
                const addressDiv = button.parentElement;
                addressDiv.remove();
            }

            function getArea(selectElement) {
                const addressDiv = selectElement.closest('.work_city_div');
                const areaSelect = addressDiv.querySelector('select[name="area[]"]');
                const cityId = selectElement.value;

                $.ajax({
                    type: "POST",
                    url: "getArea.php",
                    data: "city_id=" + cityId,
                    success: function(data) {
                        areaSelect.innerHTML = data;
                    }
                });
            }

        </script>
    </body>
</html>    