<?php

namespace App\Exports;

use App\Models\Matkul;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class MatkulsExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * Return the collection of matkuls to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Matkul::all(); // Export all matkuls
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
        ];
    }

    public function title(): string
    {
        return 'matkuls';
    }
}
