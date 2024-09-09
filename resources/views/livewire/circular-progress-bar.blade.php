<!-- resources/views/livewire/circular-progress-bar.blade.php -->

<div class="flex items-center justify-center">
    <div class="relative flex items-center justify-center w-36 h-32">
        <svg class="absolute w-full h-full transform ">
            <circle class="text-gray-300" stroke-width="8" stroke="red" fill="transparent" r="50" cx="64" cy="64" />
            <circle class="text-blue-500" stroke-width="8" stroke-dasharray="301.6" :stroke-dashoffset="301.6 - (301.6 * {{ $percentage }} / 100)" stroke-linecap="round"
                stroke="currentColor" fill="transparent" r="50" cx="64" cy="64" transform="rotate(-90 64 64)"/>
        </svg>
        <div class="absolute text-2xl text-center text-gray-800">{{ $kg }}<span class="text-sm">kg</span></div>
    </div>
</div>
