<?php

namespace App\Http\Controllers;
use App\Models\ChartResponsive;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChartControl extends Controller
{
    public function chart(){
        $data=ChartResponsive::all();//fetch data from db
        $chartData = $data->map(function ($item) {
            return [
                'time' => Carbon::parse($item->created_at)->format('Y-m-d H:i'), // show time labels
                'value' => $item->value // Adjust according to your data structure
            ];
        });

        return view('dashboard', compact('chartData'));
    }

    public function getChartData()
    {
        // Fetch the latest 15 records from the database
        $data = ChartResponsive::orderBy('created_at', 'desc') // Order by time descending
            ->take(15) // Limit to 15 records
            ->get()
            ->map(function ($item) {
                return [
                    'time' => $item->created_at->format('Y-m-d H:i:s'),
                    'value' => $item->value
                ];
            });

        return response()->json($data);
    }
}
