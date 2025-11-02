<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SyncController extends Controller
{
    /**
     * Show sync data page
     */
    public function index(): View
    {
        return view('sync.index');
    }
}

