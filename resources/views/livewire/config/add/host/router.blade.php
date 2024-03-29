<div class="container w-50 mx-auto">
    
    <form action="{{ route('create-router-host') }}" method="get">
        
        <div class="card my-3 rounded bg-white shadow-sm">

            <h5 class="card-header">Define Host :</h5>

            <div class="card-body">
            
                <label for="host_name"><b>Host Name <span class="text-danger font-weight-bolder">*</span></b></label>
                <input type="text" name="hostName" class="form-control @error('hostName') border-danger @enderror" id="host_name" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,200}" title="Host name must be between 2 & 200 charcarters in length and containes only letters, numbers, and these symbols -_+">
                @error('hostName')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror

                <br>

                <label for="ip"><b>IP Address <span class="text-danger font-weight-bolder">*</span></b></label>
                <input type="text" name="addressIP" class="form-control @error('addressIP') border-danger @enderror" id="ip" minlength="7" maxlength="15" size="15" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$" title="Please enter the IP address correctly e.g. 192.168.1.1">
                @error('addressIP')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror

                <br>

                {{-- Community String --}}
                <label for="community"><b>Community String <span class="text-danger font-weight-bolder">*</span></b></label>
                <input type="text" name="community" class="form-control @error('community') border-danger @enderror" id="community" value="public">
                @error('community')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            
                                
                <br>

                {{-- Number of ports --}}
                <label for="pNbr"><b>Ports Number <span class="text-danger font-weight-bolder">*</span></b></label>
                <input  type="number" min="1" max="50" name="portsNbr" class="iNbr1 form-control w-75 @error('portsNbr') border-danger @enderror" id="pNbr" value="1">
                @error('portsNbr')
                    <div class="text-danger">
                    {{ $message }}
                    </div>
                @enderror

                <br>

                {{-- Choose Site --}}
                <div>
                  <label for="site_name"><b>Choose Site <span class="text-danger">*</span></b> </label>
                  @error('site')
                  <div class="text-danger">
                      {{ $message }}
                  </div>
                  @enderror
                  <div class="p-2 rounded @error('site') border-danger @enderror" style="max-height: 120px;overflow: auto;border: 1px solid #ced4da;">
                      @forelse ($sites as $site)
                          <input type="radio" name="site" id="{{$site->id}}" value="{{$site->site_name}}"> <label for="{{$site->id}}">{{ $site->site_name }}</label>
                          <br>
                      @empty
                          <p>No site found</p>
                      @endforelse
                  </div>
                </div>

        </div>
          
            <div class="card rounded bg-white shadow-sm">

                <h5 class="card-header">Parent :</h5>
                
                <div class="card-body">
                    
                    <div class="sizing" style="max-height:150px;overflow: auto">
                        
                        @foreach ($hosts as $host)
                            <input type="radio" name="hosts" value="{{$host->display_name}}"> {{$host->display_name}}
                            <br>
                        @endforeach
                        
                    </div>

                </div>
            </div>

          <br>

        <button type="submit" class="btn btn-primary mx-auto">Create</button>
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

</script>
