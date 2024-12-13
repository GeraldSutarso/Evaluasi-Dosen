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
        <a class="btn btn-danger" href="{{ URL::previous() }}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5"/>
          </svg> Kembali</a>
    </div>
</div>
@endsection
