@extends('layouts.app')

@section('title', 'New Loan Request')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff-loans.index') }}">Staff Loans</a></li>
    <li class="breadcrumb-item active">New Loan Request</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>New Loan Application</h5>
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

            <form id="loanForm">
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
                        <label>Loan Amount (₦) *</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Interest Rate (%) *</label>
                        <input type="number" step="0.1" name="interest_rate" id="interest_rate" class="form-control" value="10" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Tenure (Months) *</label>
                        <select name="tenure_months" id="tenure_months" class="form-control" required>
                            <option value="">Select Tenure</option>
                            <option value="3">3 months</option>
                            <option value="6">6 months</option>
                            <option value="9">9 months</option>
                            <option value="12">12 months</option>
                            <option value="18">18 months</option>
                            <option value="24">24 months</option>
                            <option value="36">36 months</option>
                            <option value="48">48 months</option>
                            <option value="60">60 months</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>First Installment Date *</label>
                        <input type="date" name="first_installment_date" id="first_installment_date" class="form-control" value="{{ date('Y-m-d', strtotime('+1 month')) }}" required>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Purpose of Loan *</label>
                        <textarea name="purpose" id="purpose" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Remarks (Optional)</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="button" id="calculateBtn" class="btn btn-primary btn-lg">
                        <i class="fas fa-calculator"></i> Calculate Loan
                    </button>
                </div>
            </form>
            
            <div id="loanResult" style="display: none;" class="mt-4">
                <hr>
                <h5 class="mb-3">Loan Calculation Summary</h5>
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
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6>Loan Calculation</h6>
                                <table class="table table-sm text-white">
                                    <tr>
                                        <td>Loan Amount:</td>
                                        <td class="text-end">₦<span id="calcAmount">0</span></td>
                                    </tr>
                                    <tr>
                                        <td>Interest Rate:</td>
                                        <td class="text-end"><span id="calcInterestRate">0</span>%</td>
                                    </tr>
                                    <tr>
                                        <td>Tenure:</td>
                                        <td class="text-end"><span id="calcTenure">0</span> months</td>
                                    </tr>
                                    <tr>
                                        <td>Interest Amount:</td>
                                        <td class="text-end">₦<span id="calcInterest">0</span></td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><strong>Total Payable:</strong></td>
                                        <td class="text-end"><strong>₦<span id="calcTotalPayable">0</span></strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Monthly Installment:</strong></td>
                                        <td class="text-end"><strong>₦<span id="calcMonthlyInstallment">0</span></strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3" id="warningMessage" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i> <span id="warningText"></span>
                </div>
                
                <form id="processLoanForm" method="POST" action="{{ route('staff-loans.store') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="employee_id" id="formEmployeeId">
                    <input type="hidden" name="amount" id="formAmount">
                    <input type="hidden" name="interest_rate" id="formInterestRate">
                    <input type="hidden" name="tenure_months" id="formTenureMonths">
                    <input type="hidden" name="monthly_installment" id="formMonthlyInstallment">
                    <input type="hidden" name="total_payable" id="formTotalPayable">
                    <input type="hidden" name="purpose" id="formPurpose">
                    <input type="hidden" name="first_installment_date" id="formFirstInstallmentDate">
                    <input type="hidden" name="remarks" id="formRemarks">
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle"></i> Approve & Disburse Loan
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
            const amount = parseFloat($('#amount').val());
            const interestRate = parseFloat($('#interest_rate').val());
            const tenureMonths = parseInt($('#tenure_months').val());
            
            if (!employeeId || !amount || !interestRate || !tenureMonths) {
                alert('Please fill all required fields');
                return;
            }
            
            $.ajax({
                url: '{{ route("staff-loans.calculate") }}',
                method: 'POST',
                data: {
                    employee_id: employeeId,
                    amount: amount,
                    interest_rate: interestRate,
                    tenure_months: tenureMonths,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    // Display employee info
                    const selectedEmployee = $('#employee_id option:selected');
                    $('#empName').text(selectedEmployee.text().split(' - ')[1]);
                    $('#empSalary').text(data.employee.base_salary + data.employee.allowances);
                    
                    // Display calculation
                    $('#calcAmount').text(data.amount.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    $('#calcInterestRate').text(data.interest_rate);
                    $('#calcTenure').text(data.tenure_months);
                    $('#calcInterest').text(data.interest.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    $('#calcTotalPayable').text(data.total_payable.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    $('#calcMonthlyInstallment').text(data.monthly_installment.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    
                    // Set form values
                    $('#formEmployeeId').val(data.employee.id);
                    $('#formAmount').val(data.amount);
                    $('#formInterestRate').val(data.interest_rate);
                    $('#formTenureMonths').val(data.tenure_months);
                    $('#formMonthlyInstallment').val(data.monthly_installment);
                    $('#formTotalPayable').val(data.total_payable);
                    $('#formPurpose').val($('#purpose').val());
                    $('#formFirstInstallmentDate').val($('#first_installment_date').val());
                    $('#formRemarks').val($('#remarks').val());
                    
                    // Show warning if monthly installment exceeds 50% of salary
                    if (data.warning) {
                        $('#warningText').text(data.message);
                        $('#warningMessage').show();
                    } else {
                        $('#warningMessage').hide();
                    }
                    
                    $('#loanResult').show();
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.error || 'Could not calculate loan'));
                }
            });
        });
    });
    
    function resetForm() {
        $('#loanForm')[0].reset();
        $('#loanResult').hide();
        $('#warningMessage').hide();
    }
</script>
@endpush
@endsection