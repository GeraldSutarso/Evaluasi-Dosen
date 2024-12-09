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
use App\Models\SummaryRecord;

class AdminController extends Controller
{
    public function home(Request $request)
    {
        $week = $request->input('week');
        $search = $request->input('search');
        $lecturerType = $request->input('lecturer_type');
        $completionStatus = $request->input('completion_status');
        $group = $request->input('group');

        // Query evaluations with group information
        $evaluationsQuery = Evaluation::with(['lecturer', 'matkul'])
            ->selectRaw('
                evaluations.matkul_id,
                evaluations.lecturer_id,
                evaluations.week_number,
                COUNT(evaluations.id) as total_evaluations,
                SUM(evaluations.completed) as completed_evaluations,
                GROUP_CONCAT(DISTINCT groups.name SEPARATOR ", ") as group_names
            ')
            ->leftJoin('users', 'evaluations.user_id', '=', 'users.id')
            ->leftJoin('groups', 'users.group_id', '=', 'groups.id')
            ->groupBy('evaluations.matkul_id', 'evaluations.lecturer_id', 'evaluations.week_number');

        // Apply filters
        if ($week) {
            $evaluationsQuery->where('evaluations.week_number', $week);
        }

        if ($search) {
            $searchTerms = explode(' ', $search);

            $evaluationsQuery->where(function ($query) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $query->where(function ($q) use ($term) {
                        $q->whereHas('lecturer', function ($q) use ($term) {
                            $q->where('name', 'LIKE', "%{$term}%");
                        })
                        ->orWhereHas('matkul', function ($q) use ($term) {
                            $q->where('name', 'LIKE', "%{$term}%");
                        });
                    });
                }
            });
        }

        if (!is_null($completionStatus)) {
            if ($completionStatus == '1') {
                // Only include evaluations where all entries are completed
                $evaluationsQuery->havingRaw('SUM(evaluations.completed) = COUNT(evaluations.id)');
            } elseif ($completionStatus == '0') {
                // Only include evaluations where not all entries are completed
                $evaluationsQuery->havingRaw('SUM(evaluations.completed) < COUNT(evaluations.id)');
            }
        }
        
        

        if ($lecturerType) {
            $evaluationsQuery->whereHas('lecturer', function ($query) use ($lecturerType) {
                $query->where('type', $lecturerType);
            });
        }

        if ($group) {
            $evaluationsQuery->where('groups.name', $group);
        }

        $evaluations = $evaluationsQuery->paginate(10);
        $allGroups = Group::select('name')->distinct()->get();

        return view('admin.home', [
            'evaluations' => $evaluations,
            'week' => $week,
            'search' => $search,
            'lecturer_type' => $lecturerType,
            'completion_status' => $completionStatus,
            'group' => $group,
            'allGroups' => $allGroups,
        ]);
    }

    public function showEvaluationGroups($matkul_id, $lecturer_id)
    {
        // Load all groups with their users and evaluations for the given lecturer and matkul
        $groups = Group::with(['users.evaluations' => function ($query) use ($matkul_id, $lecturer_id) {
            $query->where('matkul_id', $matkul_id)
                ->where('lecturer_id', $lecturer_id);
        }])->get();
        

        // Loop through each group
        foreach ($groups as $group) {
            // Collect evaluations for this group's users
            $evaluations = $group->users->flatMap->evaluations;

            // Determine group status and week_number
            if ($evaluations->isEmpty()) {
                // No evaluations assigned to this group for this lecturer and matkul
                $group->setAttribute('status', 'no_evaluations');
            } else {
                // Check if all evaluations are completed
                $allCompleted = $evaluations->every(fn($evaluation) => $evaluation->completed);
                $group->setAttribute('status', $allCompleted ? 'complete' : 'incomplete');

                // Use the week_number from the first evaluation (assuming consistency)
                $group->setAttribute('week_number', $evaluations->first()->week_number);
            }
        }

        // Fetch the lecturer and matkul details
        $lecturer = Lecturer::find($lecturer_id);
        $matkul = Matkul::find($matkul_id);
        $summaryRecord = SummaryRecord::latest()->first() ?? new SummaryRecord();



        return view('admin.evaluation_groups', compact('groups', 'matkul_id', 'lecturer_id', 'lecturer', 'matkul','summaryRecord'));
    }



