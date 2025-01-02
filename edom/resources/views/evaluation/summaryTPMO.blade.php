@extends('layout.main')

@section('content')
<div class="container mt-4">
<div class="pdf-content" style="width: 100%; margin-top: 1px; font-family: Arial, sans-serif; font-size: 10px;">
    <div style="text-align: center; margin-bottom: 2px; font-weight: bold;">
        @if($lecturer->type == 1)
            <p style="font-size: 11px; line-height: 1.4; margin: 0; padding: 0;">TABULASI HASIL DATA EVALUASI DOSEN OLEH MAHASISWA</p>
        @elseif($lecturer->type == 2)
            <p style="font-size: 11px; line-height: 1.4; margin: 0; padding: 0;">TABULASI HASIL DATA EVALUASI INSTRUKTUR OLEH MAHASISWA</p>
        @endif
        <p style="font-size: 11px; line-height: 1.4; margin: 0; padding: 0;">AKADEMI KOMUNITAS TOYOTA INDONESIA</p>
        <p style="font-size: 11px; line-height: 1.4; margin: 0; padding: 0;">PROGRAM STUDI TEKNIK PEMELIHARAAN MESIN OTOMASI</p>
        {{-- ganti sesuai tahun ajaran --}}
        <p style="font-size: 11px; line-height: 1.4; margin: 0; padding: 0;">TAHUN AJARAN {{ session('tahunajaran', '2024/2025') }}</p>
    </div>
    
    <div style="margin-bottom: 2px; text-align: left;">
        @if($lecturer->type == 1)
        <p style="margin: 0; padding: 0;">Nama Dosen: {{ $lecturer->name }}</p>
        @elseif($lecturer->type == 2)
        <p style="margin: 0; padding: 0;">Nama Instruktur: {{ $lecturer->name }}</p>
        @endif
        <p style="margin: 0; padding: 0;">Mata Kuliah: {{ $matkul->name }}</p>
        {{-- ini juga ganti sesuai tahun ajaran --}}
        <p style="margin: 0; padding: 0;">Semester {{ session('semester','I') }} Tahun Akademik {{ session('tahunajaran', '2024/2025') }}</p>
    </div>

    @php
    $totalCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
    $totalResponses = 0;
    $totalScoreSum = 0;
    $sectionAverages = [];
    $sectionTotals = [
        'KESIAPAN MENGAJAR ( KM )' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
        'MATERI PENGAJARAN ( MP )' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
        'DISIPLIN MENGAJAR ( DM )' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
        'EVALUASI MENGAJAR ( EMJ )' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
        'KEPRIBADIAN DOSEN ( KD )' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
    ];
    $grandTotal = 0;
    @endphp

    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
        <thead>
            <tr style="background-color: #ffc404; color: #333; border: 1px solid #000;">
                <th rowspan="2" style="width: 2%; text-align: center; vertical-align: middle; border: 1px solid #000;">No.</th>
                <th rowspan="2" style="width: 60%; text-align: center; vertical-align: middle; border: 1px solid #000;">Uraian Kinerja Dosen</th>
                <th colspan="5" style="text-align: center; vertical-align: middle; border: 1px solid #000;">Kriteria Penilaian</th>
                <th rowspan="2" style="width: 10%; text-align: center; vertical-align: middle; border: 1px solid #000;">Nilai Kecukupan</th>
            </tr>
            <tr style="background-color: #ffc404; color: #333; border: 1px solid #000;">
                <th style="width: 4%; text-align: center; border: 1px solid #000;">1</th>
                <th style="width: 4%; text-align: center; border: 1px solid #000;">2</th>
                <th style="width: 4%; text-align: center; border: 1px solid #000;">3</th>
                <th style="width: 4%; text-align: center; border: 1px solid #000;">4</th>
                <th style="width: 5%; text-align: center; border: 1px solid #000;">Total</th> <!-- Reduced width of 'Total' column -->
            </tr>
        </thead>
        <tbody>
            @php
                $index = 1;
                $sectionAverages = [];
            @endphp
        
            @foreach ($summary as $section => $data)
                @php
                    if (!isset($sectionTotals[$section])) {
                        $sectionTotals[$section] = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0];
                    }
                @endphp
        
                <!-- Section Header Row -->
                <tr style="background-color: #ffc404; border: 1px solid #000;">
                    <td style="text-align: center; vertical-align: middle; border: 1px solid #000;"><strong>{{ $index++ }}</strong></td>
                    <td colspan="7" style="text-align: center; vertical-align: middle; border: 1px solid #000;"><strong>{{ strtoupper($section) }}</strong></td>
                </tr>
        
                @foreach ($data['questions'] as $questionId => $questionData)
                    @php
                        $totalResponsesForQuestion = array_sum($questionData['counts']);
                        $nilaiKecukupan = $totalResponsesForQuestion > 0
                            ? (($questionData['counts'][1] ?? 0) * 1 + ($questionData['counts'][2] ?? 0) * 2 + ($questionData['counts'][3] ?? 0) * 3 + ($questionData['counts'][4] ?? 0) * 4) / $totalResponsesForQuestion
                            : 0;
        
                        foreach ([1, 2, 3, 4] as $value) {
                            $sectionTotals[$section][$value] += $questionData['counts'][$value] ?? 0;
                        }
                        $sectionTotals[$section]['total'] += $totalResponsesForQuestion;
        
                        foreach ([1, 2, 3, 4] as $value) {
                            $totalCounts[$value] += $questionData['counts'][$value] ?? 0;
                        }
        
                        $totalResponses += $totalResponsesForQuestion;
                        $totalScoreSum += $nilaiKecukupan * $totalResponsesForQuestion;
                        $sectionAverages[] = $nilaiKecukupan;
                    @endphp
        
                    <tr style="border: 1px solid #000;">
                        <td style="text-align: center; border: 1px solid #000;"></td>
                        <td>{{ $questionData['text'] }}</td>
                        <td style="text-align: center; border: 1px solid #000;">{{ $questionData['counts'][1] ?? 0 }}</td>
                        <td style="text-align: center; border: 1px solid #000;">{{ $questionData['counts'][2] ?? 0 }}</td>
                        <td style="text-align: center; border: 1px solid #000;">{{ $questionData['counts'][3] ?? 0 }}</td>
                        <td style="text-align: center; border: 1px solid #000;">{{ $questionData['counts'][4] ?? 0 }}</td>
                        <td style="text-align: center; border: 1px solid #000;">{{ $totalResponsesForQuestion }}</td>
                        <td style="text-align: center; border: 1px solid #000;">
                            {{ number_format($nilaiKecukupan, 2) }}
                        </td>
                    </tr>
                @endforeach
        
                <!-- Section Total Row -->
                <tr style=" border: 1px solid #000;">
                    <td colspan="2" style="text-align: center; border: 1px solid #000;"><strong>Total {{ strtoupper($section) }}</strong></td>
                    <td style="text-align: center; border: 1px solid #000;"><strong>{{ $sectionTotals[$section][1] }}</strong></td>
                    <td style="text-align: center; border: 1px solid #000;"><strong>{{ $sectionTotals[$section][2] }}</strong></td>
                    <td style="text-align: center; border: 1px solid #000;"><strong>{{ $sectionTotals[$section][3] }}</strong></td>
                    <td style="text-align: center; border: 1px solid #000;"><strong>{{ $sectionTotals[$section][4] }}</strong></td>
                    <td style="text-align: center; border: 1px solid #000;"><strong>{{ $sectionTotals[$section]['total'] }}</strong></td>
                    <td style="text-align: center; border: 1px solid #000;">
                        <strong>{{ $sectionTotals[$section]['total'] > 0 ? number_format((($sectionTotals[$section][1] * 1) + ($sectionTotals[$section][2] * 2) + ($sectionTotals[$section][3] * 3) + ($sectionTotals[$section][4] * 4)) / $sectionTotals[$section]['total'], 2): '-' }}</strong>
                    </td>
                </tr>
            @endforeach
        
            <!-- Grand Total Row -->
            <tr style="background-color: #ffc404; border: 1px solid #000;">
                <td colspan="2" style="text-align: center; border: 1px solid #000;"><strong>TOTAL HASIL</strong></td>
                <td style="text-align: center; border: 1px solid #000;"><strong>{{ $totalCounts[1] }}</strong></td>
                <td style="text-align: center; border: 1px solid #000;"><strong>{{ $totalCounts[2] }}</strong></td>
                <td style="text-align: center; border: 1px solid #000;"><strong>{{ $totalCounts[3] }}</strong></td>
                <td style="text-align: center; border: 1px solid #000;"><strong>{{ $totalCounts[4] }}</strong></td>
                <td style="text-align: center; border: 1px solid #000;"><strong>{{ $totalResponses }}</strong></td>
                <td style="text-align: center; border: 1px solid #000;">
                    <strong>{{ $totalResponses >0 ? number_format($totalScoreSum / $totalResponses, 2): '-' }}</strong>
                </td>
            </tr>
            <!-- Percentage Row -->
            <tr style="background-color: #ffc404; color: #333; border: 1px solid #000;">
                <td colspan="2" style="text-align: center;border: 1px solid #000;"><strong>PERSENTASE PENILAIAN</strong></td>
                <td style="text-align: center;border: 1px solid #000;">
                    <strong>{{ $totalResponses > 0 ? number_format(($totalCounts[1] / $totalResponses) * 100, 2) : '0' }}%</strong>
                </td>
                <td style="text-align: center;border: 1px solid #000;">
                    <strong>{{ $totalResponses > 0 ? number_format(($totalCounts[2] / $totalResponses) * 100, 2) : '0' }}%</strong>
                </td>
                <td style="text-align: center;border: 1px solid #000;">
                    <strong>{{ $totalResponses > 0 ? number_format(($totalCounts[3] / $totalResponses) * 100, 2) : '0' }}%</strong>
                </td>
                <td style="text-align: center;border: 1px solid #000;">
                    <strong>{{ $totalResponses > 0 ? number_format(($totalCounts[4] / $totalResponses) * 100, 2) : '0' }}%</strong>
                </td>
                <td style="text-align: center;border: 1px solid #000;">
                    <strong>{{ $totalResponses > 0 ? number_format(($totalResponses / $totalResponses) * 100, 2) : '0' }}%</strong>
                </td>
                <td style="text-align: center;border: 1px solid #000;">
                    <strong></strong>
                </td>
            </tr>
        </tbody>                
    </table>
    {{-- footer below --}}
    {{-- Keterangan --}}
    <div style = "margin-top: 1px; margin-bottom: 2px">
        <p style="margin: 0; padding: 0;">Keterangan: 1 = Tidak Setuju &nbsp;&nbsp;&nbsp; 2 = Kurang Setuju &nbsp;&nbsp;&nbsp; 3 = Cukup Setuju &nbsp;&nbsp;&nbsp; 4 = Sangat Setuju</p>
    </div>
    {{-- tanda tangan --}}
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr>
                <td style="width:50%;text-align: center; vertical-align: middle;">
                    <p>Mengetahui,<br>Wakil Direktur II</p>
                    <br>
                    <br>
                    <p style="margin-bottom:0px;text-align: center;"><u><b>Mursyid</b></u></p>
                </td>
                <td style="width:50%;text-align: center; vertical-align: middle;">
                    <p><br>Ketua Program Studi</p>
                    <br>
                    <br>
                    <p style="text-align: center;"><b><u>Praditya Alambara</u></b></p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@if (empty($isPdf))
<div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
    <a class="btn btn-danger" href="{{ URL::previous() }}">Kembali</a>
    <a class="btn btn-primary" href="{{ route('evaluation.summaryTPMO.pdf', ['matkulId' => $matkul->id, 'lecturerId' => $lecturer->id]) }}">Download PDF</a>
</div>
@endif
</div>
@endsection
