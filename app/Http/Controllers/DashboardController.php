<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user()->load('role', 'unitKerja');
        
        $data = [
            'user' => $user,
        ];

        // Add role-specific data here later
        
        return Inertia::render('Dashboard/Index', $data);
    }
}
