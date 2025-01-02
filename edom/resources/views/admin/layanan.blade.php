@extends('layout.main')

@section('content')
<div class="container mt-4">
    <h1 class="mt-4"> Evaluasi Layanan</h1>
    <div class="card">
        <div class="card-header">
            <!-- Search Form -->
            <form action="{{ route('admin.layanan') }}" method="GET" class="float-end" style="max-width: 300px;">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari Nama atau ID" name="search" value="{{ request('search') }}">
                    <!-- Preserve Filters -->
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="group" value="{{ request('group') }}">
                    <button class="btn btn-outline-secondary" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Status Filter Form -->
            <form action="{{ route('admin.layanan') }}" method="GET" class="d-inline-block me-3">
                <div class="input-group">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Filter Status</option>
                        <option value="no_layanan" {{ request('status') == 'no_layanan' ? 'selected' : '' }}>Belum Ada Respon</option>
                        <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <!-- Preserve Search and Group -->
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="group" value="{{ request('group') }}">
                </div>
            </form>

            <!-- Group Filter Form -->
            <form action="{{ route('admin.layanan') }}" method="GET" class="d-inline-block me-3">
                <div class="input-group">
                    <select name="group" class="form-select" onchange="this.form.submit()">
                        <option value="">Filter Grup</option>
                        @foreach($allGroups as $groupOption)
                            <option value="{{ $groupOption->name }}" 
                                {{ request('group') == $groupOption->name ? 'selected' : '' }}>
                                {{ $groupOption->name }}
                            </option>
                        @endforeach
                    </select>
                    <!-- Preserve Search and Status -->
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                </div>
            </form>
        </div>
        <div class="card-body">
            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Grup</th>
                            <th>Status Evaluasi</th>
                        </tr>
                    </thead>
                    @php
                    $currentPage = $users->currentPage();
                    $perPage = $users->perPage();
                    $startingNumber = ($currentPage - 1) * $perPage + 1;
                    @endphp
                    <tbody>
                        @foreach($users as $index=>$user)
                            <tr
                                @if($user->status == 'no_layanan')
                                style="cursor:pointer; background-color: #ffd1d1;"
                                @else
                                    style="background-color: #e2fade;"
                                @endif
                            >
                                <td>{{ $startingNumber + $index }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->student_id }}</td>
                                <td>{{ $user->group->name }}</td>
                                <td>
                                    @if($user->status == 'no_layanan')
                                        <span class="badge bg-danger">Belum Mengevaluasi</span>
                                    @else
                                        <span class="badge bg-success">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center">
                {!! $users->appends(request()->query())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
