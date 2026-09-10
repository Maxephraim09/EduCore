@extends('layouts.app')

@section('title', 'Apply for Overdraft')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff-overdrafts.index') }}">Overdrafts</a></li>
    <li class="breadcrumb-item active">Apply Overdraft</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Apply for Overdraft Facility</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="overdraftForm">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Select Employee *</label>
                        <select name="employee_id" id="employee_id" class="form-control" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" data-salary="{{ $employee->base_salary + $employee->allowances }}">
                                    {{ $employee->employee_id }} - {{ $employee->full_name }} ({{ $employee->position }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Interest Rate (%) *</label>
                        <input type="number" step="0.1" name="interest_rate" id="interest_rate" class="form-control" value="12" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Requested Limit (₦) *</label>
                        <input type="number" step="0.01" name="limit_amount" id="limit_amount" class="form-control" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Expiry Date *</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control" value="{{ date('Y-m-d', strtotime('+1 year')) }}" required>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Remarks (Optional)</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="button" id="calculateBtn" class="btn btn-primary btn-lg">
                        <i class="fas fa-calculator"></i> Calculate Recommended Limit
                    </button>
                </div>
            </form>
            
            <div id="calculationResult" style="display: none;" class="mt-4">
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Employee Information</h6>
                                <p><strong>Name:</strong> <span id="empName"></span></p>
                                <p><strong>Monthly Salary:</strong> ₦<span id="empSalary">0</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6>Recommended Limit</h6>
                                <p><strong>Recommended Limit:</strong> ₦<span id="recommendedLimit">0</span></p>
                                <p><strong>Maximum Limit:</strong> ₦<span id="maxLimit">0</span></p>
                                <div class="alert alert-light text-dark mt-2">
                                    <small><i class="fas fa-info-circle"></i> Recommended limit is based on 3 months salary. Maximum limit is 6 months salary.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form id="processForm" method="POST" action="{{ route('overdrafts.store') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="employee_id" id="formEmployeeId">
                    <input type="hidden" name="limit_amount" id="formLimitAmount">
                    <input type="hidden" name="interest_rate" id="formInterestRate">
                    <input type="hidden" name="expiry_date" id="formExpiryDate">
                    <input type="hidden" name="remarks" id="formRemarks">
                    
                    <div class="alert alert-warning" id="warningMessage" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <span id="warningText"></span>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle"></i> Approve & Activate Overdraft
                        </button>
                        <button type="button" class="btn btn-secondary btn-lg" onclick="resetForm()">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#calculateBtn').click(function() {
            const employeeId = $('#employee_id').val();
            
            if (!employeeId) {
                alert('Please select an employee');
                return;
            }
            
            $.ajax({
                url: '{{ route("overdrafts.calculate") }}',
                method: 'POST',
                data: {
                    employee_id: employeeId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.error) {
                        alert(data.error);
                        if (data.existing_limit) {
                            alert('Existing Overdraft: Limit ₦' + data.existing_limit + 
                                  ', Used: ₦' + data.existing_used + 
                                  ', Available: ₦' + data.existing_available);
                        }
                        return;
                    }
                    
                    // Display employee info
                    const selectedEmployee = $('#employee_id option:selected');
                    $('#empName').text(selectedEmployee.text().split(' - ')[1]);
                    $('#empSalary').text(data.monthly_salary.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    
                    // Display recommendations
                    $('#recommendedLimit').text(data.recommended_limit.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    $('#maxLimit').text(data.max_limit.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    
                    // Set form values
                    $('#formEmployeeId').val(data.employee.id);
                    $('#formInterestRate').val($('#interest_rate').val());
                    $('#formExpiryDate').val($('#expiry_date').val());
                    $('#formRemarks').val($('#remarks').val());
                    
                    // Check if requested limit exceeds recommended
                    const requestedLimit = parseFloat($('#limit_amount').val());
                    if (requestedLimit && requestedLimit > data.recommended_limit) {
                        $('#warningText').text('Requested limit (₦' + requestedLimit.toLocaleString() + 
                            ') exceeds recommended limit. Please ensure this amount is approved by management.');
                        $('#warningMessage').show();
                    } else {
                        $('#warningMessage').hide();
                    }
                    
                    $('#calculationResult').show();
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.error || 'Could not calculate limit'));
                }
            });
        });
        
        $('#limit_amount').on('change', function() {
            const requestedLimit = parseFloat($(this).val());
            const recommendedLimit = parseFloat($('#recommendedLimit').text().replace(/,/g, ''));
            
            if (requestedLimit && recommendedLimit && requestedLimit > recommendedLimit) {
                $('#warningText').text('Requested limit (₦' + requestedLimit.toLocaleString() + 
                    ') exceeds recommended limit. Please ensure this amount is approved by management.');
                $('#warningMessage').show();
            } else {
                $('#warningMessage').hide();
            }
            
            $('#formLimitAmount').val(requestedLimit);
        });
    });
    
    function resetForm() {
        $('#overdraftForm')[0].reset();
        $('#calculationResult').hide();
        $('#warningMessage').hide();
    }
</script>
@endpush
@endsection
