@extends('layout.main')

@section('content')
<div class="container mt-4">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Grup</th>
                    <th>Status Evaluasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $group)
                    <tr onclick="window.location='{{ route('admin.evaluation.group.users', ['group_id' => $group->id, 'matkul_id' => $matkul_id, 'lecturer_id' => $lecturer_id]) }}'"
                        style="cursor:pointer; background-color: {{ $group->allCompleted ? '#e2fade' : '#ffd1d1' }};">
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->allCompleted ? 'Semua Evaluasi Selesai' : 'Evaluasi Belum Selesai' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Button to view evaluation summary -->
    <div class="mt-4 text-center">
        <a href="{{ route('evaluation.summaryTPMO', ['matkulId' => $matkul_id, 'lecturerId' => $lecturer_id]) }}" class="btn btn-primary">
            Lihat Tabulasi TPMO
        </a>
        <a href="{{ route('evaluation.summaryTOPKR', ['matkulId' => $matkul_id, 'lecturerId' => $lecturer_id]) }}" class="btn btn-primary">
            Lihat Tabulasi TOPKR
        </a>
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <a class="btn btn-danger" href="{{ route('admin.home') }}">Kembali</a>
        </div>
    </div>
    
</div>
@endsection