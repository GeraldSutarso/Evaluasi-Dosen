<?php

namespace App\Exports;

use App\Models\Evaluation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class EvaluationsExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * Return the collection of evaluations to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Evaluation::all(); // Export all evaluations
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
            'user_id',
            'matkul_id',
            'lecturer_id',
            'completed',
            'week_number'
        ];
    }

    public function title(): string
    {
        return 'evaluations';
    }
}
