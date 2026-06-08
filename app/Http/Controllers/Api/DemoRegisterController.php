<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DemoRegistration;
use Illuminate\Http\Request;

class DemoRegisterController extends Controller
{
    /**
     * Register a test account request from demo sites.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username'  => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone'     => 'required|string|max:20',
            'site_name' => 'required|string|max:255',
        ]);

        $registration = DemoRegistration::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công! Vui lòng kiểm tra email của bạn để lấy tài khoản test.',
            'data'    => $registration
        ], 201);
    }
}
