@extends('voyager::master')

@section('page_header')
<div class="container-fluid">
    <h1 class="page-title">
        <i class="icon voyager-news"></i> Training Schedule
    </h1>
</div>
@stop

@section('content')
<div class="container">
    <h1>Training Schedule</h1>
    <table class="table">
        <thead>
            <tr>
                {{-- <th>ID</th> --}}
                <th>Training Name</th>
                <th>Departments</th>
                <th>Training Hours</th>
                <th>Training Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trainingPlans as $plan)
                {{-- Ensure $plan->training_date is a Carbon instance --}}
                @php
                    $trainingDate = \Carbon\Carbon::parse($plan->training_date);
                @endphp

                {{-- Check if training_date is today or in the future --}}
                @if($trainingDate->isToday() || $trainingDate->isFuture())
                    <tr>
                        {{-- <td>{{ $plan->id }}</td> --}}
                        <td>{{ $plan->file_name }}</td>
                        <td>{{ $plan->department }}</td>
                        <td>{{ $plan->training_hours }}</td>
                        <td>{{ $plan->training_date }}</td>
                        <td>{{ $plan->expired_date }}</td>
                        <td>
                            <a href="{{ route('training.qr.show', ['id' => $plan->id]) }}" class="btn btn-primary" target="_blank">Show QR</a>
                        </td>                
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
@endsection
