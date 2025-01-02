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
use App\Models\ResponseRecord;
use App\Models\LayananRecord;
use App\Models\LayananResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
      	$maxWeek = Evaluation::max('week_number');

        return view('admin.home', [
            'evaluations' => $evaluations,
            'week' => $week,
            'search' => $search,
            'lecturer_type' => $lecturerType,
            'completion_status' => $completionStatus,
            'group' => $group,
            'allGroups' => $allGroups,
          	'maxWeekNumber' => $maxWeek,
        ]);
    }
    
    public function deleteEvaluation(Request $request)
    {
        $lecturerId = $request->input('lecturer_id');
        $matkulId = $request->input('matkul_id');
        $weekNumber = $request->input('week_number');
    
        // Find all evaluations matching the criteria
        $evaluationIds = Evaluation::where('lecturer_id', $lecturerId)
            ->where('matkul_id', $matkulId)
            ->where('week_number', $weekNumber)
            ->pluck('id');
    
        // Delete responses related to these evaluations
        Response::whereIn('evaluation_id', $evaluationIds)->delete();
    
        // Delete the evaluations
        Evaluation::whereIn('id', $evaluationIds)->delete();
        return redirect()->back()->with('success', 'Evaluasi dan respons berhasil dihapus.');
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

        return view('admin.evaluation_groups', compact('groups', 'matkul_id', 'lecturer_id', 'lecturer', 'matkul'));
    }



//    public function showEvaluationUsers($evaluation_id) Gajadi brok >U<
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


