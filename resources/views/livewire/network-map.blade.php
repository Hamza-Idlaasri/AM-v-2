<div class="" style="height: 100%">

    {{-- Filter --}}
    {{-- <div class="bg-white mt-4">
        @include('inc.network-filter')
    </div> --}}

    {{-- Library --}}
    <script src="{{ asset('js/vis.js') }}"></script>
    
    {{-- Network Map --}}
    <div class="container p-3" style="height: 100%;">
        
        <div class="network bg-white shadow-sm rounded" style="height: 100%"></div>
        
    </div>
</div>

<script>
    
    let node = [];

    let edge = [];

    let child = [];

    let label;

    let fontColor;

   
    // Create Nodes : -----------------------------------------------------------------------------------------------------//

    {!!$hosts!!}.forEach(function(host) {

        switch (host.current_state) {
                case 0:
                    label = 'Up';
                    fontColor = 'green';
                    break;

                case 1:
                    label = 'Down';
                    fontColor = 'red';
                    break;

                case 2:
                    label = 'Unreachable';
                    fontColor = 'rgb(151, 4, 230)';
                    break;

                default:
                    break;
        };

        label += '\n' + host.display_name + '\n' + host.address;

        node.push({

            id: host.host_object_id,
            label: label,
            font:{
                color:fontColor,
            },

        });

        
    });

    // Center of Network :

    node.push({

            id: 0,
            label: 'Net Monitor',
            font:{
                color:'#132043',
            },
            shape:'box',

    });

    let nodes = new vis.DataSet(node);

    // End Create Nodes ------------------------------------------------------------------------------------------------------------//
    

    // Create Edges (Relationship between nodes) :

    {!!$parent_hosts!!}.forEach(function(host) {

        edge.push({

            from : host.parent_host_object_id,
            to : host.host_object_id

        });

        child.push(host.host_object_id);

    });

    {!!$hosts!!}.forEach(function(host) {

       if (!child.includes(host.host_object_id)) {

        edge.push({

            from : 0,
            to : host.host_object_id

        });

       } 

    });

    let edges = new vis.DataSet(edge);

    const container = document.querySelector('.network');

    let data = {
        nodes: nodes,
        edges: edges
    };

    let options = {


            nodes: {
                fixed: true,
                font: {
                    size: 14,
                    face: 'arial'
                },
                borderWidth: 0,
                color: {
                    background: '#D2E0FB',
                    border: "1",
                },
                shape:'box',


            },

            edges: {
                color: {
                    inherit: false
                },

                // dashes:true,

                // smooth: {
                //     enabled: true,
                //     type: 'dynamic'
                // },

                arrows: {
                    to: {
                        enabled: true,
                        scaleFactor: .25,
                        type: 'circle'
                    }
                },


            },


            interaction:{
                zoomView:true,

            },

            layout: {
                randomSeed: 0,
                hierarchical: {
                    enabled: true,
                    nodeSpacing: 200,
                    treeSpacing: 500,
                    // direction: 'LR'
                    parentCentralization: true,
                    // levelSeparation: 500
                }
            }
        };



        let Automap = new vis.Network(container, data, options);

</script>

{{-- <script>
document.addEventListener('livewire:update', function () {
    
        data.update()
        network.setData(data);
    })
</script> --}}

<script>

    window.addEventListener('load', function() {
        document.getElementById('network-map').classList.toggle("active");
    });
    
</script>