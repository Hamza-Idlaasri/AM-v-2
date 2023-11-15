<nav class="navbar navbar-light d-flex justify-content-center form-inline w-100">
    
    {{-- Status --}}
    <label class="font-weight-bold ml-4 mr-2" for="status">Status :</label>
    <select wire:model="status" name="status" id="status" class="form-control">
        <option value="all">All</option>
        <option value="0">Up</option>
        <option value="1">Down</option>
        <option value="2">Unreachable</option>
    </select>

    {{-- Box Name --}}
    <label class="font-weight-bold ml-4 mr-2" for="box_name">Box Name :</label>
    <select wire:model="box_name" name="box_name" id="box_name" class="form-control">
        <option value="all">All</option>
        <option value="0">Up</option>
        <option value="1">Down</option>
        <option value="2">Unreachable</option>
    </select>
</nav>