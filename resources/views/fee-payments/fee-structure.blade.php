@extends('layouts.app')

@section('title', 'Fee Structure')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Fee Structure</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Fee Structure Management</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFeeModal">
                <i class="fas fa-plus"></i> Add Fee Structure
            </button>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="feeTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fee Name</th>
                            <th>Fee Code</th>
                            <th>Class</th>
                            <th>Term</th>
                            <th>Academic Year</th>
                            <th>Amount (₦)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($feeStructures as $fee)
                        <tr>
                            <td>{{ $fee->id }}</td>
                            <td>{{ ucfirst($fee->fee_type ?? $fee->fee_name) }}</td>
                            <td>{{ $fee->fee_code }}</td>
                            <td>{{ $fee->class ? $fee->class : ( $fee->class && $fee->class()->exists() ? $fee->class->full_class_name ?? $fee->class->name : '') }}</td>
                            <td>{{ $fee->term }}</td>
                            <td>{{ $fee->academic_year }}</td>
                            <td>{{ number_format($fee->amount, 2) }}</td>
                            <td>
                                @if($fee->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-fee" data-id="{{ $fee->id }}"
                                    data-name="{{ $fee->fee_name }}" data-code="{{ $fee->fee_code }}"
                                    data-class-id="{{ $fee->class_id }}" data-class="{{ $fee->class }}" data-type="{{ $fee->fee_type }}"
                                    data-term="{{ $fee->term }}" data-year="{{ $fee->academic_year }}" data-amount="{{ $fee->amount }}"
                                    data-description="{{ htmlspecialchars($fee->description ?? '', ENT_QUOTES) }}" data-active="{{ $fee->is_active ? 1 : 0 }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('fee-structure.destroy', $fee->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $feeStructures->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Fee Modal -->
<div class="modal fade" id="addFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('fee-structure.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Fee Structure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Fee Name</label>
                        <input type="text" name="fee_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Fee Code</label>
                        <input type="text" name="fee_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Class</label>
                        <select name="class_id" class="form-control">
                            <option value="">Select Class (optional)</option>
                            @php $allClasses = \App\Models\ClassModel::where('is_active', true)->get(); @endphp
                            @foreach($allClasses as $c)
                                <option value="{{ $c->id }}">{{ $c->full_class_name ?? $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Fee Type</label>
                        <select name="fee_type" class="form-control">
                            <option value="tuition">Tuition</option>
                            <option value="registration">Registration</option>
                            <option value="textbooks">Textbooks</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Term</label>
                        <select name="term" class="form-control" required>
                            <option value="1st Term">1st Term</option>
                            <option value="2nd Term">2nd Term</option>
                            <option value="3rd Term">3rd Term</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Academic Year</label>
                        <input type="text" name="academic_year" class="form-control" value="{{ date('Y') }}/{{ date('Y')+1 }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Amount (₦)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Fee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Fee Modal -->
<div class="modal fade" id="editFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editFeeForm" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Fee Structure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Fee Name</label>
                        <input type="text" name="fee_name" id="edit_fee_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Fee Code</label>
                        <input type="text" name="fee_code" id="edit_fee_code" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Class</label>
                        <select name="class_id" id="edit_class_id" class="form-control">
                            <option value="">Select Class (optional)</option>
                            @php $allClasses = \App\Models\ClassModel::where('is_active', true)->get(); @endphp
                            @foreach($allClasses as $c)
                                <option value="{{ $c->id }}">{{ $c->full_class_name ?? $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Fee Type</label>
                        <select name="fee_type" id="edit_fee_type" class="form-control">
                            <option value="tuition">Tuition</option>
                            <option value="registration">Registration</option>
                            <option value="textbooks">Textbooks</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Term</label>
                        <select name="term" id="edit_term" class="form-control" required>
                            <option value="1st Term">1st Term</option>
                            <option value="2nd Term">2nd Term</option>
                            <option value="3rd Term">3rd Term</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Academic Year</label>
                        <input type="text" name="academic_year" id="edit_academic_year" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Amount (₦)</label>
                        <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                        <label class="form-check-label" for="edit_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Fee</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#feeTable').DataTable({
            pageLength: 10,
            responsive: true
        });
    });
</script>
<script>
    $(function(){
        // Edit button handler
        $('.edit-fee').on('click', function(){
            const btn = $(this);
            const id = btn.data('id');
            const name = btn.data('name');
            const code = btn.data('code');
            const classId = btn.data('class-id');
            const type = btn.data('type');
            const term = btn.data('term');
            const year = btn.data('year');
            const amount = btn.data('amount');
            const description = btn.data('description');
            const active = btn.data('active');

            // populate form
            $('#edit_fee_name').val(name);
            $('#edit_fee_code').val(code);
            $('#edit_class_id').val(classId);
            $('#edit_fee_type').val(type);
            $('#edit_term').val(term);
            $('#edit_academic_year').val(year);
            $('#edit_amount').val(amount);
            $('#edit_description').val(description);
            $('#edit_is_active').prop('checked', active == 1);

            // set action URL
            $('#editFeeForm').attr('action', '/fee-structure/' + id);

            // show modal
            var modal = new bootstrap.Modal(document.getElementById('editFeeModal'));
            modal.show();
        });
    });
</script>
@endpush
@endsection