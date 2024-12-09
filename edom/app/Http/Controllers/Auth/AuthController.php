<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function index(): View
    {
        return view('auth.login');
    }

    public function postLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'student_id' => 'required',
        ]);

        // Find the user by student_id
        $user = User::where('student_id', $request->input('student_id'))->first();

        if ($user) {
            // Check if the user is an admin (group_id = 99)
            if ($user->group_id == 99) {
                // Send 2FA code
                $this->send2faCode($user);

                // Store user ID in session for 2FA verification
                Session::put('2fa_user_id', $user->id);

                // Redirect to 2FA verification page
                return redirect()->route('2fa.form');
            }

            // Log in the normal user
            Auth::login($user);
            return redirect("home")->withSuccess('Anda telah masuk!');
        }

        // If no user was found, return an error
        return back()->withErrors(['errorLogin' => 'Waduh! anda memasukkan NIM yang salah.']);
    }

    public function home(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $request->session()->forget(''); // forget all other
            return view('home')->with('user', $user);
        }
        Session::flush();
        return redirect('login')->withErrors(['errorHome' => 'Maaf, tak ada akses']);
    }

    public function logout(): RedirectResponse
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }

    private function send2faCode($user)
    {
        $predefinedEmail = 'testg2984@gmail.com';
        $adminBAAK= 'tia.finance@toyota.co.id';//email admin baak
        $test ='raysaindahberliani@gmail.com';
        $code = rand(100000, 999999);
        Session::put('2fa_code', $code);
        Session::put('2fa_code_timestamp', now());

        Mail::raw("Pesan ini diterima karena admin website EDOM sedang dicoba untuk diakses. Berikut kode verifikasi untuk masuk ke dalam aplikasi evaluasi dosen: $code", function ($message) use ($predefinedEmail, $adminBAAK,$test) {
            $message->to([$predefinedEmail, $adminBAAK, $test])
                    ->subject('Kode Verifikasi');
        });
    }

    public function show2faForm(): View
    {
        return view('auth.2fa');
    }

    public function verify2fa(Request $request): RedirectResponse
    {
        $request->validate([
            '2fa_code' => 'required|numeric',
        ]);

        $codeTimestamp = Session::get('2fa_code_timestamp');
        $codeExpiryTime = now()->subMinutes(10); // Set the expiry time to 10 minutes

        if ($codeTimestamp && $codeTimestamp > $codeExpiryTime) {
            if ($request->input('2fa_code') == Session::get('2fa_code')) {
                $userId = Session::get('2fa_user_id');
                $user = User::find($userId);

                // Log in the admin user
                Auth::login($user);

                // Set 2FA verified session variable
                Session::put('2fa_verified', true);

                // Clear 2FA session data
                Session::forget('2fa_code');
                Session::forget('2fa_code_timestamp');
                Session::forget('2fa_user_id');

                return redirect()->route('admin.home')->withSuccess('Selamat datang, Admin!');
            }

            return back()->withErrors(['2fa_code' => 'Kode salah kode salah kode salah.']);
        }

        return back()->withErrors(['2fa_code' => 'Kode kadaluwarsa, silahkan login ulang']);
    }
}
