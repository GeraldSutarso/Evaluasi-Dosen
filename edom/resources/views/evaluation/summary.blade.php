@extends('layout.main')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Evaluation Summary for Matkul and Lecturer</h2>

    <div class="mb-4">
        <h3>Matkul: {{ $matkul->name }}</h3>
        <h3>Lecturer: {{ $lecturer->name }}</h3>
    </div>

    @php
    $totalCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0]; // Total counts for each value (1-4) across all sections
    $totalResponses = 0; // Total responses across all sections
    $totalScoreSum = 0; // Total score sum across all sections for calculating Nilai Kecukupan
    $sectionAverages = []; // To store Nilai Kecukupan for each section

    // Prepare total variables per section (KM, MP, etc.)
    $sectionTotals = [
        'KESIAPAN MENGAJAR ( KM )' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
        'MATERI PENGAJARAN ( MP )' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
        // Add other sections here as needed
    ];

    $grandTotal = 0;
@endphp
<table class="table table-bordered border-dark">
    <thead>
        <tr class="bg-warning text-dark">
            <th rowspan="2" class="text-center">No.</th>
            <th rowspan="2" class="text-center">Uraian Kinerja Dosen</th>
            <th colspan="5" class="text-center">Kriteria Penilaian</th>
            <th rowspan="2" class="text-center">Nilai Kecukupan</th>
        </tr>
        <tr class="bg-warning text-dark">
            <th class="text-center">1</th>
            <th class="text-center">2</th>
            <th class="text-center">3</th>
            <th class="text-center">4</th>
            <th class="text-center">Total</th>
        </tr>
    </thead>
    <tbody>
        @php
            $index = 1;
            $sectionAverages = []; // Reset section averages array for calculation
        @endphp

        @foreach ($summary as $section => $data)
            @php
                // Ensure the section exists in sectionTotals, otherwise initialize it
                if (!isset($sectionTotals[$section])) {
                    $sectionTotals[$section] = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0];
                }
            @endphp

            <!-- Section type row with background color and black border -->
            <tr class="table-secondary border-dark">
                <td class="text-center bg-warning text-dark border-dark"><strong>{{ $index++ }}</strong></td> <!-- Numbered row for KM, MP, etc. -->
                <td colspan="7" class="text-center bg-warning text-dark border-dark"><strong>{{ strtoupper($section) }}</strong></td> <!-- Question type name without numbering -->
            </tr>

            @foreach ($data['questions'] as $questionId => $questionData)
                @php
                    $totalResponsesForQuestion = array_sum($questionData['counts']);
                    $nilaiKecukupan = $totalResponsesForQuestion > 0
                        ? (($questionData['counts'][1] ?? 0) * 1 + ($questionData['counts'][2] ?? 0) * 2 + ($questionData['counts'][3] ?? 0) * 3 + ($questionData['counts'][4] ?? 0) * 4) / $totalResponsesForQuestion
                        : 0;

                    // Update section totals for each response value (1-4)
                    foreach ([1, 2, 3, 4] as $value) {
                        $sectionTotals[$section][$value] += $questionData['counts'][$value] ?? 0;
                    }
                    $sectionTotals[$section]['total'] += $totalResponsesForQuestion;

                    // Update global counts
                    foreach ([1, 2, 3, 4] as $value) {
                        $totalCounts[$value] += $questionData['counts'][$value] ?? 0;
                    }

                    $totalResponses += $totalResponsesForQuestion;
                    $totalScoreSum += $nilaiKecukupan * $totalResponsesForQuestion;

                    // Store the Nilai Kecukupan for averaging later
                    $sectionAverages[] = $nilaiKecukupan;
                @endphp

                <tr class="border-dark">
                    <td class="text-center"></td> <!-- No number here for individual questions -->
                    <td>{{ $questionData['text'] }}</td>
                    <td class="text-center">{{ $questionData['counts'][1] ?? 0 }}</td>
                    <td class="text-center">{{ $questionData['counts'][2] ?? 0 }}</td>
                    <td class="text-center">{{ $questionData['counts'][3] ?? 0 }}</td>
                    <td class="text-center">{{ $questionData['counts'][4] ?? 0 }}</td>
                    <td class="text-center">{{ $totalResponsesForQuestion }}</td>
                    <td class="text-center">{{ number_format($nilaiKecukupan, 2) }}</td>
                </tr>
            @endforeach

            <!-- Total per section (KM, MP, etc.) -->
            <tr class="bg-warning text-dark border-dark">
                <td colspan="2" class="text-end"><strong>Total {{ strtoupper($section) }}</strong></td>
                <td class="text-center"><strong>{{ $sectionTotals[$section][1] }}</strong></td>
                <td class="text-center"><strong>{{ $sectionTotals[$section][2] }}</strong></td>
                <td class="text-center"><strong>{{ $sectionTotals[$section][3] }}</strong></td>
                <td class="text-center"><strong>{{ $sectionTotals[$section][4] }}</strong></td>
                <td class="text-center"><strong>{{ $sectionTotals[$section]['total'] }}</strong></td>
                <td class="text-center">
                    <strong>
                        {{ $sectionTotals[$section]['total'] > 0 ? number_format(
                            (($sectionTotals[$section][1] * 1) + ($sectionTotals[$section][2] * 2) + ($sectionTotals[$section][3] * 3) + ($sectionTotals[$section][4] * 4)) / $sectionTotals[$section]['total'],
                            2
                        ) : '-' }}
                    </strong>
                </td>
            </tr>
        @endforeach

        <!-- Total Hasil Row (Summing all sections) -->
        <tr class="bg-warning text-dark border-dark">
            <td colspan="2" class="text-end"><strong>Total Hasil</strong></td>
            <td class="text-center"><strong>{{ $totalCounts[1] }}</strong></td>
            <td class="text-center"><strong>{{ $totalCounts[2] }}</strong></td>
            <td class="text-center"><strong>{{ $totalCounts[3] }}</strong></td>
            <td class="text-center"><strong>{{ $totalCounts[4] }}</strong></td>
            <td class="text-center"><strong>{{ $totalResponses }}</strong></td>
            <td class="text-center">
                <strong>
                    {{ $totalResponses > 0 ? number_format($totalScoreSum / $totalResponses, 2) : '-' }}
                </strong>
            </td>
        </tr>

        <!-- Percentage Row -->
        <tr class="bg-warning text-dark border-dark">
            <td colspan="2" class="text-end"><strong>Percentage</strong></td>
            <td class="text-center">
                <strong>{{ $totalResponses > 0 ? number_format(($totalCounts[1] / $totalResponses) * 100, 2) : '0' }}%</strong>
            </td>
            <td class="text-center">
                <strong>{{ $totalResponses > 0 ? number_format(($totalCounts[2] / $totalResponses) * 100, 2) : '0' }}%</strong>
            </td>
            <td class="text-center">
                <strong>{{ $totalResponses > 0 ? number_format(($totalCounts[3] / $totalResponses) * 100, 2) : '0' }}%</strong>
            </td>
            <td class="text-center">
                <strong>{{ $totalResponses > 0 ? number_format(($totalCounts[4] / $totalResponses) * 100, 2) : '0' }}%</strong>
            </td>
            <td class="text-center"><strong>100%</strong></td>
            <td class="text-center"></td>
        </tr>
    </tbody>
</table>

@endsection
