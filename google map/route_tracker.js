let map;
let userPath;
let marker;
let startTime;
let watchId;
let totalDistance = 0;
let positions = []; // 用來存儲所有位置點和時間戳
let loca = [];
let calculatedRoute = null; // 全局變數用於存儲計算的路線

function initMap() {
    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer();

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
    directionsRenderer.setMap(map);

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

            if (loca.length === 0) {
                // 第一次獲取位置時，將位置作為起點
                loca.push(new google.maps.LatLng(pos.lat, pos.lng));
            }
            // loca.push(new google.maps.LatLng(pos.lat, pos.lng));

            if (path.getLength() > 1) {
                const prevPos = path.getAt(path.getLength() - 2);
                const currPos = path.getAt(path.getLength() - 1);
                const distance = google.maps.geometry.spherical.computeDistanceBetween(prevPos, currPos);
                totalDistance += distance;

                const currentTime = new Date().getTime();
                const elapsedTime = (currentTime - startTime) / 1000; // 以秒為單位

                // const speed = (totalDistance / 1000) / (elapsedTime / 3600); // 速度以公里/小時為單位

                // 將距離轉換為公里
                const distanceInKm = totalDistance / 1000;

                // 將時間轉換為小時和分鐘
                const hours = Math.floor(elapsedTime / 3600);
                const minutes = Math.floor((elapsedTime % 3600) / 60);

                document.getElementById('distance').innerText = distanceInKm.toFixed(2) + ' 公里';
                document.getElementById('time').innerText = hours + ' 小時 ' + minutes + ' 分鐘';
                // document.getElementById('speed').innerText = speed.toFixed(2) + ' 公里/小時';
            }

            // 更新地圖中心位置和標記位置
            map.setCenter(pos);
            marker.setPosition(pos);

        }, error => {
            // 錯誤處理部分
            handleGeolocationError(error);
        }, {
            enableHighAccuracy: true,
            timeout: 3000, // 將超時設置為 1 秒
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

        // 停止追蹤時將最後的位置添加到 loca 作為終點
        if (positions.length > 0) {
            const lastPos = positions[positions.length - 1];
            loca.push(new google.maps.LatLng(lastPos.lat, lastPos.lng));
        }

        alert('路線追蹤已停止。');
    }

    // calculateAndDisplayRoute(directionsService, directionsRenderer);

    // const onChangeHandler = function () {
    //     calculateAndDisplayRoute(directionsService, directionsRenderer);
    // };

    if (loca.length >= 2) {
        calculateAndDisplayRoute(directionsService, directionsRenderer);
    } else {
        alert('路徑數據不足以計算路線。');
    }
}

function exportData() {
    let elapsedTime = 0;

    if (watchId) {
        elapsedTime = (new Date().getTime() - startTime) / 3600000; // 以小時為單位
    } else if (positions.length > 0) {
        const lastPositionTime = new Date(positions[positions.length - 1].timestamp).getTime();
        if (!isNaN(lastPositionTime)) {
            elapsedTime = (lastPositionTime - startTime) / 3600000;
        } else {
            console.error("Invalid timestamp in positions array.");
            elapsedTime = 0;  // 或者处理方式根据业务需求调整
        }
    } else {
        console.warn("No positions recorded, cannot calculate elapsed time.");
    }

    if (!isNaN(elapsedTime)) {
        const data = {
            distance: (totalDistance / 1000).toFixed(2) + ' 公里',  // 以公里為單位
            time: elapsedTime.toFixed(2) + ' 小時',  // 以小時為單位
            path: positions,  // 使用完整的路徑數據，包括時間戳
            calculatedRoute: calculatedRoute || []  // 計算的路線，如果尚未計算則為空數組
        };

        const blob = new Blob([JSON.stringify(data, null, 2)], {type: 'application/json'});
        const url = URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = 'route-data.json';
        a.click();
        URL.revokeObjectURL(url);
    } else {
        console.error("Elapsed time is NaN, data export aborted.");
    }
}




// 路線
function calculateAndDisplayRoute(directionsService, directionsRenderer) {
    if (loca.length < 2) {
        alert("路徑數據不足，無法計算路線。");
        return;
    }

    directionsService.route({
        origin: loca[0],
        destination: loca[loca.length - 1],
        travelMode: google.maps.TravelMode.DRIVING,
    })
    .then((response) => {
        directionsRenderer.setDirections(response);

        // 將計算的路線存儲到全局變數
        calculatedRoute = response.routes[0].overview_path.map(point => ({
            lat: point.lat(),
            lng: point.lng()
        }));

        console.log('Calculated Route:', calculatedRoute);

        // 导出数据（如果需要的话）
        // exportData();
    })
    .catch((e) => window.alert("Directions request failed due to " + e.message));
}


// 錯誤處理部分
function handleGeolocationError(error) {
    switch (error.code) {
        case 1:
            alert('未授權獲取位置信息。請允許網站訪問您的位置信息。');
            break;
        case 2:
            alert('無法獲取位置信息。請檢查您的網絡或 GPS 設置。');
            break;
        case 3:
            alert('獲取位置信息超時。請稍後再試。');
            break;
        default:
            alert('發生未知錯誤。錯誤代碼：' + error.code);
            break;
    }
}


window.initMap = initMap;
