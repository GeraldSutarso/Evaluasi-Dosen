<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DatabaseImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'groups' => new GroupsImport(),
            'users' => new UsersImport(),
            'lecturers' => new LecturersImport(),
            'matkuls' => new MatkulsImport(),
            'evaluations' => new EvaluationsImport(),
            'questions' => new QuestionsImport(),
        ];
    }
}
