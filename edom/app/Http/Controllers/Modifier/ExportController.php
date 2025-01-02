<?php

namespace App\Http\Controllers\Modifier;

use App\Models\User;
use App\Models\Group;
use App\Models\Lecturer;
use App\Models\Matkul;
use App\Models\Evaluation;
use App\Models\Question;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\GroupsExport;
use App\Exports\LecturersExport;
use App\Exports\MatkulsExport;
use App\Exports\EvaluationsExport;
use App\Exports\LayananQuestionsExport;
use App\Exports\QuestionsExport;
use App\Exports\ResponsesExport;
use App\Http\Controllers\Controller;

class ExportController extends Controller
{
    public function exportDatabase()
    {
        // Create the export with custom sheet names
        return Excel::download(new class implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {
            public function sheets(): array
            {
                return [
                    'Groups' => new GroupsExport(),
                    'Users' => new UsersExport(),
                    'Lecturers' => new LecturersExport(),
                    'Matkuls' => new MatkulsExport(),
                    'Evaluations' => new EvaluationsExport(),
                    'Questions' => new QuestionsExport(),
                  	// 'Responses' => new ResponsesExport(),
                    'LayananQuestions'=> new LayananQuestionsExport(),
                ];
            }
        }, 'download_edom_data.xlsx');
    }
}
