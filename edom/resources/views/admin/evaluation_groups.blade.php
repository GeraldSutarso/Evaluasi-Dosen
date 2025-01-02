@extends('layout.main')

@section('content')
<div class="container mt-4">
    <!-- Display Lecturer Information -->
    <h2>
        @if(isset($lecturer))
            {{ $lecturer->type == 1 ? 'Dosen' : 'Instruktur' }}: {{ $lecturer->name }}
        @else
            Pengajar Tidak Ditemukan
        @endif
    </h2>
    <h3>
        @if(isset($matkul))
            Mata Kuliah: {{ $matkul->name }}
        @else
            Mata Kuliah Tidak Ditemukan
        @endif
    </h3>
    <hr>

    <!-- Table of Groups -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Grup</th>
                    <th>Minggu ke-</th>
                    <th>Status Evaluasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $group)
                    @php
                        $backgroundColor = match($group->status) {
                            'complete' => '#e2fade',   // Green for completed
                            'incomplete' => '#ffd1d1', // Red for incomplete
                            'no_evaluations' => '#d3d3d3', // Gray for no evaluations
                            default => '#ffffff', // Default to white
                        };
                    @endphp
                    <tr onclick="window.location='{{ route('admin.evaluation.group.users', ['group_id' => $group->id, 'matkul_id' => $matkul_id, 'lecturer_id' => $lecturer_id]) }}'"
                        style="cursor:pointer; background-color: {{ $backgroundColor }};">
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->week_number ?? '-' }}</td>
                        <td>
                            @if($group->status === 'complete')
                                Semua Evaluasi Selesai
                            @elseif($group->status === 'incomplete')
                                Evaluasi Belum Selesai
                            @else
                                Tidak Ada Evaluasi
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <hr>
    <!-- Button to View Evaluation Summary -->
    <div class="mt-4 text-center">
        <a href="{{ route('evaluation.summaryTPMO', ['matkulId' => $matkul_id, 'lecturerId' => $lecturer_id]) }}" class="btn btn-primary">
            Lihat Tabulasi TPMO
        </a>
        <a href="{{ route('evaluation.summaryTOPKR', ['matkulId' => $matkul_id, 'lecturerId' => $lecturer_id]) }}" class="btn btn-primary">
            Lihat Tabulasi TOPKR
        </a>

        <hr>
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <a class="btn btn-danger" href="{{ URL::previous() }}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5"/>
              </svg> Kembali</a>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <a class="btn btn-warning" href="{{ route('admin.home') }}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z"/>
              </svg> Kembali ke Home</a>
        </div>
    </div>
</div>
@endsection
