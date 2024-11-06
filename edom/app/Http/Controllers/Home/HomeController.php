<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\Group;
use App\Models\User;
use App\Models\Question;
use App\Models\Respose;
use App\Models\Lecturer;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
class HomeController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function home(Request $request)
    {
        if(!Auth::check()){//if not logged in yet, the flush the session, in case some of the steps session stays
                            // then redirect to the login page, or the sign in page
            Session::flush();
            return redirect('login')->withErrors(['errorHome' => 'You have no access']);

        }
        return view('home');
    }
}
