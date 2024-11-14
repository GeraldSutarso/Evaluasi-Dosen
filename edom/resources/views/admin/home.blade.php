@extends('layout.main')

@section('content')
<div class="container mt-4">

    <div class="card">
        <div class="card-header">
            <!-- Search Form -->
            <form action="{{ route('admin.home') }}" method="GET" class="float-end" style="max-width: 300px;">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari Matkul atau Dosen" name="search" value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit" id="button-search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                          </svg>
                    </button>
                </div>
            </form>

            <!-- Week Filter Form -->
            <form action="{{ route('admin.home') }}" method="GET" class="d-inline-block me-3">
                <div class="input-group">
                    <select name="week" class="form-select" onchange="this.form.submit()">
                        <option value="">Filter per Minggu</option>
                        @for ($i = 1; $i <= 21; $i++) <!-- Adjust week range as needed -->
                            <option value="{{ $i }}" {{ request('week') == $i ? 'selected' : '' }}>Minggu ke-{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
        
        <div class="card-body">
            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Nama Pengajar</th>
                            <th>Tipe Pengajar</th>
                            <th>Minggu Ke</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $evaluation)
                            <tr onclick="window.location='{{ route('admin.evaluation.groups', ['matkul_id' => $evaluation->matkul_id, 'lecturer_id' => $evaluation->lecturer_id]) }}'"
                                style="cursor:pointer;">
                                <td>{{ $evaluation->matkul->name ?? 'N/A' }}</td>
                                <td>{{ $evaluation->lecturer->name ?? 'N/A' }}</td>
                                <td>@if($evaluation->lecturer->type == 1)Dosen @else Instruktur @endif</td>
                                <td>{{ $evaluation->week_number }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center">
                {!! $evaluations->appends(request()->query())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
