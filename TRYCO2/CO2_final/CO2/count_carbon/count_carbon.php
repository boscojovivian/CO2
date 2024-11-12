
<?php
$carbon = 0;

// 類別一
if ($carbon_type = 1){
    ($choose_oil = '柴油') ? ($oil_type = '柴油') : ($oil_type = '汽油');

    // *表示要選取所有欄位
    $GWP = $db_handle->select("gwp", "*");

    if ($GWP) {
        foreach ($GWP as $GWP_row) {
            $gwp_num = $GWP_row['num'];

            $GHG = $db_handle->select("ghg", "*", ["name" => $GWP_row['name'], "type" => $oil_type]);
            foreach ($GHG as $GHG_row) {
                $ghg_num = $GHG_row['num'];

                $carbon += $oil_liter * $ghg_num * $gwp_num;
            }
        }
    } else {
        echo "找不到指定的資料";
    }
}
// 類別三
elseif ($carbon_type = 3){
    $transportation = $db_handle->select("transportation", "*", ["type" => $type]);
    $GWP = $db_handle->select("gwp", "*", ["name" => "CO2"]);

    if ($transportation) {
        foreach ($transportation as $transportation_row) {
            $transportation_num = $transportation_row['num'];
        }
    }
    if ($GWP) {
        foreach ($GWP as $gwp_row) {
            $gwp_num = $gwp_row['num'];
        }
    }
    $carbon = $distance * $transportation_num * $gwp_num;
}

$carbon_data = [
    'type' => $carbon_type,
    'type_id' => $type_id,
    'carbon' => $carbon
];

if ($db_handle->insert('count_carbon', $carbon_data)) {
    echo "<script>console.log('碳排計算成功~');</script>";
} else {
    echo "<script>console.log('碳排計算失敗!!!!!!!!!!');</script>";
}
?>