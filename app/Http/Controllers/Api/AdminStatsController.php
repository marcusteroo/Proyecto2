<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tablero;
use App\Models\Kanban;
use App\Models\Rating;
use Illuminate\Http\Request;

class AdminStatsController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'boards' => Tablero::count(),
            'tasks' => Kanban::count(),
            'ratings' => Rating::count()
        ];
        
        return response()->json($stats);
    }
}