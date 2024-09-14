<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\command;

class EspDataController extends Controller
{
    public function getCommandData(Request $request)
    {
        // Retrieve sensor details from the database
        $mh = Command::find(1);
        $mm = Command::find(2);
        $nh = Command::find(3);
        $nm = Command::find(4);
        $ms = Command::find(5);
        $ns = Command::find(6);
        $as = Command::find(7);
        $aw = Command::find(8);
        $mw = Command::find(9);
        $nw = Command::find(10);
        $lock = Command::find(11);

        $data = [
            'mh' => $mh->command, // Command data
            'mhtag' => $mh->tag, // Tag data
            'mm' => $mm->command,
            'mmtag' => $mm->tag,
            'nh' => $nh->command,
            'nhtag' => $nh->tag,
            'nm' => $nm->command,
            'nmtag' => $nm->tag,
            'ms' => $ms->command,
            'mstag' => $ms->tag,
            'ns' => $ns->command,
            'nstag' => $ns->tag,
            'as' => $as->command,
            'astag' => $as->tag,
            'aw' => $aw->command,
            'awtag' => $aw->tag,
            'mw' => $mw->command,
            'mwtag' => $mw->tag,
            'nw' => $nw->command,
            'nwtag' => $nw->tag,
            'lock' => $lock->command,
            'locktag' => $lock->tag
        ];

        return response()->json($data);


    }
}
