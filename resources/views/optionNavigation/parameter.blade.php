<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('IoT Parameter') }}
        </h2>
    </x-slot>
    <div class="flex justify-center mt-2">
        <div class="flex sm:flex-row flex-col justify-center items-center  max-w-2xl  sm:px-6 lg:px-8 mt-2 rounded-md">
            {{-- AM --}}
            <div class="py-4 sm:py-12 ">
                <div class="max-w-sm mx-2 sm:px-6 lg:px-8">
                    <div class="bg-cyan-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex flex-col items-center">
                                 @livewire('param-weight')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- PM --}}
            <div class="sm:py-12">
                <div class="max-w-sm mx-2 sm:px-6 lg:px-8">
                    <div class="bg-cyan-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6 text-gray-100">
                            <div class="flex flex-col items-center">
                                @livewire('param-weight2')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
