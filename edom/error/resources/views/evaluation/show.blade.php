@extends('layout.main')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Lecturer Evaluation</h2>

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
</div>
@endsection