public function showLayanan(Request $request)
{
    $search = $request->input('search');
    $statusFilter = $request->input('status');
    $groupFilter = $request->input('group');

    // Query users with related layananResponses and apply filters
    $usersQuery = User::with(['layananResponses', 'group'])
        ->select('users.*')
        ->leftJoin('groups', 'users.group_id', '=', 'groups.id');

    // Apply search filter
    if ($search) {
        $searchTerms = explode(' ', $search);
        $usersQuery->where(function ($query) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $query->where('users.name', 'LIKE', "%{$term}%")
                    ->orWhere('users.student_id', 'LIKE', "%{$term}%")
                    ->orWhere('groups.name', 'LIKE', "%{$term}%");
            }
        });
    }

    // Apply group filter
    if ($groupFilter) {
        $usersQuery->where('groups.name', $groupFilter);
    }

    // Apply status filter
    if ($statusFilter) {
        if ($statusFilter === 'no_layanan') {
            $usersQuery->whereDoesntHave('layananResponses');
        } elseif ($statusFilter === 'complete') {
            $usersQuery->whereHas('layananResponses');
        }
    }

    // Paginate users
    $users = $usersQuery->paginate(10);
    // Add status attribute
    $users->getCollection()->transform(function ($user) {
        $user->status = $user->layananResponses->isEmpty() ? 'no_layanan' : 'complete';
        return $user;
    });

    // Retrieve all groups for filter dropdown
    $allGroups = Group::select('name')->distinct()->get();
    return view('admin.layanan', compact('users', 'search', 'statusFilter', 'groupFilter', 'allGroups'));
}



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
        $summaryRecord = SummaryRecord::latest()->first() ?? new SummaryRecord();
        return view('admin.modify', compact('summaryRecord'));
    }
    public function toggleLock(Request $request, $field)//toggle kunci untuk edom dan layanan
    {
        // Validate field to prevent invalid column updates
        if (!in_array($field, ['edom_lock', 'layanan_lock'])) {
            abort(400, 'Invalid lock field');
        }

        // Retrieve the single summary record (assuming there is only one row)
        $summaryRecord = SummaryRecord::latest()->first();
        if (!$summaryRecord) {
            return redirect()->back()->with('error', 'Summary record not found.');
        }

        // Toggle the specified field
        $summaryRecord->$field = !$summaryRecord->$field;
        $summaryRecord->save();

        return redirect()->back()->with('success', 'Kunci berhasil diperbarui.');
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
            'edom_lock' => 'nullable|boolean', // Optional field for edom_lock
            'layanan_lock' => 'nullable|boolean', // Optional field for layanan_lock
        ]);

        // Create a new summary record
        SummaryRecord::create([
            'year' => $request->input('tahunajaran'),
            'semester' => $request->input('semester'),
            'mengetahui' => $request->input('mengetahui'),
            'mengetahui_name' => $request->input('mengetahui_name'),
            'kaprodi_tpmo' => $request->input('kaprodi_tpmo'),
            'kaprodi_topkr' => $request->input('kaprodi_topkr'),
            'edom_lock' => $request->input('edom_lock'),
            'layanan_lock' => $request->input('layanan_lock'),
        ]);

        // Redirect back with success message
        return back()->with('success', 'Record berhasil disimpan!');
    }

    public function evaluationTable()
    {
        // Table data logic
        $weeks = Evaluation::distinct()->pluck('week_number'); // Get all unique weeks
        $groups = Group::all(); // Retrieve all groups

        $tableData = [];
        foreach ($groups as $group) {
            foreach ($weeks as $week) {
                // Get evaluations for the specific group and week
                $weekEvaluations = Evaluation::whereHas('user', function ($query) use ($group) {
                    $query->where('group_id', $group->id); // Filter users by group_id
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

                // Fetch the latest semester from SummaryRecord
        $latestSemester = SummaryRecord::latest('created_at')->value('semester'); // Assuming 'created_at' determines latest

        // Define month mapping based on the semester
        $monthMapping = [];
        if ($latestSemester === 'I') {
            // Semester I: Start from September
            $monthMapping = [
                1 => "September",
                2 => "Oktober",
                3 => "November",
                4 => "Desember",
                5 => "Januari",
                6 => "Februari",
                7 => "Maret",
                8 => "April",
                9 => "Mei",
                10 => "Juni",
                11 => "Juli",
                12 => "Agustus"
            ];
        } elseif ($latestSemester === 'II') {
            // Semester II: Start from March
            $monthMapping = [
                1 => "Maret",
                2 => "April",
                3 => "Mei",
                4 => "Juni",
                5 => "Juli",
                6 => "Agustus",
                7 => "September",
                8 => "Oktober",
                9 => "November",
                10 => "Desember",
                11 => "Januari",
                12 => "Februari"
            ];
        }

        // Monthly chart data logic
        $chartData = DB::table('evaluations')
            ->join('users', 'evaluations.user_id', '=', 'users.id')
            ->join('groups', 'users.group_id', '=', 'groups.id')
            ->selectRaw('
                CEIL(week_number / 4) as month_number,
                groups.id as group_id,
                groups.name as group_name,
                COUNT(DISTINCT groups.id) as total_groups,
                SUM(CASE 
                    WHEN evaluations.completed = 1 THEN 1 
                    ELSE 0 
                END) as completed_count,
                COUNT(*) as total_evaluations
            ')
            ->groupBy('month_number', 'groups.id', 'groups.name')
            ->orderBy('month_number')
            ->get();

        // Process data to count completed groups per month
        $monthlyStats = [];
        foreach ($chartData as $record) {
            $monthNum = $record->month_number;

            // Get the correct month name using $monthMapping
            $monthName = $monthMapping[$monthNum] ?? 'Unknown';

            if (!isset($monthlyStats[$monthNum])) {
                $monthlyStats[$monthNum] = [
                    'month' => $monthName,
                    'completed_groups' => 0,
                    'not_completed_groups' => 0
                ];
            }

    // A group is considered complete if all evaluations are completed
    if ($record->completed_count == $record->total_evaluations) {
        $monthlyStats[$monthNum]['completed_groups']++;
    } else {
        $monthlyStats[$monthNum]['not_completed_groups']++;
    }
}

$formattedChartData = array_values($monthlyStats);


            // Weekly Evaluation Chart Data Logic (New)
    $weeklyStats = DB::table('evaluations')
    ->join('users', 'evaluations.user_id', '=', 'users.id')
    ->join('groups', 'users.group_id', '=', 'groups.id')
    ->selectRaw('
        week_number,
        groups.id as group_id,
        COUNT(DISTINCT groups.id) as total_groups,
        SUM(CASE 
            WHEN evaluations.completed = 1 THEN 1 
            ELSE 0 
        END) as completed_count,
        COUNT(evaluations.id) as total_evaluations' // Count individual evaluations, not just rows
    )
    ->groupBy('week_number', 'groups.id')  // Group by week and group ID
    ->orderBy('week_number')
    ->get();

    // Process data to track completed groups per week
    $weeklyData = [];
    foreach ($weeklyStats as $stat) {
        $completedGroups = ($stat->completed_count == $stat->total_evaluations) ? 1 : 0;

        // Find if the week already exists in the data array
        if (!isset($weeklyData[$stat->week_number])) {
            $weeklyData[$stat->week_number] = [
                'week' => 'W' . $stat->week_number,
                'completed_groups' => 0,
                'not_completed_groups' => 0,
            ];
        }

        // Update the counts for completed and not completed groups
        if ($completedGroups) {
            $weeklyData[$stat->week_number]['completed_groups']++;
        } else {
            $weeklyData[$stat->week_number]['not_completed_groups']++;
        }
    }

    // Reset keys to re-index the data
    $weeklyData = array_values($weeklyData);

    // Fetch responses grouped by question
    $chartResponses = DB::table('layanan_responses')
    ->join('layanan_questions', 'layanan_responses.question_id', '=', 'layanan_questions.id')
    ->select(
        'layanan_questions.id as question_id',
        'layanan_questions.text as question_text',
        'layanan_questions.type as question_type',
        'layanan_responses.response_value'
    )
    ->get();

    $layananChartData = [];
    $feedbackData = [];

    foreach ($chartResponses as $response) {
        if ($response->question_type === 'Feedback') {
            // Collect feedback responses for "Feedback" type questions
            $feedbackData[$response->question_text][] = $response->response_value;
        } else {
            // Convert response value to integer for numeric responses
            $value = is_numeric($response->response_value) ? (int)$response->response_value : null;

            if ($value !== null) {
                // Count responses per question
                $layananChartData[$response->question_text][$value] = ($layananChartData[$response->question_text][$value] ?? 0) + 1;
            }
        }
    }

    return view('admin.dashboard', compact('layananChartData', 'feedbackData','weeks', 'tableData', 'formattedChartData', 'weeklyData'));

    }

    public function deleteAllEvaluations()
    {
        // Fetch the latest year and semester from SummaryRecord
        $latestSummary = SummaryRecord::latest('created_at')->first();
        $yearSemester = $latestSummary ? "{$latestSummary->year}_{$latestSummary->semester}" : 'unknown';

        // Fetch all evaluations
        $evaluations = Evaluation::all();

        foreach ($evaluations as $evaluation) {
            // Fetch responses related to the current evaluation
            $responses = Response::where('evaluation_id', $evaluation->id)->get();

            // Extract and save responses to response_records
            foreach ($responses as $response) {
                ResponseRecord::create([
                    'evaluation_id'   => $evaluation->id,
                    'user_name'       => optional($evaluation->user)->name,
                    'group_name'      => optional($evaluation->user->group)->name,
                    'lecturer_name'   => optional($evaluation->lecturer)->name,
                    'matkul_name'     => optional($evaluation->matkul)->name,
                    'question_text'   => optional($response->question)->text,
                    'response_value'  => $response->response_value,
                    'year_semester'   => $yearSemester, // From SummaryRecord
                    'created_at'      => now(),
                ]);
            }

            // Delete responses for this evaluation
            Response::where('evaluation_id', $evaluation->id)->delete();
        }

            // Handle Layanan Responses
        $layananResponses = LayananResponse::all();
        foreach ($layananResponses as $layananResponse) {
            LayananRecord::create([
                'response_id'    => $layananResponse->id,
                'user_name'      => optional($layananResponse->user)->name,
                'student_id'     => optional($layananResponse->user)->student_id,
                'group_name'     => optional($layananResponse->user->group)->name,
                'question_text'  => optional($layananResponse->question)->text,
                'response_value' => $layananResponse->response_value,
                'year_semester'  => $yearSemester, // From SummaryRecord
                'created_at'     => now(),
            ]);
        }

        // Delete all evaluations
        Evaluation::query()->delete(); // Use delete() to avoid truncation issues

        // Delete all layanan responses
        LayananResponse::query()->delete();

        return redirect()->route('admin.modify')->with('success', 'Evaluasi telah direset.');
    }


}