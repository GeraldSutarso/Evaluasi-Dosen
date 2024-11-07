@extends('layout.main')

@section('content')
    <div class="container mt-5">
        <h2>Evaluasi Penilaian Dosen</h2>
        <form action="{{ route('evaluation.store') }}" method="POST">
            @csrf
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pertanyaan</th>
                        <th>Kategori</th>
                        <th>Penilaian (1-4)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $questions = [
                            ['id' => 1, 'question' => 'Dosen menyampaikan tujuan dan manfaat perkuliahan', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 2, 'question' => 'Dosen sangat siap mengajar di kelas', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 3, 'question' => 'Dosen menyediakan diktat/hand out untuk melengkapi perkuliahan', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 4, 'question' => 'Isi kurikulum sangat jelas dan membantu anda memahami matakuliah', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 5, 'question' => 'Dosen memperlihatkan penguasaan materi matakuliah', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 6, 'question' => 'Dosen mengajaran materi dengan metode yang efektif', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 7, 'question' => 'Dosen selalu memberi contoh konkrit setiap menjelaskan suatu hal', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 8, 'question' => 'Dosen sangat komunikatif', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 9, 'question' => 'Dosen menciptakan suasana kelas yang kondusif/termotivasi dan menyenangkan', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 10, 'question' => 'Dosen mengajar tidak terlalu cepat/lambat, sehingga mudah dimengerti mahasiswa', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 11, 'question' => 'Dosen selalu memberi kesempatan mahasiswa untuk bertanya', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 12, 'question' => 'Materi kuliah telah menambah/memperluas pengetahuan dan wawasan anda', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 13, 'question' => 'Mahasiswa puas telah mengikuti perkuliahan matakuliah tersebut', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 14, 'question' => 'Matakuliah tersebut sangat mudah dipahami mahasiswa', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 15, 'question' => 'Dosen memperlihatkan sikap menghormati dan mendorong/memotivasi mahasiswa', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 16, 'question' => 'Dosen terampil menggunakan sarana teknologi modern dalam memberikan perkuliahan', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 17, 'question' => 'Menggunakan metode pengajaran yang dapat meningkatkan interaksi antar mahasiswa dan mahasiswa dengan dosen', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 18, 'question' => 'Menggunakan metode pembelajaran yang mampu meningkatkan pemahaman mahasiswa', 'type' => 'KESIAPAN MENGAJAR (KM)'],
                            ['id' => 19, 'question' => 'Dosen menyelesaikan seluruh materi sesuai dengan kurikulum', 'type' => 'MATERI PENGAJARAN (MP)'],
                            ['id' => 20, 'question' => 'Dosen tidak banyak bercerita tentang hal diluar materi matakuliah yang bersangkutan', 'type' => 'MATERI PENGAJARAN (MP)'],
                            ['id' => 21, 'question' => 'Buku teks matakuliah tersebut mudah di dapat', 'type' => 'MATERI PENGAJARAN (MP)'],
                            ['id' => 22, 'question' => 'Diktat dari Dosen telah tersedia dan mudah diperoleh', 'type' => 'MATERI PENGAJARAN (MP)'],
                            ['id' => 23, 'question' => 'Penggunaan buku acuan dan literatur yang mutakhir (≤5tahun terakhir)', 'type' => 'MATERI PENGAJARAN (MP)'],
                            ['id' => 24, 'question' => 'Materi matakuliah selalu diperbaharui dengan contoh atau perkembangan terakhir', 'type' => 'MATERI PENGAJARAN (MP)'],
                            ['id' => 25, 'question' => 'Isi buku teks/diktat mudah dipahami', 'type' => 'MATERI PENGAJARAN (MP)'],
                            ['id' => 26, 'question' => 'Dosen selalu hadir memberi kuliah setiap pertemuan', 'type' => 'DISIPLIN MENGAJAR (DM)'],
                            ['id' => 27, 'question' => 'Dosen hadir dikelas tepat waktu', 'type' => 'DISIPLIN MENGAJAR (DM)'],
                            ['id' => 28, 'question' => 'Dosen tidak pernah meniadakan kuliah tanpa alasan', 'type' => 'DISIPLIN MENGAJAR (DM)'],
                            ['id' => 29, 'question' => 'Dosen meninggalkan kelas tepat waktu', 'type' => 'DISIPLIN MENGAJAR (DM)'],
                            ['id' => 30, 'question' => 'Dosen memberi penilaian yang obyektif', 'type' => 'EVALUASI MENGAJAR (EMJ)'],
                            ['id' => 31, 'question' => 'Dosen selalu memberi penjelasan tentang cara penilaian dan pembelajaran', 'type' => 'EVALUASI MENGAJAR (EMJ)'],
                            ['id' => 32, 'question' => 'Dosen selalu mengembalikan hasil tes/tugas dengan catatan/komentar', 'type' => 'EVALUASI MENGAJAR (EMJ)'],
                            ['id' => 33, 'question' => 'Materi tugas, tes, dan ujian sesuai dengan materi matakuliah dan selaras dengan isi kurikulum', 'type' => 'EVALUASI MENGAJAR (EMJ)'],
                            ['id' => 34, 'question' => 'Dosen selalu mengembalikan hasil tes/tugas kepada mahasiswa dalam waktu yang wajar', 'type' => 'EVALUASI MENGAJAR (EMJ)'],
                            ['id' => 35, 'question' => 'Dosen mudah ditemui diluar kelas', 'type' => 'KEPRIBADIAN DOSEN (KD)'],
                            ['id' => 36, 'question' => 'Sikap dan perilaku dosen pada saat pelaksanaan perkuliahan', 'type' => 'KEPRIBADIAN DOSEN (KD)'],
                            ['id' => 37, 'question' => 'Dosen berwibawa dimata mahasiswa', 'type' => 'KEPRIBADIAN DOSEN (KD)'],
                            ['id' => 38, 'question' => 'Dosen memberi pendidikan tentang nilai (values), moral, etika selain tentang materi matakuliah', 'type' => 'KEPRIBADIAN DOSEN (KD)'],
                        ];
                    @endphp

                    @foreach ($questions as $index => $q)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $q['question'] }}</td>
                            <td>{{ $q['type'] }}</td>
                            <td>
                                @for ($i = 1; $i <= 4; $i++)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="ratings[{{ $q['id'] }}]" id="q{{ $q['id'] }}_rating{{ $i }}" value="{{ $i }}" required>
                                        <label class="form-check-label" for="q{{ $q['id'] }}_rating{{ $i }}">{{ $i }}</label>
                                    </div>
                                @endfor
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" class="btn
