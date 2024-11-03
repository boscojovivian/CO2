<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>加油登記</title>
        <link rel="stylesheet" href="css/cm_car_oil.css" type="text/css">
        <link rel="shortcut icon" href="img/logo.png" >
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
        <script>
        const message = (message) => {
            var icon = 'img/logo.png';
            Swal.fire({
                // icon: icon,
                title: message,
                imageUrl: icon,
                imageWidth: 150,
                imageHeight: 100,
            });
        }
    </script>
    </head>
    <body>
        <!-- 導入導覽列 -->
        <?php include('nav/cm_nav.php') ?>

        <div class="container-fluid">
            <div class="row g-3 d-flex justify-content-center align-items-center">
                <form class="col-11 col-md-8 align-items-center oil_form mt-5" method="POST">
                    <div class="fs-5 m-5">
                        <h1 class="title m-4 text-center fw-bold">加油登記</h1>

                        <!-- 日期 -->
                        <div class="mb-3 row justify-content-center align-items-center">
                            <label for="oil_date" class="form-label col-2">加油日期 :</label>
                            <input type="date" id="oil_date" name="oil_date" class="date-range-picker col-6 oil_select" placeholder="加油日期" value="oil_date" required>
                        </div>

                        <!-- 交通車 -->
                        <div class="mb-3 row justify-content-center align-items-center">
                            <label for="oil_car" class="form-label col-2">交通車：</label>
                            <select id="oil_car" name="oil_car" class="col-6 oil_select" required>
                                <option value="">請選擇</option>
                                <?php
                                include_once("dropdown_list/dbcontroller.php");
                                $db_handle = new DBController();

                                $sql = "SELECT cc_id, cc_name FROM cm_car";
                                $result_car = $db_handle->runQuery($sql);

                                if (!empty($result_car)) {
                                    foreach ($result_car as $row) {
                                        // 確保選中的選項保持不變
                                        $selected = (isset($_GET['oil_car']) && $_GET['oil_car'] == $row['cc_id']) ? 'selected' : '';
                                        echo "<option value='" . $row['cc_id'] . "' $selected>" . $row['cc_name'] . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>沒有資料</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- 油種 -->
                        <div class="mb-3 row justify-content-center align-items-center">
                            <label for="choose_oil" class="form-label col-2">加油種類：</label>
                            <div class="col-6">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input oil_select" type="radio" name="choose_oil" id="choose_oil_92" value="92汽油" required>
                                    <label class="form-check-label" for="choose_oil_92">92 汽油</label>
                                </div>
                                    <div class="form-check form-check-inline">
                                    <input class="form-check-input oil_select" type="radio" name="choose_oil" id="choose_oil_95" value="95汽油">
                                    <label class="form-check-label" for="choose_oil_95">95 汽油</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input oil_select" type="radio" name="choose_oil" id="choose_oil_98" value="98汽油">
                                    <label class="form-check-label" for="choose_oil_98">98 汽油</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input oil_select" type="radio" name="choose_oil" id="choose_oil_diesel" value="柴油">
                                    <label class="form-check-label" for="choose_oil_diesel">柴油</label>
                                </div>
                            </div>
                            
                        </div>

                        <!-- 加油量 -->
                        <div class="mb-3 row justify-content-center align-items-center">
                            <label for="liter" class="form-label col-2">加油量：</label>
                            <div class="col-6">
                                <div class="col-6 input-group">
                                    <input type="text" id="oil_liter" name="oil_liter" class="form-control oil_select" placeholder="請輸入加油公升數" aria-label="liter" aria-describedby="liter" required>
                                    <span class="input-group-text" id="liter"> 公升(Liter) </span>
                                </div>
                            </div>
                            
                            
                        </div>

                        <!-- 價格 -->
                        <div class="mb-3 row justify-content-center align-items-center">
                            <label for="price" class="form-label col-2">價格：</label>
                            <div class="col-6">
                                <div class="input-group">
                                    <span class="input-group-text" id="price"> NT </span>
                                    <input type="text" id="oil_price" name="oil_price" class="form-control oil_select" placeholder="請輸入價格" aria-label="price" aria-describedby="price" required>
                                </div>
                            </div>
                            
                            
                        </div>

                        <div class="mb-3 row justify-content-center align-items-center">
                            <button type="submit" name="car_oil" class="btn oil_btn col-6 fs-5">確認登記</button>
                        </div>

                        <?php
                        include_once("dropdown_list/dbcontroller.php");
                        $db_handle = new DBController();

                        if (isset($_POST['car_oil'])) {
                            $oil_date = $_POST['oil_date'];
                            $oil_car = $_POST['oil_car'];
                            $choose_oil = $_POST['choose_oil'];
                            $oil_liter = $_POST['oil_liter'];
                            $oil_price = $_POST['oil_price'];
                        
                            $data = [
                                'oil_date' => $oil_date,
                                'car_id' => $oil_car,
                                'type' => $choose_oil,
                                'liter' => $oil_liter,
                                'price' => $oil_price
                            ];
                        
                            if ($db_handle->insert('cm_car_oil', data: $data)) {
                                $last_id = $db_handle->getLastInsertId();
                                
                                echo "<script>message('加油記錄已成功保存！');</script>";
                                
                                // 將剛新增的 ID 傳遞給 count_carbon.php
                                $carbon_type = 1;
                                $type_id = $last_id;  // 設定變數以供 count_carbon.php 使用
                                include_once("count_carbon/count_carbon.php");
                            } else {
                                echo "<script>message('無法保存加油記錄');</script>";
                            }
                            
                            $db_handle->close();
                        }
                        ?>
                        
                    </div>
                    
                </form>
            </div>
        </div>
        

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    </body>
</html>