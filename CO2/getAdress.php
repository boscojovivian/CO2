<?php
include("editAdress.php");

$Ad_handle = new editAdress();    //將DBController類別實體化為物件，透過new這個關鍵字來初始化

if (!empty($_POST["edit_address"])){     //如過從前端傳送的city_id不為空，就下SQL語法查詢area資料表的資料
    $query = "SELECT * FROM em_address WHERE ea_id = '" . $_POST["edit_address"] . "'";
    $results = $Ad_handle->runQuery($query);
?>

<?php
    foreach ($results as $address){    //將讀取到的鄉鎮區資料放入選單中
?>

<option value = "<?php echo $address["area_id"]; ?>"><?php echo $address["area_name"]; ?></option>

<?php
    }
}
?>