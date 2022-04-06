<style>
    .unity{
        background: rgb(211, 210, 210);
        color: black;
        display: flex;
        justify-content: center;
        align-items: center;
        padding:0 10px;
        border-radius: 0 10px 10px 0;
    }
    
    .p-unity
    {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
</style>

<div class="container my-3 w-50">

    <form action="{{ route('save-host-edits', $host[0]->host_id) }}" method="get">

        <div class="card shadow-sm">

            <div class="card-header">Define Host</div>

            <div class="card-body">
                {{-- Host Name --}}
                <label for="host_name"><b>Host Name {{--<span class="text-danger">*</span>--}}</b></label>
                <input type="text" name="hostName" class="form-control @error('hostName') border-danger @enderror" id="host_name" value="{{ $host[0]->display_name }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,20}" title="Host name must be between 4 & 20 charcarters in length and containes only letters, numbers, and these symbols (-_+)">
                @error('hostName')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror

                <br>

                {{-- IP Address --}}
                <label for="ip"><b>IP Address {{--<span class="text-danger">*</span>--}}</b></label>
                <input type="text" name="addressIP" class="form-control @error('addressIP') border-danger @enderror" id="ip" minlength="7" maxlength="15" size="15" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$" value="{{ $host[0]->address }}" title="Please enter the IP address correctly e.g. 192.168.1.1">
                @error('addressIP')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror

                <br>

                {{-- Community --}}
                {{-- @if ($type == 'switch' || $type == 'router' || $type == 'printer')
                    <label for="community"><b>Community String <span class="text-danger">*</span></b></label>
                    <input type="text" name="community" class="form-control p-unity @error('community') border-danger @enderror" id="community" value="public">
                    @error('community')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                @endif --}}

                <br>

                {{-- Parent host --}}
                <div>
                    <label for=""><b>Parent :</b></label>
                    <div class="sizing" style="max-height:100px; overflow: auto">
                        
                        @forelse ($all_hosts as $item)

                            @if (sizeof($parent_hosts) != 0)
                                @if ($item->host_object_id == $parent_hosts[0]->parent_host_object_id)
                                    <input type="radio" name="hosts" value="{{ $item->host_name }}" checked> {{ $item->host_name }}
                                    <br>
                                @endif
                            @else
                                <input type="radio" name="hosts" value="{{ $item->host_name }}"> {{ $item->host_name }}
                                <br>
                            @endif                            
                            
                        @empty
                            <p>No hosts found</p>
                        @endforelse
                    
                    </div>
                </div>
            </div>

        </div>
        
        <br>

        <div class="card shadow-sm">

            <div class="card-header">Paramétrage de supervision</div>
            
            <div class="card-body">
                {{-- Check Interval --}}
                <label for="CheckInterval"><b>Check Interval <!--<span class="text-danger">*</span>--></b></label>
                <div class="d-flex">
                    <input  type="number" min="1" max="100" name="check_interval" class="form-control p-unity @error('check_interval') border-danger @enderror" id="CheckInterval" value="{{ $host[0]->check_interval }}">
                    <span class="unity">min</span>
                </div>
                @error('check_interval')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror

                <br>

                {{-- Retry Interval --}}
                <label for="retryInterval"><b>Retry Interval <!--<span class="text-danger">*</span>--></b></label>
                <div class="d-flex">
                    <input  type="number" min="1" max="100" name="retry_interval" class="form-control p-unity @error('retry_interval') border-danger @enderror" id="retryInterval" value="{{ $host[0]->retry_interval }}">
                    <span class="unity">min</span>
                </div>
                @error('retry_interval')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror

                <br>

                {{-- Max Check --}}
                <label for="maxInterval"><b>Max Check <!--<span class="text-danger">*</span>--></b></label>
                <div class="d-flex">
                    <input  type="number" min="1" max="100" name="max_attempts" class="form-control p-unity @error('max_attempts') border-danger @enderror" id="maxInterval" value="{{ $host[0]->max_check_attempts }}">
                    <span class="unity">attempts</span>
                </div>
                @error('max_attempts')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror

                <br>

                {{-- Notification Interval --}}
                <label for="notifInterval"><b>Notification Interval <!--<span class="text-danger">*</span>--></b></label>
                <div class="d-flex">
                    <input  type="number" min="1" max="1000" name="notif_interval" class="form-control p-unity @error('notif_interval') border-danger @enderror" id="notifInterval" value="{{ $host[0]->notification_interval }}">
                    <span class="unity">min</span>
                </div>
                @error('notif_interval')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror

                <br>

                {{-- Active Check --}}
                <label for="check"><b>Check this host</b></label>
                <select name="check" id="check">

                    @if ($host[0]->active_checks_enabled)
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    @else
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    @endif
                    
                </select>

                <br>

                {{-- Active Notifications --}}
                <label for="activeNotif"><b>Active Notification</b></label>
                <select name="active_notif" id="activeNotif">

                    @if ($host[0]->notifications_enabled)
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    @else
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    @endif
                    
                </select>
            </div>

        </div>
        
        <br>

        <button type="submit" class="btn btn-primary">Save</button>
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