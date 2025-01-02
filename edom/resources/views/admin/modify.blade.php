@extends('layout.main')
@section('styles')
<style>
    /* Page-specific Left Navigation Bar */
    .page-sidebar {
        position: sticky;
        top: 0;
        height: 100vh;
        z-index: 900;
        padding-top: 20px;
        border-right: 2px solid #ddd;
        background-color: #f8f9fa;
        overflow-y: auto; /* Allow scrolling if content overflows */
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Add subtle shadow for depth */
    }
    
    .page-sidebar .list-group {
        padding: 0; /* Remove default padding */
    }
    
    .page-sidebar .list-group-item {
        cursor: pointer;
        font-size: 1rem;
        padding: 12px 20px; /* Add padding for better touch targets */
        border: none; /* Remove borders */
        border-radius: 0; /* Remove border radius for a flat look */
        transition: background-color 0.3s, color 0.3s; /* Smooth background and color transition */
    }
    
    .page-sidebar .list-group-item:hover {
        background-color: #e9ecef; /* Highlight on hover */
        color: #007bff; /* Change text color on hover */
    }
    
    .page-sidebar .list-group-item.active {
        background-color: #007bff; /* Active item background */
        color: white; /* Active item text color */
        font-weight: bold; /* Make active item text bold */
    }
    
    .col-md-9 {
        padding-left: 25px; /* Add padding to the main content area */
    }
    
    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }
</style>

@endsection
@section('content')


