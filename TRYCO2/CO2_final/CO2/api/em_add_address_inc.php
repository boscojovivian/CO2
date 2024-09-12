<?php
session_start();

if (isset($_POST["address_name"], $_POST["city"], $_POST["area"], $_POST["address_detail"])) {
    $link = mysqli_connect("localhost", "root", "A12345678", "carbon_emissions") or die("无法连接到数据库");

    $address_name = mysqli_real_escape_string($link, $_POST['address_name']);
    $city = mysqli_real_escape_string($link, $_POST['city']);
    $area = mysqli_real_escape_string($link, $_POST['area']);
    $address_detail = mysqli_real_escape_string($link, $_POST['address_detail']);
    $isDefault = mysqli_real_escape_string($link, $_POST['default_checkbox']); // 获取复选框值
    $emp_id = $_SESSION['em_id'];

    $city_area_id_sql = "SELECT city_id.city_id, area_id.area_id
                        FROM (SELECT city_id FROM city WHERE city.city_name = '" . $city . "') AS city_id, 
                            (SELECT area_id FROM area WHERE area.area_name = '" . $area . "') AS area_id";

    mysqli_query($link, "SET NAMES utf8");
    $city_area_id_result = mysqli_query($link, $city_area_id_sql);
    while ($city_area_id_rows = mysqli_fetch_array($city_area_id_result)){
        $city_id = $city_area_id_rows['city_id'];
        $area_id = $city_area_id_rows['area_id'];
    }

    echo "<script>console.log('" . $address_detail . "');</script>";

    // if($isDefault == 1){
    //     $isDefault_sql = "SELECT * FROM em_address WHERE ea_default = '$isDefault' AND em_id = $emp_id";
    //     $isDefault_result = mysqli_query($link, $isDefault_sql);

    //     if (mysqli_num_rows($isDefault_result) == 0) {
    //         $ea_default = $isDefault;
    //     }else{
    //         while ($isDefault_rows = mysqli_fetch_array($isDefault_result)){
    //             $Default_address_name = $isDefault_rows['ea_name'];
    //             echo "<script>showAlert_isDefault(" . $Default_address_name . ");</script>";
    //             exit();
    //         }
    //     }  
    // }else{
    //     $ea_default = $isDefault;
    // }

    if($isDefault == 1){
        $isDefault_sql = "SELECT * FROM em_address WHERE ea_default = '$isDefault' AND em_id = $emp_id";
        $isDefault_result = mysqli_query($link, $isDefault_sql);

        if (mysqli_num_rows($isDefault_result) == 0) {
            $ea_default = $isDefault;
        }else{
            $chang_Default_sql = "UPDATE em_address
                                    SET ea_default = 0
                                    WHERE em_id = " . $emp_id . " AND ea_default = 1";
            mysqli_query($link, $chang_Default_sql);
            $ea_default = $isDefault;
        }  
    }else{
        $ea_default = 0;
    }

    $check = "SELECT * FROM em_address WHERE ea_name = '$address_name' AND em_id = $emp_id";
    $result_check = mysqli_query($link, $check);

    if (mysqli_num_rows($result_check) == 0) {
        $sql = "INSERT INTO em_address (ea_name, ea_address_city, ea_address_area, ea_address_detial, ea_default, em_id) 
                VALUES ('$address_name', '$city_id', '$area_id', '$address_detail', '$ea_default', '$emp_id')";

        $result = mysqli_query($link, $sql);
        if ($result) {
            echo "<script>showAlert_success('地址新增成功!');</script>";
            // echo "<script>alert('地址新增成功');</script>";
        } else {
            die("插入失敗：" . mysqli_error($link));
        }
    } else {
        echo "<script>showAlert_fail('該地址代名已存在');</script>";
        // echo "<script>alert('該地址代名已存在');</script>";
    }
    mysqli_close($link);
} else {
    echo "请填写所有字段";
}
?>
