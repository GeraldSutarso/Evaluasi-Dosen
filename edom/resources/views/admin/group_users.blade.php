@extends('layout.main')

@section('content')
<div class="container mt-4">
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
	<h1>
      	@if(isset($group))
      		Grup: {{ $group->name }}
      	@else
      		Tidak ada Grup
      	@endif
  	</h1>
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
                        // Determine background color based on user status
                        $backgroundColor = match($user->status) {
                            'complete' => '#e2fade',   // Green for all evaluations completed
                            'incomplete' => '#ffd1d1', // Red for some evaluations incomplete
                            'no_evaluations' => '#d3d3d3', // Gray for no evaluations assigned
                            default => '#ffffff',      // Default to white
                        };
                    @endphp
                    <tr style="background-color: {{ $backgroundColor }};">
                        <td>{{ $user->student_id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>
                            @if($user->status === 'complete')
                                Sudah diisi
                            @elseif($user->status === 'incomplete')
                                Belum diisi
                            @else
                                Tidak Ada Evaluasi
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <a class="btn btn-danger" href="{{ URL::previous() }}">Kembali</a>
    </div>
</div>
@endsection
