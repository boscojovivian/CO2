<?php
session_start();
include("dropdown_list/dbcontroller.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>新增員工</title>
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
    <?php include 'nav/cm_nav.php'; ?>
    <?php
    // 連接資料庫
    $link = mysqli_connect("localhost", "root", "", "carbon_emissions") or die("無法開啟 MySQL 資料庫連結!");
    mysqli_query($link, "SET NAMES utf8");

    // 處理表單提交
    if (isset($_POST['add_employee'])) {
        $em_name = $_POST['em_name'];
        $em_email = $_POST['em_email'];
        $em_psd = password_hash($_POST['em_psd'], PASSWORD_DEFAULT); // 密碼加密
        $flag = isset($_POST['flag']) ? 1 : 0; // 判斷是否為管理員

        // 新增資料到資料庫
        $insert_sql = "INSERT INTO employee (em_name, em_email, em_psd, flag) VALUES ('$em_name', '$em_email', '$em_psd', '$flag')";

        if (mysqli_query($link, $insert_sql)) {
            echo "<script>
                Swal.fire({
                    title: '已成功新增員工',
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'em_employee.php';
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

    <div class="address container">
        <div class="address_item">
            <div class="container-fluid">
                <div class="row g-3 d-flex justify-content-center align-items-center">
                    <form class="col-11 col-md-10 align-items-center add_form mt-5" method="POST">
                        <a href="em_employee.php" class="goback_add ms-3"><img src="img/goback.png" class="goback_img"></a>
                        <div class="fs-4 mt-2 mb-5 ms-5 me-5">
                            <h1 class="text-center mb-4 fw-bold">新增員工</h1>

                            <div class="mb-3 row justify-content-center align-items-center">
                                <label for="em_name" class="form-label col-2">姓名：</label>
                                <input type="text" id="em_name" name="em_name" class="add_select date-range-picker col-6 oil_select" required>
                            </div>
                            <div class="mb-3 row justify-content-center align-items-center">
                                <label for="em_email" class="form-label col-2">信箱：</label>
                                <input type="email" id="em_email" name="em_email" class="add_select date-range-picker col-6 oil_select" required>
                            </div>
                            <div class="mb-3 row justify-content-center align-items-center">
                                <label for="em_psd" class="form-label col-2">密碼：</label>
                                <input type="password" id="em_psd" name="em_psd" class="add_select date-range-picker col-6 oil_select" required>
                            </div>
                            <div class="mb-3 row justify-content-center align-items-center">
                                <label for="confirm_psd" class="form-label col-2">確認密碼：</label>
                                <input type="password" id="confirm_psd" class="add_select date-range-picker col-6 oil_select" required>
                            </div>

                            <div class="mb-3 form-check d-flex justify-content-center align-items-center">
                                <input type="checkbox" id="flag" name="flag" class="form-check-input me-2">
                                <label for="flag" class="form-check-label">設為管理員</label>
                            </div>

                            <div class="mb-3 row justify-content-center align-items-center">
                                <button type="submit" name="add_employee" class="btn add_btn col-6 fs-5 mt-4">新增</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form.add_form");
    if (form) {
        form.addEventListener("submit", function(event) {
            const password = document.getElementById("em_psd").value;
            const confirmPassword = document.getElementById("confirm_psd").value;

            if (password !== confirmPassword) {
                event.preventDefault(); // 阻止表單提交
                Swal.fire({
                    title: '密碼不一致',
                    text: '請確認兩次輸入的密碼相同。',
                    icon: 'error'
                });
            }
        });
    } else {
        console.error("無法找到表單，請確認表單的 class 或 DOM 結構是否正確。");
    }
});

    </script>
</body>
</html>
