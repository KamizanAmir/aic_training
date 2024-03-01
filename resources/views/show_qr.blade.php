{{-- show_qr.blade.php --}}
@extends('voyager::master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            {!! $qrCode !!}
        </div>
    </div>
</div>
@endsection
