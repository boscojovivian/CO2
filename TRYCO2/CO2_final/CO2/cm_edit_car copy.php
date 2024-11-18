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
    <link rel="stylesheet" href="css/em_edit_CO2.css" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 設置網頁的字符集與 viewport，方便手機瀏覽 -->
    <link rel="shortcut icon" href="img/logo.png">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <script>
        const showAlert_success = (title) => {
            Swal.fire({
                title: title,
                icon: 'success'
            }).then(() => {
                window.location.href = 'index.php';
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

    <!-- 導入導覽列 -->
    <?php include 'nav/em_nav.php'?>

    <?php
        $link = mysqli_connect("localhost", "root", "") or die("無法開啟 MySQL 資料庫連結!<br>");
        mysqli_select_db($link, "carbon_emissions");
        mysqli_query($link, "SET NAMES utf8");

        $em_id = $_SESSION['em_id'];
        $editAddress_id = intval($_GET['add_address']);

        if (isset($_POST['update_address'])) {
            $address_name = $_POST['address_name'];
            $city_id = $_POST['city'];
            $area_id = $_POST['area'];
            $address_detail = $_POST['address_detail'];

            // 更新資料庫
            $update_sql = "UPDATE em_address SET 
                ea_name = '$address_name', 
                ea_address_city = $city_id, 
                ea_address_area = $area_id, 
                ea_address_detial = '$address_detail'
                WHERE ea_id = $editAddress_id";

            if (mysqli_query($link, $update_sql)) {
                echo "<script>showAlert_success('地址已成功更新');</script>";
            } else {
                echo "<script>showAlert_fail('更新失敗，請稍後再試');</script>";
            }
        }

        $sql = "SELECT a.ea_name, 
                    (SELECT city_id FROM city AS b WHERE a.ea_address_city=b.city_id) AS city_id, 
                    (SELECT city_name FROM city AS b WHERE a.ea_address_city=b.city_id) AS city_name, 
                    (SELECT area_id FROM area AS c WHERE a.ea_address_area=c.area_id) AS area_id, 
                    (SELECT area_name FROM area AS c WHERE a.ea_address_area=c.area_id) AS area_name, 
                    a.ea_address_detial
                FROM em_address AS a
                WHERE a.ea_id = $editAddress_id";

        $result = mysqli_query($link, $sql);
    ?>

    <!-- 編輯地址 -->
    <div class="address container">
        <div class="address_item">
            <div class="container-fluid">
                <div class="row g-3 d-flex justify-content-center align-items-center">
                    <form class="col-11 col-md-10 align-items-center add_form mt-5" method="POST">
                        <a href="index.php" class="goback_add ms-3"><img src="img/goback.png" class="goback_img"></a>
                        <div class="fs-4 mt-2 mb-5 ms-5 me-5">
                            <h1 class="title mb-5 text-center fw-bold">編輯地址</h1>

                            <?php while ($rows = mysqli_fetch_array($result)) { ?>

                            <!-- 地址代名 -->
                            <div class="mb-3 row justify-content-center align-items-center">
                                <label for="address_name" class="form-label col-2">地址代名：</label>
                                <input type="text" id="address_name" name="address_name" class="add_select date-range-picker col-6 oil_select" value="<?php echo $rows['ea_name']; ?>" required>
                            </div>

                            <!-- 城市 -->
                            <div class="mb-3 row justify-content-center align-items-center">
                                <label for="address_city" class="form-label col-2">城市：</label>
                                <select class="add_select col-6 oil_select" id="city_list" name="city" onChange='getArea(this.value);' required>
                                    <option value="">請選擇</option>
                                    <?php
                                    foreach($results as $city){
                                        $selected = ($city["city_name"] == $rows[2]) ? "selected" : "";
                                    ?>
                                    <option value="<?php echo $city["city_id"]; ?>" <?php echo $selected; ?>><?php echo $city["city_name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <!-- 鄉鎮區 -->
                            <div class="mb-3 row justify-content-center align-items-center">
                                <label for="address_area" class="form-label col-2">鄉鎮區：</label>
                                <select class="add_select col-6 oil_select" id="area_list" name="area" required>
                                    <option value="">請選擇鄉鎮區</option>
                                    <option value="<?php echo $rows[3]; ?>" selected><?php echo $rows[4]; ?></option>
                                </select>
                            </div>

                            <!-- 詳細地址 -->
                            <div class="mb-3 row justify-content-center align-items-center">
                                <label for="address_detail" class="form-label col-2">詳細地址：</label>
                                <input type="text" id="address_detail" name="address_detail" class="add_select date-range-picker col-6 oil_select" value="<?php echo $rows[5]; ?>" required>
                            </div>

                            <div class="mb-3 row justify-content-center align-items-center">
                                <button type="submit" name="update_address" class="btn add_btn col-6 fs-5 mt-4">確認修改</button>
                            </div>

                            <?php } ?>

                        </div>
                    </form>
                </div>
            </div> 
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
