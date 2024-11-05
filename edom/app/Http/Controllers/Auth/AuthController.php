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
            'username' => 'required',
            'password' => 'required',
        ]);
   
        $credentials = $request->only('username', 'password');
        $remember = $request->filled('remember'); //check the remember box
        if (Auth::attempt($credentials,$remember)) {
            return redirect("home")->withSuccess('You have Successfully logged in!');

        }
  
        return back()->withErrors(['errorLogin' => 'Oops! You have entered invalid credentials']);
    }
      
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postRegistration(Request $request): RedirectResponse
    {  
        $request->validate([
            'username' => 'required',
            'username' => 'required|username|unique:users',
            'password' => 'required|min:6',
            'department' =>'required'
        ]);
           
        $data = $request->all();
        $check = $this->create($data);
        $remember = $request->filled('remember'); //check the remember box
        return redirect("home")->withSuccess('Great! You have Successfully signed in');
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
        return redirect('login')->withErrors(['errorHome' => 'You have no access']);
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