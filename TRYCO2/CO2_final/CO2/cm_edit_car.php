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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
</head>
<body>
<?php include 'nav/em_nav.php'?>
<div class="car  container">
<?php
    include_once("dbcontroller.php");
    $dbController = new DBController();
    
    if (isset($_GET['cc_id'])) {
        $cc_id = $_GET['cc_id'];
        $query = "SELECT * FROM cm_car WHERE cc_id = ?";
        $params = array($cc_id);
        $car = $dbController->runQuery($query, "i", $params);
    
        if (!$car) {
            echo "找不到該交通車資料。";
            exit();
        }
        $car = $car[0];
    } else {
        echo "沒有提供交通車編號。";
        exit();
    }
    
    if (isset($_POST['update'])) {
        $cc_name = $_POST['cc_name'];
        $cc_type = $_POST['cc_type'];
    
        $updateQuery = "UPDATE cm_car SET cc_name = ?, cc_type = ? WHERE cc_id = ?";
        $updateParams = array($cc_name, $cc_type, $cc_id);
        $updateResult = $dbController->updateQuery($updateQuery, "ssi", $updateParams);
    
        if ($updateResult) {
            echo '<script>showAlert_success("交通車資料更新成功！");</script>';
            exit();
        } else {
            echo '<script>showAlert_fail("更新交通車失敗！");</script>';
        }
    }
    ?>
      
       <div class="car_item">
       <a href="cm_manage_car.php" class="goback_add1"><img src="img/goback.png" class="goback_img"></a>
        <h1>編輯交通車資料</h1>
        <div class="car_div">
        <form method="post">
            <label for="cc_name">交通車名稱：</label>
            <input type="text" id="cc_name" name="cc_name" value="<?php echo htmlspecialchars($car['cc_name']); ?>" required>
            <br><br>
            <div class="水平靠左">
            <label for="cc_type">交通車類型：</label>
            <select class="car_type" id="cc_type" name="cc_type" required>
                <option value="" disabled <?php echo empty($car['cc_type']) ? 'selected' : ''; ?>>選擇交通車類型</option>
                <option value="motorcycle" <?php echo $car['cc_type'] == 'motorcycle' ? 'selected' : ''; ?>>機車</option>
                <option value="car" <?php echo $car['cc_type'] == 'car' ? 'selected' : ''; ?>>汽車</option>    
                <option value="truck" <?php echo $car['cc_type'] == 'truck' ? 'selected' : ''; ?>>卡車</option>
            </select>
            </div>
            <br><br>
            </div>
            <input type="submit" name="update" data-style='car_submit' value="更新交通車">
            <br>
            <!-- <input type="button" onclick="deleteCar(<?php echo $car['cc_id']; ?>)" data-style='car_delete' value="刪除"> -->
            <input type="button" onclick="deleteRecord('<?php echo $cc_id; ?>')" data-style='car_delete' value="刪除">
            <br><br>
        </form>
        
 
</div>
 <!-- 引入 Bootstrap JS -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
</body>
<script>
    function deleteCar(cc_id) {
        if (confirm('確定要刪除這輛交通車嗎？')) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    alert(response.message);
                    if (response.status === 'success') {
                        window.location.href = 'cm_manage_car.php';
                    }
                }
            };
            xhttp.open("POST", "delete_car.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("cc_id=" + cc_id);
        }
    }
</script>
</html>
