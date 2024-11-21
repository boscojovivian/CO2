<?php
session_start();
include("dropdown_list/dbcontroller.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>新增公司車</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/em_edit_CO2.css" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/logo.png">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- 導入導覽列 -->
    <?php include 'nav/cm_nav.php'?>
    <?php
    // 連接資料庫
        $link = mysqli_connect("localhost", "root", "", "carbon_emissions") or die("無法開啟 MySQL 資料庫連結!");
        mysqli_query($link, "SET NAMES utf8");
    // 處理表單提交
        if (isset($_POST['add_car'])) {
            $cc_name = $_POST['cc_name'];  
            $cc_type = $_POST['cc_type'];  

            // 新增資料到資料庫
            $insert_sql = "INSERT INTO cm_car (cc_name, cc_type) VALUES ('$cc_name', '$cc_type');";

            if (mysqli_query($link, $insert_sql)) {
                echo "<script>
                    Swal.fire({
                        title: '已成功新增交通車',
                        icon: 'success'
                    }).then(() => {
                        window.location.href = 'cm_manage_car.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        title: '新增失敗，請稍後再試',
                        icon: 'error'
                    });
                </script>";
            }
        }
    ?>

    <!-- 新增交通車資訊 -->
    <div class="address container">
        <div class="address_item">
            <div class="container-fluid">
                <div class="row g-3 d-flex justify-content-center align-items-center">
                    <form class="col-11 col-md-10 align-items-center add_form mt-5" method="POST">
                        <a href="cm_manage_car.php" class="goback_add ms-3"><img src="img/goback.png" class="goback_img"></a>
                        <div class="fs-4 mt-2 mb-5 ms-5 me-5">
                            <h1 class="title mb-3 text-center fw-bold">新增交通車資訊</h1>

                            <div class="mb-3 row justify-content-center align-items-center">
                                <label for="cc_name" class="form-label col-2">名稱：</label>
                                <input type="text" id="cc_name" name="cc_name" class="add_select date-range-picker col-6 oil_select" required>
                            </div>

                            <div class="mb-3 row justify-content-center align-items-center">
                                <label for="cc_type" class="form-label col-2">類型：</label>
                                <select id="cc_type" name="cc_type" class="add_select col-6 oil_select" required>
                                    <option value="" disabled selected>選擇交通車類型</option>
                                    <option value="motorcycle">機車</option>
                                    <option value="car">汽車</option>
                                    <option value="truck">卡車</option>
                                </select>
                            </div>

                            <div class="mb-3 row justify-content-center align-items-center">
                                <button type="submit" name="add_car" class="btn add_btn col-6 fs-5 mt-4">新增</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
