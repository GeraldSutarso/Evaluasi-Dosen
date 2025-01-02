<?php

namespace App\Imports;

use App\Models\LayananQuestion;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;

class LayananQuestionsImport implements ToModel, WithHeadingRow, WithEvents
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
        $layananquestions = LayananQuestion::updateOrCreate(
            ['id' => $row['id']], // Match by ID
            [
                'text' => $row['text'],
                'type' => $row['type'],
            ]
        );

        // Log the user data after updateOrCreate
        Log::info('LayananQuestion Data: ' . json_encode($layananquestions));

        // Check if the user was recently created or updated
        if ($layananquestions->wasRecentlyCreated) {
            Log::info('LayananQuestion was recently created: ' . json_encode($layananquestions));
        } else {
            Log::info('LayananQuestion was updated: ' . json_encode($layananquestions));
        }

        return $layananquestions;
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

                // Delete layananquestions not present in the imported Excel file
                LayananQuestion::whereNotIn('id', $this->importedIds)->delete();
            },
        ];
    }
}
