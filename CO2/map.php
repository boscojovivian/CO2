<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps Embed Example</title>
</head>
<body>
    <h1>Google Maps Embed Example</h1>
    <!-- 在這裡插入 Google 地圖的 iframe -->
    <iframe
        id="mapIframe"
        width="600"
        height="450"
        frameborder="0" style="border:0"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3615.7772651821194!2d121.56469271452888!3d25.033963599999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3442ab3e25c703d3%3A0x806faa2b6acaf51f!2z5Y-w5YyX5biC5aSn5a24!5e0!3m2!1szh-TW!2stw!4v1620041675312!5m2!1szh-TW!2stw" allowfullscreen>
    </iframe>

    <!-- 起點和終點的表單 -->
    <form id="directionForm">
        <label for="start">起點:</label>
        <input type="text" id="start" name="start" required>
        <br>
        <label for="end">終點:</label>
        <input type="text" id="end" name="end" required>
        <br>
        <button type="submit">開始路線</button>
    </form>

    <script>
        // 獲取表單和 iframe 元素
        var directionForm = document.getElementById('directionForm');
        var mapIframe = document.getElementById('mapIframe');

        // 監聽表單提交事件
        directionForm.addEventListener('submit', function(event) {
            event.preventDefault(); // 防止表單默認提交

            // 獲取使用者輸入的起點和終點
            var start = document.getElementById('start').value;
            var end = document.getElementById('end').value;

            // 更新 iframe 的 src 屬性以顯示新的地圖
            mapIframe.src = 'https://www.google.com/maps/embed/v1/directions?' +
                             'origin=' + encodeURIComponent(start) +
                             '&destination=' + encodeURIComponent(end) +
                             '&key=AIzaSyA2-wV3iFnTPuDaTycl6bP6vSVwanF4abQ';
        });
    </script>
</body>
</html>
