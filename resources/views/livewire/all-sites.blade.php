<div>

<div class="card w-50 container p-0 shadow">

    <div class="card-header">
        <h4 class="float-left">Sites</h4>
        <div class="float-right" style="width: 40%">
            <form wire:submit.prevent="addSite" class="d-flex justify-content-around">
                <div class="input-group-prepend">
                    <div class="input-group-text bg-light" style="color:rgb(189, 189, 189);border-radius: 0;border:none;border-bottom: 1px solid rgb(189, 189, 189);"><i class="fas fa-map-marker-alt"></i></div>
                </div>
                <input wire:model="site" type="text" class="form-control bg-light shadow-none" style="border-radius: 0;border:none;border-bottom: 1px solid rgb(189, 189, 189);outline: none;" placeholder="Add New Site">
                <button class="btn text-primary"><i class="fas fa-plus"></i></button>
            </form>
        </div>
    </div>

    <div class="card-body d-flex justify-content-around flex-wrap">
        @forelse ($all_sites as $site)
            <a wire:click="site({{$site->id}})" class="w-25 p-4 my-4 h4 bg-white rounded shadow text-center" style="cursor: pointer">
                {{ $site->site_name }}
            </a>
        @empty
            <h6>No sites found</h6>
        @endforelse
    </div>

</div>

{{-- <div class="container p-3 rounded shadow-lg bg-white w-25" id="add-form" style="display:none;position: absolute;top:50%;left:50%;transform: translate(-50%,-50%)">
    <h5 class="text-secondary text-center">Add new site</h5>
    <form wire:submit="addSite">
        
        @error('site')
            <div class="text-danger">
                    {{ $message }}
            </div>
        @enderror
        <br>
        <button type="submit" class="btn btn-primary d-inline float-right mx-1">Add</button>
        <span class="btn btn-light d-inline float-right mx-1" id="cancel">Cancel</span>

    </form>
</div> --}}

</div>

{{-- <script>

document.getElementById('addSite').onclick = () => {
    document.getElementById('back').style.opacity = '0.2';
    document.getElementById('add-form').style.display = 'block';
}

document.getElementById('cancel').onclick = () => {
    document.getElementById('back').style.opacity = '1';
    document.getElementById('add-form').style.display = 'none';
}

</script> --}}