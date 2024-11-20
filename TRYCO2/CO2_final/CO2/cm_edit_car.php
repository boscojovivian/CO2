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
    <title>編輯公司車</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/em_edit_CO2.css" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <?php include 'nav/cm_nav.php'?>
    <?php
        $link = mysqli_connect("localhost", "root", "", "carbon_emissions") or die("無法開啟 MySQL 資料庫連結!");
        mysqli_query($link, "SET NAMES utf8");

        $editcar = isset($_GET['cc_id']) ? $_GET['cc_id'] : (isset($_SESSION['cc_id']) ? $_SESSION['cc_id'] : null);

        if (!$editcar) {
            die("未指定交通車編號。");
        }

        if (isset($_POST['update_car'])) {
            $cc_name = $_POST['cc_name'];  
            $cc_type = $_POST['cc_type'];  

            $update_sql = "UPDATE cm_car
                SET cc_name = '$cc_name', cc_type = '$cc_type'
                WHERE cc_id = '$editcar';";

            if (mysqli_query($link, $update_sql)) {
                echo "<script>
                    Swal.fire({
                        title: '已成功編輯公司車資訊',
                        icon: 'success'
                    }).then(() => {
                        window.location.href = 'cm_manage_car.php';
                    });
                </script>";
            } else {
                echo "<script>showAlert_fail('編輯失敗，請稍後再試');</script>";
            }

        }

        $sql = "SELECT * FROM cm_car WHERE cc_id = '$editcar';";
        $result = mysqli_query($link, $sql);
        ?>

        <!-- 編輯資訊 -->
        <div class="address container">
            <div class="address_item">
                <div class="container-fluid">
                    <div class="row g-3 d-flex justify-content-center align-items-center">
                        <form class="col-11 col-md-10 align-items-center add_form mt-5" method="POST">
                            <a href="cm_manage_car.php" class="goback_add ms-3"><img src="img/goback.png" class="goback_img"></a>
                            <div class="fs-4 mt-2 mb-5 ms-5 me-5">
                                <h1 class="title mb-3 text-center fw-bold">編輯公司車資訊</h1>

                                <?php 
                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($rows = mysqli_fetch_array($result)) { ?>

                                    <h4 class="mb-5 text-center">公司車編號 : <?php echo $rows['cc_id']; ?></h4>

                                    <div class="mb-3 row justify-content-center align-items-center">
                                        <label for="cc_name" class="form-label col-2">名稱：</label>
                                        <input type="text" id="cc_name" name="cc_name" class="add_select date-range-picker col-6 oil_select" value="<?php echo $rows['cc_name']; ?>" required>
                                    </div>

                                    <div class="mb-3 row justify-content-center align-items-center">
                                        <label for="cc_type" class="form-label col-2">類型：</label>
                                        <select id="cc_type" name="cc_type" class="add_select col-6 oil_select" required>
                                            <option value="" disabled <?php echo empty($rows['cc_type']) ? 'selected' : ''; ?>>選擇交通車類型</option>
                                            <option value="motorcycle" <?php echo $rows['cc_type'] == 'motorcycle' ? 'selected' : ''; ?>>機車</option>
                                            <option value="car" <?php echo $rows['cc_type'] == 'car' ? 'selected' : ''; ?>>汽車</option>
                                            <option value="truck" <?php echo $rows['cc_type'] == 'truck' ? 'selected' : ''; ?>>卡車</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 row justify-content-center align-items-center">
                                        <button type="submit" name="update_car" class="btn add_btn col-6 fs-5 mt-4">確認修改</button>
                                    </div>
                                <?php }
                                } else {
                                    echo "<p class='text-center'>找不到相關資料。</p>";
                                }
                                ?>

                            </div>
                        </form>
                    </div>
                </div> 
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
