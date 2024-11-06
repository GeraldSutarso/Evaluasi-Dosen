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
use App\Models\Response;
use App\Models\Lecturer;
use App\Models\Matkul;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
class HomeController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    // public function home(Request $request)
    // {
    //     if(!Auth::check()){//if not logged in yet, the flush the session, in case some of the steps session stays
    //                         // then redirect to the login page, or the sign in page
    //         Session::flush();
    //         return redirect('login')->withErrors(['errorHome' => 'You have no access']);

    //     }
    //     return view('home');
    // }

    public function home(Request $request)
    {
        if(!Auth::check()){//if not logged in yet, the flush the session, in case some of the steps session stays
                            // then redirect to the login page, or the sign in page
            Session::flush();
            return redirect('login')->withErrors(['errorHome' => 'You have no access']);

        }

        $user = Auth::user();

        // Clear any session data related to evaluations, if needed
        $request->session()->forget([
            // Add session keys here if you need to clear specific ones
        ]);

        // If the user is an admin, show all evaluations
        if ($user->group_id == 99) {    
            $evaluations = Evaluation::paginate(10); 
        } 
        // If the user is not an admin, only show evaluations tied to their user ID
        else {
            $evaluations = Evaluation::where('student_id', $user->id)->paginate(10); 
        }

        // Pass evaluations and user to the view
        return view('home', ['evaluations' => $evaluations, 'user' => $user]);
    }
     /**
     * Write code on Method
     *
     * @return response()
     */
    public function search(Request $request)
{
    $searchTerm = $request->input('search');

    // If admin (group 99), allow search across all models
    if (Auth::user()->group == 99) {
        $evaluations = Evaluation::where('student_id', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('lecturer_id', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('matkul_id', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('week_number', 'LIKE', "%{$searchTerm}%")
                        ->paginate(10);
                        
        $responses = Response::where('answer', 'LIKE', "%{$searchTerm}%")->paginate(10);
        $questions = Question::where('question', 'LIKE', "%{$searchTerm}%")->paginate(10);
        $users = User::where('name', 'LIKE', "%{$searchTerm}%")->paginate(10);
        $lecturers = Lecturer::where('name', 'LIKE', "%{$searchTerm}%")->paginate(10);
        $matkuls = Matkul::where('name', 'LIKE', "%{$searchTerm}%")->paginate(10);
        $groups = Group::where('name', 'LIKE', "%{$searchTerm}%")->paginate(10);

        return view('home', compact('evaluations', 'responses', 'questions', 'users', 'lecturers', 'matkuls', 'groups'));
    } else {
        // Regular user, only access evaluations tied to their student_id
        $evaluations = Evaluation::where('student_id', Auth::user()->id)
                        ->where(function ($query) use ($searchTerm) {
                            $query->where('lecturer_id', 'LIKE', "%{$searchTerm}%")
                                  ->orWhere('matkul_id', 'LIKE', "%{$searchTerm}%")
                                  ->orWhere('week_number', 'LIKE', "%{$searchTerm}%");
                        })
                        ->paginate(10);

        // Get the related lecturers and matkuls
        $lecturers = Lecturer::whereIn('id', $evaluations->pluck('lecturer_id'))->paginate(10);
        $matkuls = Matkul::whereIn('id', $evaluations->pluck('matkul_id'))->paginate(10);

        return view('home', compact('evaluations', 'lecturers', 'matkuls'));
    }
}
}
