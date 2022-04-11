@extends('layouts.app')

@section('content')

<div class="container p-4 bg-white rounded shadow mx-auto my-4 w-50">

    <form action="{{ route('createHG') }}" method="get">
        <label for="hg_name"><b>Hostgroup Name <span class="text-danger">*</span></b></label>
        <input type="text" name="hostgroup_name" class="form-control @error('hostgroup_name') border-danger @enderror" id="hg_name" value="{{ old('hostgroup_name') }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{4,20}" title="HostGroup name must be between 4 & 20 charcarters in length and containes only letters, numbers, and these symbols -_+">
        @error('hostgroup_name')
            <div class="text-danger">
                {{ $message }}
            </div>
        @enderror
        
        <br>
        
        <label for="mbrs"><b>Members <span class="text-danger">*</span></b></label>
        <br>
        @error('members')
            <div class="text-danger">
                {{ $message }}
            </div>
        @enderror

        <div class="p-2 bg-white" style="overflow: auto;max-height:200px;border:1px solid rgb(216, 215, 215);border-radius:5px">
            @forelse ($hosts as $host)
                <input type="checkbox" name="members[]" id="{{$host->host_name}}" value="{{$host->host_name}}"> <label for="{{$host->host_name}}">{{$host->host_name}}</label>
                <br>
            @empty
                <p>No hosts</p>
            @endforelse
        </div>
        
        <br>

        <button class="btn btn-primary">Add</button>

    </form>

</div>

@endsection