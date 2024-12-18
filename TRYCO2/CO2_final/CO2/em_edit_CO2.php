<?php
session_start();
include("dropdown_list/dbcontroller.php");

$db_handle = new DBController();
$query = "SELECT * FROM city";
$results = $db_handle->runQuery($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>編輯通勤資訊</title>
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
<body>

    <!-- 導入導覽列 -->
    <?php include 'nav/em_nav.php'?>
    <?php
        $link = mysqli_connect("localhost", "root", "") or die("無法開啟 MySQL 資料庫連結!<br>");
        mysqli_select_db($link, "carbon_emissions");
        mysqli_query($link, "SET NAMES utf8");

        $em_id = $_SESSION['em_id'];
        if (isset($_GET['edit_CO2'])) {
            $editCO2_id = $_GET['edit_CO2'];
        }

        if (isset($_POST['update_CO2'])) {
            $ea_id = $_POST['address_name'];  
            $ec_type = $_POST['transport_type'];  

            // 更新資料庫
            $update_sql = "UPDATE em_CO2 SET 
                ea_id = (SELECT ea_id FROM em_address WHERE ea_name = '$ea_id' LIMIT 1), 
                ec_type = '$ec_type'
                WHERE eCO2_id = $editCO2_id";

            if (mysqli_query($link, $update_sql)) {
                echo "<script>showAlert_success('已成功編輯通勤資訊');</script>";
            } else {
                echo "<script>showAlert_fail('編輯失敗，請稍後再試');</script>";
            }
        }

        $sql = "SELECT a.eCO2_id, a.eCO2_date, a.eCO2_commute, a.eCO2_carbon, a.em_id, a.ec_type, a.ea_id, 
                (SELECT b.ea_name FROM em_address AS b WHERE b.ea_id = a.ea_id) AS ea_name
                FROM em_CO2 AS a
                WHERE a.eCO2_id = $editCO2_id";

        $result = mysqli_query($link, $sql);
        ?>

        <!-- 編輯通勤資訊 -->
        <div class="address container">
            <div class="address_item">
                <div class="container-fluid">
                    <div class="row g-3 d-flex justify-content-center align-items-center">
                        <form class="col-11 col-md-10 align-items-center add_form mt-5" method="POST">
                            <a href="index.php" class="goback_add ms-3"><img src="img/goback.png" class="goback_img"></a>
                            <div class="fs-4 mt-2 mb-5 ms-5 me-5">
                                <h1 class="title mb-3 text-center fw-bold">編輯通勤資訊</h1>

                                <?php while ($rows = mysqli_fetch_array($result)) { ?>

                                <?php
                                ($rows['eCO2_commute']=="go") ? ($commute = "上班") : ($commute = "下班");
                                ?>
                                <h4 class="mb-5 text-center">通勤日期 : <?php echo $rows['eCO2_date'] . " " . $commute ?></h4>

                                <!-- 地址代名 -->
                                <div class="mb-3 row justify-content-center align-items-center">
                                    <label for="address_name" class="form-label col-2">地址代名：</label>
                                    <input type="text" id="address_name" name="address_name" class="add_select date-range-picker col-6 oil_select" value="<?php echo $rows['ea_name']; ?>" required>
                                </div>

                                <div class="mb-3 row justify-content-center align-items-center">
                                    <label for="transport_type" class="form-label col-2">交通方式：</label>
                                    <select id="transport_type" name="transport_type" class="add_select col-6 oil_select" required>
                                        <option value="car" <?php echo ($rows['ec_type'] == 'car') ? 'selected' : ''; ?>>汽車</option>
                                        <option value="bicycle" <?php echo ($rows['ec_type'] == 'bicycle') ? 'selected' : ''; ?>>機車</option>
                                        <option value="public_transport" <?php echo ($rows['ec_type'] == 'public_transport') ? 'selected' : ''; ?>>大眾運輸</option>
                                    </select>
                                </div>

                                <div class="mb-3 row justify-content-center align-items-center">
                                    <button type="submit" name="update_CO2" class="btn add_btn col-6 fs-5 mt-4">確認修改</button>
                                </div>

                                <?php } ?>

                            </div>
                        </form>
                    </div>
                </div> 
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
