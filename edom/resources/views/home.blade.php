@extends('layout.main')

@section('content')
<div class="container mt-4">
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <!-- Week Filter Form -->
            <form action="{{ route('home') }}" method="GET" class="d-inline-block me-3">
                <div class="input-group">
                    <select name="week" class="form-select" onchange="this.form.submit()">
                        <option value="">Filter per Minggu</option>
                        @for ($i = 1; $i <= 21; $i++) <!-- Adjust week range as needed -->
                            <option value="{{ $i }}" {{ request('week') == $i ? 'selected' : '' }}>Minggu ke-{{ $i }}</option>
                        @endfor
                    </select>
                    <!-- Preserve Search Term and Lecturer Type -->
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="completed" value="{{ request('completed') }}">
                    <input type="hidden" name="lecturer_type" value="{{ request('lecturer_type') }}">
                </div>
            </form>
        
            <!-- Completed Filter Form -->
            <form action="{{ route('home') }}" method="GET" class="d-inline-block me-3">
                <div class="input-group">
                    <select name="completed" class="form-select" onchange="this.form.submit()">
                        <option value="">Filter per Status</option>
                        <option value="true" {{ request('completed') == 'true' ? 'selected' : '' }}>Sudah diisi</option>
                        <option value="false" {{ request('completed') == 'false' ? 'selected' : '' }}>Belum diisi</option>
                    </select>
                    <!-- Preserve Other Filter Values -->
                    <input type="hidden" name="week" value="{{ request('week') }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="lecturer_type" value="{{ request('lecturer_type') }}">
                </div>
            </form>
        
            <!-- Lecturer Type Filter Form -->
            <form action="{{ route('home') }}" method="GET" class="d-inline-block me-3">
                <div class="input-group">
                    <select name="lecturer_type" class="form-select" onchange="this.form.submit()">
                        <option value="">Filter per Tipe Pengajar</option>
                        <option value="1" {{ request('lecturer_type') == '1' ? 'selected' : '' }}>Dosen</option>
                        <option value="2" {{ request('lecturer_type') == '2' ? 'selected' : '' }}>Instruktur</option>
                    </select>
                    <!-- Preserve Other Filter Values -->
                    <input type="hidden" name="week" value="{{ request('week') }}">
                    <input type="hidden" name="completed" value="{{ request('completed') }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                </div>
            </form>
        
            <!-- Search Form -->
            <form action="{{ route('home') }}" method="GET" class="float-end" style="max-width: 300px;">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari Matkul atau Dosen" name="search" value="{{ request('search') }}">
                    <!-- Preserve Week, Completed, and Lecturer Type Filters -->
                    <input type="hidden" name="week" value="{{ request('week') }}">
                    <input type="hidden" name="completed" value="{{ request('completed') }}">
                    <input type="hidden" name="lecturer_type" value="{{ request('lecturer_type') }}">
                    <button class="btn btn-outline-secondary" type="submit" id="button-search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        
        
        
        <div class="card-body">
            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Mata Kuliah</th>
                            <th>Nama Pengajar</th>
                            <th>Tipe Pengajar</th>
                            <th>Status Evaluasi</th>
                            <th>Minggu Ke</th>
                        </tr>
                    </thead>
                    @php
                        $currentPage = $evaluations->currentPage();
                        $perPage = $evaluations->perPage();
                        $startingNumber = ($currentPage - 1) * $perPage + 1;
                    @endphp
            
                    <tbody>
                        @foreach($evaluations as $index => $evaluation)
                            <tr
                                @if(!$evaluation->completed)
                                    onclick="window.location='{{ route('evaluation.show', ['id' => $evaluation->id]) }}'"
                                    style="cursor:pointer; background-color: #ffd1d1;"
                                @else
                                    style="background-color: #e2fade;"
                                @endif
                            >
                                <td>{{ $startingNumber + $index }}</td>
                                <td>{{ $evaluation->matkul->name ?? 'N/A' }}</td>
                                <td>{{ $evaluation->lecturer->name ?? 'N/A' }}</td>
                                <td>@if($evaluation->lecturer->type == 1)Dosen @else Instruktur @endif</td>
                                <td>{{ $evaluation->completed ? 'Sudah diisi' : 'Belum diisi' }}</td>
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
