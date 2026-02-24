<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controllers\Middleware;

class DashboardController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Dashboard Index', only: ['index']),
        ];
    }

    public function index()
    {
        $userCount = User::count();

        return response()->json([
            'success' => true,
            'user_count' => $userCount,

        ]);
    }
}
