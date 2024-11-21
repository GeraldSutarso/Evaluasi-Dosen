<?php

namespace App\Exports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class QuestionsExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * Return the collection of questions to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Question::all(); // Export all question
    }

    /**
     * Set the headings for the exported file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'id',
            'text',
            'type',
        ];
    }
    public function title(): string
    {
        return 'questions';
    }
}
