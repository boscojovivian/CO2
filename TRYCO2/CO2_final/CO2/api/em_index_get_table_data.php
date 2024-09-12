<?php
session_start();
?>

<div class="wrap">
    <table class="my_gowork_table">
        <thead>
            <tr>
                <th class="my_gowork_th">日期</th>
                <th class="my_gowork_th">上下班</th>
                <th class="my_gowork_th">地址</th>
                <th class="my_gowork_th">交通工具</th>
                <th class="my_gowork_th">碳排量</th>
                <th class="my_gowork_th">編輯</th>
            </tr>
        </thead>
        <tbody>


            <?php
            $one_for_pages = 10;

            if (isset($_GET["Pages"])) {
                $pages = $_GET["Pages"];
            } else {
                $pages = 1;
            }

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
            
            //計算總頁數
            $total_pages = ceil($rows / $one_for_pages); //ceil 無條件進位
            //計算這一頁第1筆紀錄的位置
            $offset = ($pages - 1) * $one_for_pages;
            mysqli_data_seek($result, $offset); //移到此紀錄
            
            $j = 1;
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result) and $j <= $one_for_pages) {
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
                    while ($ea_id_rows = mysqli_fetch_array($ea_id_result)) {
                        echo "<td class='my_gowork_td'>" . $ea_id_rows[0] . "</td>";
                    }

                    // 交通工具
                    if ($row['ec_type'] == "car") {
                        echo "<td class='my_gowork_td'>汽車</td>";
                    } elseif ($row['ec_type'] == "bicycle") {
                        echo "<td class='my_gowork_td'>機車</td>";
                    } else {
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

                    $j++;
                }
            } else {
                echo "<tr class='my_gowork_tr'><td class='my_gowork_td' colspan='6'>查無資料</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- 切換頁面按鈕 -->
<?php
echo "<div class='turn_pages_div' id='置中'>";

// 顯示最前面一頁和上一頁
if ($pages > 1) {
    echo "<a class='turn_pages' href='em_index.php?Pages=1'><<</a> ";
    echo "<a class='turn_pages' href='em_index.php?Pages=" . ($pages - 1) . "'><</a> ";
} else {
    echo "<a class='Noturn_pages'><<</a> ";
    echo "<a class='Noturn_pages'><</a> ";
}

// 確定分頁範圍
if ($total_pages <= 5) {
    $start_page = 1;
    $end_page = $total_pages;
} else {
    if ($pages <= 3) {
        $start_page = 1;
        $end_page = 5;
    } elseif ($pages > $total_pages - 3) {
        $start_page = $total_pages - 4;
        $end_page = $total_pages;
    } else {
        $start_page = $pages - 2;
        $end_page = $pages + 2;
    }
}

// 確保正確的分頁範圍
$start_page = max(1, $start_page);
$end_page = min($total_pages, $end_page);

if ($start_page > 1) {
    echo "<a class='turn_pages_more'> ...<a>";
}

for ($i = $start_page; $i <= $end_page; $i++) {
    if ($i != $pages) {
        echo "<a class='turn_pages' href='em_index.php?Pages=" . $i . "'>" . $i . " </a>";
    } else {
        echo "<a class='Noturn_pages'>" . $i . " </a>";
    }
}

if ($end_page < $total_pages) {
    echo "<a class='turn_pages_more'> ...<a>";
}

// 顯示下一頁和最後面一頁
if ($pages < $total_pages) {
    echo "<a class='turn_pages' href='em_index.php?Pages=" . ($pages + 1) . "'>></a> ";
    echo "<a class='turn_pages' href='em_index.php?Pages=" . $total_pages . "'>>></a>";
} else {
    echo "<a class='Noturn_pages'>></a> ";
    echo "<a class='Noturn_pages'>>></a>";
}

echo "</div>";
?>



<?php
mysqli_close($link);
?>