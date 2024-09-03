<?php

namespace App\Livewire;

use Livewire\Component;

class CircularProgressBar extends Component
{
    public $percentage = 70;

    public function render()
    {
        return view('livewire.circular-progress-bar');
    }
}
