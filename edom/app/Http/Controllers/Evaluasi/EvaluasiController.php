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
use App\Models\LayananQuestion;
use App\Models\LayananResponse;
use App\Models\SummaryRecord;
use Dompdf\Dompdf;
use Dompdf\Options;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;

class EvaluasiController extends Controller
{
    public function show($id)
    {
        $summaryRecord = SummaryRecord::latest()->first() ?? new SummaryRecord();
        // Retrieve the evaluation with its related matkul, lecturer
        $evaluation = Evaluation::with(['matkul', 'lecturer'])->findOrFail($id);

        // Check if the evaluation exists and belongs to the logged-in user
        if (!$evaluation || $evaluation->user_id != Auth::id()) {
            // Redirect if the evaluation is not accessible
            return redirect('/home')->withErrors(['error' => 'Sebaiknya jangan gegabah']);
        }

        // Check if the evaluation is already completed
        if ($evaluation->completed) {
            return redirect('/home')->withErrors(['error' => 'Kan sudah diisi, jangan diisi lagi ya']);
        }

        // Fetch questions and group them by type
        $groupedQuestions = Question::all()->groupBy('type');

        // Fetch the lecturer and matkul details from the evaluation
        $lecturer = $evaluation->lecturer; // From the `Evaluation::with` eager load
        $matkul = $evaluation->matkul;     // From the `Evaluation::with` eager load

        // Return the view with evaluation details, grouped questions, and additional data
        return view('evaluation.show', compact('summaryRecord','evaluation', 'groupedQuestions', 'lecturer', 'matkul'));
    }
    public function layananMahasiswa()
    {

        // HALAHA AAAA
        $summaryRecord = SummaryRecord::latest()->first();
        $groupedQuestions = LayananQuestion::all()->groupBy('type');
        $shitExists = LayananResponse::where('user_id', Auth::id())->exists();
        $alreadySubmitted = false;
        if ($shitExists) {
            return view('evaluation.layanan', [
                'alreadySubmitted' => true,
                'groupedQuestions' => $groupedQuestions,
            ]);
        }

        return view('evaluation.layanan', compact('summaryRecord','groupedQuestions','alreadySubmitted'));
    }

    public function submitLayananM(Request $request)
    {
        $user = Auth::user();

        // Validate that responses are provided for each question
        $request->validate([
            'responses' => 'required|array',
        ]);

        // Loop through each question's response
        foreach ($request->responses as $questionId => $responseValue) {
            // Check if a response already exists for this user and question
            $existingResponse = LayananResponse::where('user_id', $user->id)
                ->where('question_id', $questionId)
                ->exists();

            if ($existingResponse) {
                return redirect()->route('home')->withErrors(['error' => 'Sudah pernah diisi.']);
            }

            // Save the new response
            LayananResponse::create([
                'user_id' => $user->id,
                'name'=>$user->name,
                'grup'=>$user->group_id->name,
                'question_id' => $questionId,
                'response_value' => $responseValue,
            ]);
        }

        // Redirect back with a success message
        return redirect()->route('home')->with('success', 'Evaluasi layanan Berhasil dikumpul.');
    }

    public function downloadPDF($matkulId, $lecturerId)
    {
        // Fetch the summary data
        $summaryData = $this->getSummaryData($matkulId, $lecturerId);
        
        // Fetch lecturer and matkul names (assuming you have these relationships set up in your models)
        $lecturer = Lecturer::find($lecturerId);
        $matkul = Matkul::find($matkulId);

        // Check if both lecturer and matkul exist
        if (!$lecturer || !$matkul) {
            abort(404, 'Lecturer or Matkul not found');
        }

        // Add lecturer and matkul names to the summary data
        $summaryData['lecturerName'] = $lecturer->name;
        $summaryData['matkulName'] = $matkul->name;

        // Add a flag to indicate this is for PDF download
        $summaryData['isPdf'] = true;
        
        // Prepare the PDF content (render the Blade view into HTML)
        $html = view('evaluation.summary', $summaryData)->render();

        // Initialize Dompdf with options to enable HTML5 rendering and styles
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);

        // Load the HTML content
        $dompdf->loadHtml($html);

        // Set paper size (A4)
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (first pass)
        $dompdf->render();

        // Generate the filename using the lecturer and matkul names
        $filename = "Tabulasi EDOM/EIOM {$lecturer->name}-{$matkul->name}.pdf";

