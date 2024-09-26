<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>回報問題</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/feedback.css" type="text/css">
    <link rel="shortcut icon" href="img/logo.png">
</head>
<body>
    <?php include('nav/em_nav.php') ?>
    <div class="container mt-5">
        <h1 class="text-center">問題回報</h1>
        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <form id="reportForm">
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">問題類型:</label>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                通勤相關 
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item active" href="#" data-value="通勤相關">通勤相關</a></li>
                                <li><a class="dropdown-item" href="#" data-value="交通車出勤相關">交通車出勤相關</a></li>
                                <li><a class="dropdown-item" href="#" data-value="圖表相關">圖表相關</a></li>
                                <li><a class="dropdown-item" href="#" data-value="其他">其他</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label">問題詳述:</label>
                        <textarea class="form-control" id="message-text" rows="4"></textarea>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary" id="submitButton">發送</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dropdownItems = document.querySelectorAll('.dropdown-item');
            var dropdownToggle = document.getElementById('dropdownMenuButton1'); // 修改為正確的 ID
            var selectedValue = '通勤相關'; // 預設值

            // 監聽下拉選單點擊事件
            dropdownItems.forEach(function (item) {
                item.addEventListener('click', function (e) {
                    e.preventDefault();
                    selectedValue = this.getAttribute('data-value');
                    dropdownToggle.textContent = selectedValue;
                });
            });

            // 綁定 "發送" 按鈕事件
            document.getElementById('submitButton').addEventListener('click', function () {
                var messageText = document.getElementById('message-text').value;

                // 檢查是否輸入了問題詳述
                if (messageText.trim() === '') {
                    alert('請填寫問題詳述');
                    return;
                }

                // 使用 AJAX 發送資料到後端
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'save_feedback.php', true); // 設置正確的路徑
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert('填寫完畢');  // 彈出「填寫完畢」的提示框
                        
                        // 清空表單
                        document.getElementById('message-text').value = ''; // 清空問題詳述
                        dropdownToggle.textContent = '通勤相關'; // 重置為預設值
                    }
                };

                // 發送表單數據
                xhr.send('type=' + encodeURIComponent(selectedValue) + '&message=' + encodeURIComponent(messageText));
            });

        });
    </script>
</body>
</html>
