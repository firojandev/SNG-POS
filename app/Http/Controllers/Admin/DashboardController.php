<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * @return View
     */

    public function index(): View
    {
        $data['title'] = 'Dashboard';
        $data['menu'] = 'dashboard';
        return view('admin.dashboard', $data);
    }
}