//    public function showEvaluationUsers($evaluation_id)
//    {
//        // Fetch the evaluation with its associated matkul and lecturer
//        $evaluation = Evaluation::with(['matkul', 'lecturer', 'user'])->findOrFail($evaluation_id);
//
//        // Fetch users who haven't completed the evaluation
//        $users = $evaluation->users()
//            ->where('evaluations.matkul_id', $evaluation->matkul_id)
//            ->where('evaluations.lecturer_id', $evaluation->lecturer_id)
//            ->where('evaluations.week_number', $evaluation->week_number)
//            ->paginate(10);

//        return view('admin.evaluation-users', compact('evaluation', 'users'));
//    }
    public function showGroupUsers($group_id, $matkul_id, $lecturer_id)
    {
        // Retrieve users in the group with their evaluation status
        $users = User::where('group_id', $group_id)
            ->with(['evaluations' => function ($query) use ($matkul_id, $lecturer_id) {
                $query->where('matkul_id', $matkul_id)
                    ->where('lecturer_id', $lecturer_id);
            }])->get();

        // Set the status for each user
        foreach ($users as $user) {
            if ($user->evaluations->isEmpty()) {
                $user->setAttribute('status', 'no_evaluations'); // No evaluations assigned
            } else {
                $user->setAttribute('status', $user->evaluations->every->completed ? 'complete' : 'incomplete');
            }
        }

        $lecturer = Lecturer::find($lecturer_id);
        $matkul = Matkul::find($matkul_id);
        $group = Group::find($group_id);

        return view('admin.group_users', compact('users', 'matkul_id', 'lecturer_id', 'lecturer', 'matkul', 'group'));
    }



    public function modify(){
        return view('admin.modify');
    }

    public function setSummaryRecord(Request $request)
    {
        // Validate the inputs
        $request->validate([
            'tahunajaran' => 'required|string|max:20', // Academic year is required
            'semester' => 'required|string|max:10',   // Semester is required
            'mengetahui' => 'nullable|string|max:100', // Optional "mengetahui" field
            'mengetahui_name' => 'nullable|string|max:100', // Optional "mengetahui name" field
            'kaprodi_tpmo' => 'nullable|string|max:100', // Optional field for kaprodi_tpmo
            'kaprodi_topkr' => 'nullable|string|max:100', // Optional field for kaprodi_topkr
        ]);

        // Create a new summary record
        SummaryRecord::create([
            'year' => $request->input('tahunajaran'),
            'semester' => $request->input('semester'),
            'mengetahui' => $request->input('mengetahui'),
            'mengetahui_name' => $request->input('mengetahui_name'),
            'kaprodi_tpmo' => $request->input('kaprodi_tpmo'),
            'kaprodi_topkr' => $request->input('kaprodi_topkr'),
        ]);

        // Redirect back with success message
        return back()->with('success', 'Record berhasil disimpan!');
    }

    public function evaluationTable()
    {
    $weeks = Evaluation::distinct()->pluck('week_number'); // Get all unique weeks
    $groups = Group::all(); // Retrieve all groups

    $tableData = [];
    foreach ($groups as $group) {
        foreach ($weeks as $week) {
            // Get evaluations for the specific group and week
            $weekEvaluations = Evaluation::whereHas('user', function ($query) use ($group) {
                $query->where('group_id', $group->id); // Filter evaluations by group
            })->where('week_number', $week)->get();

            if ($weekEvaluations->isEmpty()) {
                // If no evaluations are assigned, mark as `null`
                $tableData[$group->name][$week] = null;
            } else {
                // Check if all evaluations for the group and week are completed
                $allCompleted = $weekEvaluations->every('completed');
                $tableData[$group->name][$week] = [
                    'group_name' => $group->name,
                    'all_completed' => $allCompleted,
                ];
            }
        }
    }
    
        return view('admin.dashboard', compact('weeks', 'tableData'));
    }
      

}