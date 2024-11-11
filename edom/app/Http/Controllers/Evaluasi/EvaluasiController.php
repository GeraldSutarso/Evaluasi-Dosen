<?php

namespace App\Http\Controllers\Evaluasi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Response;
use App\Models\Matkul;
use App\Models\Lecturer;
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

        // Validate that responses are provided for each question
        $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'required|in:1,2,3,4', // Ensures each response is between 1 and 4
        ]);

        // Get the evaluation based on the provided ID
        $evaluation = Evaluation::findOrFail($evaluationId);

        // Loop through each question's response and save it to the responses table
        foreach ($request->responses as $questionId => $responseValue) {
            Response::create([
                'evaluation_id' => $evaluation->id,
                'question_id' => $questionId,
                'response_value' => $responseValue,
            ]);
        }

        $evaluation->completed = true;
        $evaluation->save();

        // Redirect back with a success message
        return redirect()->route('home')->with('success', 'Evaluasi berhasil');
    

    }
    public function calculateSummary($matkulId, $lecturerId)
    {
        $matkul = Matkul::find($matkulId);
        $lecturer = Lecturer::find($lecturerId);

        $responses = Response::whereHas('evaluation', function ($query) use ($matkulId, $lecturerId) {
                $query->where('matkul_id', $matkulId)
                    ->where('lecturer_id', $lecturerId);
            })
            ->with('question')
            ->get();

        $questions = Question::all();

        $summary = [];
        $sectionTotals = [];
        $overallTotal = 0;

        foreach ($questions as $question) {
            $section = $question->type;
            $questionId = $question->id;

            if (!isset($summary[$section])) {
                $summary[$section] = [
                    'questions' => [],
                    'sectionTotal' => 0,
                ];
            }

            $counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
            $totalScore = 0;

            foreach ($responses->where('question_id', $questionId) as $response) {
                $counts[$response->response_value]++;
                $totalScore += $response->response_value;
            }

            $summary[$section]['questions'][$questionId] = [
                'text' => $question->text,
                'counts' => $counts,
                'totalScore' => $totalScore,
            ];

            $summary[$section]['sectionTotal'] += $totalScore;
        }

        foreach ($summary as $section => $data) {
            $sectionTotals[$section] = $data['sectionTotal'];
            $overallTotal += $data['sectionTotal'];
        }

        return view('evaluation.summary', compact('matkul', 'lecturer', 'summary', 'sectionTotals', 'overallTotal'));
    }

}
