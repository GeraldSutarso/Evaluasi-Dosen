<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Lecturer;
use App\Models\Matkul;
use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Group;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class FullDatabaseExport implements WithMultipleSheets
{
    /**
     * Return the collection of sheets.
     *
     * @return array
     */
    public function sheets(): array
    {
        return [
            'users' => new UsersExport(),
            'groups' => new GroupsExport(),
            'lecturers' => new LecturersExport(),
            'matkuls' => new MatkulsExport(),
            'evaluations' => new EvaluationsExport(),
            'questions' => new QuestionsExport(),
        ];
    }
}
