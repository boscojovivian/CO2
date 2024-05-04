<?php
include("dbcontroller.php");

$db_handle = new DBController();    //將DBController類別實體化為物件，透過new這個關鍵字來初始化

if (!empty($_POST["city_id"])){     //如過從前端傳送的city_id不為空，就下SQL語法查詢area資料表的資料
    $query = "SELECT * FROM area WHERE city_id = '" . $_POST["city_id"] . "'";
    $results = $db_handle->runQuery($query);
?>

<!-- 如過沒有可縣市的鄉鎮區資料，把選單內容設為"請選擇鄉鎮區" -->
<option value disabled select>請選擇鄉鎮區</option>

<?php
    foreach ($results as $area){    //將讀取到的鄉鎮區資料放入選單中
?>

<option value = "<?php echo $area["area_id"]; ?>"><?php echo $area["area_name"]; ?></option>

<?php
    }
}
?>