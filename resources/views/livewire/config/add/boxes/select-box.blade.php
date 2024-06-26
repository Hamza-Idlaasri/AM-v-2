<div class="container my-4 d-flex justify-content-around align-items-center flex-wrap">
            
    @forelse ($boxes as $box)
        
        <a href="{{ route('add-pin', $box->host_object_id) }}" class="m-2 pt-4 pb-2 px-1 bg-white shadow text-center" style="text-decoration: none;min-width:150px;border-radius: 12px">
            <h1>
                <i class="fa-solid fa-microchip"></i>
            </h1>
            
            <br>

            <h4>{{ $box->display_name }}</h4>
        </a>    

    @empty
        <h6>No result found</h6>
    @endforelse

</div>

<script>

    window.addEventListener('load', function() {
        document.getElementById('config').style.display = 'block';
        document.getElementById('config-btn').classList.toggle("active-btn");
        document.getElementById('c-pins').classList.toggle("active-link");
    });
        
</script>