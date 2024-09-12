const ctx = document.getElementById('carbonChart').getContext('2d');
const carbonChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
        datasets: [{
            label: '2023年',
            data: [3, 4, 3, 5, 4, 6, 5, 7, 4, 6, 7, 8],
            backgroundColor: '#FF6633'
        }, {
            label: '2024年',
            data: [5, 6, 4, 5, 6, 7, 8, 9, 6, 8, 9, 10],
            backgroundColor: '#3366FF'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
