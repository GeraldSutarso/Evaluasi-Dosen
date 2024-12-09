<?php

namespace App\Imports;

use App\Models\Evaluation;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;

class EvaluationsImport implements ToModel, WithHeadingRow, WithEvents
{
    private $importedIds = []; // To track which IDs are in the Excel file

    /**
     * Map each row to the database.
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
        $evaluations = Evaluation::updateOrCreate(
            ['id' => $row['id']], // Match by ID
            [
                'user_id' => $row['user_id'],
                'matkul_id' => $row['matkul_id'],
                'lecturer_id' => $row['lecturer_id'],
                'completed' => $row['completed'],  // Use 'completed' instead of 'completion'
                'week_number' => $row['week_number'],
            ]
        );

        // Log the user data after updateOrCreate
        Log::info('Evaluation Data: ' . json_encode($evaluations));

        // Check if the user was recently created or updated
        if ($evaluations->wasRecentlyCreated) {
            Log::info('Evaluation was recently created: ' . json_encode($evaluations));
        } else {
            Log::info('Evaluation was updated: ' . json_encode($evaluations));
        }

        return $evaluations;
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

                // Delete evaluations not present in the imported Excel file
                Evaluation::whereNotIn('id', $this->importedIds)->delete();
            },
        ];
    }
}


// class evaluationsImport implements ToModel, WithHeadingRow, WithValidation
// {
//     private $importedRows = []; // Property to track imported rows

//     /**
//      * Process each row in the file and track the rows.
//      *
//      * @param array $row
//      * @return \Illuminate\Database\Eloquent\Model|null
//      */
//     public function model(array $row)
//     {
//         // Collect the row ID for use in afterImport
//         $this->importedRows[] = $row['id'];

//         // Attempt to find the user by ID and update if exists
//         $user = User::find($row['id']);
//         if ($user) {
//             $user->update([
//                 'student_id' => $row['student_id'], // Update student_id
//                 'name' => $row['name'],           // Update name
//                 'group_id' => $row['group_id'],   // Update group_id
//             ]);

//             return null; // Return null since we're updating, not creating
//         }

//         // If the user doesn't exist, create a new one
//         return new User([
//             'id' => $row['id'],                  // Use ID as the primary key
//             'student_id' => $row['student_id'], // Import student_id
//             'name' => $row['name'],             // Import name
//             'group_id' => $row['group_id'],     // Import group_id
//         ]);
//     }

//     /**
//      * Define the rules for each column to validate data.
//      *
//      * @return array
//      */
//     public function rules(): array
//     {
//         return [
//             'id' => 'required|integer|unique:evaluations,id', // Ensure unique ID for evaluations
//             'student_id' => 'required|unique:evaluations,student_id', // Ensure unique student_id
//             'name' => 'required|string',
//             'group_id' => 'required|exists:groups,id', // Ensure group exists in the groups table
//         ];
//     }

//     /**
//      * After the import, clean up any missing evaluations.
//      */
//     public function afterImport()
//     {
//         // Get all IDs from the Excel file (collected during the import process)
//         $importedIds = $this->importedRows;

//         // Get all IDs in the database
//         $existingIds = User::pluck('id')->toArray();

//         // Find the difference (evaluations in DB but not in the file)
//         $missingIds = array_diff($existingIds, $importedIds);

//         // Delete evaluations whose IDs are missing in the Excel file
//         User::whereIn('id', $missingIds)->delete();
//     }
// }