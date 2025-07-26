<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index() : View {
        $applications = auth()->user()->applications()->latest()->get();
        return view('dashboard', [
            'applications' => $applications,
        ]);
    }
}
