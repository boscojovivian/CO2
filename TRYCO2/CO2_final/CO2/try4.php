<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>通勤地址</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #dfe7f3;
            margin: 0;
            padding: 0;
        }
        /* 導覽列 */
        .navbar {
            background-color: #8bc17b;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar img {
            height: 50px;
        }
        .navbar ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .navbar ul li {
            margin-right: 20px;
        }
        .navbar ul li a {
            text-decoration: none;
            color: black;
            font-size: 18px;
        }

        /* 主體內容 */
        .container {
            margin: 30px;
        }
        h2 {
            text-align: center;
            font-size: 28px;
        }
        .button {
            padding: 10px 20px;
            background-color: #b7d7a8;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #6fa3ef;
            color: white;
        }
        .action-buttons button {
            padding: 5px 10px;
            margin-right: 5px;
        }
    </style>
</head>
<body>

    <!-- 導覽列 -->
    <div class="navbar">
        <div>
            <img src=".\api\img\logo.png" alt="Logo">
        </div>
        <ul>
            <li><a href="#">個人首頁</a></li>
            <li><a href="#">交通車出勤</a></li>
            <li><a href="#">最新消息</a></li>
            <li><a href="#">環保教室</a></li>
            <li><a href="#">回報問題</a></li>
            <li><a href="#">Jo</a></li>
        </ul>
    </div>

    <!-- 主體內容 -->
    <div class="container">
        <h2>通勤地址</h2>
        <button class="button">新增</button>
        <table>
            <tr>
                <th>預設</th>
                <th>地址代名</th>
                <th>地址</th>
                <th>通勤工具</th>
                <th>操作</th>
            </tr>
            <tr>
                <td><input type="checkbox" class="default-checkbox"></td>
                <td>家_1</td>
                <td>台中市昌平路..</td>
                <td>機車</td>
                <td class="action-buttons">
                    <button>編輯</button>
                    <button>刪除</button>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" class="default-checkbox"></td>
                <td>家_2</td>
                <td>台中市太平路..</td>
                <td>機車</td>
                <td class="action-buttons">
                    <button>編輯</button>
                    <button>刪除</button>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" class="default-checkbox"></td>
                <td>家_3</td>
                <td>台中市雅潭路..</td>
                <td>汽車</td>
                <td class="action-buttons">
                    <button>編輯</button>
                    <button>刪除</button>
                </td>
            </tr>
            <!-- 可根據需要添加更多行 -->
        </table>
    </div>


    <script>
        // 確保只有一個 checkbox 被勾選
        const checkboxes = document.querySelectorAll('.default-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                    checkboxes.forEach(otherCheckbox => {
                        if (otherCheckbox !== checkbox) {
                            otherCheckbox.checked = false;
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
