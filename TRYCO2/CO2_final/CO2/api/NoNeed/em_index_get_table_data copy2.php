<!-- no -->


<?php
session_start();

$link = mysqli_connect("localhost", "root", "A12345678") 
or die("無法開啟 MySQL 資料庫連結!<br>");
mysqli_select_db($link, "carbon_emissions");

$em_id = $_SESSION['em_id'];

if (isset($_GET['startDate'])) {
    $dateRange = explode(" 至 ", $_GET['startDate']);
    $startDate = $dateRange[0];
    $endDate = isset($dateRange[1]) ? $dateRange[1] : $startDate;

    // echo "<script>console.log('startDate" . $startDate . "');</script>";
    // echo "<script>console.log('endDate" . $endDate . "');</script>";

    $sql = "SELECT a.eCO2_date, a.eCO2_commute, a.ea_id, a.ec_type, a.eCO2_carbon, a.eCO2_id
            FROM em_co2 AS a 
            WHERE eCO2_date BETWEEN '" . $startDate . "' AND '" . $endDate . "' AND a.em_id = '" . $em_id . "'
            ORDER BY a.eCO2_date DESC";
} else {
    $sql = "SELECT a.eCO2_date, a.eCO2_commute, a.ea_id, a.ec_type, a.eCO2_carbon, a.eCO2_id
            FROM em_co2 AS a 
            WHERE a.em_id = '" . $em_id . "'
            ORDER BY a.eCO2_date DESC";
}

mysqli_query($link, "SET NAMES utf8");
$result = mysqli_query($link, $sql);
$fields = mysqli_num_fields($result); //取得欄位數
$rows = mysqli_num_rows($result); //取得記錄數

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr class='my_gowork_tr'>";

        // 日期
        echo "<td class='my_gowork_td'>" . $row['eCO2_date'] . "</td>";

        // 上下班
        echo "<td class='my_gowork_td'>" . ($row['eCO2_commute'] == "go" ? "上班" : "下班") . "</td>";

        // 地址
        $ea_id_sql = "SELECT ea_name
                FROM em_address
                WHERE ea_id = " . $row['ea_id'];
        $ea_id_result = mysqli_query($link, $ea_id_sql);
        while ($ea_id_rows = mysqli_fetch_array($ea_id_result)){
        echo "<td class='my_gowork_td'>" . $ea_id_rows[0] . "</td>";
        }

        // 交通工具
        if($row['ec_type'] == "car"){
            echo "<td class='my_gowork_td'>汽車</td>";
        }
        elseif($row['ec_type'] == "bicycle"){
            echo "<td class='my_gowork_td'>機車</td>";
        }
        else{
            echo "<td class='my_gowork_td'>大眾運輸</td>";
        }

        // 碳排量
        echo "<td class='my_gowork_td'>" . $row['eCO2_carbon'] . "</td>";

        // 編輯
        echo "<td class='my_gowork_td'>
            <form action='em_edit_CO2.php' method='GET'>
                <button type='submit' name='edit_CO2' class='edit_CO2' value='" . $row['eCO2_id'] . "'>編輯</button>
            </form>
        </td>";
        echo "</tr>";
    }
} else {
    echo "<tr class='my_gowork_tr'><td class='my_gowork_td' colspan='6'>查無資料</td></tr>";
}

mysqli_close($link);
?>
