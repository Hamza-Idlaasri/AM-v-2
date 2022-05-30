<div class="container">

    <h4 class="text-center m-3"><b>Box Name : </b>{{ $box->box_name }}</h4>

    @if (sizeof($inputs_not_used) > 0)
        <form action="{{ route('create-equip', $box->box_id) }}" method="get">

        <div class="card rounded bg-white m-3 shadow-sm">
            <div class="card-header">Define Equipements :</div>
            <div class="container p-3 defineEquip">
                <div class="equip1 d-flex w-100 my-3">
                    <div class="w-50">
                        <label for="equip_name"><b>Equipement name <span class="text-danger">*</span></b></label>
                        <input type="text" name="equipName[]" class="eqName1 form-control w-75 @error('equipName.*') border-danger @enderror" id="equip_name" value="{{ old('equipName.*')}}"  pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,20}" title="Equip. name must be between 2 & 20 charcarters in length and containes only letters, numbers, and the symbols -_+">
                        @error('equipName.*')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="w-50">
                        <label for="input"><b>Input Number <!--<span class="text-danger">*</span>--></b></label>
                        <input  type="number" min="1" max="10" name="inputNbr[]" class="iNbr1 form-control w-75 @error('inputNbr.*') border-danger @enderror" id="input" value="1">
                        @error('inputNbr.*')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="float-right">
                @if (sizeof($inputs_not_used) > 1)
                <span class="btn text-primary bg-white float-right shadow-sm add" title="Add another equipement for monitoring"><i class="fas fa-plus"></i></span>
                @endif

                <span class="btn text-primary bg-white float-right shadow-sm" id="rmv" title="Remove last equipement" style="display: none"><i class="fas fa-minus"></i></span>            
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
                    <th>Equipement Name</th>
                    <th>Connected to the Input</th>
                </tr>

                @foreach ($inputs_used as $equip)
                    <tr>
                    <td>{{ $equip->equip_name }}</td>
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
                @for ($i=0 ;$i < sizeof($inputs_not_used); $i++)
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
  
{{-- this script for adding new equipement --}}
<script>

const rmv = document.getElementById('rmv');

let i = 2;
const addEquip = document.querySelector('.add');
const Equip = document.querySelector('.equip1');

let inputs_not_used = @json($inputs_not_used);

addEquip.onclick = () => {

    let test = document.createElement('div');
    test.classList.add('equip'+i,'w-100','d-flex','my-3');
    test.innerHTML = Equip.innerHTML;
    document.querySelector('.defineEquip').appendChild(test);

    i++;
    
    if(i > inputs_not_used.length)
        addEquip.style.display = 'none';
    
    if (i > 2) {
    rmv.style.display = 'block';
    }
}


// Remove equip

rmv.onclick = () => {

if (i > 2) {
    document.querySelector('.defineEquip').lastElementChild.remove();
    i--;
}

if (i >= 2) {
    addEquip.style.display = 'block';
}

if (i == 2) {
    rmv.style.display = 'none';
}

}
  
  
</script>
