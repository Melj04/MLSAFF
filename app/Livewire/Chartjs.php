<?php

namespace App\Livewire;
use App\Models\ChartResponsive;
use App\Charts\myChart;
use Livewire\Component;

class Chartjs extends Component
{
    public $chartData = [];
    public $labels = [];

    public function mount()
    {
        $sales = ChartResponsive::selectRaw('DATE(created_at) as date, SUM(value) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $this->labels = $sales->pluck('date')->toArray();
        $this->chartData = $sales->pluck('total')->toArray();
    }
    public function render()
    {
        return view('livewire.chartjs');
    }
}
