var ctx = document.getElementById('myChart');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['01.04.', '02.04.', '03.04.', '04.04.', '05.04.', '06.04.', '07.04'],
        datasets: [{
            label: '# of Sessions',
            data: [12, 19, 3, 5, 2, 3, 41],
        }]
    },
    options: {
        elements: {
            line: {
                tension: 0
            }
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
