<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Command;

class EspDataController extends Controller
{
    public function getCommandData(Request $request)
    {
        // Retrieve sensor details from the database
        $hour = Command::find(1);
        $minute = Command::find(2);
        $data = [
            'h' => $hour->command, // Command data
            'htag' => $hour->tag, // Tag data
            'm' => $minute->command,
            'mtag' => $minute->tag
        ];

        return response()->json($data);


    }
}
