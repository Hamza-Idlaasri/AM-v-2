<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.0"></script>
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>

<div class="container m-2 d-flex justify-content-center align-items-center flex-wrap">
    
    <div class="bg-white shadow py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px">
        <h6 class="mb-2 text-secondary">Porcentage des Hosts</h6>
        <canvas  id="hosts"></canvas>     
    </div>

    <div class="bg-white shadow py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px">
        <h6 class="mb-2 text-secondary">Porcentage des Boxes</h6>
        <canvas  id="boxes"></canvas>     
    </div>

    <div class="bg-white shadow py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px">
        <h6 class="mb-2 text-secondary">Porcentage des Services</h6>
        <canvas  id="services"></canvas>     
    </div>

    <div class="bg-white shadow py-3 px-4 m-3" style="position: relative; width:32vw;border-radius: 12px">
        <h6 class="mb-2 text-secondary">Porcentage des Equipements</h6>
        <canvas  id="equips"></canvas>
    </div>
</div>

{{-- Hosts chart --}}
<script>

    let ctxHostPie = document.getElementById('hosts').getContext('2d');
    let hostChart = new Chart(ctxHostPie, {
        type: 'doughnut',
        data:{
            labels:['Up','Down','Unreachable'],
            datasets:[{
                data: [{{ $hosts_up }},{{ $hosts_down }},{{ $hosts_unreachable }}],
                backgroundColor: [
                        '#38c172',
                        '#e3342f',
                        'rgb(151, 4, 230)'
                    ],
                
            }],

        },
        
        options:{
            responsive: true,
            legend:{
                position:'left',
                labels:{
                    boxWidth:12,
                }
            },
            plugins: {
                labels : {
                    fontColor : ['#fff','#fff','#fff'],
                    fontSize : 13,
                },

            },
            cutoutPercentage: 60,
            
        },   
        
    });

</script>

{{-- Boxes chart --}}
<script>

    let ctxBoxesPie = document.getElementById('boxes').getContext('2d');
    let boxesChart = new Chart(ctxBoxesPie, {
        type: 'doughnut',
        data:{
            labels:['Up','Down','Unreachable'],
            datasets:[{
                data: [{{ $boxes_up }},{{ $boxes_down }},{{ $boxes_unreachable }}],
                backgroundColor: [
                        '#38c172',
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
                    boxWidth:12,
                }
            },
            plugins: {
                labels : {
                    fontColor : ['#fff','#fff','#fff'],
                    fontSize : 13,
                }
            },
            cutoutPercentage: 60,
            
        },   
        
    });

</script>

{{-- Services chart --}}
<script>

    let ctxServicesPie = document.getElementById('services').getContext('2d');
    let servicesChart = new Chart(ctxServicesPie, {
        type: 'doughnut',
        data:{
            labels:['Ok','Warning','Critical','Unknown'],
            datasets:[{
                data: [{{ $services_ok }},{{ $services_warning }},{{ $services_critical }},{{ $services_unknown }}],
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
                    boxWidth:12,
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

{{-- Equips chart --}}
<script>

    let ctxEquipsPie = document.getElementById('equips').getContext('2d');
    let equipsChart = new Chart(ctxEquipsPie, {
        type: 'doughnut',
        data:{
            labels:['Ok','Warning','Critical','Unknown'],
            datasets:[{
                data: [{{ $equips_ok }},{{ $equips_warning }},{{ $equips_critical }},{{ $equips_unknown }}],
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
                    boxWidth:12,
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

<script>

window.addEventListener('load', function() {
    document.getElementById('overview').classList.toggle("active");
});

</script>