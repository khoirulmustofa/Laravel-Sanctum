<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SemesterController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Semester View', only: ['index']),
        ];
    }
    public function index()
    {
        $semesters = Semester::orderBy('semester', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $semesters
        ]);
    }
}
