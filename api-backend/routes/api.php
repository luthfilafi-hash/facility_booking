<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/facilities', function () {
    // Fetch all facilities using Laravel's Query Builder
    $facilities = DB::table('facilities')->where('status', 'available')->get();
    
    // Convert paths to absolute URLs (since Laravel API might run from /api-backend/public)
    foreach ($facilities as $f) {
        if ($f->image_path) {
            $f->image_url = url('/../' . $f->image_path);
        }
    }
    
    return response()->json([
        'success' => true,
        'count' => count($facilities),
        'data' => $facilities
    ]);
});
