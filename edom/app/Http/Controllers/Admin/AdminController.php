<?php

namespace App\Http\Controllers\Admin;

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

class AdminController extends Controller
{
    public function home(Request $request)
    {
        if (!Auth::check()) {
            // If not logged in, flush session and redirect to login page
            Session::flush();
            return redirect('login')->withErrors(['errorHome' => 'You have no access']);
        }

        $user = Auth::user();
        $query = Evaluation::query();

        // Check for week filter
        if ($request->filled('week')) {
            $query->where('week_number', $request->input('week'));
        }

        // Check for completion filter (for admin)
        if ($request->has('completed')) {
            $completedStatus = $request->input('completed') == 'true' ? true : false;
            $query->where('completed', $completedStatus);
        }

        // Clear any session data related to evaluations if needed
        $request->session()->forget([
            // Add session keys here if you need to clear specific ones
        ]);

        if ($user->group_id == 99) {
            // Admin: retrieve all evaluations with related data
            $evaluations = Evaluation::with(['lecturers', 'matkuls', 'users'])->paginate(10);
        } else {
            // Regular user: retrieve only evaluations tied to their user_id with related data
            $evaluations = Evaluation::where('user_id', $user->id)
                ->with(['lecturers', 'matkuls'])
                ->paginate(10);
        }

        // Pass all the necessary data to the view
        return view('admin.home', compact('evaluations', 'user'));
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $keywords = explode(' ', $searchTerm); // Split search term by spaces into keywords

        // If admin (group 99), allow search across all models and also filter by completion status
        if (Auth::user()->group_id == 99) {
            $query = Evaluation::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('user_id', 'LIKE', "%{$keyword}%")
                        ->orWhere('lecturer_id', 'LIKE', "%{$keyword}%")
                        ->orWhere('matkul_id', 'LIKE', "%{$keyword}%")
                        ->orWhere('week_number', 'LIKE', "%{$keyword}%");
                }
            });

            // Check for 'completed' status search
            if ($request->has('completed')) {
                $completedStatus = $request->input('completed') == 'true' ? true : false;
                $query->where('completed', $completedStatus);
            }

            $evaluations = $query->paginate(10);

            // Search for users, lecturers, matkuls, and groups
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

            return view('admin.home', compact(
                'evaluations', 'users', 'lecturers', 'matkuls', 'groups'
            ));
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

    public function showEvaluationUsers($evaluation_id)
    {
        // Fetch the evaluation with its associated matkul and lecturer
        $evaluation = Evaluation::with(['matkul', 'lecturer', 'users'])->findOrFail($evaluation_id);

        // Fetch users who haven't completed the evaluation
        $users = $evaluation->users()
            ->where('evaluations.matkul_id', $evaluation->matkul_id)
            ->where('evaluations.lecturer_id', $evaluation->lecturer_id)
            ->where('evaluations.week_number', $evaluation->week_number)
            ->paginate(10);

        return view('admin.evaluation-users', compact('evaluation', 'users'));
    }
}