        // Stream the generated PDF with the custom filename
        return $dompdf->stream($filename);
    }

        
    public function getSummaryData($matkulId, $lecturerId)
    {
        // Fetch the required data
        $matkul = Matkul::find($matkulId);
        $lecturer = Lecturer::find($lecturerId);
    
        $responses = Response::whereHas('evaluation', function ($query) use ($matkulId, $lecturerId) {
            $query->where('matkul_id', $matkulId)
                  ->where('lecturer_id', $lecturerId);
        })
        ->with('question')
        ->get();
    
        $questions = Question::all();
    
        // Initialize the summary array and other necessary variables
        $summary = [];
        $sectionTotals = [];
        $overallTotal = 0;
    
        // Iterate through each question and build the summary
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
    
            // Loop through responses for the current question
            foreach ($responses->where('question_id', $questionId) as $response) {
                $counts[$response->response_value]++;
                $totalScore += $response->response_value;
            }
    
            // Add the question's data to the summary
            $summary[$section]['questions'][$questionId] = [
                'text' => $question->text,
                'counts' => $counts,
                'totalScore' => $totalScore,
            ];
    
            // Add the question's total score to the section total
            $summary[$section]['sectionTotal'] += $totalScore;
        }
    
        // Calculate the section totals and overall total
        foreach ($summary as $section => $data) {
            $sectionTotals[$section] = $data['sectionTotal'];
            $overallTotal += $data['sectionTotal'];
        }
    
        // Return the data to be used in the view
        return compact('matkul', 'lecturer', 'summary', 'sectionTotals', 'overallTotal');
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

    public function calculateSummaryTPMO($matkulId, $lecturerId)
    {
        $matkul = Matkul::find($matkulId);
        $lecturer = Lecturer::find($lecturerId);

        // Retrieve responses for groups where prodi is "TPMO"
        $responses = Response::whereHas('evaluation', function ($query) use ($matkulId, $lecturerId) {
                $query->where('matkul_id', $matkulId)
                    ->where('lecturer_id', $lecturerId)
                    ->whereHas('user.group', function ($query) {
                        $query->where('prodi', 'TPMO');
                    });
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

        return view('evaluation.summaryTPMO', compact('matkul', 'lecturer', 'summary', 'sectionTotals', 'overallTotal'));
    }

    public function calculateSummaryTOPKR($matkulId, $lecturerId)
    {
        $matkul = Matkul::find($matkulId);
        $lecturer = Lecturer::find($lecturerId);

        // Retrieve responses specifically for group IDs 3 to 8
        $responses = Response::whereHas('evaluation', function ($query) use ($matkulId, $lecturerId) {
            $query->where('matkul_id', $matkulId)
                ->where('lecturer_id', $lecturerId)
                ->whereHas('user.group', function ($query) {
                    $query->where('prodi', 'TOPKR4');
                });
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

        return view('evaluation.summaryTOPKR', compact('matkul', 'lecturer', 'summary', 'sectionTotals', 'overallTotal'));
    }
    public function downloadTPMO($matkulId, $lecturerId)
    {
        // Fetch the summary data specifically for group IDs 1 and 2
        $summaryData = $this->getTPMOdata($matkulId, $lecturerId);
        
        $lecturer = Lecturer::find($lecturerId);
        $matkul = Matkul::find($matkulId);
    
        if (!$lecturer || !$matkul) {
            abort(404, 'Lecturer or Matkul not found');
        }
    
        $summaryData['lecturerName'] = $lecturer->name;
        $summaryData['matkulName'] = $matkul->name;
        $summaryData['isPdf'] = true;
    
        $html = view('evaluation.summaryTPMO', $summaryData)->render();
    
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
    
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        $filename = "Tabulasi EDOM-EIOM TPMO {$lecturer->name}-{$matkul->name}.pdf";
        return $dompdf->stream($filename);
    }
    
    public function downloadTOPKR($matkulId, $lecturerId)
    {
        // Fetch the summary data specifically for group IDs 3 to 8
        $summaryData = $this->getTOPKRdata($matkulId, $lecturerId);
    
        $lecturer = Lecturer::find($lecturerId);
        $matkul = Matkul::find($matkulId);
    
        if (!$lecturer || !$matkul) {
            abort(404, 'Lecturer or Matkul not found');
        }
    
        $summaryData['lecturerName'] = $lecturer->name;
        $summaryData['matkulName'] = $matkul->name;
        $summaryData['isPdf'] = true;
    
        $html = view('evaluation.summaryTOPKR', $summaryData)->render();
    
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
    
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        $filename = "Tabulasi EDOM-EIOM TOPKR4 {$lecturer->name}-{$matkul->name}.pdf";
        return $dompdf->stream($filename);
    }
    public function getTPMOdata($matkulId, $lecturerId)
    {
        $matkul = Matkul::find($matkulId);
        $lecturer = Lecturer::find($lecturerId);

        $responses = Response::whereHas('evaluation', function ($query) use ($matkulId, $lecturerId) {
                $query->where('matkul_id', $matkulId)
                    ->where('lecturer_id', $lecturerId)
                    ->whereHas('user', function ($query) {
                        $query->whereIn('group_id', [1, 2]);
                    });
            })
            ->with('question')
            ->get();

        return $this->processSummaryData($matkul, $lecturer, $responses);
    }

    public function getTOPKRdata($matkulId, $lecturerId)
    {
        $matkul = Matkul::find($matkulId);
        $lecturer = Lecturer::find($lecturerId);

        $responses = Response::whereHas('evaluation', function ($query) use ($matkulId, $lecturerId) {
                $query->where('matkul_id', $matkulId)
                    ->where('lecturer_id', $lecturerId)
                    ->whereHas('user', function ($query) {
                        $query->whereIn('group_id', [3, 4, 5, 6, 7, 8]);
                    });
            })
            ->with('question')
            ->get();

        return $this->processSummaryData($matkul, $lecturer, $responses);
    }
    protected function processSummaryData($matkul, $lecturer, $responses)
    {
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

        return compact('matkul', 'lecturer', 'summary', 'sectionTotals', 'overallTotal');
    }


}
