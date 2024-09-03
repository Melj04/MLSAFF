<?php

namespace App\Livewire;

use Livewire\Component;

class ManageUserDropdown extends Component
{
    public $isOpen = false;

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function render()
    {
        return view('livewire.manage-user-dropdown');
    }
}
