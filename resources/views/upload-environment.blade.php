@extends('layouts.app')

@section('content')    

    <div class="container bg-white p-4 m-4 shadow-sm w-50">
        <h1 class="text-center">Set Environment</h1>
        <form action="{{ route('import-envir')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="custom-file my-2 w-50">
                <input type="file" class="form-control-file @error('excel') text-danger @enderror" id="customFile" name="excel" style="cursor: pointer">
                {{-- <label class="custom-file-label @error('excel') border-danger @enderror" for="customFile"></label> --}}
            </div>
            @error('excel')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            <div>
                <button class="btn btn-primary mt-2"><i class="fa-solid fa-arrow-up-from-bracket"></i> Upload</button>
            </div>
        </form>
    </div>

    <script>

        window.addEventListener('load', function() {
            document.getElementById('config').style.display = 'block';
            document.getElementById('config-btn').classList.toggle("active-btn");
            document.getElementById('c-set-envir').classList.toggle("active-link");
        });
            
    </script>
@endsection