<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Update settings including company name, logo (file upload or URL) and admin email.
     */
    public function update(Request $request)
    {
        if (!session('is_admin', false)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'admin_email'        => 'nullable|email|max:255',
            'company_name'       => 'nullable|string|max:255',
            'company_logo'       => 'nullable|string|max:255',
            'company_logo_file'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($request->filled('admin_email')) {
            Setting::set('admin_email', $request->admin_email);
        }
        if ($request->filled('company_name')) {
            Setting::set('company_name', $request->company_name);
        }

        if ($request->hasFile('company_logo_file')) {
            $file = $request->file('company_logo_file');
            $filename = 'logo_custom_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img'), $filename);
            Setting::set('company_logo', '/img/' . $filename);
        } elseif ($request->filled('company_logo')) {
            Setting::set('company_logo', $request->company_logo);
        }

        return redirect()->back()->with('success', 'Settings saved successfully!');
    }
}
