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
        $week = $request->input('week');
        $search = $request->input('search');
        $lecturerType = $request->input('lecturer_type'); // Get lecturer type filter
    
        // Query evaluations with week filter if specified
        $evaluationsQuery = Evaluation::with(['lecturer', 'matkul'])
            ->select('matkul_id', 'lecturer_id', 'week_number')
            ->groupBy('matkul_id', 'lecturer_id', 'week_number');
    
        // Apply week filter if specified
        if ($week) {
            $evaluationsQuery->where('week_number', $week);
        }
    
        // Apply search filter if specified
        if ($search) {
            $searchTerms = explode(' ', $search);
    
            if (count($searchTerms) === 2) {
                // If there are two terms, handle combination matching
                $evaluationsQuery->where(function ($query) use ($searchTerms) {
                    $query->whereHas('lecturer', function ($q) use ($searchTerms) {
                        $q->where('name', 'LIKE', "%{$searchTerms[0]}%");
                    })->whereHas('matkul', function ($q) use ($searchTerms) {
                        $q->where('name', 'LIKE', "%{$searchTerms[1]}%");
                    });
                })->orWhere(function ($query) use ($searchTerms) {
                    $query->whereHas('lecturer', function ($q) use ($searchTerms) {
                        $q->where('name', 'LIKE', "%{$searchTerms[1]}%");
                    })->whereHas('matkul', function ($q) use ($searchTerms) {
                        $q->where('name', 'LIKE', "%{$searchTerms[0]}%");
                    });
                });
            } else {
                // Handle single-term search
                $evaluationsQuery->where(function ($query) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $query->orWhereHas('lecturer', function ($q) use ($term) {
                            $q->where('name', 'LIKE', "%{$term}%");
                        })->orWhereHas('matkul', function ($q) use ($term) {
                            $q->where('name', 'LIKE', "%{$term}%");
                        });
                    }
                });
            }
        }
    
        // Apply lecturer type filter if specified (1 = Dosen, 2 = Instruktur)
        if ($lecturerType) {
            $evaluationsQuery->whereHas('lecturer', function ($query) use ($lecturerType) {
                $query->where('type', $lecturerType);
            });
        }
    
        // Fetch the evaluations
        $evaluations = $evaluationsQuery->paginate(10);
    
        // Return the view with the evaluations and filter data
        return view('admin.home', [
            'evaluations' => $evaluations,
            'week' => $week,
            'search' => $search,
            'lecturer_type' => $lecturerType, // Pass the selected lecturer type to the view
        ]);
    }
    
    public function showEvaluationGroups($matkul_id, $lecturer_id)
    {
        // Load all groups with their users and the evaluations filtered by the specific criteria
        $groups = Group::with(['users.evaluations' => function ($query) use ($matkul_id, $lecturer_id) {
            $query->where('matkul_id', $matkul_id)
                    ->where('lecturer_id', $lecturer_id)
                    ;
        }])->get();

        // Loop through each group to calculate if all users have completed their evaluations
        foreach ($groups as $group) {
            $group->setAttribute('allCompleted', $group->users->every(function ($user) {
                return $user->evaluations->where('completed', true)->isNotEmpty();
            }));
        }

        return view('admin.evaluation_groups', compact('groups', 'matkul_id', 'lecturer_id'));
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
    public function showGroupUsers($group_id, $matkul_id, $lecturer_id)
    {
        // Retrieve users in the group with their evaluation status
        $users = User::where('group_id', $group_id)
            ->with(['evaluations' => function ($query) use ($matkul_id, $lecturer_id) {
                $query->where('matkul_id', $matkul_id)
                    ->where('lecturer_id', $lecturer_id)
;
            }])->get();

        return view('admin.group_users', compact('users', 'matkul_id', 'lecturer_id'));
    }

    public function modify(){
        return view('admin.modify');
    }
}