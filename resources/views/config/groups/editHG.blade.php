@extends('layouts.app')

@section('content')

<div class="container p-3 bg-white w-50 rounded shadow mx-auto my-4">

    <form action="{{ route('editHG', $hostgroup->hostgroup_id) }}" method="get">
        <label for="hg_name"><b>Hostgroup Name <span class="text-danger">*</span></b></label>
        <input type="text" name="hostgroup_name" class="form-control w-100 @error('hostgroup_name') border-danger @enderror" id="hg_name" value="{{ $hostgroup->hostgroup_name }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,200}" title="Hostgroup name must be between 2 & 200 charcarters in length and containes only letters, numbers, and these symbols -_+">
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

        <div class="p-2 bg-white w-100" style="overflow: auto;max-height:250px;border:1px solid rgb(216, 215, 215);border-radius:5px">
            @foreach ($hosts as $host)
                @if (in_array($host->host_object_id, $all_members))
                    <input type="checkbox" name="members[]" id="{{$host->host_object_id}}" value="{{$host->host_name}}" checked> <label for="{{$host->host_object_id}}" style="user-select: none"> {{$host->host_name}}</label>
                    <br>
                @else
                    <input type="checkbox" name="members[]" id="{{$host->host_object_id}}" value="{{$host->host_name}}"> <label for="{{$host->host_object_id}}" style="user-select: none"> {{$host->host_name}}</label>
                    <br>
                @endif
            @endforeach
        </div>
        <br>

        <button class="btn btn-primary">Edit</button>

    </form>

</div>

@endsection