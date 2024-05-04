//創建Platform物件，HERE Maps API 的入口點
var platform = new H.service.Platform({
  'apikey': 'vLOV0OZxoNgUvE2m00AvrNTQzGhZtOPuCSwU9_BFcBg'
});

//創建defaultLayers物件，包含了默認地圖圖層
var defaultLayers = platform.createDefaultLayers();

//創建map物件，顯示為向量圖層
var map = new H.Map(
  document.getElementById('mapContainer'),
  defaultLayers.vector.normal.map,
  {
      zoom: 11,   //縮放級別
      center: { lat: 25.037975, lng: 121.5493 }   //初始中心點經緯度座標
  });
