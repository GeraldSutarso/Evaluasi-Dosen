<?php

namespace App\Exports;

use App\Models\LayananQuestion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LayananQuestionsExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * Return the collection of questions to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return LayananQuestion::all(); // Export all question
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
        return 'mahasiswa_layanan_questions';
    }
}
