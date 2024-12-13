<?php

namespace App\Exports;

use App\Models\Group;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GroupsExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * Return the collection of groups to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Group::all(); // Export all groups
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
            'prodi',
        ];
    }

    public function title(): string
    {
        return 'groups';
    }
}
