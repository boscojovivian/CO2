let map;
let userPath;
let marker;
let startTime;
let watchId;
let totalDistance = 0;
let positions = []; // 用來存儲所有位置點和時間戳

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 0, lng: 0},
        zoom: 15
    });

    // 創建一條用來表示路徑的折線
    userPath = new google.maps.Polyline({
        path: [],
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2
    });

    userPath.setMap(map);

    // 檢查瀏覽器是否支持Geolocation API
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const initialPosition = {lat: position.coords.latitude, lng: position.coords.longitude};
            map.setCenter(initialPosition);

            // 創建並顯示用戶位置的標記
            marker = new google.maps.Marker({
                position: initialPosition,
                map: map,
                title: '你的當前位置'
            });
        });
    } else {
        alert('此瀏覽器不支援地理定位功能。');
    }

    // 綁定按鈕事件
    document.getElementById('startBtn').addEventListener('click', startTracking);
    document.getElementById('stopBtn').addEventListener('click', stopTracking);
    document.getElementById('exportBtn').addEventListener('click', exportData);

    // 設定日期時間的自動更新
    updateDateTime();
    setInterval(updateDateTime, 1000); // 每秒更新一次
}

function updateDateTime() {
    const now = new Date();
    const formattedDateTime = now.toLocaleDateString('zh-TW', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: 'Asia/Taipei' // 設定台灣時間
    });

    document.getElementById('dateTime').innerText = `現在時間：${formattedDateTime}`;
}

function startTracking() {
    if (navigator.geolocation) {
        startTime = new Date().getTime();

        // 使用 watchPosition 只在第一次要求權限，之後自動監測位置變化
        watchId = navigator.geolocation.watchPosition(position => {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
                timestamp: new Date().toLocaleString('zh-TW', { timeZone: 'Asia/Taipei' })  // 記錄當前台灣時間
            };

            positions.push(pos); // 存儲位置和時間戳

            const path = userPath.getPath();
            path.push(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));

            if (path.getLength() > 1) {
                const prevPos = path.getAt(path.getLength() - 2);
                const currPos = path.getAt(path.getLength() - 1);
                const distance = google.maps.geometry.spherical.computeDistanceBetween(prevPos, currPos);
                totalDistance += distance;

                const currentTime = new Date().getTime();
                const elapsedTime = (currentTime - startTime) / 1000; // 以秒為單位

                const speed = (totalDistance / 1000) / (elapsedTime / 3600); // 速度以公里/小時為單位

                // 將距離轉換為公里
                const distanceInKm = totalDistance / 1000;

                // 將時間轉換為小時和分
                const hours = Math.floor(elapsedTime / 3600);
                const minutes = Math.floor((elapsedTime % 3600) / 60);

                document.getElementById('distance').innerText = distanceInKm.toFixed(2) + ' 公里';
                document.getElementById('time').innerText = hours + ' 小時 ' + minutes + ' 分鐘';
                document.getElementById('speed').innerText = speed.toFixed(2) + ' 公里/小時';
            }

            // 更新地圖中心位置和標記位置
            map.setCenter(pos);
            marker.setPosition(pos);

        }, error => {
            // 錯誤處理部分鐘
            if (error.code === 1) {
                alert('未授權獲取位置信息。請允許網站訪問您的位置信息。');
            } else if (error.code === 2) {
                alert('無法獲取位置信息。請檢查您的網絡或 GPS 設置。');
            } else if (error.code === 3) {
                alert('獲取位置信息超時。請稍後再試。');
            } else {
                alert('發生未知錯誤。錯誤代碼：' + error.code);
            }
        }, {
            enableHighAccuracy: true,
            timeout: 3000, // 將超時設置為 3 秒
            maximumAge: 0 // 設定最大允許的定位信息年齡為0
        });

        // watchId = navigator.geolocation.watchPosition(successCallback, errorCallback, options);
    } else {
        alert('此瀏覽器不支援地理定位功能。');
    }
}


function stopTracking() {
    if (watchId) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
        alert('路線追蹤已停止。');
    }
}

function exportData() {
    const data = {
        distance: (totalDistance / 1000).toFixed(2) + ' 公里',  // 以公里為單位
        time: ((new Date().getTime() - startTime) / 3600000).toFixed(2) + ' 小時',  // 以小時為單位
        path: positions  // 使用完整的路径数据，包括时间戳
    };

    const blob = new Blob([JSON.stringify(data, null, 2)], {type: 'application/json'});
    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = 'route-data.json';
    a.click();
    URL.revokeObjectURL(url);
}

window.initMap = initMap;
