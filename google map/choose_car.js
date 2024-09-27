document.getElementById('transportMode').addEventListener('change', function () {
  const transportMode = this.value;
  const vehicleTypeSelect = document.getElementById('vehicleType');

  // 清空車輛選擇的選項
  vehicleTypeSelect.innerHTML = '<option value="">載入中...</option>';

  // 判斷使用者選擇的是公司車還是其他交通工具
  let apiUrl = '';
  if (transportMode === 'is_cm_car') {
      apiUrl = '/api/getCmCars';  // 從資料庫取得公司車車名的API
  } else if (transportMode === 'not_cm_car') {
      apiUrl = '/api/getTransportations';  // 從資料庫取得車種的API
  }

  // 發送 AJAX 請求，從伺服器獲取資料
  fetch(apiUrl)
      .then(response => response.json())
      .then(data => {
          // 清空並加入新的選項
          vehicleTypeSelect.innerHTML = '<option value="">請選擇車輛</option>';
          data.forEach(item => {
              const option = document.createElement('option');
              option.value = item.id;  // 假設從資料庫取得的資料中有id
              option.textContent = item.name;  // 顯示的車名或車種
              vehicleTypeSelect.appendChild(option);
          });
      })
      .catch(error => {
          console.error('Error fetching vehicle data:', error);
          vehicleTypeSelect.innerHTML = '<option value="">資料載入失敗</option>';
      });
});




// 假設您使用 Express 和 MySQL 或其他資料庫

// API 路由：取得公司車
app.get('/api/getCmCars', (req, res) => {
    // 查詢資料庫中的公司車
    const sql = 'SELECT cc_id, cc_name, cc_type FROM cm_car';
    db.query(sql, (err, results) => {
        if (err) {
            return res.status(500).json({ error: '資料庫查詢錯誤' });
        }
        res.json(results);  // 將查詢結果傳回前端
    });
});

// API 路由：取得其他交通工具的車種
app.get('/api/getTransportations', (req, res) => {
    // 查詢資料庫中的交通工具種類
    const sql = 'SELECT id, typr, num FROM transportation';
    db.query(sql, (err, results) => {
        if (err) {
            return res.status(500).json({ error: '資料庫查詢錯誤' });
        }
        res.json(results);  // 將查詢結果傳回前端
    });
});
