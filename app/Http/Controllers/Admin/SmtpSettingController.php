<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmtpSetting;
use Illuminate\Http\Request;

class SmtpSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:admin_permission');
    }

    /**
     * Display SMTP settings page
     */
    public function index()
    {
        $smtpSetting = SmtpSetting::first();
        $title = 'SMTP Settings';

        return view('admin.SmtpSetting.index', compact('smtpSetting', 'title'))
            ->with('menu', 'smtp-setting');
    }

    /**
     * Update SMTP settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'mail_driver' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'required|in:tls,ssl,null',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string'
        ]);

        // Convert 'null' string to actual null for encryption
        if ($validated['mail_encryption'] === 'null') {
            $validated['mail_encryption'] = null;
        }

        // Always set is_active to true
        $validated['is_active'] = true;

        $smtpSetting = SmtpSetting::first();

        if ($smtpSetting) {
            // Only update password if provided
            if (empty($validated['mail_password'])) {
                unset($validated['mail_password']);
            }
            $smtpSetting->update($validated);
        } else {
            SmtpSetting::create($validated);
        }

        // Update configuration using helper function
        update_mail_config();

        notyf()->success('SMTP settings updated successfully');
        return redirect()->back();
    }

    /**
     * Test SMTP connection
     */
    public function testConnection(Request $request)
    {
        $validated = $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            // Update mail config with current settings
            update_mail_config();

            // Send test email
            \Mail::raw('This is a test email from your SMTP configuration.', function ($message) use ($validated) {
                $message->to($validated['test_email'])
                    ->subject('SMTP Test Email');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully! Please check your inbox.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }
}
