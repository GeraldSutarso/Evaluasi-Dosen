<?php
  
namespace App\Http\Controllers\Auth;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
  
class AuthController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(): View
    {
        return view('auth.login');
    }  
      
      
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postLogin(Request $request): RedirectResponse
        {
            $request->validate([
                'student_id' => 'required',
            ]);

            // Find the user by student_id
            $user = User::where('student_id', $request->input('student_id'))->first();

            if ($user) {
                // Log in the user
                Auth::login($user);
                return redirect("home")->withSuccess('Anda telah masuk!');
            }

            // If no user was found, return an error
            return back()->withErrors(['errorLogin' => 'Waduh! anda memasukkan NIM yang salah.']);
        }
      
    /**
     * Write code on Method
     *
     * @return response()
     */
    
    public function home(Request $request)
    {
        if(Auth::check()){
            $user = Auth::user();
            $request->session()->forget('');//forget all other
            return view('home')->with('user',$user);
        }
        Session::flush();
        return redirect('login')->withErrors(['errorHome' => 'Maaf, tak ada akses']);
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function logout(): RedirectResponse
    {
        Session::flush();
        Auth::logout();
  
        return Redirect('login');
    }
}