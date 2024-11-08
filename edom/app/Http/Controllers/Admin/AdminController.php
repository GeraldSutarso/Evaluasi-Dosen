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
        // Retrieve filter and search inputs
        $week = $request->input('week');
        $search = $request->input('search');

        // Query evaluations with filters
        $evaluationsQuery = Evaluation::with(['lecturer', 'matkul'])
            ->select('matkul_id', 'lecturer_id', 'week_number')
            ->groupBy('matkul_id', 'lecturer_id', 'week_number');

        // Apply week filter if specified
        if ($week) {
            $evaluationsQuery->where('week_number', $week);
        }

        // Apply search filter if specified
        if ($search) {
            $evaluationsQuery->whereHas('matkul', function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%");
            })->orWhereHas('lecturer', function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%");
            });
        }

        // Paginate the results
        $evaluations = $evaluationsQuery->paginate(10);

        return view('admin.home', compact('evaluations', 'week', 'search'));
    }


    public function showEvaluationGroups($matkul_id, $lecturer_id, $week)
    {
        // Retrieve groups with users who attended the selected matkul and lecturer in the given week
        $groups = Group::with(['users' => function ($query) use ($matkul_id, $lecturer_id, $week) {
            $query->whereHas('evaluations', function ($evaluationQuery) use ($matkul_id, $lecturer_id, $week) {
                $evaluationQuery->where('matkul_id', $matkul_id)
                                ->where('lecturer_id', $lecturer_id)
                                ->where('week_number', $week);
            });
        }])->get();

        return view('admin.evaluation_groups', compact('groups', 'matkul_id', 'lecturer_id', 'week'));
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
        $evaluation = Evaluation::with(['matkul', 'lecturer', 'user'])->findOrFail($evaluation_id);

        // Fetch users who haven't completed the evaluation
        $users = $evaluation->users()
            ->where('evaluations.matkul_id', $evaluation->matkul_id)
            ->where('evaluations.lecturer_id', $evaluation->lecturer_id)
            ->where('evaluations.week_number', $evaluation->week_number)
            ->paginate(10);

        return view('admin.evaluation-users', compact('evaluation', 'users'));
    }
}