<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>小知識</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/knowledge_style.css" type="text/css">
    <link rel="shortcut icon" href="img/logo.png" >
</head>
<body>
    <?php include('nav/em_nav.php') ?>    
    <div class="container">
        <a href="em_index.php"><img src="img/goback.png" class="gobacklogo"></a>
        <div class="row">
            <div class="col-md-6 text-center">
                <h1>生活減碳小知識</h1><br>
                <h4>減少浪費，地球更美</h4><br>
                <img src="img/life1.png" class="img-fluid">
                <br>
                <a href="index_life.php" class="btn btn-success mt-3">前往查看-></a>
            </div>

            <div class="col-md-6 text-center">
                <h1>開車減碳小知識</h1><br>
                <h4>溫柔駕駛，綠色相隨</h4><br>
                <img src="img/drive1.png" class="img-fluid">
                <br>
                <a href="index_drive.php" class="btn btn-success mt-3">前往查看-></a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
