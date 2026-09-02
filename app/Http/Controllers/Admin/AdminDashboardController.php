<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Mapel;
use App\Models\Progres;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::where('role', 'user')->count(),
            'subscribed' => User::where('role', 'user')->where('subscribed_until', '>', now())->count(),
            'mapels' => Mapel::count(),
            'games' => Game::count(),
            'progres' => Progres::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
