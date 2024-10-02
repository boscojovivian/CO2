<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    header("Location: Sign_in.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>環保教室</title>
    <link rel="stylesheet" href="./css/CO2_class.css" type="text/css">
    <link rel="shortcut icon" href="img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('nav/em_nav.php') ?>

    <div class="container mt-5">
        <div class="title1">環保教室</div>
        
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="carbon-tab" data-bs-toggle="tab" data-bs-target="#carbon" type="button" role="tab" aria-controls="carbon" aria-selected="true">交通相關</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="renewable-tab" data-bs-toggle="tab" data-bs-target="#renewable" type="button" role="tab" aria-controls="renewable" aria-selected="false">各國碳稅比較</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="waste-tab" data-bs-toggle="tab" data-bs-target="#waste" type="button" role="tab" aria-controls="waste" aria-selected="false">各國碳政策比較</button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="myTabContent">
            <div class="tab-pane fade show active" id="carbon" role="tabpanel" aria-labelledby="carbon-tab">
                <?php include('co2_class1.php'); ?>
            </div>
            <div class="tab-pane fade" id="renewable" role="tabpanel" aria-labelledby="renewable-tab">
                <?php include('co2_class2.php'); ?>
            </div>
            <div class="tab-pane fade" id="waste" role="tabpanel" aria-labelledby="waste-tab">
                <?php include('co2_class3.php'); ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
