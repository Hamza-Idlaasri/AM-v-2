let ctxHostPie = document.getElementById('hosts').getContext('2d');
let hostChart = new Chart(ctxHostPie, {
    type: 'doughnut',
    data: {
        labels: ['Up', 'Down', 'Unreachable'],
        datasets: [{
            data: [{
                { $hosts_up } }, {
                { $hosts_down } }, {
                { $hosts_unreachable } }],
            backgroundColor: [
                '#6ccf01',
                'crimson',
                '#C200FF'
            ],

        }]
    },

    options: {
        responsive: true,
        legend: {
            position: 'left',
            labels: {
                boxWidth: 15,
            }
        },
        plugins: {
            labels: {
                fontColor: ['#fff', '#212529', '#fff', '#fff'],
                fontSize: 13,
            }
        },

    },

});