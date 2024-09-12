<!-- no -->

<?php
session_start();

$link = mysqli_connect("localhost", "root", "A12345678") 
        or die("無法開啟 MySQL 資料庫連結!<br>");
mysqli_select_db($link, "carbon_emissions");

// 檢查是否設置了日期參數
if(isset($_GET['date'])) {
    $em_id = $_SESSION['em_id'];

    // 解析日期參數
    $date = $_GET['date'];

    // 將日期範圍拆分為開始日期和結束日期
    $date_range = explode(" to ", $date);
    $start_date = $date_range[0];
    $end_date = $date_range[1];

    // 構建 SQL 查詢
    $sql = "SELECT a.eCO2_date, a.eCO2_commute, a.ea_id, a.ec_type, a.eCO2_carbon, a.eCO2_id
            FROM em_co2 AS a 
            WHERE date_column BETWEEN '$start_date' AND '$end_date' AND a.em_id = $em_id
            ORDER BY a.eCO2_date DESC";

    // 執行 SQL 查詢
    $result = mysqli_query($link, $sql);

    // 初始化表格 HTML
    $table_html = "<table class='my_gowork_table'>
                        <tr>
                            <th class='my_gowork_th'>日期</th>
                            <th class='my_gowork_th'>上下班</th>
                            <th class='my_gowork_th'>地址</th>
                            <th class='my_gowork_th'>交通工具</th>
                            <th class='my_gowork_th'>碳排量</th>
                            <th class='my_gowork_th'>編輯</th>
                        </tr>";

    // 將查詢結果轉換為表格行
    while ($CO2_rows = mysqli_fetch_array($result)) {
        $eCO2_date = $CO2_rows[0];
        $eCO2_commute = $CO2_rows[1];
        $ea_id = $CO2_rows[2];
        $ec_type = $CO2_rows[3];
        $eCO2_carbon = $CO2_rows[4];
        $eCO2_id = $CO2_rows[5];

        // 獲取地址
        $ea_id_sql = "SELECT ea_name FROM em_address WHERE ea_id = $ea_id";
        $ea_id_result = mysqli_query($link, $ea_id_sql);
        $ea_name = "";
        while ($ea_id_rows = mysqli_fetch_array($ea_id_result)) {
            $ea_name = $ea_id_rows[0];
        }

        // 構建表格行 HTML
        $table_html .= "<tr class='my_gowork_tr'>
                            <td class='my_gowork_td'>$eCO2_date</td>
                            <td class='my_gowork_td'>" . ($eCO2_commute == "go" ? "上班" : "下班") . "</td>
                            <td class='my_gowork_td'>$ea_name</td>
                            <td class='my_gowork_td'>" . ($ec_type == "car" ? "汽車" : ($ec_type == "bicycle" ? "機車" : "大眾運輸")) . "</td>
                            <td class='my_gowork_td'>$eCO2_carbon kg</td>
                            <td class='my_gowork_td'>
                                <form action='em_edit_CO2.php' method='GET'>
                                    <button type='submit' name='edit_CO2' class='edit_CO2' value='$eCO2_id'>編輯</button>
                                </form>
                            </td>
                        </tr>";
    }

    // 關閉表格
    $table_html .= "</table>";

    // 輸出表格 HTML
    echo $table_html;
} else {
    // 如果日期參數未設置，返回錯誤訊息
    echo "日期參數未設置！";
}
?>
