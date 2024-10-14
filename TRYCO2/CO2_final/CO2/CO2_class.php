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
    <title>碳排教室</title>
    <link rel="stylesheet" href="./css/CO2_class.css" type="text/css">
    <link rel="shortcut icon" href="img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('nav/em_nav.php') ?>

    <div class="container mt-5">
        <div class="title1">碳排教室</div>
        
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="know1-tab" data-bs-toggle="tab" data-bs-target="#know1" type="button" role="tab" aria-controls="know1" aria-selected="true">國際清潔空氣藍天日</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="know2-tab" data-bs-toggle="tab" data-bs-target="#know2" type="button" role="tab" aria-controls="know2" aria-selected="false">交通相關</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="know3-tab" data-bs-toggle="tab" data-bs-target="#know3" type="button" role="tab" aria-controls="know3" aria-selected="false">各國碳稅比較</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="know4-tab" data-bs-toggle="tab" data-bs-target="#know4" type="button" role="tab" aria-controls="know4" aria-selected="false">各國碳政策比較</button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="myTabContent">
            <div class="tab-pane fade show active" id="know1" role="tabpanel" aria-labelledby="know1-tab">
                <?php include('co2_class0.php'); ?>
            </div>
            <div class="tab-pane fade" id="know2" role="tabpanel" aria-labelledby="know2-tab">
                <?php include('co2_class1.php'); ?>
            </div>
            <div class="tab-pane fade" id="know3" role="tabpanel" aria-labelledby="know3-tab">
                <?php include('co2_class2.php'); ?>
            </div>
            <div class="tab-pane fade" id="know4" role="tabpanel" aria-labelledby="know4-tab">
                <?php include('co2_class3.php'); ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
