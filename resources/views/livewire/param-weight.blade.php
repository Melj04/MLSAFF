<div class="relative flex flex-col items-center">
    <h1 class="font-semibold text-xl text-neutral-50 leading-tight mb-4">
        Morning Feeding
    </h1>
    <p class="text-md text-neutral-100 mb-1"><i>Time</i></p>
    <div class="flex flex-row mb-3">
        <input type="number" wire:model="hour" min="6" max="12" class="w-28 text-center text-sky-900 rounded-md [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
        <span class="mx-2 mt-2 text-white">:</span>
        <input type="number" wire:model="minute" min="0" max="59" class="w-28 text-center text-sky-900 rounded-md [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
    </div>
    <p id="helper-text-explanation" class="mb-2 text-sm text-neutral-100"><i>Feeding amount (grams)</i></p>

    <div class="relative flex items-center bg-white rounded-xl">
        <button class="btn btn-danger w-10" wire:click="down">-</button>
        <input type="number" value="{{$numWeight}}" disabled class=" border-none text-sky-900 w-16 h-9 text-center [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
        <button class="btn btn-success w-10" wire:click="up">+</button>
    </div>
    <button wire:click="showModal" class="w-40 h-8 mt-4 bg-blue-500 hover:bg-blue-700 text-white rounded">
        {{ __('Update') }}
    </button>
    <p id="helper-text-explanation" class=" text-sm text-white mt-2"><i>{{ $message }}</i></p>

    <!-- Bootstrap Modal -->
<div class="modal fade @if($modalVisible) show @endif" style="display: @if($modalVisible) block @else none @endif;" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title " id="confirmModalLabel">Confirm Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="$set('modalVisible', false)">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to perform this action?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="$set('modalVisible', false)">Cancel</button>
                <button type="button" class="btn btn-danger" wire:click="saveWeightAfternoon">Confirm</button>
            </div>
        </div>
    </div>
</div>
</div>

