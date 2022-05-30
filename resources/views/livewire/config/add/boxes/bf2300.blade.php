<div class="container w-50 my-auto">
    
    <form action="{{ route('create-box-BF2300') }}" method="get">
        
            <div class="card my-2 rounded bg-white shadow-sm">
                
                <div class="card-header">
                    Define Box :
                </div>

                <div class="card-body">
                    
                    <label for="box_name"><b>Box name <span class="text-danger">*</span></b> </label>
                    <input type="text" name="boxName" class="form-control @error('boxName') border-danger @enderror" id="box_name" value="{{ old('boxName') }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,20}" title="Box name must be between 2 & 20 charcarters in length and containes only letters, numbers, and these symbols -_+">
                    @error('boxName')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
    
                    <br>
                    <label for="ip"><b>IP Address <span class="text-danger">*</span></b></label>
                    <input type="text" name="addressIP" class="form-control @error('addressIP') border-danger @enderror" id="ip" minlength="7" maxlength="15" size="15" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$" value="{{ old('addressIP') }}" title="Please enter the IP address correctly e.g. 192.168.1.1">
                    @error('addressIP')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                </div>
        
            </div>

            <div class="card my-2">
                <div class="card-header">
                    Parent :
                </div>

                <div class="card-body">

                    <div class="sizing" style="max-height:200px;overflow: auto">
                        
                        @foreach ($hosts as $host)
                            <input type="radio" name="hosts" value="{{$host->display_name}}"> {{$host->display_name}}
                            <br>
                        @endforeach
                        
                    </div>

                </div>
            </div>
        

        <div class="card rounded bg-white shadow-sm">
            <div class="card-header">Define Equipements :</div>
            <div class="card-body  defineEquip">
                <div class="equip1 d-flex w-100 my-3">
                    <div class="w-50 mx-1">
                        <label for="equip_name"><b>Equipement name <span class="text-danger">*</span></b></label>
                        <input type="text" name="equipName[]" class="eqName1 form-control @error('equipName.*') border-danger @enderror" id="equip_name" value="{{ old('equipName.*')}}"  pattern="[a-zA-Z][a-zA-Z0-9-_+\s]{4,20}" title="Equip. name must be between 4 & 20 charcarters in length and containes only letters, numbers, and periodes (-_+)">
                        @error('equipName.*')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="w-50 mx-1">
                        <label for="input"><b>Input Number <!--<span class="text-danger">*</span>--></b></label>
                        <input  type="number" min="1" max="10" name="inputNbr[]" class="iNbr1 form-control @error('inputNbr.*') border-danger @enderror" id="input" value="1">
                        @error('inputNbr.*')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="float-right">
                <span class="btn text-primary bg-white float-right add" title="Add another equipement for monitoring"><i class="fas fa-plus"></i></span>
                <span class="btn text-primary bg-white float-right shadow-sm" id="rmv" title="Remove last equipement" style="display: none"><i class="fas fa-minus"></i></span>            

            </div>

        </div>
        <br>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>

</div>

<script>

(function($) {
  $.fn.uncheckableRadio = function() {
    var $root = this;
    $root.each(function() {
      var $radio = $(this);
      if ($radio.prop('checked')) {
        $radio.data('checked', true);
      } else {
        $radio.data('checked', false);
      }
        
      $radio.click(function() {
        var $this = $(this);
        if ($this.data('checked')) {
          $this.prop('checked', false);
          $this.data('checked', false);
          $this.trigger('change');
        } else {
          $this.data('checked', true);
          $this.closest('form').find('[name="' + $this.prop('name') + '"]').not($this).data('checked', false);
        }
      });
    });
    return $root;
  };
}(jQuery));

$('[type=radio]').uncheckableRadio();

// Add Equip
const rmv = document.getElementById('rmv');

let i = 2;
const addEquip = document.querySelector('.add');
const Equip = document.querySelector('.equip1');

addEquip.onclick = () => {

    let test = document.createElement('div');
    test.classList.add('equip'+i,'w-100','d-flex','my-3');
    test.innerHTML = Equip.innerHTML;
    document.querySelector('.defineEquip').appendChild(test);

    i++;
    
    if(i > 12)
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