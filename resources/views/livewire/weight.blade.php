<div class="relative flex flex-col items-center ">
<div class="relative flex items-center bg-white rounded-xl">

    <button class="btn btn-danger w-10" wire:click="down">-</button>
        <input type="number" id="weight" value="{{$numWeight}}" disabled class=" bg-white text-black w-16 h-9 text-center border-none
        [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
    <button class="btn btn-success w-10" wire:click="up">+</button>

</div>

@if (!$shouldHideButton)
<button wire:click="saveWeight" class="w-40 h-8 mt-4 bg-blue-500 hover:bg-blue-700 text-white rounded">
    {{ __('Feed Now') }}
</button>
@endif


<p id="helper-text-explanation" class=" text-sm text-neutral-100 mt-1"><i>{{$message}}</i></p>
</div>
