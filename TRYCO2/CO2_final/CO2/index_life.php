<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>生活減碳小知識</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/knowledge_style.css" type="text/css">
    <link rel="shortcut icon" href="img/logo.png" >
</head>
<body>
    <div class="container">
        <a href="knowledge_index.php"><img src="img/goback.png" class="gobacklogo"></a>
        <h2 class="title1">生活減碳小知識</h2>
        <div class="row">
            <?php
            include_once("dropdown_list/dbcontroller.php");
            $dbController = new DBController();
            $query = "SELECT id, title, content, img_id FROM knowledge_life";
            $result = $dbController->runQuery($query); 

            if ($result) {
                foreach($result as $row) {
                    echo "<div class='col-md-4 mb-4'>";
                    echo "  <div class='card h-100 custom-card-bg'>";
                    echo "      <div class='card-body'>";
                    echo "          <h3 class='card-title'>" . $row["title"] . "</h3>";
                    echo "          <img src='img/".$row["img_id"].".jpg' class='img-fluid'>";
                    echo "          <p class='card-text'>" . $row["content"] . "</p>";
                    echo "      </div>";
                    echo "  </div>";
                    echo "</div>";
                }
            } else {
                echo "<p>沒有發現任何小知識。</p>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


