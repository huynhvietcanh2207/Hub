<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DemoRegistration;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        // Send email to admin if configured
        $adminEmail = Setting::get('admin_email');
        if ($adminEmail) {
            try {
                Mail::raw(
                    "Chào Admin,\n\nCó một tài khoản vừa đăng ký chạy thử demo website:\n" .
                    "- Username: {$registration->username}\n" .
                    "- Email: {$registration->email}\n" .
                    "- Số điện thoại: {$registration->phone}\n" .
                    "- Website demo: {$registration->site_name}\n\n" .
                    "Tài khoản test của khách hàng:\n" .
                    "- Email: {$registration->email}\n" .
                    "- Mật khẩu mặc định: password\n\n" .
                    "Trân trọng,\nSystem Hub Demo Center",
                    function ($message) use ($adminEmail, $registration) {
                        $message->to($adminEmail)
                                ->subject("[RingNet Hub] Đăng ký Demo mới từ {$registration->site_name}");
                    }
                );
            } catch (\Exception $e) {
                logger()->error("Failed to send demo registration email: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công! Vui lòng dùng tài khoản dưới đây để đăng nhập và trải nghiệm.',
            'test_account' => [
                'email' => $registration->email,
                'password' => 'password'
            ],
            'data'    => $registration
        ], 201);
    }
}
