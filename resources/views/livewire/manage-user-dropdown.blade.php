<div>
    <!-- Manage User Section -->
    @if(auth()->user()->role === 1)
    <button wire:click="toggleDropdown" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none">
        {{ __('Management') }}
        <svg class="fill-current h-4 w-4 ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <!-- Options under Manage User -->
    @if($isOpen)
    <div class="mt-2 ml-4">
        <x-dropdown-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
            {{ __('Manage User') }}
            </x-dropdown-link>
        <x-dropdown-link :href="route('rfid.unbound')" :active="request()->routeIs('rfid.unbound')">
            {{ __('Unbound RFID') }}
        </x-dropdown-link>
    </div>
    @endif
    @endif
</div>
