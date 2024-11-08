@extends('layout.main')

@section('content')
<div class="container mt-4">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Nama</th>
                    <th>Status Evaluasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    @php
                        $evaluation = $user->evaluations->first();
                    @endphp
                    <tr style="background-color: {{ $evaluation && $evaluation->completed ? '#e2fade' : '#ffd1d1' }};">
                        <td>{{ $user->student_id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $evaluation && $evaluation->completed ? 'Sudah diisi' : 'Belum diisi' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
