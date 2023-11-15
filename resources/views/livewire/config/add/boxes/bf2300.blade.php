<div class="container w-50 my-auto pt-2 pb-4">
    
    <form action="{{ route('create-box-BF2300') }}" method="get">
        
            <div class="card my-2 rounded bg-white shadow-sm">
                
                <div class="card-header">
                    Define Box :
                </div>

                <div class="card-body">
                    
                    {{-- Box Name --}}
                    <label for="box_name"><b>Box name <span class="text-danger">*</span></b> </label>
                    <input type="text" name="boxName" class="form-control @error('boxName') border-danger @enderror" id="box_name" value="{{ old('boxName') }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,200}" title="Box name must be between 2 & 200 charcarters in length and containes only letters, numbers, and these symbols -_+">
                    @error('boxName')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
    
                    <br>

                    {{-- IP Address --}}
                    <label for="ip"><b>IP Address <span class="text-danger">*</span></b></label>
                    <input type="text" name="addressIP" class="form-control @error('addressIP') border-danger @enderror" id="ip" minlength="7" maxlength="15" size="15" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$" value="{{ old('addressIP') }}" title="Please enter the IP address correctly e.g. 192.168.1.1">
                    @error('addressIP')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                    <br>

                    {{-- Box Site --}}
                    @if ($site_name == "All")
                    <label for="box-site"><b>Box Site <span class="text-danger">*</span></b></label>
                    @error('site')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="sizing border p-1 rounded @error('site') border-danger @enderror" style="max-height:200px;overflow: auto">
                        
                        @forelse ($sites as $site)
                            <input type="radio" name="site" id="{{$site->id}}" value="{{$site->site_name}}" {{ old('site') == $site->site_name ? 'checked' : '' }}> <label for="{{$site->id}}">{{$site->site_name}}</label>
                            <br>
                        @empty
                            <i>No Site found</i>
                        @endforelse
                        
                    </div>
                    @else
                        <input type="hidden" name="site" value="specific">
                    @endif

                </div>
        
            </div>

            <div class="card my-2">
                <div class="card-header">
                    Parent :
                </div>

                <div class="card-body">

                    <div class="sizing" style="max-height:200px;overflow: auto">
                        
                        @forelse ($hosts as $host)
                            <input type="radio" name="hosts" value="{{$host->display_name}}"> {{$host->display_name}}
                            <br>
                        @empty
                            <i>No Parent found</i>
                        @endforelse
                        
                    </div>

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
const Pin = document.querySelector('.pin1');
const Hall = document.querySelector('.hall1');

addEquip.onclick = () => {

    let test = document.createElement('div');
    test.classList.add('equip'+i,'w-100','d-flex','my-3');
    test.innerHTML = Equip.innerHTML;
    document.querySelector('.defineEquip').appendChild(test);

    i++;
    
    if(i > 10)
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

<script>

    window.addEventListener('load', function() {
        document.getElementById('config').style.display = 'block';
        document.getElementById('config-btn').classList.toggle("active-btn");
        document.getElementById('c-boxes').classList.toggle("active-link");
    });
        
</script>