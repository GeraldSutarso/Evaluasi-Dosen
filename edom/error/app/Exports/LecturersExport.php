<?php

namespace App\Exports;

use App\Models\Lecturer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LecturersExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * Return the collection of lecturers to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Lecturer::all(); // Export all lecturers
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
            'name',
            'type',
        ];
    }

    public function title(): string
    {
        return 'lecturers';
    }
}
