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
            <!-- Search Form -->
            <form action="{{ route('search') }}" method="GET" class="float-end" style="max-width: 300px;">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari..." name="search">
                    <button class="btn btn-outline-secondary" type="submit" id="button-search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                          </svg>
                    </button>
                </div>
            </form>

            <!-- Week Filter Form -->
            <form action="{{ route('home') }}" method="GET" class="d-inline-block me-3">
                <div class="input-group">
                    <select name="week" class="form-select" onchange="this.form.submit()">
                        <option value="">Filter per Minggu</option>
                        @for ($i = 1; $i <= 21; $i++) <!-- $i <= 16 adalah total week, 16 weeks, ganti seperlunya -->
                            <option value="{{ $i }}" {{ request('week') == $i ? 'selected' : '' }}>Minggu ke-{{ $i }}</option>
                        @endfor
                    </select>
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
                {!! $evaluations->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
