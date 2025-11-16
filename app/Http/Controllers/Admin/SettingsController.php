<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Option;

class SettingsController extends Controller
{
    /**
     * Apply permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:admin_permission');
    }

    public function index()
    {
        $data['title'] = 'General Settings';
        return view('admin.settings.index', $data)->with('menu', 'settings');
    }

    public function update(UpdateSettingsRequest $request)
    {
        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $logoPath = $request->file('app_logo')->store('settings', 'public');
            Option::set('app_logo', $logoPath);
        }

        // Handle favicon upload
        if ($request->hasFile('app_favicon')) {
            $faviconPath = $request->file('app_favicon')->store('settings', 'public');
            Option::set('app_favicon', $faviconPath);
        }

        // Update other settings
        Option::set('app_name', $request->app_name);
        Option::set('app_address', $request->app_address);
        Option::set('app_phone', $request->app_phone);
        Option::set('date_format', $request->date_format);

        notyf()->success('Settings updated successfully');

        return redirect()->back();
    }
}
