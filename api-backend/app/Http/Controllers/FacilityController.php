<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacilityController extends Controller
{
    public function index()
    {
        // Query the existing native app's facilities table
        $facilities = DB::table('facilities')
            ->where('status', 'available')
            ->orderBy('name')
            ->get();
            
        // Render the view and pass the data
        return view('facilities', compact('facilities'));
    }
}