<div class="container-fluid mt-4">
    <div class="row">
        <!-- Page-specific Left Navigation Bar (Avoiding 'sidebar' ID conflict) -->
        <div class="col-md-2 page-sidebar sticky-top">
            <h4 class="mt-4"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-building-gear" viewBox="0 0 16 16">
                <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6.5a.5.5 0 0 1-1 0V1H3v14h3v-2.5a.5.5 0 0 1 .5-.5H8v4H3a1 1 0 0 1-1-1z"/>
                <path d="M4.5 2a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm4.386 1.46c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
              </svg> Manage Website</h4>
            <div class="list-group">
                <a href="#ubahData" class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                  </svg> Ubah Data</a>
                <a href="#lock" class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-lock2" viewBox="0 0 16 16">
                    <path d="M10 7v1.076c.54.166 1 .597 1 1.224v2.4c0 .816-.781 1.3-1.5 1.3h-3c-.719 0-1.5-.484-1.5-1.3V9.3c0-.627.46-1.058 1-1.224V7a2 2 0 1 1 4 0M7 7v1h2V7a1 1 0 0 0-2 0"/>
                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                  </svg> Kunci Form Evaluasi</a>
                <a href="#config" class="list-group-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                    <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                    <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                  </svg> Konfigurasi</a>
            </div>
        </div>
        <div class="col-md-9 col-lg-10" id="ubahData">
            <h1><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-building-gear" viewBox="0 0 16 16">
                <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6.5a.5.5 0 0 1-1 0V1H3v14h3v-2.5a.5.5 0 0 1 .5-.5H8v4H3a1 1 0 0 1-1-1z"/>
                <path d="M4.5 2a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm4.386 1.46c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
              </svg> Manajemen App</h1>
            <p class="lead">Data dapat dimanage lewat sini, <strong> tolong hati-hati</strong> saat menggunakan fitur manajemen data, karena data kebanyakan saling berhubungan.</p>

            <!-- Buttons for Import and Export -->
            <div class="row">
                <div class="col-md-6">
                    <h3>Import/Upload Data dalam Excel</h3>
                    <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Pilih file Excel untuk diupload</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                        </svg>  Upload Data</button>
                    </form>
                </div>

                <div class="col-md-6">
                    <h3>Export/Download Data dalam Excel</h3>
                    <p>Klik untuk mendownload data</p>
                    <a href="{{ route('export.process') }}" class="btn btn-success mt-3"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                    </svg>  Download Data</a>
                </div>
            </div>
            <hr>
            <!-- Pembantaian data evaluasi -->
            <div class = "row">
                <h3>Hapus Data Evaluasi</h3>
                <p class="lead">Gunakan ini jika ingin mengganti Tahun ajaran/Semester dan ingin membersihkan sheet data evaluasi. Seluruh data evaluasi akan<strong> hilang</strong>, namun data responsnya akan tetap<strong> tersimpan</strong> pada tabel records di database.</p>
                <div class="col-md-6">
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                        </svg> Hapus & Backup data Evaluasi
                    </button>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteAllModalLabel">Yakin menghapus data evaluasi?</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Data evaluasi nanti akan hilang, namun data respons mahasiswa akan dibackup sehingga tidak hilang. Agar lebih aman, baiknya didownload dulu excel dan disimpan sebagai back up. Kalau sudah yakin, tekan ya, kalau tidak yakin tekan tidak.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                                <form action="{{ route('evaluations.delete.all') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Ya</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row mt-4" id="lock">
                <h3>Kunci Form Evaluasi Dari Mahasiswa</h3>
                <p class="lead">Gunakan ini untuk mengunci evaluasi dari mahasiswa. Setelah evaluasi dikunci, mahasiswa tidak bisa lagi mengisi evaluasi. Namun, data respons mahasiswa tetap tersimpan.</p>
                
                <!-- Buttons for edom_lock -->
                <div class="col-md-6">
                    <form method="POST" action="{{ route('toggleLock', ['field' => 'edom_lock']) }}">
                        @csrf
                        <button type="submit" class="btn btn-{{ $summaryRecord->edom_lock ? 'success' : 'danger' }}">
                            @if($summaryRecord->edom_lock)
                                <!-- Unlock Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-unlock-fill" viewBox="0 0 16 16">
                                    <path d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2"/>
                                </svg> 
                                Buka Evaluasi Pengajar
                            @else
                                <!-- Lock Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
                                </svg> 
                                Kunci Evaluasi Pengajar
                            @endif
                        </button>
                    </form>
                </div>
            
                <!-- Buttons for layanan_lock -->
                <div class="col-md-6">
                    <form method="POST" action="{{ route('toggleLock', ['field' => 'layanan_lock']) }}">
                        @csrf
                        <button type="submit" class="btn btn-{{ $summaryRecord->layanan_lock ? 'success' : 'danger' }}">
                            @if($summaryRecord->layanan_lock)
                                <!-- Unlock Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-unlock-fill" viewBox="0 0 16 16">
                                    <path d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2"/>
                                </svg> 
                                Buka Evaluasi Layanan
                            @else
                                <!-- Lock Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
                                </svg> 
                                Kunci Evaluasi Layanan
                            @endif
                        </button>
                    </form>
                </div>
            </div>
            
            
            <hr>
            <div class="row mt-4" id="config">
                <h3>Konfigurasi Informasi untuk Website</h3>
                <p class="lead">Konfigurasi informasi general dari website ini seperti Tahun ajaran, penandatangan tabulasi, dan lain-lain</p>

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
                    <input type="text" name="edom_lock" id="edom_lock" class="form-control" hidden value="{{ $summaryRecord->edom_lock ?? '0' }}">
                    <input type="text" name="layanan_lock" id="layanan_lock" class="form-control" hidden value="{{ $summaryRecord->layanan_lock ?? '0' }}">
                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                            <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>
                        </svg> Simpan</button>
                    </div>
                </form>
            </div>
            <br><hr>
            <div class="row">
                <div class="col-md-6">
                    <a class="btn btn-danger" href="{{ URL::previous() }}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5"/>
                    </svg> Kembali</a>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ asset('Guide Pengisian Data.pdf') }}" class="btn btn-secondary" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-text" viewBox="0 0 16 16">
                            <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/>
                            <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                            <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
                        </svg> Download Guide (Outdated)
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
@section('scripts')
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