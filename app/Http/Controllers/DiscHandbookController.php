<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiscHandbookController extends Controller
{
    public function index(Request $request)
    {
        $type = strtoupper($request->query('type', 'DISC'));
        $availableTypes = ['DISC', 'MBTI', '16P'];

        if (!in_array($type, $availableTypes, true)) {
            $type = 'DISC';
        }

        return view('disc.handbook', compact('type', 'availableTypes'));
    }
}
