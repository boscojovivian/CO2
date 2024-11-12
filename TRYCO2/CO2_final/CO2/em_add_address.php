

<?php
session_start();

include("dropdown_list/dbcontroller.php");

$db_handle = new DBController();    //將DBController類別實體化為物件，透過new這個關鍵字來初始化

$query = "SELECT * FROM city";

$results = $db_handle->runQuery($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>編輯地址</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/em_add_adrress.css" type="text/css">
    <link rel="shortcut icon" href="img/logo.png">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
      <script>
            const showAlert_success = (title) => {
                // var icon = 'img/logo.png';
                Swal.fire({
                    title: title,
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'index.php';
                });
            }
            const showAlert_fail = (title) => {
                // var icon = 'img/logo.png';
                Swal.fire({
                    title: title,
                    icon: 'error'
                })
            }
    </script>

    <style>
        /* 修改表單中標籤和輸入框的字體 */
        form label, form input, form select {
            font-size: 30px; /* 調整字體大小 */
        }

        /* 修改按鈕字體 */
        input[type="submit"], input[type="button"] {
            font-size: 40px; /* 調整按鈕字體大小 */
        }
    </style>
</head>
<body class="body1">

    <!-- 導入導覽列 -->
    <?php include 'nav/em_nav.php'?>

    <?php
        $link = mysqli_connect("localhost", "root", "") 
        or die("無法開啟 MySQL 資料庫連結!<br>");
        mysqli_select_db($link, "carbon_emissions");

        $em_id = $_SESSION['em_id'];
        $editAddress_id = intval($_GET['add_address']);
        // $_SESSION['editAddress_id'] = $editAddress_id;


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
        // $fields = mysqli_num_fields($result); //取得欄位數
        // $rows = mysqli_num_rows($result); //取得記錄數
    ?>

    
    <!-- 新增地址 -->
    <div class="address container">
    <div class="address_item">
        <div class="container-fluid">
            <div class="row g-3 d-flex justify-content-center align-items-center">
                <form class="col-11 col-md-8 align-items-center add_form mt-5" method="POST">
                    <a href="index.php" class="goback_add ms-3"><img src="img/goback.png" class="goback_img"></a>
                    <div class="fs-5 m-5">
                        <h1 class="title m-4 text-center fw-bold">編輯地址</h1>

                        <?php
                        while ($rows = mysqli_fetch_array($result)) {
                        ?>

                        <div class="mb-3 row justify-content-center align-items-center">
                            <label for="address_name">地址代名：</label>
                            <input type="text" id="address_name" name="address_name" value="<?php echo $rows['ea_name']; ?>" required>
                        </div>

                    

                        <div class="mb-3 row justify-content-center align-items-center">
                            <button type="submit" name="car_oil" class="btn add_btn col-6 fs-5">確認修改</button>
                        </div>
                        
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div> 
            <?php
                // if (isset($_POST["address_submit"])) {
                //     include_once('inc/em_add_address.inc');
                // }
            ?>
    </div>
    </div>

    <script type="text/javascript">
        function getArea(val){
            $.ajax({
                type : "POST",
                url : "dropdown_list/getArea.php",
                data : "city_id=" + val,

                success : function(data){
                    $("#area_list").html(data);
                }
            })
        }

        // 初始化 HERE 平台
        const platform = new H.service.Platform({
            apikey: 'vLOV0OZxoNgUvE2m00AvrNTQzGhZtOPuCSwU9_BFcBg'
        });

        // 地理编码函数
        function geocode(address, callback) {
            const geocoder = platform.getSearchService();
            const geocodingParameters = { q: address };

            geocoder.geocode(geocodingParameters, (result) => {
                const locations = result.items;
                if (locations.length > 0) {
                    callback(true);
                } else {
                    // alert('未找到地址，請輸入正確地址');
                    showAlert_fail('未找到地址，請輸入正確地址');
                    callback(false);
                }
            }, (error) => {
                showAlert_fail('地理編碼失敗');
                // alert('地理編碼失敗');
                callback(false);
            });
        }

        // 处理表单提交
        function handleFormSubmission(event) {
            event.preventDefault();

            const address_name = document.getElementById('address_name').value;
            const city = document.getElementById('city_list').options[document.getElementById('city_list').selectedIndex].text;
            const area = document.getElementById('area_list').options[document.getElementById('area_list').selectedIndex].text;
            const address_detail = document.getElementById('address_detail').value;
            const isDefault = document.querySelector('.default_checkbox').checked ? 1 : 0;

            // 组合地址进行地理编码
            const address = city + area + address_detail;

            console.log(address);

            geocode(address, (isValid) => {
                if (isValid) {
                    // 如果地址有效，通过 AJAX 提交表单数据
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'em_add_address_inc.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            // 处理服务器的响应
                            console.log(isDefault);
                            console.log(xhr.responseText);
                            if (xhr.responseText.includes('地址新增成功')) {
                                // alert('地址新增成功');
                                showAlert_success('地址新增成功!');
                                // window.location.href = 'em_index.php';
                            }
                            else if(xhr.responseText.includes('該地址代名已存在')) {
                                showAlert_fail('該地址代名已存在');
                            }
                        }
                    };
                    const params = `address_name=${address_name}&city=${city}&area=${area}&address_detail=${address_detail}&default_checkbox=${isDefault}`;
                    console.log(params);
                    xhr.send(params);
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
</body>
</html>
