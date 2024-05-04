<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>交通車</title>
<style>
  /* CSS 樣式 */
  table {
    width: 80%; /* 設置表格寬度 */
    border-collapse: collapse; /* 合併邊框 */
    margin: 50px auto; /* 居中顯示 */
  }
  th, td {
    border: 1px solid #000; /* 設置單元格邊框 */
    padding: 8px; /* 設置內邊距 */
    text-align: center; /* 文字居中對齊 */
  }
  th {
    background-color: #f2f2f2; /* 設置表頭背景顏色 */
  }
</style>
</head>
<body>

<!-- 表格 -->
<table>
  <!-- 表頭 -->
  <tr>
    <th>編號</th>
    <th>代名</th>
  </tr>
  
  <?php
  // 連接到資料庫
  $servername = "localhost";
  $username = "your_username";
  $password = "your_password";
  $dbname = "your_database_name";

  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // 從資料庫中檢索資料
  $sql = "SELECT id, name FROM transportation_table";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // 輸出每一行資料
    while($row = $result->fetch_assoc()) {
      echo "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td></tr>";
    }
  } else {
    echo "<tr><td colspan='2'>沒有結果</td></tr>";
  }
  $conn->close();
  ?>
  
</table>

</body>
</html>
