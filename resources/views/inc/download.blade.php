<div class="px-4" style="width: 50px" x-data="{ open: false }">
    <button class="btn text-secondary" @click.prevent="open = true"><i class="fa-solid fa-file-arrow-down fa-lg"></i></button>
    <div class="bg-white rounded shadow p-2 position-absolute" style="width: 90px" x-show="open" x-cloak @click.away="open = false" 
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter="ease-out transition-medium"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave="ease-in transition-faster"
        x-transition:leave-end="opacity-0 scale-90">
        {{-- <a href="{{ route($pdf_path, $data) }}" class="btn btn-outline-danger font-weight-bold mb-1"><i class="fas fa-file-pdf fa-lg"></i> PDF</a>
        <a href="{{ route($csv_path, $data) }}" class="btn btn-outline-success font-weight-bold mt-1"><i class="fas fa-file-csv fa-lg"></i> CSV</a> --}}
    
        <form action="{{ route($pdf_path) }}" method="post">
            @csrf
            <input type="hidden" name="data" value="{{$query}}">
            <button type="submit" class="btn btn-outline-danger font-weight-bold mb-1"><i class="fas fa-file-pdf fa-lg"></i> PDF</button>
        </form>
    
        <form action="{{ route($csv_path) }}" method="post">
            @csrf
            <input type="hidden" name="data" value="{{$query}}">
            <button type="submit" class="btn btn-outline-success font-weight-bold mt-1"><i class="fas fa-file-csv fa-lg"></i> CSV</button>
        </form>
    </div>
</div>