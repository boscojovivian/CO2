<?php
session_start();
include("dropdown_list/dbcontroller.php");

$db_handle = new DBController();
$link = mysqli_connect("localhost", "root", "", "carbon_emissions");

// 確認連線是否成功
if (!$link) {
    die("資料庫連線失敗：" . mysqli_connect_error());
}

// 取得要刪除的交通車 ID
if (isset($_GET['cc_id'])) {
    $cc_id = intval($_GET['cc_id']); // 確保 cc_id 為整數

    // 刪除 SQL 語句
    $delete_sql = "DELETE FROM cm_car WHERE cc_id = $cc_id";

    // 執行刪除操作
    if (mysqli_query($link, $delete_sql)) {
        echo "<script>
                alert('成功刪除交通車');
                window.location.href = 'cm_manage_car.php';
              </script>";
    } else {
        echo "<script>
                alert('刪除失敗：" . mysqli_error($link) . "');
                w   indow.location.href = 'cm_manage_car.php';
              </script>";
    }

    mysqli_close($link); // 關閉資料庫連線
} else {
    echo "<script>
            alert('無效的交通車 ID');
            window.location.href = 'cm_manage_car.php';
          </script>";
}
?>
