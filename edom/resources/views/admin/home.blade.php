@extends('layout.main')

@section('content')
<div class="container mt-4">

    <div class="card">
        <div class="card-header">
            <!-- Search Form -->
            <form action="{{ route('admin.home') }}" method="GET" class="float-end" style="max-width: 300px;">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search Mata Kuliah or Lecturer" name="search" value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit" id="button-search">
                        Search
                    </button>
                </div>
            </form>

            <!-- Week Filter Form -->
            <form action="{{ route('admin.home') }}" method="GET" class="d-inline-block me-3">
                <div class="input-group">
                    <select name="week" class="form-select" onchange="this.form.submit()">
                        <option value="">Filter per Minggu</option>
                        @for ($i = 10; $i <= 21; $i++) <!-- Adjust week range as needed -->
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
                            <th>Nama Dosen</th>
                            <th>Minggu Ke</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $evaluation)
                            <tr onclick="window.location='{{ route('admin.evaluation.groups', ['matkul_id' => $evaluation->matkul_id, 'lecturer_id' => $evaluation->lecturer_id, 'week' => $evaluation->week_number]) }}'"
                                style="cursor:pointer;">
                                <td>{{ $evaluation->matkul->name ?? 'N/A' }}</td>
                                <td>{{ $evaluation->lecturer->name ?? 'N/A' }}</td>
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
