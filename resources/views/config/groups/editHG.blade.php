@extends('layouts.app')

@section('content')

<div class="container p-3 bg-white w-50 rounded shadow mx-auto my-4">

    <form action="{{ route('editHG', $hostgroup->hostgroup_id) }}" method="get">
        <label for="hg_name"><b>Hostgroup Name <span class="text-danger">*</span></b></label>
        <input type="text" name="hostgroup_name" class="form-control w-100 @error('hostgroup_name') border-danger @enderror" id="hg_name" value="{{ $hostgroup->hostgroup_name }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,20}" title="Hostgroup name must be between 2 & 20 charcarters in length and containes only letters, numbers, and these symbols -_+">
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

        <div class="p-2 bg-white w-100" style="overflow: auto;max-height:200px;border:1px solid rgb(216, 215, 215);border-radius:5px">
            @foreach ($hosts as $host)
                @if (in_array($host->host_object_id, $all_members))
                    <input type="checkbox" name="members[]" id="mbrs" value="{{$host->host_name}}" checked> {{$host->host_name}}
                    <br>
                @else
                    <input type="checkbox" name="members[]" id="mbrs" value="{{$host->host_name}}"> {{$host->host_name}}
                    <br>
                @endif
            @endforeach
        </div>
        <br>

        <button class="btn btn-primary">Edit</button>

    </form>

</div>

@endsection