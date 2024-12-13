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
         if (!Auth::check()) {
             // Flush session terus lempar ke login if not authenticate
             Session::flush();
             return redirect('login')->withErrors(['errorHome' => 'You have no access']);
         }
     
         $user = Auth::user();
         $query = Evaluation::query()->where('user_id', $user->id);
     
         // Week filter
         if ($request->filled('week')) {
             $query->where('week_number', $request->input('week'));
         }
     
         // Completed (status pengisian) filter
         if ($request->filled('completed')) {
             $completedStatus = $request->input('completed') == 'true' ? true : false;
             $query->where('completed', $completedStatus);
         }
     
         // Tipe pengajar filter (1 = Dosen, 2 = Instruktur)
         if ($request->filled('lecturer_type')) {
             $lecturerType = $request->input('lecturer_type');
             $query->whereHas('lecturer', function ($query) use ($lecturerType) {
                 $query->where('type', $lecturerType);
             });
         }
     
         // search
         if ($request->filled('search')) {
            $searchTerm = $request->input('search');
        
            // Split the search term by spaces for multiple terms
            $searchTerms = explode(' ', $searchTerm);
        
            if (count($searchTerms) === 2) {
                // Handle two-term combination matching
                $query->where(function ($subQuery) use ($searchTerms, $user) {
                    $subQuery->where('user_id', $user->id) // Enforce user_id condition
                        ->whereHas('lecturer', function ($lecturerQuery) use ($searchTerms) {
                            $lecturerQuery->where('name', 'LIKE', "%{$searchTerms[0]}%");
                        })->whereHas('matkul', function ($matkulQuery) use ($searchTerms) {
                            $matkulQuery->where('name', 'LIKE', "%{$searchTerms[1]}%");
                        });
                })->orWhere(function ($subQuery) use ($searchTerms, $user) {
                    $subQuery->where('user_id', $user->id) // Enforce user_id condition
                        ->whereHas('lecturer', function ($lecturerQuery) use ($searchTerms) {
                            $lecturerQuery->where('name', 'LIKE', "%{$searchTerms[1]}%");
                        })->whereHas('matkul', function ($matkulQuery) use ($searchTerms) {
                            $matkulQuery->where('name', 'LIKE', "%{$searchTerms[0]}%");
                        });
                });
            } else {
                // Handle single-term search (fallback)
                $query->where(function ($subQuery) use ($searchTerms, $user) {
                    $subQuery->where('user_id', $user->id); // Enforce user_id condition
                    foreach ($searchTerms as $term) {
                        $subQuery->whereHas('lecturer', function ($lecturerQuery) use ($term) {
                            $lecturerQuery->where('name', 'LIKE', "%{$term}%");
                        })
                        ->orWhereHas('matkul', function ($matkulQuery) use ($term) {
                            $matkulQuery->where('name', 'LIKE', "%{$term}%");
                        });
                    }
                });
            }
        }
            
         // Retrieve evaluations with lecturer and matkul relationships
         $evaluations = $query->with(['lecturer', 'matkul'])->paginate(10);
       
       	// max week num
       	 $maxWeekNumber = Evaluation::where('user_id', $user->id)->max('week_number');
     
         // Pass data to the view, including search term, week, completed, and lecturer_type for the form fields
         return view('home', [
             'evaluations' => $evaluations,
             'user' => $user,
             'week' => $request->input('week'),
             'search' => $request->input('search'),
             'completed' => $request->input('completed'),
             'lecturer_type' => $request->input('lecturer_type'),
         	 'maxWeekNumber' => $maxWeekNumber,
         ]);
     }
}
