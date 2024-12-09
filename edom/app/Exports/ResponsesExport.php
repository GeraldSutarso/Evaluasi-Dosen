<?php

namespace App\Exports;

use App\Models\Response;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ResponsesExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    /**
     * Return the collection of evaluations to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Response::all(); // Export all evaluations
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
            'evaluation_id',
            'question_id',
            'response_value',
            'created_at',
            'updated_at'
        ];
    }

    public function title(): string
    {
        return 'responses';
    }
}
