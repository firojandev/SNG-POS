<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $data['title'] = 'Purchase';
        $data['menu'] = 'purchase';
        return view('admin.Purchase.index', $data);
    }

    public function create(): View
    {
        $data['title'] = 'Create Purchase';
        $data['menu'] = 'create-purchase';
        return view('admin.Purchase.create', $data);
    }
}
