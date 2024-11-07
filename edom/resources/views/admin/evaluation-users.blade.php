@extends('layout.main')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3>Evaluasi Mata Kuliah: {{ $evaluation->matkul->name ?? 'N/A' }}</h3>
            <p>Dosen: {{ $evaluation->lecturer->name ?? 'N/A' }} | Minggu ke-{{ $evaluation->week_number }}</p>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Mahasiswa</th>
                            <th>Status Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                            @php
                                $completed = $user->evaluations()
                                    ->where('matkul_id', $evaluation->matkul_id)
                                    ->where('lecturer_id', $evaluation->lecturer_id)
                                    ->where('week_number', $evaluation->week_number)
                                    ->first()->completed ?? false;
                            @endphp

                            <tr style="background-color: {{ $completed ? '#e2fade' : '#ffd1d1' }};">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $completed ? 'Sudah diisi' : 'Belum diisi' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination Links -->
            <div class="d-flex justify-content-center">
                {!! $users->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
