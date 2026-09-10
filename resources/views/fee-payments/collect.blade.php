@extends('layouts.app')

@section('title', 'Collect Payment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('fee-payments.index') }}">Fee Payments</a></li>
    <li class="breadcrumb-item active">Collect Payment</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> Collect Fee Payment</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form id="paymentForm">
                        @csrf
                        <div class="mb-3">
                            <label>Select Student *</label>
                            <input type="text" id="student_search" class="form-control mb-2" placeholder="Search student by name, admission number, or class">
                            <select name="student_id" id="student_id" class="form-control" required>
                                <option value="">-- Select Student --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" data-email="{{ $student->email ?? $student->parent_email }}" data-class-id="{{ $student->class_id }}" data-class-name="{{ is_object($student->class) ? ($student->class->name ?? '') : $student->class }}">
                                        {{ $student->admission_number }} - {{ $student->full_name }} ({{ is_object($student->class) ? ($student->class->name ?? $student->class) : $student->class }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Fee Type *</label>
                            <select name="fee_type" id="fee_type" class="form-control" required>
                                <option value="">-- Select Fee Type --</option>
                                {{-- Options populated dynamically from $feeStructures based on selected student/class --}}
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Term *</label>
                            <select name="term" id="term" class="form-control" required>
                                <option value="">-- Select Term --</option>
                                <option value="1st Term">1st Term</option>
                                <option value="2nd Term">2nd Term</option>
                                <option value="3rd Term">3rd Term</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Academic Year *</label>
                            <input type="text" name="academic_year" id="academic_year" class="form-control" 
                                value="{{ date('Y') }}/{{ date('Y')+1 }}" required>
                        </div>

                        <div class="mb-3">
                            <label>Amount (₦) *</label>
                            <input type="number" name="amount" id="amount" class="form-control" 
                                placeholder="Enter amount" min="0" step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> You will be redirected to Paystack secure payment page to complete payment
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100" id="payNowBtn">
                            <i class="fas fa-credit-card"></i> Pay Now with Paystack
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')

@php
    $feeStructuresJson = $feeStructures->map(function($fs) {
        return [
            'id' => $fs->id,
            'fee_name' => $fs->fee_name ?? $fs->fee_type,
            'fee_type' => $fs->fee_type,
            'class' => $fs->class,
            'class_id' => $fs->class_id,
            'term' => $fs->term,
            'academic_year' => $fs->academic_year,
            'amount' => (float) $fs->amount,
            'is_active' => (bool) $fs->is_active,
        ];
    })->toArray();
@endphp

<script>
    $(document).ready(function() {
        // Prepare fee structures data
        const feeStructures = @json($feeStructuresJson);

        function populateFeeTypesForStudent(classId, className) {
            const $feeType = $('#fee_type');
            $feeType.empty().append('<option value="">-- Select Fee Type --</option>');

            const filtered = feeStructures.filter(fs => {
                // Match by class_id when available, otherwise by class name
                if (fs.class_id && classId) return String(fs.class_id) === String(classId);
                if (fs.class && className) return String(fs.class).toLowerCase() === String(className).toLowerCase();
                return false;
            });

            // If none matched, fall back to listing all active fee structures
            const list = filtered.length ? filtered : feeStructures.filter(fs => fs.is_active);

            list.forEach(fs => {
                const label = fs.fee_name || fs.fee_type || fs.fee_code || ('Fee ' + fs.id);
                // Use fee structure ID as the option value for reliable lookup
                const option = $('<option/>')
                    .val(fs.id)
                    .text(label + ' - ' + (fs.amount ? '₦' + Number(fs.amount).toFixed(2) : 'Amount') )
                    .attr('data-amount', fs.amount)
                    .attr('data-term', fs.term)
                    .attr('data-academic-year', fs.academic_year)
                    .attr('data-fee-name', fs.fee_name || fs.fee_type);
                $feeType.append(option);
            });
        }

        // When student changes, populate fee types relevant to their class
        $('#student_id').on('change', function() {
            const selected = $(this).find('option:selected');
            const classId = selected.data('class-id');
            const className = selected.data('class-name');
            populateFeeTypesForStudent(classId, className);
        });

        // When fee type changes, set amount/term/academic year if provided
        $('#fee_type').on('change', function() {
            const selected = $(this).find('option:selected');
            const fsId = selected.val();
            // Find fee structure by id in our JS array
            const fs = feeStructures.find(x => String(x.id) === String(fsId));
            if (fs) {
                $('#amount').val(fs.amount ?? '');
                if (fs.term) $('#term').val(fs.term);
                if (fs.academic_year) $('#academic_year').val(fs.academic_year);
            } else {
                const amount = selected.data('amount');
                const term = selected.data('term');
                const academicYear = selected.data('academic-year');
                if (amount !== undefined) {
                    $('#amount').val(amount);
                }
                if (term) {
                    $('#term').val(term);
                }
                if (academicYear) {
                    $('#academic_year').val(academicYear);
                }
            }
        });

        // Optionally pre-populate fee types if a student is already selected (e.g. when reloading)
        if ($('#student_id').val()) {
            const sel = $('#student_id').find('option:selected');
            populateFeeTypesForStudent(sel.data('class-id'), sel.data('class-name'));
        }

        // Preserve original student option list for client-side search
        const originalStudentOptions = [];
        $('#student_id option').each(function() {
            const $opt = $(this);
            if ($opt.val()) {
                originalStudentOptions.push({
                    value: $opt.val(),
                    text: $opt.text(),
                    classId: $opt.data('class-id'),
                    className: $opt.data('class-name')
                });
            }
        });

        // Filter students as user types
        $('#student_search').on('input', function() {
            const q = $(this).val().toLowerCase().trim();
            const $select = $('#student_id');
            $select.empty().append('<option value="">-- Select Student --</option>');
            const filtered = originalStudentOptions.filter(o => {
                return o.text.toLowerCase().includes(q) || (o.value && String(o.value).includes(q)) || (o.className && o.className.toLowerCase().includes(q));
            });
            filtered.forEach(o => {
                const option = $('<option/>').val(o.value).text(o.text).attr('data-class-id', o.classId).attr('data-class-name', o.className);
                $select.append(option);
            });
        });

        $('#paymentForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $('#payNowBtn');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            const formData = {
                student_id: $('#student_id').val(),
                fee_type: $('#fee_type option:selected').data('fee-name') || $('#fee_type').val(),
                fee_structure_id: $('#fee_type').val(),
                term: $('#term').val(),
                academic_year: $('#academic_year').val(),
                amount: $('#amount').val(),
                _token: '{{ csrf_token() }}'
            };
            
            $.ajax({
                url: '{{ route("fee-payments.initialize") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        // Redirect to Paystack payment page
                        window.location.href = response.authorization_url;
                    } else {
                        alert('Error: ' + response.message);
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endpush
@endsection