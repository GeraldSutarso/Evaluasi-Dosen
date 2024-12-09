<?php

namespace App\Http\Controllers\Modifier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\Imports\GroupsImport;
use App\Imports\LecturersImport;
use App\Imports\MatkulsImport;
use App\Imports\EvaluationsImport;
use App\Imports\QuestionsImport;
use Illuminate\Support\Facades\Log;
use App\Imports\DatabaseImport;

class ImportController extends Controller
{
    /**
     * Show the import page.
     */
    public function index()
    {
        return view('modifier.import'); // Ensure the Blade file exists
    }

    /**
     * Handle file upload and data import.
     */


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        // Import the file
        Excel::import(new DatabaseImport, $request->file('file'));

        return back()->with('success', 'Database synchronized successfully!');
    }

}
