<?php
$carbon = 0;

// 類別一
if ($carbon_type = 1) {
    ($choose_oil = '柴油') ? ($oil_type = '柴油') : ($oil_type = '汽油');

    // *表示要選取所有欄位
    $GWP = $db_handle->select("gwp", "*");

    $calculation_results = []; // 新增一個陣列來儲存每次計算的結果

    if ($GWP) {
        foreach ($GWP as $GWP_row) {
            $gwp_num = $GWP_row['num'];

            $GHG = $db_handle->select("ghg", "*", ["name" => $GWP_row['name'], "type" => $oil_type]);
            foreach ($GHG as $GHG_row) {
                $ghg_num = $GHG_row['num'];

                // 計算結果
                $result = $oil_liter * $ghg_num * $gwp_num;

                // 將計算結果存入陣列
                $calculation_results[] = [
                    'name' => $GWP_row['name'],
                    'type' => $oil_type,
                    'result' => $result,
                    'car_id' => $car_id
                ];

                // 累加到總碳排放量
                $carbon += $result;
            }
        }
    } else {
        echo "找不到指定的資料";
    }

    // Optional: 輸出計算結果陣列
    // print_r($calculation_results);
}
// 類別三
elseif ($carbon_type = 3) {
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

// 將結果存入資料庫
foreach ($calculation_results as $result_data) {
    $carbon_data = [
        'type' => $carbon_type,
        'type_id' => $type_id,
        'name' => $result_data['name'],
        // 'type' => $result_data['type'],
        'carbon' => $result_data['result'],
        'car_id' => 0
    ];

    if ($db_handle->insert('count_carbon', $carbon_data)) {
        echo "<script>console.log('碳排計算成功~');</script>";
    } else {
        echo "<script>console.log('碳排計算失敗!!!!!!!!!!');</script>";
    }
}
?>
