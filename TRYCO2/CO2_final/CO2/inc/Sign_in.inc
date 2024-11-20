<?php
session_start();

// 建立資料庫連線
$link = mysqli_connect("localhost", "root", "", "carbon_emissions");
if (!$link) {
    die("無法連接資料庫：" . mysqli_connect_error());
}

// 設定 UTF8 編碼
mysqli_set_charset($link, "utf8");

// 收集表單數據
$email = trim($_POST["email_in"] ?? '');
$password = trim($_POST["password_in"] ?? '');

// 確認輸入是否為空
if (empty($email) || empty($password)) {
    echo "<script>showAlert_fail('請輸入完整的帳號與密碼！');</script>";
    exit;
}

// 使用 Prepared Statements 查詢資料
$sql = "SELECT em_id, em_name, em_psd, em_email, flag FROM employee WHERE em_email = ?";
$stmt = mysqli_prepare($link, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // 確認是否找到帳號
    if ($row && password_verify($password, $row['em_psd'])) {
        // 設定 Session
        $_SESSION['em_id'] = $row['em_id'];
        $_SESSION['em_name'] = $row['em_name'];
        $_SESSION['em_email'] = $row['em_email'];
        $_SESSION['flag'] = $row['flag'];

        echo "<script>showAlert_logo('" . htmlspecialchars($_SESSION["em_name"]) . " 登入成功！');</script>";
    } else {
        echo "<script>showAlert_fail('登入失敗！帳號或密碼錯誤。');</script>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<script>showAlert_fail('系統錯誤，請稍後再試！');</script>";
}

// 關閉資料庫連結
mysqli_close($link);
?>
