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
            // Flush session and redirect to login if not authenticated
            Session::flush();
            return redirect('login')->withErrors(['errorHome' => 'You have no access']);
        }

        $user = Auth::user();
        $query = Evaluation::query()->where('user_id', $user->id);

        // Apply week filter if specified
        if ($request->filled('week')) {
            $query->where('week_number', $request->input('week'));
        }

        // Apply completed filter if specified (true or false)
        if ($request->filled('completed')) {
            $completedStatus = $request->input('completed') == 'true' ? true : false;
            $query->where('completed', $completedStatus);
        }

        // Apply lecturer type filter if specified (1 = Dosen, 2 = Instruktur)
        if ($request->filled('lecturer_type')) {
            $lecturerType = $request->input('lecturer_type');
            $query->whereHas('lecturer', function ($query) use ($lecturerType) {
                $query->where('type', $lecturerType);
            });
        }

        // Apply search filter if specified
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->whereHas('lecturer', function ($lecturerQuery) use ($searchTerm) {
                    $lecturerQuery->where('name', 'LIKE', "%{$searchTerm}%");
                })
                ->orWhereHas('matkul', function ($matkulQuery) use ($searchTerm) {
                    $matkulQuery->where('name', 'LIKE', "%{$searchTerm}%");
                });
            });
        }

        // Retrieve evaluations with lecturer and matkul relationships
        $evaluations = $query->with(['lecturer', 'matkul'])->paginate(10);

        // Pass data to the view, including search term, week, completed, and lecturer_type for the form fields
        return view('home', [
            'evaluations' => $evaluations,
            'user' => $user,
            'week' => $request->input('week'),
            'search' => $request->input('search'),
            'completed' => $request->input('completed'),
            'lecturer_type' => $request->input('lecturer_type'),
        ]);
    }
}
