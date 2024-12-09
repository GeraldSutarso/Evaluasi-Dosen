<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class UsersExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * Return the collection of users to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::all(); // Export all users
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
            'student_id',
            'group_id',
        ];
    }

    public function title(): string
    {
        return 'users';
    }
}
