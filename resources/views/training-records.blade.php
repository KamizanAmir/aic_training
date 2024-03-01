@extends('voyager::master')

@section('page_header')
<div class="container-fluid">
    <h1 class="page-title">
        <i class="icon voyager-news"></i> Training Setup 
    </h1>
</div>
@stop

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/select2/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('content')
<div class="page-content browse container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <form action="{{ route('training-records.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="emp_id">Employee ID</label>
                            <select id="emp_id" name="emp_id" class="form-control" onchange="populateEmpName()">
                                <option value="">Select Employee</option>
                                @foreach ($newEmps as $emp)
                                    <option value="{{ $emp->emp_id }}" data-emp-name="{{ $emp->emp_name }}">{{ $emp->emp_id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="emp_name">Employee Name</label>
                            <input type="text" id="emp_name" name="emp_name" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="training_hours">Training Hours</label>
                            <input type="text" id="training_hours" name="training_hours" class="form-control" value="{{ request()->training_hours }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="training_date">Training Date</label>
                            <input type="training_date" id="training_date" name="training_date" class="form-control" value="{{ request()->training_date }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="expired_date">Expired Date</label>
                            <input type="expired_date" id="expired_date" name="expired_date" class="form-control" value="{{ request()->expired_date }}" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Record</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@section('javascript')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2/dist/js/select2.min.js"></script>
<script>
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            // If the user clicks "OK", redirect to the logout route.
            if (result.isConfirmed) {
                window.location.href = "{{ route('voyager.dashboard') }}"; 
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    @endif
    function populateEmpName() {
        var empIdSelect = document.getElementById('emp_id');
        var selectedOption = empIdSelect.options[empIdSelect.selectedIndex];
        var empName = selectedOption.getAttribute('data-emp-name');
        document.getElementById('emp_name').value = empName;

        $(document).ready(function() {
            $('#emp_id').select2({
                placeholder: "Select Employee",
                allowClear: true
            });

            $('#emp_id').on('change', function(e) {
                populateEmpName();
            });
        });

    }
</script>
@stop
@endsection