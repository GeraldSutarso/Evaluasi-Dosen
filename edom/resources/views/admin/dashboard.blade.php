@extends('layout.main')

@section('content')
<div class="container">
    <h1 class="mt-4">Kalender Pengisian Evaluasi</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center mt-4">
            <thead class="table-light">
                <tr>
                    <th class="sticky-header" style="width: 150px;">Group</th>
                    @foreach ($weeks->sort() as $week) <!-- Sort weeks numerically -->
                        <th class="sticky-header" style="width: 150px;">Week {{ $week }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($tableData as $groupName => $evaluations)
                    <tr>
                        <td style="width: 150px;">{{ $groupName }}</td> <!-- Fixed width for Group column -->
                        @foreach ($weeks->sort() as $week)
                            @if (isset($evaluations[$week]))
                                @php
                                    $isCompleted = $evaluations[$week]['all_completed'];
                                @endphp
                                <td class="evaluation-cell" 
                                    style="background-color: {{ $isCompleted ? '#e2fade' : '#ffd1d1' }}; width: 150px;">
                                    <a href="{{ url('/admin/home') . '?week=' . $week . '&group=' . $evaluations[$week]['group_name'] }}" 
                                    class="d-block w-100 h-100 text-decoration-none text-dark">
                                        {{ $isCompleted ? '✔' : '✘' }}
                                    </a>

                                </td>
                            @else
                                <td class="table-light evaluation-cell"></td> <!-- Blank cell for no assigned evaluations -->
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
