<div class="container">

    <h4 class="text-center m-3"><b>Box Name : </b>{{ $box->box_name }}</h4>

    @if (sizeof($inputs_not_used) > 0)
        <form action="{{ route('create-pin', $box->box_id) }}" method="get">

            <div class="card my-2 rounded bg-white shadow-sm w-50 mx-auto">

                <div class="card-header">
                    Define Equipement:
                </div>
                {{-- Equipements --}}
                <div class="card-body">
                    <label for="equip_name"><b>Choose Equipment <span class="text-danger">*</span></b> </label>
                    @error('equipName')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="sizing border p-1 rounded @error('equipName') border-danger @enderror" style="max-height:200px;overflow: auto">

                        @forelse ($equips as $equip)
                            <input type="radio" name="equipName" id="{{ $equip->id }}" value="{{ $equip->equip_name }}" {{ old('equipName') == $equip->equip_name ? 'checked' : '' }}> <label for="{{ $equip->id }}">{{ $equip->equip_name }}</label>
                            <br>
                        @empty
                            <i>No Equipment found</i>
                        @endforelse

                    </div>
                </div>

            </div>

            <div class="card rounded bg-white m-3 shadow-sm">
                <div class="card-header">Define Pins :</div>
                <div class="container p-3 definePin">
                    <div class="pin1 d-flex w-100 my-3">
                        <div class="w-25 mx-1">
                            <label for="pin_name"><b>Pin name <span class="text-danger">*</span></b></label>
                            <input type="text" name="pinName[]"
                                class="pinName1 form-control @error('pinName.*') border-danger @enderror" id="pin_name"
                                value="{{ old('pinName.*') }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,200}"
                                title="Pin name must be between 2 & 200 charcarters in length and containes only letters, numbers, and the symbols -_+">
                            @error('pinName.*')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="w-25 mx-1">
                            <label for="hall_name"><b>Hall name <span class="text-danger">*</span></b></label>
                            <input type="text" name="hallName[]"
                                class="hallName1 form-control @error('hallName.*') border-danger @enderror"
                                id="hall_name" value="{{ old('hallName.*') }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,200}"
                                title="Hall name must be between 2 & 200 charcarters in length and containes only letters, numbers, and the symbols -_+">
                            @error('hallName.*')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="w-25 mx-1">
                            <label for="input"><b>Input Number <span class="text-danger">*</span></b></label>
                            <input type="number" min="1" max="10" name="inputNbr[]"
                                class="iNbr1 form-control @error('inputNbr.*') border-danger @enderror" id="input"
                                value="1">
                            @error('inputNbr.*')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="w-25 ml-3">
                            <label for="working_state"><b>Working State <span class="text-danger">*</span></b></label>
                            @error('workingState')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                            <br>
                            <div class="w-50 form-check d-flex justify-content-around">
                                <div>
                                    <input type="radio" name="workingState" class="workingState1 form-check-input"
                                        id="working_state_H" value="H" checked>
                                    <label for="working_state_H" class="form-check-label"> H</label>
                                </div>
                                <div>
                                    <input type="radio" name="workingState" class="workingState1 form-check-input"
                                        id="working_state_L" value="L">
                                    <label for="working_state_L" class="form-check-label"> L</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="float-right">
                    @if (sizeof($inputs_not_used) > 1)
                        <span class="btn text-primary bg-white float-right shadow-sm add"
                            title="Add another pin for monitoring"><i class="fas fa-plus"></i></span>
                    @endif

                    <span class="btn text-danger bg-white float-right shadow-sm" id="rmv" title="Remove last pin"
                        style="display: none"><i class="fas fa-minus"></i></span>
                </div>

            </div>

            <button type="submit" class="btn btn-primary mx-3">Add</button>
        </form>
    @else
        <p class="alert alert-danger w-50 m-auto text-center">All inputs connected, You can't add more equipments!</p>
    @endif

    <div class="container p-3 text-center">

        <div class="d-flex justify-content-around">

            <div class="container float-left rounded mr-1 w-50 bg-white shadow-sm">
                <h6 class="text-muted m-2"><i>Inputs already used</i></h6>
                <table class="table table-bordered table-hover">
                    <tr class="bg-light">
                        <th>Pin Name</th>
                        <th>Connected to the Input</th>
                    </tr>

                    @foreach ($inputs_used as $equip)
                        <tr>
                            <td>{{ $equip->pin_name }}</td>
                            <td>{{ $equip->input_nbr }}</td>
                        </tr>
                    @endforeach

                </table>
            </div>

            <div class="container float-right rounded ml-1 w-50 bg-white shadow-sm">
                <h6 class="text-muted m-2"><i>Inputs not used</i></h6>
                <table class="table table-bordered table-hover">
                    <tr class="bg-light">
                        <th>Input Number</th>
                    </tr>

                    @if ($inputs_not_used)
                        @for ($i = 0; $i < sizeof($inputs_not_used); $i++)
                            <tr>
                                <td>{{ $inputs_not_used[$i] }}</td>
                            </tr>
                        @endfor
                    @else
                        <tr>
                            <td class="text-danger"><b>All inputs for this box are used</b></td>
                        </tr>
                    @endif


                </table>

            </div>

        </div>

    </div>

</div>

{{-- this script for adding new pin --}}
<script>
    const rmv = document.getElementById('rmv');

    let i = 2;
    const addPin = document.querySelector('.add');
    const Pin = document.querySelector('.pin1');

    let inputs_not_used = @json($inputs_not_used);

    addPin.onclick = () => {

        let test = document.createElement('div');
        test.classList.add('pin' + i, 'w-100', 'd-flex', 'my-3');
        test.innerHTML = Pin.innerHTML;
        document.querySelector('.definePin').appendChild(test);

        i++;

        if (i > inputs_not_used.length)
            addPin.style.display = 'none';

        if (i > 2) {
            rmv.style.display = 'block';
        }
    }


    // Remove equip

    rmv.onclick = () => {

        if (i > 2) {
            document.querySelector('.definePin').lastElementChild.remove();
            i--;
        }

        if (i >= 2) {
            addPin.style.display = 'block';
        }

        if (i == 2) {
            rmv.style.display = 'none';
        }

    }
</script>


<script>
    window.addEventListener('load', function() {
        document.getElementById('config').style.display = 'block';
        document.getElementById('config-btn').classList.toggle("active-btn");
        document.getElementById('c-pins').classList.toggle("active-link");
    });
</script>
