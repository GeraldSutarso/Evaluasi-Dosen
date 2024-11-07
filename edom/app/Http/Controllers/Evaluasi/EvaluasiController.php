<?php

namespace App\Http\Controllers\Evaluasi;

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

class EvaluasiController extends Controller
{
    //
        public function show($id)
    {
        $evaluation = Evaluation::with(['matkul', 'lecturer', 'response'])->findOrFail($id);

        // Check if the evaluation exists and if it belongs to the logged-in user
        if (!$evaluation || $evaluation->user_id != Auth::id()) {
            // If the evaluation doesn't exist or doesn't belong to the logged-in user, redirect to the home page
            return redirect('/home')->withErrors(['error' => 'You are not authorized to view this evaluation.']);
        }

        // If the evaluation is valid and belongs to the user, show the evaluation details
        
        return view('evaluation.show', compact('evaluation'));
        
    }
}
