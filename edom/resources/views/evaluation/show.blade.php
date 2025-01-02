@extends('layout.main')

@section('content')
{{-- if layanan_mahasiswa_evaluasi = "closed" --}}
@if ($summaryRecord->edom_lock == 1)
<div class="container mt-4 mb-4">
    <br><br><br><br><br><br><br>
    <div class="text-center mt-5">
        <h1><svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="#ff7900" class="bi bi-cone-striped" viewBox="0 0 16 16">
            <path d="m9.97 4.88.953 3.811C10.159 8.878 9.14 9 8 9s-2.158-.122-2.923-.309L6.03 4.88C6.635 4.957 7.3 5 8 5s1.365-.043 1.97-.12m-.245-.978L8.97.88C8.718-.13 7.282-.13 7.03.88L6.275 3.9C6.8 3.965 7.382 4 8 4s1.2-.036 1.725-.098m4.396 8.613a.5.5 0 0 1 .037.96l-6 2a.5.5 0 0 1-.316 0l-6-2a.5.5 0 0 1 .037-.96l2.391-.598.565-2.257c.862.212 1.964.339 3.165.339s2.303-.127 3.165-.339l.565 2.257z"/>
        </svg></h1>
        <h1 class="display-4">EDOM dan EIOM sedang ditutup,</h1>
        <p class="lead mt-3"> silahkan coba lagi lain kali.</p>
    </div>
    <br><br><br><br><hr>
    <!-- Back Button -->
    <div>
        <a class="btn btn-danger mt-4" href="{{ URL::previous() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5"/>
            </svg> Kembali
        </a>
    </div>
</div>
{{-- blank page with big black text in the middle saying "Evaluasi Layanan sedang ditutup, silahkan coba lagi lain kali."--}}
@else
{{-- else --}}
<div class="container mt-4">
    <h2 class="mb-4">Evaluasi Layanan AKTI</h2>

    <!-- Rating Scale Illustration -->
    <div class="p-3 mb-4" style="border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
        <h5 class="mb-3 text-center">Scaling Angka</h5>
        
        <div class="rating-scale d-flex justify-content-between align-items-center" style="height: 30px; background: linear-gradient(to right, #ff9999, #ffcc66, #99cc99, #66b3ff); border-radius: 5px; position: relative;">
            <span class="text-white fw-bold" style="position: absolute; left: 0; padding-left: 8px;">1 - Tidak Setuju</span>
            <span class="text-white fw-bold" style="position: absolute; left: 25%;">2 - Kurang Setuju</span>
            <span class="text-white fw-bold" style="position: absolute; left: 50%;">3 - Cukup Setuju</span>
            <span class="text-white fw-bold" style="position: absolute; right: 0; padding-right: 8px;">4 - Sangat Setuju</span>
        </div>
        <p class="mt-3 text-muted text-center">Skor yang lebih tinggi menunjukkan evaluasi yang lebih baik. Harap pilih penilaian anda dengan hati-hati.</p>
    </div>

    <!-- Form to submit the evaluation -->
    <form action="{{ route('evaluation.submit', $evaluation->id) }}" method="POST">
        @csrf

        @php $questionNumber = 1; @endphp
        @foreach ($groupedQuestions as $type => $questions)
            <h3 class="mt-4 mb-3">{{ $type }}</h3>
            @foreach ($questions as $question)
                <div class="form-group mb-4">
                    <label class="h5 d-block mb-2">{{ $questionNumber }}. {{ $question->text }}</label>
                    <div class="d-flex gap-3">
                        <!-- Radio buttons for ratings 1 to 4 -->
                        @for ($i = 1; $i <= 4; $i++)
                            <label class="form-check-label me-3">
                                <input type="radio" name="responses[{{ $question->id }}]" value="{{ $i }}" required class="form-check-input">
                                {{ $i }}
                            </label>
                        @endfor
                    </div>
                </div>
                @php $questionNumber++; @endphp
            @endforeach
        @endforeach
        
        <button onclick='return confirm("Yakinkah anda, akan pilihan anda? Tidakkah anda ingin mempertimbangkan kembali pilihan pada setiap pertanyaan?")' type="submit" class="btn btn-primary mt-4">Submit Evaluation</button>
    </form>
</div><hr>
<div>
<a class="btn btn-danger" href="{{ URL::previous() }}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5"/>
  </svg> Kembali</a>
</div>
@endif
@endsection
