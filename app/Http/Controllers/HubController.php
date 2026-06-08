<?php

namespace App\Http\Controllers;

use App\Models\DemoWebsite;
use App\Models\Setting;
use Illuminate\Http\Request;

class HubController extends Controller
{
    /**
     * Display the Hub page with all demo websites for visitors.
     */
    public function index()
    {
        session(['is_admin' => false]);

        $websites = DemoWebsite::latest()->get();
        $adminEmail = Setting::get('admin_email', '');
        $companyName = Setting::get('company_name', 'RingNet');
        $companyLogo = Setting::get('company_logo', '/img/logo_ringnet.webp');
        $isAdmin = false;
        $registrations = collect();

        return view('hub', compact('websites', 'adminEmail', 'companyName', 'companyLogo', 'isAdmin', 'registrations'));
    }

    /**
     * Display the Hub page with editing tools for the administrator.
     */
    public function admin()
    {
        session(['is_admin' => true]);

        $websites = DemoWebsite::latest()->get();
        $adminEmail = Setting::get('admin_email', '');
        $companyName = Setting::get('company_name', 'RingNet');
        $companyLogo = Setting::get('company_logo', '/img/logo_ringnet.webp');
        $isAdmin = true;
        $registrations = \App\Models\DemoRegistration::latest()->get();

        return view('hub', compact('websites', 'adminEmail', 'companyName', 'companyLogo', 'isAdmin', 'registrations'));
    }

    /**
     * Store a new demo website.
     */
    public function store(Request $request)
    {
        if (!session('is_admin', false)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url'  => 'required|url|max:255',
            'icon_url' => 'nullable|string|max:255',
            'icon_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'url' => $validated['url'],
            'icon_url' => $validated['icon_url'] ?? null,
        ];

        if ($request->hasFile('icon_file')) {
            $file = $request->file('icon_file');
            $filename = 'icon_custom_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img'), $filename);
            $data['icon_url'] = '/img/' . $filename;
        }

        DemoWebsite::create($data);

        return redirect()->back()->with('success', 'Website added successfully!');
    }

    /**
     * Delete a demo website.
     */
    public function destroy(DemoWebsite $website)
    {
        if (!session('is_admin', false)) {
            abort(403, 'Unauthorized action.');
        }

        $website->delete();

        return redirect()->back()->with('success', 'Website removed successfully!');
    }
}
