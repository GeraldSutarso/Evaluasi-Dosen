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

    public function home(Request $request)
    {
        if(!Auth::check()){//if not logged in yet, the flush the session, in case some of the steps session stays
                            // then redirect to the login page, or the sign in page
            Session::flush();
            return redirect('login')->withErrors(['errorHome' => 'You have no access']);

        }

        $user = Auth::user();
        $query = Evaluation::query();

        // Check for week filter
        if ($request->filled('week')) {
            $query->where('week_number', $request->input('week'));
        }
        // Clear any session data related to evaluations, if needed
        $request->session()->forget([
            // Add session keys here if you need to clear specific ones
        ]);

        if ($user->group_id == 99) {    
            // Admin: retrieve all evaluations with related data
            $evaluations = Evaluation::with(['lecturer', 'matkul', 'response'])->paginate(10); 
        } else {
            // Regular user: retrieve only evaluations tied to their student ID with related data
            $evaluations = Evaluation::where('user_id', $user->id)
                                    ->with(['lecturer', 'matkul', 'response'])
                                    ->paginate(10); 
        }

        // Pass all the necessary data to the view
        return view('home', [
            'evaluations' => $evaluations,
            'user' => $user
        ]);
    }
     /**
     * Write code on Method
     *
     * @return response()
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $keywords = explode(' ', $searchTerm); // Split search term by spaces into keywords

        // If admin (group 99), allow search across all models
        if (Auth::user()->group_id == 99) {
            $evaluations = Evaluation::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('user_id', 'LIKE', "%{$keyword}%")
                        ->orWhere('lecturer_id', 'LIKE', "%{$keyword}%")
                        ->orWhere('matkul_id', 'LIKE', "%{$keyword}%")
                        ->orWhere('week_number', 'LIKE', "%{$keyword}%");
                }
            })->paginate(10);

            $responses = Response::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('answer', 'LIKE', "%{$keyword}%");
                }
            })->paginate(10);

            $questions = Question::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('question', 'LIKE', "%{$keyword}%");
                }
            })->paginate(10);

            $users = User::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'LIKE', "%{$keyword}%");
                }
            })->paginate(10);

            $lecturers = Lecturer::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'LIKE', "%{$keyword}%");
                }
            })->paginate(10);

            $matkuls = Matkul::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'LIKE', "%{$keyword}%");
                }
            })->paginate(10);

            $groups = Group::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'LIKE', "%{$keyword}%");
                }
            })->paginate(10);

            return view('home', compact('evaluations', 'responses', 'questions', 'users', 'lecturers', 'matkuls', 'groups'));
        } else {
            // Regular user, only access evaluations tied to their user_id
            $evaluations = Evaluation::where('user_id', Auth::user()->id)
                            ->where(function ($query) use ($keywords) {
                                foreach ($keywords as $keyword) {
                                    $query->orWhere('lecturer_id', 'LIKE', "%{$keyword}%")
                                        ->orWhere('matkul_id', 'LIKE', "%{$keyword}%")
                                        ->orWhere('week_number', 'LIKE', "%{$keyword}%");
                                }
                            })
                            ->paginate(10);

            // Get the related lecturers and matkuls
            $lecturers = Lecturer::whereIn('id', $evaluations->pluck('lecturer_id'))->paginate(10);
            $matkuls = Matkul::whereIn('id', $evaluations->pluck('matkul_id'))->paginate(10);

            return view('home', compact('evaluations', 'lecturers', 'matkuls'));
        }
    }
}
