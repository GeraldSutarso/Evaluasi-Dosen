@extends('layout.main')

@section('content')
<div class="container mt-4">
    <!-- Display Lecturer Information -->
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

    <!-- Table of Groups -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Grup</th>
                    <th>Minggu ke-</th>
                    <th>Status Evaluasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $group)
                    @php
                        $backgroundColor = match($group->status) {
                            'complete' => '#e2fade',   // Green for completed
                            'incomplete' => '#ffd1d1', // Red for incomplete
                            'no_evaluations' => '#d3d3d3', // Gray for no evaluations
                            default => '#ffffff', // Default to white
                        };
                    @endphp
                    <tr onclick="window.location='{{ route('admin.evaluation.group.users', ['group_id' => $group->id, 'matkul_id' => $matkul_id, 'lecturer_id' => $lecturer_id]) }}'"
                        style="cursor:pointer; background-color: {{ $backgroundColor }};">
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->week_number ?? '-' }}</td>
                        <td>
                            @if($group->status === 'complete')
                                Semua Evaluasi Selesai
                            @elseif($group->status === 'incomplete')
                                Evaluasi Belum Selesai
                            @else
                                Tidak Ada Evaluasi
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        <form id="summary-record-form" action="{{ route('set.summary.record') }}" method="POST">
            @csrf
            <!-- Tahun Akademik/Ajaran and Semester -->
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="tahunajaran">Tahun Akademik/Ajaran:</label>
                        <input type="text" name="tahunajaran" id="tahunajaran" class="form-control" placeholder="2024/2025" value="{{ $summaryRecord->year ?? '2024/2025' }}">
                    </div>
                </div>
                <div class="col-md-5 text-right">
                    <div class="form-group">
                        <label for="semester">Semester:</label>
                        <input type="text" name="semester" id="semester" class="form-control" placeholder="I atau II" value="{{ $summaryRecord->semester ?? 'I' }}">
                    </div>
                </div>
            </div>
    
            <!-- Mengetahui and Mengetahui Name -->
            <div class="row d-flex justify-content-between align-items-center mt-3">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="mengetahui">Jabatan Mengetahui I:</label>
                        <input type="text" name="mengetahui" id="mengetahui" class="form-control" placeholder="Mengetahui" value="{{ $summaryRecord->mengetahui ?? '' }}">
                    </div>
                </div>
                <div class="col-md-5 text-right">
                    <div class="form-group">
                        <label for="mengetahui_name" id="mengetahui_name_label">
                            Nama {{ $summaryRecord->mengetahui ?? 'Mengetahui' }}
                        </label>
                        <input type="text" name="mengetahui_name" id="mengetahui_name" class="form-control" placeholder="Nama Mengetahui" value="{{ $summaryRecord->mengetahui_name ?? '' }}">
                    </div>
                </div>
            </div>
    
            <!-- Kaprodi TPMO and Kaprodi TOPKR -->
            <div class="row d-flex justify-content-between align-items-center mt-3">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="kaprodi_tpmo">Kaprodi TPMO:</label>
                        <input type="text" name="kaprodi_tpmo" id="kaprodi_tpmo" class="form-control" placeholder="Kaprodi TPMO" value="{{ $summaryRecord->kaprodi_tpmo ?? '' }}">
                    </div>
                </div>
                <div class="col-md-5 text-right">
                    <div class="form-group">
                        <label for="kaprodi_topkr">Kaprodi TOPKR:</label>
                        <input type="text" name="kaprodi_topkr" id="kaprodi_topkr" class="form-control" placeholder="Kaprodi TOPKR" value="{{ $summaryRecord->kaprodi_topkr ?? '' }}">
                    </div>
                </div>
            </div>
    
            <div class="text-right mt-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
    
    


    <!-- Button to View Evaluation Summary -->
    <div class="mt-4 text-center">
        <a href="{{ route('evaluation.summaryTPMO', ['matkulId' => $matkul_id, 'lecturerId' => $lecturer_id]) }}" class="btn btn-primary">
            Lihat Tabulasi TPMO
        </a>
        <a href="{{ route('evaluation.summaryTOPKR', ['matkulId' => $matkul_id, 'lecturerId' => $lecturer_id]) }}" class="btn btn-primary">
            Lihat Tabulasi TOPKR
        </a>
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <a class="btn btn-danger" href="{{ URL::previous() }}">Kembali</a>
            <a class="btn btn-warning" href="{{ route('admin.home') }}">Kembali ke Home</a>
        </div>
    </div>
</div>
<script>
    // Get references to the input and label
    const mengetahuiInput = document.getElementById('mengetahui');
    const mengetahuiNameLabel = document.getElementById('mengetahui_name_label');

    // Get the default value for "mengetahui" from the server
    const defaultMengetahui = mengetahuiInput.value.trim();

    // Set the initial label text based on the default value
    mengetahuiNameLabel.textContent = defaultMengetahui 
        ? `Nama ${defaultMengetahui}:` 
        : 'Nama Mengetahui:'; // Default label if empty

    // Update the label dynamically when the user types
    mengetahuiInput.addEventListener('input', function () {
        const mengetahuiValue = this.value.trim();
        mengetahuiNameLabel.textContent = mengetahuiValue 
            ? `Nama ${mengetahuiValue}:` 
            : defaultMengetahui 
                ? `Nama ${defaultMengetahui}:` 
                : 'Nama Mengetahui:'; // Revert to default if empty
    });
</script>



@endsection
