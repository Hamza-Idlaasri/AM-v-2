@extends('layouts.app')
<style>
    #center-td{
        text-align: center;
        vertical-align: middle;
    }
</style>
@section('content')

    @php
        $envir = session()->get('data');
    @endphp

    <div class="container bg-white p-4 m-4 shadow rounded">
        <form action="{{ route('set-environment') }}" method="post" id="set-environment">
            @csrf
            <table class="table table-bordered text-center">
                <thead class="bg-light text-dark">
                    <th>Box Name</th>
                    <th>IP Address</th>
                    <th>Box Type</th>
                    <th>Equipment Name</th>
                    <th>Pin Description</th>
                    <th>Input Nbr</th>
                    <th style="width: 100px">Hall</th>
                </thead>

                <tbody>
                    @foreach ($envir as $item)
                        <tr>
                            {{-- Box Name --}}
                            <td rowspan="{{ $item->spans }}" id="center-td">
                                <input type="text" class="form-control border-0 @error('box_name.'.$loop->index) border-danger @enderror" name="box_name[]" value="{{ old('box_name.'.$loop->index) == true ? old('box_name.'.$loop->index) : $item->box_name }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,200}" title="Box name must be between 2 & 200 charcarters in length and containes only letters, numbers, and these symbols -_+" required>
                                @error('box_name.'.$loop->index)
                                    <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                @enderror
                            </td>
                            {{-- IP Address --}}
                            <td rowspan="{{ $item->spans }}" id="center-td">
                                <input type="text" name="ip_address[{{$item->box_name}}]" class="form-control border-0 @error('ip_address.'.$item->box_name) border-danger @enderror" id="ip" value="{{  old('ip_address.'.$item->box_name) == true ? old('ip_address.'.$item->box_name) : $item->ip_address  }}" title="Please enter the IP address correctly e.g. 192.168.1.1" required>
                                @error('ip_address.'.$item->box_name)
                                    <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                @enderror
                            </td>
                            {{-- Box Type --}}
                            <td rowspan="{{ $item->spans }}" id="center-td">
                                <select class="form-control my-4" name="box_type[{{$item->box_name}}]">
                                    @if (strcasecmp($item->box_type, "BF1010") == 0)
                                        <option value="BF1010">BF1010</option>
                                        <option value="BF2300">BF2300</option>
                                    @endif
                                    
                                    @if (strcasecmp($item->box_type, "BF2300") == 0)
                                        <option value="BF2300">BF2300</option>
                                        <option value="BF1010">BF1010</option>
                                    @endif
                                </select>
                            </td>

                            {{-- Equip Name --}}
                            @php
                                $equip_index = 0;
                            @endphp
                            <td rowspan="{{ sizeof($item->equips[0]->pins) }}" id="center-td">
                                <input type="text" class="form-control border-0" name="equip_name[{{$item->box_name}}][]" value="{{ $item->equips[0]->equip_name }}" required>
                                @error('equip_name.'.$item->box_name.'.'.$equip_index)
                                    <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                @enderror
                            </td>
                            {{-- Pin Desc --}}
                            <td id="center-td">
                                <input type="text" class="form-control border-0" name="pin_desc[{{$item->box_name}}][{{$item->equips[0]->equip_name}}][]" value="{{$item->equips[0]->pins[0]->pin_desc}}" required>
                                @error('pin_desc.'.$item->box_name.'.'.$item->equips[0]->equip_name.'.0')
                                    <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                @enderror
                            </td>
                            {{-- Input Nbr --}}
                            <td id="center-td">
                                <input type="number" min="1" max="{{strcasecmp($item->box_type, "BF1010") == 0 ? 10 : 12}}" name="input_nbr[{{$item->box_name}}][{{$item->equips[0]->equip_name}}][]" class="form-control" id="input" value="{{$item->equips[0]->pins[0]->input_nbr}}">
                                @error('input_nbr.'.$item->box_name.'.'.$item->equips[0]->equip_name.'.0')
                                    <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                @enderror
                            </td>
                            {{-- Hall Name --}}
                            <td id="center-td">
                                <input type="text" class="form-control border-0" name="hall[{{$item->box_name}}][{{$item->equips[0]->equip_name}}][]" value="{{$item->equips[0]->pins[0]->hall}}" required>
                                @error('hall.'.$item->box_name.'.'.$item->equips[0]->equip_name.'.0')
                                    <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                @enderror
                            </td>

                        </tr>

                        @for ($i = 0; $i < sizeof($item->equips); $i++)
                        
                            @for ($j = ($i == 0 ? 1 : 0); $j < sizeof($item->equips[$i]->pins); $j++)
                                <tr>
                                    @if (sizeof($item->equips[$i]->pins) > 1 && $i == 0)
                                        {{-- Pin Desc --}}
                                        <td id="center-td">
                                            <input type="text" class="form-control border-0" name="pin_desc[{{$item->box_name}}][{{$item->equips[$i]->equip_name}}][]" value="{{$item->equips[$i]->pins[$j]->pin_desc}}" required>
                                            @error('pin_desc.'.$item->box_name.'.'.$item->equips[$i]->equip_name.'.'.$j)
                                                <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                            @enderror
                                        </td>
                                        {{-- Input Nbr --}}
                                        <td id="center-td">
                                            {{-- TODO: SET MAX NUMBER DEPAND ON BOX TYPE --}}
                                            <input type="number" min="1" max="{{strcasecmp($item->box_type, "BF1010") == 0 ? 10 : 12}}" name="input_nbr[{{$item->box_name}}][{{$item->equips[$i]->equip_name}}][]" class="form-control" id="input" value="{{$item->equips[$i]->pins[$j]->input_nbr}}">
                                            @error('input_nbr.'.$item->box_name.'.'.$item->equips[$i]->equip_name.'.'.$j)
                                                <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                            @enderror
                                        </td>
                                        {{-- Hall Name --}}
                                        <td id="center-td">
                                            <input type="text" class="form-control border-0" name="hall[{{$item->box_name}}][{{$item->equips[$i]->equip_name}}][]" value="{{$item->equips[$i]->pins[$j]->hall}}" required>
                                            @error('hall.'.$item->box_name.'.'.$item->equips[$i]->equip_name.'.'.$j)
                                                <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                            @enderror
                                        </td>
                                    @else
                                        @if ($j == 0)
                                            {{-- Equip Name --}}
                                            @php
                                                $equip_index++
                                            @endphp
                                            <td rowspan="{{ sizeof($item->equips[$i]->pins) }}" id="center-td">
                                                <input type="text" class="form-control border-0" name="equip_name[{{$item->box_name}}][]" value="{{ $item->equips[$i]->equip_name }}" required>
                                                @error('equip_name.'.$item->box_name.'.'.$equip_index)
                                                    <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                                @enderror
                                            </td>
                                        @endif
                                        {{-- Pin Desc --}}
                                        <td id="center-td">
                                            <input type="text" class="form-control border-0" name="pin_desc[{{$item->box_name}}][{{$item->equips[$i]->equip_name}}][]" value="{{$item->equips[$i]->pins[$j]->pin_desc}}" required>
                                            @error('pin_desc.'.$item->box_name.'.'.$item->equips[$i]->equip_name.'.'.$j)
                                                <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                            @enderror
                                        </td>
                                        {{-- Input Nbr --}}
                                        <td id="center-td">
                                            {{-- TODO: SET MAX NUMBER DEPAND ON BOX TYPE --}}
                                            <input type="number" min="1" max="{{strcasecmp($item->box_type, "BF1010") == 0 ? 10 : 12}}" name="input_nbr[{{$item->box_name}}][{{$item->equips[$i]->equip_name}}][]" class="form-control" id="input" value="{{$item->equips[$i]->pins[$j]->input_nbr}}">
                                            @error('input_nbr.'.$item->box_name.'.'.$item->equips[$i]->equip_name.'.'.$j)
                                                <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                            @enderror
                                        </td>
                                        {{-- Hall Name --}}
                                        <td id="center-td">
                                            <input type="text" class="form-control border-0" name="hall[{{$item->box_name}}][{{$item->equips[$i]->equip_name}}][]" value="{{$item->equips[$i]->pins[$j]->hall}}" required>
                                            @error('hall.'.$item->box_name.'.'.$item->equips[$i]->equip_name.'.'.$j)
                                                <div class="text-danger text-left" style="font-size: 12px">{{$message}}</div>
                                            @enderror
                                        </td>
                                    @endif
                                </tr>
                            @endfor
                        @endfor
                    @endforeach
                </tbody>
            </table>
        </form>

        <div class="d-flex align-items-center float-right">
            <button type="submit" class="btn btn-primary m-2" form="set-environment">Set Environment</button>
            <a href="{{ route('upload-environment') }}" class="btn btn-danger m-2">Cancel</a>
        </div>
    </div>

    <script>

        window.addEventListener('load', function() {
            document.getElementById('config').style.display = 'block';
            document.getElementById('config-btn').classList.toggle("active-btn");
            document.getElementById('c-set-envir').classList.toggle("active-link");
        });
            
    </script>
@endsection