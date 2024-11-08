@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Lecturer Evaluation</h2>

    <!-- Form to submit the evaluation -->
    <form action="{{ route('evaluation.submit', $evaluation->id) }}" method="POST">
        @csrf

        @foreach ($groupedQuestions as $type => $questions)
            <h3>{{ $type }}</h3>
            @foreach ($questions as $question)
                <div class="form-group">
                    <label>{{ $question->text }}</label>
                    <div>
                        <!-- Radio buttons for ratings 1 to 4 -->
                        @for ($i = 1; $i <= 4; $i++)
                            <label>
                                <input type="radio" name="responses[{{ $question->id }}]" value="{{ $i }}" required>
                                {{ $i }}
                            </label>
                        @endfor
                    </div>
                </div>
            @endforeach
        @endforeach

        <button type="submit" class="btn btn-primary">Submit Evaluation</button>
    </form>
</div>
@endsection
