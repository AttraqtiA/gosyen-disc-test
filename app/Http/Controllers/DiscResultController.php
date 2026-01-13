<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiscTest;

class DiscResultController extends Controller
{
    public function show(DiscTest $test)
    {
        // scoring logic will go here next
        return view('disc.result', compact('test'));
    }
}

