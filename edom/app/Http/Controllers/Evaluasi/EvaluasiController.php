<?php

namespace App\Http\Controllers\Evaluasi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Response;
use Illuminate\Http\RedirectResponse;

class EvaluasiController extends Controller
{
    public function show($id)
    {
        $evaluation = Evaluation::with(['matkul', 'lecturer', 'response'])->findOrFail($id);

        // Check if the evaluation exists and if it belongs to the logged-in user
        if (!$evaluation || $evaluation->user_id != Auth::id()) {
            // If the evaluation doesn't exist or doesn't belong to the logged-in user, redirect to the home page
            return redirect('/home')->withErrors(['error' => 'You are not authorized to view this evaluation.']);
        }

        // Fetch questions and group them by type
        $groupedQuestions = Question::all()->groupBy('type');

        // Return the view with evaluation details and grouped questions
        return view('evaluation.show', compact('evaluation', 'groupedQuestions'));
    }

    public function submitEvaluation(Request $request, $evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $user = Auth::user();

        // Ensure only the assigned user can submit responses for this evaluation
        if ($evaluation->user_id !== $user->id) {
            return redirect()->route('home')->withErrors(['error' => 'Unauthorized access.']);
        }

        // Validate that each response has a value from 1 to 4
        $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'required|integer|min:1|max:4',
        ]);

        // Array of question_id => selected value
        $responses = $request->input('responses');

        foreach ($responses as $questionId => $value) {
            Response::updateOrCreate(
                [
                    'evaluation_id' => $evaluation->id,
                    'question_id' => $questionId,
                ],
                [
                    'answer' => $value,
                ]
            );
        }

        // Mark the evaluation as complete
        $evaluation->complete = true;
        $evaluation->save();

        return redirect()->route('home')->withSuccess('Evaluation submitted successfully.');
    }
}
