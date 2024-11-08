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
                    <input type="text" class="form-control" placeholder="Search..." name="search">
                    <button class="btn btn-outline-secondary" type="submit" id="button-search">
                        Search
                    </button>
                </div>
            </form>

            <!-- Week Filter Form -->
            <form action="{{ route('home') }}" method="GET" class="d-inline-block me-3">
                <div class="input-group">
                    <select name="week" class="form-select" onchange="this.form.submit()">
                        <option value="">Filter per Minggu</option>
                        @for ($i = 10; $i <= 21; $i++) <!-- $i <= 16 adalah total week, 16 weeks, ganti seperlunya -->
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
                            <th>Nama Dosen</th>
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
                            <tr onclick="window.location='{{ route('evaluation.show', ['id' => $evaluation->id]) }}'"
                                style="cursor:pointer; background-color: {{ $evaluation->completed ? '#e2fade' : '#ffd1d1' }};">
                                <td>{{ $startingNumber + $index }}</td>
                                <td>{{ $evaluation->matkul->name ?? 'N/A' }}</td>
                                <td>{{ $evaluation->lecturer->name ?? 'N/A' }}</td>
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
