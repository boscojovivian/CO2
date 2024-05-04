<?php
session_start();

// 關閉顯示 PHP 錯誤
//error_reporting(0);

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
    $email = mysqli_real_escape_string($link, $_POST["email_in"]);
    $password = mysqli_real_escape_string($link, $_POST["password_in"]);

    // 搜尋資料庫資料
    $sql = "SELECT * FROM employee WHERE em_email = '$email'";
    // 執行查詢 mysqli_query(連接要使用的MySQL, 要查詢的資料)
    $result = mysqli_query($link, $sql);

    // 如果有结果
    if ($result) {
        $row = mysqli_fetch_assoc($result);

        // 判斷帳號與密碼是否為空白 以及確認是否為MySQL資料庫裡是否有這個會員
        if ($email != null && 
            $password != null &&
            $row['em_email'] == $email && 
            $row['em_psd'] == $password )
        {
            // 將帳號寫入session，方便驗證使用者身份
            $_SESSION['em_id'] = $row['em_id'];
            $_SESSION['em_name'] = $row['em_name'];
            $_SESSION['em_account'] = $row['em_account'];
            $_SESSION['em_psd'] = $row['em_psd'];
            $_SESSION['em_email'] = $row['em_email'];
            $_SESSION['flag'] = $row['flag'];

            echo "<script>alert('" . $_SESSION["em_name"] . "登入成功!');</script>";
            echo "<script>window.location.href = 'em_index.php';</script>";

        } else {
            echo "<script>alert('登入失敗! 資料錯誤');</script>";
        }
    } else {
        echo "<script>alert('登入失敗! 資料錯誤');</script>";
    }

// 關閉 MySQL 資料庫連結
mysqli_close($link);
/*
} catch (Exception $e) {
    // 记录错误到日志文件或其他处理
}
*/
?>
