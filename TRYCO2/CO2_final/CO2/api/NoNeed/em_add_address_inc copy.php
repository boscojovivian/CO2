<!-- no -->


<!-- <script>
// 初始化
const platform = new H.service.Platform({
    apikey: 'vLOV0OZxoNgUvE2m00AvrNTQzGhZtOPuCSwU9_BFcBg'
});

function geocode(address, transportMode) {
    const geocoder = platform.getSearchService();
    const geocodingParameters = { q: address };

    geocoder.geocode(geocodingParameters, (result) => {
        const locations = result.items;
        if (locations.length > 0) {
            return true;
        } else {
            alert('未找到地址，請輸入正確地址');
            return false;
        }
        }, (error) => {
            alert('地理編碼失敗');
        });
}
</script> -->


<?php

// 建立 MySQL 資料庫連結
$link = mysqli_connect("localhost", "root", "A12345678") 
or die("無法開啟 MySQL 資料庫連結!<br>");
mysqli_select_db($link, "carbon_emissions"); // 選擇 feedback 資料庫

// 設定 MySQL 查詢字串
$sql = "";

// 送出 UTF8 編碼的 MySQL 指令
mysqli_query($link, "SET NAMES utf8");

// 获取表单数据
$address_name = $_POST['address_name'];
$city = $_POST['city'];
$area = $_POST['area'];
$address_detail = $_POST['address_detail'];
$emp_id = $_SESSION['em_id'];

$address_sql = "SELECT city_name.city_name, area_name.area_name
                FROM (SELECT city_name FROM city WHERE city.city_id = '" . $city . "') AS city_name, 
                    (SELECT area_name FROM area WHERE area.area_id = '" . $area . "') AS area_name";

mysqli_query($link, "SET NAMES utf8");
$result_default = mysqli_query($link, $address_sql);
while ($rows_default = mysqli_fetch_array($result_default)){
    $address = $rows_default['city_name'] . $rows_default['area_name'] . $address_detail;
}

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ("<script>geocode($address);</script>") {
        //檢查帳號是否重複
        $check = "SELECT * 
                FROM em_address 
                WHERE ea_name='" . $address_name . "' AND em_id=" . $emp_id;

                

        if (mysqli_num_rows(mysqli_query($link, $check)) == 0) {
            // 构建插入数据的 SQL 语句
            $sql = "INSERT INTO em_address (ea_name, ea_address_city, ea_address_area, ea_address_detial, em_id) 
                    VALUES ('$address_name', '$city', '$area', '$address_detail', '$emp_id')";

            // 执行 SQL 语句
            $result = mysqli_query($link, $sql);

            echo "<script>alert('地址新增成功');</script>";

            // echo "<script>window.location.href = 'em_index.php';</script>";

            // 检查插入是否成功
            if (!$result) {
                die("插入失敗：" . mysqli_error($link));
            }
        }
        else{
            echo "<script>alert('該地址代名已用過');</script>";
        }   
    } else {
        // 地址无效，向用户提供相应的反馈
        echo "<script>alert('無效的地址，請輸入正確地址')</script>";
    }
}

// 關閉 MySQL 資料庫連結
mysqli_close($link);

// 地址验证函数
// function geocode($address) {
//     // HERE API credentials
//     $apiKey = 'vLOV0OZxoNgUvE2m00AvrNTQzGhZtOPuCSwU9_BFcBg';
//     $url = 'https://geocode.search.hereapi.com/v1/geocode';
//     $params = http_build_query(['q' => $address, 'apiKey' => $apiKey]);

//     // Perform GET request to the geocoding API
//     $response = file_get_contents("$url?$params");
//     if ($response === true) {
//         return true;
//     }else{
//         echo "<script>alert('未找到地址，請輸入正確地址');</script>";
//         return false;
//     }
// }







// function geocode($address) {
//     // 使用 Nominatim 服務進行地理編碼
//     $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address) . "&format=json&addressdetails=1";
//     $response = file_get_contents($url);
//     $data = json_decode($response, true);

//     // 檢查是否找到地址
//     if (!empty($data)) {
//         // 獲取第一個地址的位置信息
//         return true;
//     } else {
//         // 如果未找到地址，則彈出警告
//         echo "<script>alert('無效的地址，請輸入正確地址');</script>";
//         return false;
//     }
// }





// 地址验证函数
// function validateAddress($address) {
    // // 要驗證的地址
    // $urlencode_address = urlencode($address);

    // echo "<script>console.log('address: " . $address . "');</script>";
    // echo "<script>console.log('urlencode_address: " . $urlencode_address . "');</script>";

    // // 構建 API 請求 URL
    // $url = "https://nominatim.openstreetmap.org/search?q={$urlencode_address}&format=json&addressdetails=1";

    // // 發送請求並獲取響應
    // $curl = curl_init();
    // curl_setopt_array($curl, [
    //     CURLOPT_URL => $url,
    //     CURLOPT_RETURNTRANSFER => true,
    // ]);
    // $response = curl_exec($curl);
    // curl_close($curl);

    // // 解析 JSON 響應
    // $data = json_decode($response, true);

    // // 檢查 API 響應是否包含地址資訊
    // if (!empty($data)) {
    //     // 驗證成功，地址存在
    //     echo "地址有效。";
    // } else {
    //     // 驗證失敗，地址無效
    //     echo "地址無效。";
    // }




    // // Construct the API request URL
    // $address_to_validate = urlencode($address);
    // $app_id = "BdW6VZqdz9GRefvtsUwq";
    // $app_code = "HERE-f262805c-55fd-4203-aa49-af687dce8890";
    // $api_url = "https://geocoder.ls.hereapi.com/6.2/geocode.json?searchtext=$address_to_validate&app_id=$app_id&app_code=$app_code";

    // // Send the API request
    // $response = file_get_contents($api_url);

    // // Decode the JSON response
    // $data = json_decode($response, true);

    // // Check if the response contains valid results
    // if ($data['Response']['View']) {
    //     // Address is valid
    //     echo "Address is valid!";
    // } else {
    //     // Address is invalid
    //     echo "Invalid address!";
    // }



    // // 替换为你的 HERE Geocoding API App ID 和 App Code
    // // $app_id = 'mrdmkz4aRJMI1Rhw9THe0g';
    // // $app_code = 'A5-uPypm20ioRUpS5_qAa-V_Ge4C98SF6XoMeKctNX74MSpJDX3TNDVvi44dEGMYRrP-0rG6HvkS3lBjBcK3bQ';
    // $api_key = 'vLOV0OZxoNgUvE2m00AvrNTQzGhZtOPuCSwU9_BFcBg';

    // // 使用 HERE Geocoding API 请求地址的地理坐标
    // $address = urlencode($address);
    // $url = "https://geocoder.ls.hereapi.com/6.2/geocode.json?searchtext=$address&apiKey=$api_key";
    // $response = file_get_contents($url);
    // $data = json_decode($response, true);

    // // 检查是否成功获取地理坐标
    // if (isset($data['Response']['View'][0]['Result'][0]['Location']['DisplayPosition'])) {
    //     // 获取第一个地址的地理坐标
    //     $latitude = $data['Response']['View'][0]['Result'][0]['Location']['DisplayPosition']['Latitude'];
    //     $longitude = $data['Response']['View'][0]['Result'][0]['Location']['DisplayPosition']['Longitude'];

    //     // 在这里你可以进一步处理地理坐标，比如计算与用户输入地址的地理坐标的距离等等

    //     // 返回 true 表示地址有效
    //     return true;
    // } else {
    //     // 返回 false 表示地址无效
    //     return false;
    // }
// }

?>
