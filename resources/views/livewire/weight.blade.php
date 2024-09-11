<div class="relative flex flex-col items-center">
    <div class="relative flex items-center bg-white rounded-xl">
        <button class="btn btn-danger w-10" wire:click="down">-</button>
        <input type="number" id="weight" value="{{$numWeight}}" disabled class="bg-white text-black w-16 h-9 text-center border-none
        [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
        <button class="btn btn-success w-10" wire:click="up">+</button>
    </div>

    @if (!$shouldHideButton)
        <button wire:click="showModal" class="w-40 h-8 mt-4 bg-blue-500 hover:bg-blue-700 text-white rounded">
            {{ __('Feed Now') }}
        </button>
    @endif

    <p id="helper-text-explanation" class="text-sm text-neutral-100 mt-1"><i>{{$message}}</i></p>
<!-- Bootstrap Modal -->
<div class="modal fade @if($modalVisible) show @endif" style="display: @if($modalVisible) block @else none @endif;" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="$set('modalVisible', false)">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to perform this action?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="$set('modalVisible', false)">Cancel</button>
                <button type="button" class="btn btn-danger" wire:click="saveWeight">Confirm</button>
            </div>
        </div>
    </div>
</div>
</div>
