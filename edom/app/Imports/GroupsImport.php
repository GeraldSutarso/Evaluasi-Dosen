<?php

namespace App\Imports;

use App\Models\Group;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;

class GroupsImport implements ToModel, WithHeadingRow, WithEvents
{
    private $importedIds = []; // Property to track imported rows

    /**
     * Process each row in the file and track the rows.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Log row data for debugging
        Log::info('Row Data: ' . json_encode($row));

        // Track the IDs that are being imported
        $this->importedIds[] = $row['id'];

        // Use updateOrCreate to either update existing records or create new ones
        $groups = Group::updateOrCreate(
            ['id' => $row['id']], // Match by ID
            [
                'name' => $row['name'],
                'prodi' =>$row['prodi'],
            ]
        );

        // Log the user data after updateOrCreate
        Log::info('Group Data: ' . json_encode($groups));

        // Check if the user was recently created or updated
        if ($groups->wasRecentlyCreated) {
            Log::info('Group was recently created: ' . json_encode($groups));
        } else {
            Log::info('group was updated: ' . json_encode($groups));
        }

        return $groups;
    }

    /**
     * Register events to perform actions after the import.
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterImport::class => function () {
                // Log the imported IDs
                Log::info('Imported IDs: ' . json_encode($this->importedIds));

                // Delete groups not present in the imported Excel file
                Group::whereNotIn('id', $this->importedIds)->delete();
            },
        ];
    }
}
