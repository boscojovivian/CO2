<?php
//try {

//建立MySQL資料庫連結
$link = mysqli_connect("localhost", "root", "A12345678")
    or die("無法開啟MySQL資料庫連結!<br>");
mysqli_select_db($link, "carbon_emissions"); //選擇myschool資料庫

// 設定 MySQL 查詢字串
$sql = "";

//送出UTF8編碼的MySQL指令
mysqli_query($link, "SET NAMES utf8");

// 收取表單數據
$name = mysqli_real_escape_string($link, $_POST["name_up"]);
$email = mysqli_real_escape_string($link, $_POST["email_up"]);
$password = mysqli_real_escape_string($link, $_POST["password_up"]);
$permission = mysqli_real_escape_string($link, $_POST["permission"]);
    if($permission == "manage"){
        $flag = 1;
    } elseif($permission == "employee"){
        $flag = 0;
    }

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //檢查帳號是否重複
    $check = "SELECT * FROM employee WHERE em_email='" . $email . "'";

    if (mysqli_num_rows(mysqli_query($link, $check)) == 0) {
        $sql = "INSERT INTO employee (em_name, em_psd, em_email, flag) 
                VALUES ('$name', '$password', '$email', '$flag')";
    
        if (mysqli_query($link, $sql)) {
            echo "<script>alert('註冊成功！');</script>";
            
        } else {
            echo "Error creating table: " . mysqli_error($link);
        }
    } else {
        echo "<script>alert('該帳號已註冊！');</script>";
        
    }    
}
// 關閉 MySQL 資料庫連結
mysqli_close($link);



// 跳转到注册结果页面
//header("Location: 會員專區.php");
//exit;

/*
} catch (Exception $e) {
    // 这里可以处理异常，例如记录到日志文件
    // 如果你不想执行任何操作，可以留空
}*/
?>