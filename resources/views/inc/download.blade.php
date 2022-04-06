{{-- <div class="px-4" style="width: 50px">
    <span class="btn text-secondary" style="opacity: .7" id="download"><i class="far fa-file-download fa-lg"></i></span>
</div> --}}

{{-- <div class="container w-25 bg-white shadow rounded text-center p-2" style="position: absolute;top: 50%;left: 50%">
    <span class="btn text-muted" style="position: absolute;right: 5px;top: 5px"><i class="far fa-times"></i></span>
    <br>
    <p class="font-weight-bold">Choose type of file</p>
    <div class="d-flex justify-content-around w-100">
        <a href="" class="btn btn-danger font-weight-bold"><i class="fas fa-file-pdf fa-lg"></i> PDF</a>
        <a href="" class="btn btn-success font-weight-bold"><i class="fas fa-file-csv fa-lg"></i> CSV</a>
    </div>
</div> --}}

<div class="px-4" style="width: 50px" x-data="{ open: false }">
    <button class="btn" @click.prevent="open = true"><i class="far fa-file-download fa-lg"></i></button>
    <div class="bg-white rounded shadow p-2 position-absolute" style="width: 90px" x-show="open" x-cloak @click.away="open = false" 
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter="ease-out transition-medium"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave="ease-in transition-faster"
        x-transition:leave-end="opacity-0 scale-90">
        <a href="{{ route($pdf_path) }}" class="btn btn-outline-danger font-weight-bold mb-1"><i class="fas fa-file-pdf fa-lg"></i> PDF</a>
        <a href="{{ route($csv_path) }}" class="btn btn-outline-success font-weight-bold mt-1"><i class="fas fa-file-csv fa-lg"></i> CSV</a>
    </div>
</div>