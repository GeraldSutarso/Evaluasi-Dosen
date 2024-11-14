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
            $keywords = explode(' ', $search); // Split search terms by spaces
            $evaluationsQuery->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhereHas('matkul', function ($subQuery) use ($keyword) {
                        $subQuery->where('name', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('lecturer', function ($subQuery) use ($keyword) {
                        $subQuery->where('name', 'LIKE', "%$keyword%");
                    })
                    ->orWhere('week_number', 'LIKE', "%$keyword%");
                }
            });
        }

        // Paginate the results
        $evaluations = $evaluationsQuery->paginate(10);

        return view('admin.home', compact('evaluations', 'week', 'search'));
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $week = $request->input('week');
        $keywords = explode(' ', $searchTerm);
    
        // Base query for evaluations with related lecturers and matkuls
        $evaluationsQuery = Evaluation::with(['lecturer', 'matkul']);
    
        // If admin (group 99), allow full search capabilities
        if (Auth::user()->group_id == 99) {
            // Filter by week if specified
            if ($week) {
                $evaluationsQuery->where('week_number', $week);
            }
    
            // Apply search filters based on keywords
            $evaluationsQuery->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhereHas('lecturer', function ($subQuery) use ($keyword) {
                        // Check if searching for 'dosen' or 'instruktur'
                        if (strtolower($keyword) === 'dosen') {
                            $subQuery->where('type', 1); // Type 1 for 'dosen'
                        } elseif (strtolower($keyword) === 'instruktur') {
                            $subQuery->where('type', 2); // Type 2 for 'instruktur'
                        } else {
                            // General search on lecturer name
                            $subQuery->where('name', 'LIKE', "%$keyword%");
                        }
                    })
                    ->orWhereHas('matkul', function ($subQuery) use ($keyword) {
                        $subQuery->where('name', 'LIKE', "%$keyword%");
                    })
                    ->orWhere('week_number', 'LIKE', "%$keyword%");
                }
            });
    
            // Check for 'completed' status search
            if ($request->has('completed')) {
                $completedStatus = $request->input('completed') === 'true';
                $evaluationsQuery->where('completed', $completedStatus);
            }
    
            // Paginate the filtered results
            $evaluations = $evaluationsQuery->paginate(10);
    
            // Get related records for display
            $lecturerIds = $evaluationsQuery->pluck('lecturer_id')->unique();
            $matkulIds = $evaluationsQuery->pluck('matkul_id')->unique();
            $lecturers = Lecturer::whereIn('id', $lecturerIds)->paginate(10);
            $matkuls = Matkul::whereIn('id', $matkulIds)->paginate(10);
    
            return view('admin.home', compact('evaluations', 'lecturers', 'matkuls', 'week', 'searchTerm'));
    
        } else {
            // Regular user: limit search to user's evaluations only
            $evaluationsQuery->where('user_id', Auth::user()->id);
    
            // Apply week filter if specified
            if ($week) {
                $evaluationsQuery->where('week_number', $week);
            }
    
            // Apply search filters based on keywords
            $evaluationsQuery->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhereHas('lecturer', function ($subQuery) use ($keyword) {
                        if (strtolower($keyword) === 'dosen') {
                            $subQuery->where('type', 1);
                        } elseif (strtolower($keyword) === 'instruktur') {
                            $subQuery->where('type', 2);
                        } else {
                            $subQuery->where('name', 'LIKE', "%$keyword%");
                        }
                    })
                    ->orWhereHas('matkul', function ($subQuery) use ($keyword) {
                        $subQuery->where('name', 'LIKE', "%$keyword%");
                    })
                    ->orWhere('week_number', 'LIKE', "%$keyword%");
                }
            });
    
            $evaluations = $evaluationsQuery->paginate(10);
    
            // Get related lecturers and matkuls for display
            $lecturerIds = $evaluationsQuery->pluck('lecturer_id')->unique();
            $matkulIds = $evaluationsQuery->pluck('matkul_id')->unique();
            $lecturers = Lecturer::whereIn('id', $lecturerIds)->paginate(10);
            $matkuls = Matkul::whereIn('id', $matkulIds)->paginate(10);
    
            return view('home', compact('evaluations', 'lecturers', 'matkuls', 'week', 'searchTerm'));
        }
    }
    

    // public function search(Request $request)
    // {
    //     $searchTerm = $request->input('search');
    //     $week = $request->input('week');
    //     $keywords = explode(' ', $searchTerm);

    //     // Query evaluations based on user role
    //     $evaluationsQuery = Evaluation::query();

    //     // Define types for lecturer type filtering
    //     $lecturerTypes = [
    //         'dosen' => 1,
    //         'instruktur' => 2,
    //     ];

    //     // If admin (group 99), allow search across all models and filter by completion status
    //     if (Auth::user()->group_id == 99) {
    //         $evaluationsQuery->where(function ($query) use ($keywords, $lecturerTypes) {
    //             foreach ($keywords as $keyword) {
    //                 // Check if the keyword matches 'dosen' or 'instruktur' to filter by lecturer type
    //                 if (isset($lecturerTypes[strtolower($keyword)])) {
    //                     $type = $lecturerTypes[strtolower($keyword)];
    //                     $query->orWhereHas('lecturer', function ($subQuery) use ($type) {
    //                         $subQuery->where('type', $type);
    //                     });
    //                 } else {
    //                     // General search across user_id, lecturer name, matkul name, and week number
    //                     $query->orWhere('user_id', 'LIKE', "%{$keyword}%")
    //                         ->orWhereHas('lecturer', function ($subQuery) use ($keyword) {
    //                             $subQuery->where('name', 'LIKE', "%$keyword%");
    //                         })
    //                         ->orWhereHas('matkul', function ($subQuery) use ($keyword) {
    //                             $subQuery->where('name', 'LIKE', "%$keyword%");
    //                         })
    //                         ->orWhere('week_number', 'LIKE', "%$keyword%");
    //                 }
    //             }
    //         });

    //         // Apply week filter if specified
    //         if ($week) {
    //             $evaluationsQuery->where('week_number', $week);
    //         }

    //         // Check for 'completed' status search
    //         if ($request->has('completed')) {
    //             $completedStatus = $request->input('completed') == 'true';
    //             $evaluationsQuery->where('completed', $completedStatus);
    //         }

    //         $evaluations = $evaluationsQuery->paginate(10);

    //         // Search for related users, lecturers, matkuls, and groups
    //         $users = User::where(function ($query) use ($keywords) {
    //             foreach ($keywords as $keyword) {
    //                 $query->orWhere('name', 'LIKE', "%{$keyword}%");
    //             }
    //         })->paginate(10);

    //         $lecturers = Lecturer::where(function ($query) use ($keywords) {
    //             foreach ($keywords as $keyword) {
    //                 $query->orWhere('name', 'LIKE', "%{$keyword}%");
    //             }
    //         })->paginate(10);

    //         $matkuls = Matkul::where(function ($query) use ($keywords) {
    //             foreach ($keywords as $keyword) {
    //                 $query->orWhere('name', 'LIKE', "%{$keyword}%");
    //             }
    //         })->paginate(10);

    //         $groups = Group::where(function ($query) use ($keywords) {
    //             foreach ($keywords as $keyword) {
    //                 $query->orWhere('name', 'LIKE', "%{$keyword}%");
    //             }
    //         })->paginate(10);

    //         return view('admin.home', compact('evaluations', 'users', 'lecturers', 'matkuls', 'groups', 'week', 'searchTerm'));
    //     } else {
    //         // Regular user case: limit to user's own evaluations
    //         $evaluationsQuery->where('user_id', Auth::user()->id);

    //         $evaluationsQuery->where(function ($query) use ($keywords, $lecturerTypes) {
    //             foreach ($keywords as $keyword) {
    //                 if (isset($lecturerTypes[strtolower($keyword)])) {
    //                     $type = $lecturerTypes[strtolower($keyword)];
    //                     $query->orWhereHas('lecturer', function ($subQuery) use ($type) {
    //                         $subQuery->where('type', $type);
    //                     });
    //                 } else {
    //                     $query->orWhereHas('lecturer', function ($subQuery) use ($keyword) {
    //                         $subQuery->where('name', 'LIKE', "%$keyword%");
    //                     })
    //                     ->orWhereHas('matkul', function ($subQuery) use ($keyword) {
    //                         $subQuery->where('name', 'LIKE', "%$keyword%");
    //                     })
    //                     ->orWhere('week_number', 'LIKE', "%$keyword%");
    //                 }
    //             }
    //         });

    //         // Apply week filter if specified
    //         if ($week) {
    //             $evaluationsQuery->where('week_number', $week);
    //         }

    //         $evaluations = $evaluationsQuery->paginate(10);

    //         // Get related lecturers and matkuls based on evaluations
    //         // First, retrieve IDs from the evaluations before pagination
    //         $lecturerIds = $evaluationsQuery->pluck('lecturer_id')->unique();
    //         $matkulIds = $evaluationsQuery->pluck('matkul_id')->unique();

    //         // Then, perform pagination
    //         $evaluations = $evaluationsQuery->paginate(10);

    //         // Use the unique IDs for related lecturers and matkuls
    //         $lecturers = Lecturer::whereIn('id', $lecturerIds)->paginate(10);
    //         $matkuls = Matkul::whereIn('id', $matkulIds)->paginate(10);
    //         return view('home', compact('evaluations', 'lecturers', 'matkuls', 'week', 'searchTerm'));
    //     }
    // }

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
}