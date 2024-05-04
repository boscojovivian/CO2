<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>首頁</title>
<style>
  /* CSS 樣式 */
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
  }
  button {
    padding: 10px 20px; /* 調整按鈕內容的填充 */
    font-size: 16px; /* 調整按鈕文字大小 */
    border: none; /* 移除按鈕邊框 */
    background-color: #10ab1d; /* 設置按鈕背景顏色 */
    color: #fff; /* 設置按鈕文字顏色 */
    cursor: pointer; /* 將滑鼠指標設置為手型 */
    font-size: 30px; 
  }

  /* 更改按鈕文字大小 */
  input[type="button"] {
    font-size: 30px; /* 設置按鈕文字大小為 20 像素 */
  }

  #logo {
    float: left;
    margin: 10px;
    margin-left: 50px; 
  }

  #top-right {
    float: right;
    margin: 10px;
  }

  #top-right input[type="text"], #top-right img {
    margin-right: 10px;
  }

  #left-section {
    float: left;
    width: 30%; /* 調整左部分的寬度 */
    text-align: center;
    margin-top: 250px;
    margin-left: 50px; /* 調整左部分的左邊距 */
  }

  #right-section {
    float: right;
    width: 50%;
    text-align: center;
    margin-top: 200px;
  }

  /* 調整右部分按鈕排列 */
  #right-section button {
    display: block; /* 將按鈕設置為塊級元素 */
    margin-bottom: 100px; /* 設置按鈕之間的下邊距為 50px */
  }

  #carbon-circle {
    margin-top: 50px;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background-color: #ccc;
    line-height: 100px;
    font-size: 100px;
    color: #fff;
    margin: 0 auto;
    margin-top: 100px; /* 調整左部分的左邊距 */
  }
</style>
</head>
<body>

<?php

// 連接到資料庫
//$servername = "localhost"; // 資料庫伺服器名稱
//$username = "your_username"; // 資料庫使用者名稱
//$password = "your_password"; // 資料庫密碼
//$dbname = "your_database_name"; // 資料庫名稱

// 創建連接
//$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接是否成功
//if ($conn->connect_error) {
 // die("Connection failed: " . $conn->connect_error);
//}

// 從資料庫中獲取數字
//$sql = "SELECT carbon_count FROM your_table_name";
//$result = $conn->query($sql);

// 檢查是否有數據
//if ($result->num_rows > 0) {
  // 將數字顯示在網頁上
  //while($row = $result->fetch_assoc()) {
   // echo "<div id='carbon-circle'>" . $row["carbon_count"] . "</div>";
  //}
//} else {
 // echo "0 結果";
//}

// 關閉資料庫連接
//$conn->close();
?>



<!-- 左上方 logo -->
<a href="首頁.php"><img src="your_logo_image_url" alt="Your Logo" id="logo"></a>

<!-- 右上方功能區域 -->
<div id="top-right">
  <input type="button" value="新增路線" onclick="location.href='管理者新增路線.php';">
  <input type="button" value="管理者資料" onclick="location.href='管理者資料.php';">
  <a href="member_profile.html"><img src="member_image_url" alt="Member" id="member-image"></a>
</div>

<!-- 左部分 -->
<div id="left-section">
  <input type="button" value="目前碳排">
  <div id="carbon-circle">0</div>
</div>

<!-- 右部分 -->
<div id="right-section">
  <button onclick="location.href='管理交通車碳排.php';">交通車</button>
  <button onclick="location.href='管理員工碳排.php';">員工</button>
</div>

</body>
</html>
