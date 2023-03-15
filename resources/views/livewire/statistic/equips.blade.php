<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2">

    <div>
        {{-- Filter --}}
        <div class="container bg-white w-75 p-0 my-3 mx-auto d-flex justify-content-between align-items-center">
            {{-- Filter --}}
            @include('inc.filter',['names' => $equips_names,'type' => 'equip','from' => 'statistic'])
        </div>
    </div>

    <hr>

    <script src="{{ asset('js/chartjs-plugin.js') }}"></script>

    <div class="container m-2 d-flex justify-content-center align-items-center flex-wrap">

        {{-- Doughnut Chart --}}
        <div class="bg-white border py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px;border-color:rgb(218, 218, 218)!important">
            <h6 class="mb-2 text-secondary">Porcentage des Equipements</h6>
            <canvas  id="PieChart"></canvas>     
        </div>

        {{-- Bar Chart --}}
        <div class="bg-white border py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px;border-color:rgb(218, 218, 218!important">
            <h6 class="mb-2 text-secondary">Total des Equipements</h6>
            <canvas id="BarChart"></canvas>
        </div>
        
        {{-- <div class="bg-white shadow py-3 px-4 m-3" id="timeline" style="width:66vw;border-radius: 12px;">
            <h6 class="mb-2 text-secondary">Timeline des Equipements</h6>
            <br>
            <div id="floating" style="height: 60vh"></div>
        </div> --}}

    </div>
</div>

<script>
    let ok = @json($equips_status[0]);
    let warning = @json($equips_status[1]);
    let critical = @json($equips_status[2]);
    let unknown = @json($equips_status[3]);
</script>

{{------------------------------------------ Piechart -----------------------------------------------------------}}
<script>

    let ctxPieChart = document.getElementById('PieChart').getContext('2d');
    let equipsPieChart = new Chart(ctxPieChart, {
        type: 'doughnut',
        data:{
            labels:['Ok','Warning','Critical','Unknown'],
            datasets:[{
                data: @json($equips_status),
                backgroundColor: [
                        '#38c172',
                        '#ffed4a',
                        '#e3342f',
                        'rgb(151, 4, 230)'
                    ],

            }]
        },
        
        options:{
            responsive: true,
            legend:{
                position:'left',
                labels:{
                    boxWidth:15,
                }
            },
            plugins: {
                labels : {
                    fontColor : ['#fff','#212529','#fff','#fff'],
                    fontSize : 13,
                }
            },
            cutoutPercentage: 60,
            
        },   
        
    });

</script>

<!---------------------------------------- BarChart ------------------------------------------------------------------->
<script>

    let ctxBarChart = document.getElementById('BarChart').getContext('2d');
    let equipsBarChart = new Chart(ctxBarChart, {
        type: 'bar',
        data: {
            labels: ['Ok','Warning','Critical','Unknown'],
            datasets: [{

                data: @json($equips_status),
                backgroundColor: [
                    '#38c172',
                    '#ffed4a',
                    '#e3342f',
                    'rgb(151, 4, 230)'
                ],
                // borderWidth: 1,
            }],

        },
        
        options: {
            
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        stepSize:2,
                        autoSkip: true,
                        maxTicksLimit: 10
                    },
                    gridLines: {
                        // display:false
                        color:'#f3f3f3',
                    },
                }],

                xAxes:[{
                    barPercentage:0.3,
                    gridLines: {
                        // display:false
                        color:'#f3f3f3',
                    },
                }],
            },
            legend:{
                display:false
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem) {
                            return tooltipItem.yLabel;
                    }
                }
            },
            
        }
    });
    
</script>

{{-- <script>
    
    var refreshBtn = document.getElementById('refreshBtn');

    refreshBtn.addEventListener('click', function() {
        // update chart data and options
        equipsBarChart.data.datasets[0].data = @json($equips_status);
        equipsBarChart.options.title.text = 'Updated Chart Title';

        // call chart update method
        equipsBarChart.update();

        console.log(ok);
    });

</script> --}}

{{-- <script type="text/javascript">
    google.charts.load("current", {packages:["timeline"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
  
      var container = document.getElementById('floating');
      var chart = new google.visualization.Timeline(container);
      var dataTable = new google.visualization.DataTable();
      dataTable.addColumn({ type: 'string', id: 'Position' });
      dataTable.addColumn({ type: 'string', id: 'Name' });
      dataTable.addColumn({ type: 'string', id: 'style', role: 'style' });
      dataTable.addColumn({ type: 'date', id: 'Start' });
      dataTable.addColumn({ type: 'date', id: 'End' });

      let datasets = @json($datasets);

      let data = [];
      
      datasets.forEach(element => {

            for (let i = 0; i < element.Ok.length; i++) {
                
                data.push([element.equip_name,'Ok','#38c172',new Date(element.Ok[i][0]), new Date(element.Ok[i][1])]);
                
            }
            for (let i = 0; i < element.Warning.length; i++) {
                
                data.push([element.equip_name,'Warning','#ffed4a',new Date(element.Warning[i][0]), new Date(element.Warning[i][1])]);
                
            }
            for (let i = 0; i < element.Critical.length; i++) {
                
                data.push([element.equip_name,'Critical','#e3342f',new Date(element.Critical[i][0]), new Date(element.Critical[i][1])]);
                
            }
            for (let i = 0; i < element.Unknown.length; i++) {
                
                data.push([element.equip_name,'Unknown','rgb(151, 4, 230)',new Date(element.Unknown[i][0]), new Date(element.Unknown[i][1])]);
                
            }
            
      });


        dataTable.addRows(data);
    
        // let height = dataTable.getNumberOfRows() * 90;
    
        // document.getElementById('timeline').style.height = height+'px';

        let options = {
                // height: height,
                timeline: { 
                    colorByRowLabel: true,
                },
                alternatingRowStyle: false,
                avoidOverlappingGridLines: false
            }

        chart.draw(dataTable,options);
    }
  
</script> --}}

<script>

window.addEventListener('load', function() {
    document.getElementById('statistic').style.display = 'block';
    document.getElementById('statistic-btn').classList.toggle("active-btn");
    document.getElementById('s-equips').classList.toggle("active-link");
});

</script>