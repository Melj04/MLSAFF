<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('IoT Controller') }}
        </h2>
    </x-slot>
    <div class="py-4 mx-2">
        <div class="max-w-sm mx-auto sm:px-6 lg:px-8">
            <div class="bg-cyan-800 rounded dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg flex items-center justify-center">
                <div class="w-96 flex flex-col items-center my-20 mx-3">
                    <h1 class="font-semibold text-xl text-neutral-50  leading-tight mb-1">
                        Manual Feeding
                    </h1>
                    <p id="helper-text-explanation" class="mt-2 text-sm text-neutral-100 mb-3"><i>gram/s</i></p>
                    @livewire('weight')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
