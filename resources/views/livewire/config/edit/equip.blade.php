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

    .selector{
        margin-left:25px;
        width: 75px;
        padding:5px 10px;
        border-radius: 5px;
        border: 1px solid gray;
    }

</style>

<div class="container my-3 w-50">

    <form action="{{ route('save-equip-edits', $equip->service_object_id) }}" method="get">

        <div class="card">

            <div class="card-header">Define Pin</div>

            <div class="card-body">
                {{-- pin Name --}}
                <label for="pin_name"><b>Pin name {{--<span class="text-danger">*</span>--}}</b></label>
                <input type="text" name="pinName" class="form-control @error('pinName') border-danger @enderror" id="pin_name" value="{{ $equip->display_name }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,200}" title="Service name must be between 2 & 200 charcarters in length and containes only letters, numbers, and these symbols -_+">
                @error('pinName')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror

                {{-- Community --}}
                {{-- @if ($type == 'switch' || $type == 'router' || $type == 'printer')
                    <label for="community"><b>Community String <span class="text-danger">*</span></b></label>
                    <input type="text" name="community" class="form-control @error('community') border-danger @enderror" id="community" value="public">
                    @error('community')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                @endif --}}

            </div>

        </div>
        
        <br>

        <div class="card">

            <div class="card-header">Paramétrage de supervision</div>
            
            <div class="card-body">

                {{-- Active Notifications --}}
                <label for="workingState"><b>Working State :</b></label>
                <select name="working_state" id="workingState" class="selector">

                    @if ($equip->working_state == "H")
                        <option value="H">H</option>
                        <option value="L">L</option>
                    @else
                        <option value="L">L</option>
                        <option value="H">H</option>
                    @endif
                    
                </select>

                <br><br>

                {{-- Check Interval --}}
                <label for="CheckInterval"><b>Check Interval <!--<span class="text-danger">*</span>--></b></label>
                <div class="d-flex">
                    <input  type="number" min="5" max="100" name="check_interval" class="form-control p-unity @error('check_interval') border-danger @enderror" id="CheckInterval" value="{{ $equip->check_interval }}">
                    <span class="unity">sec</span>
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
                    <input  type="number" min="5" max="100" name="retry_interval" class="form-control p-unity @error('retry_interval') border-danger @enderror" id="retryInterval" value="{{ $equip->retry_interval }}">
                    <span class="unity">sec</span>
                </div>
                @error('retry_interval')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror

                <br>

                {{-- Max Check --}}
                <label for="maxInterval"><b>Max Check Attempts<!--<span class="text-danger">*</span>--></b></label>
                <div class="d-flex">
                    <input  type="number" min="1" max="100" name="max_attempts" class="form-control p-unity @error('max_attempts') border-danger @enderror" id="maxInterval" value="{{ $equip->max_check_attempts }}">
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
                    <input  type="number" min="1" max="1000" name="notif_interval" class="form-control p-unity @error('notif_interval') border-danger @enderror" id="notifInterval" value="{{ $equip->notification_interval }}">
                    <span class="unity">min</span>
                </div>
                @error('notif_interval')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror

                <br>

                {{-- Active Check --}}
                <label for="check"><b>Check this equipement</b></label>
                <select name="check" id="check" class="selector">

                    @if ($equip->active_checks_enabled)
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
                <select name="active_notif" id="activeNotif" class="selector">

                    @if ($equip->notifications_enabled)
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

window.addEventListener('load', function() {
    document.getElementById('config').style.display = 'block';
    document.getElementById('config-btn').classList.toggle("active-btn");
    document.getElementById('c-equips').classList.toggle("active-link");
});
    
</script>