@extends('layouts.app')

@section('title', 'Academic Calendar')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Academic Calendar</li>
@endsection

@section('styles')
<style>
    .calendar-event {
        border-left: 4px solid #667eea;
        padding: 10px 15px;
        margin-bottom: 10px;
        background: #f8f9fa;
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    .calendar-event:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }
    .calendar-event .event-date {
        font-weight: 700;
        color: #667eea;
        font-size: 14px;
    }
    .calendar-event .event-title {
        font-weight: 600;
        color: #1e293b;
    }
    .calendar-event .event-description {
        color: #64748b;
        font-size: 14px;
    }
    .calendar-event .event-badge {
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 12px;
    }
    .calendar-event .event-badge.exam { background: #fee2e2; color: #dc2626; }
    .calendar-event .event-badge.holiday { background: #d1fae5; color: #059669; }
    .calendar-event .event-badge.event { background: #e0e7ff; color: #4f46e5; }
    .calendar-event .event-badge.meeting { background: #fef3c7; color: #d97706; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Academic Calendar
                    </h5>
                    <div>
                        <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#addEventModal">
                            <i class="fas fa-plus-circle me-1"></i> Add Event
                        </button>
                        <button type="button" class="btn btn-light btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Calendar Navigation -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary" onclick="changeMonth(-1)">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-primary" id="currentMonth">
                                    {{ date('F Y') }}
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="changeMonth(1)">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary active">Month</button>
                                <button type="button" class="btn btn-outline-secondary">Week</button>
                                <button type="button" class="btn btn-outline-secondary">Day</button>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="table-responsive">
                        <table class="table table-bordered calendar-table">
                            <thead>
                                <tr>
                                    <th class="text-center">Sun</th>
                                    <th class="text-center">Mon</th>
                                    <th class="text-center">Tue</th>
                                    <th class="text-center">Wed</th>
                                    <th class="text-center">Thu</th>
                                    <th class="text-center">Fri</th>
                                    <th class="text-center">Sat</th>
                                </tr>
                            </thead>
                            <tbody id="calendarBody">
                                <!-- Calendar rows will be generated by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="mt-4">
                        <h6 class="fw-bold">
                            <i class="fas fa-clock me-2" style="color: #667eea;"></i>Upcoming Events
                        </h6>
                        <div id="upcomingEvents">
                            <!-- Sample Events -->
                            <div class="calendar-event">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="event-date">Dec 15, 2024</span>
                                        <span class="event-title ms-2">First Term Exams</span>
                                        <span class="badge event-badge exam ms-2">Exam</span>
                                    </div>
                                    <div>
                                        <small class="text-muted">8:00 AM - 2:00 PM</small>
                                    </div>
                                </div>
                                <div class="event-description mt-1">
                                    First Term examinations for all classes
                                </div>
                            </div>

                            <div class="calendar-event">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="event-date">Dec 20, 2024</span>
                                        <span class="event-title ms-2">Christmas Break</span>
                                        <span class="badge event-badge holiday ms-2">Holiday</span>
                                    </div>
                                    <div>
                                        <small class="text-muted">All Day</small>
                                    </div>
                                </div>
                                <div class="event-description mt-1">
                                    School closes for Christmas holidays
                                </div>
                            </div>

                            <div class="calendar-event">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="event-date">Jan 5, 2025</span>
                                        <span class="event-title ms-2">Staff Meeting</span>
                                        <span class="badge event-badge meeting ms-2">Meeting</span>
                                    </div>
                                    <div>
                                        <small class="text-muted">10:00 AM - 12:00 PM</small>
                                    </div>
                                </div>
                                <div class="event-description mt-1">
                                    End of term staff meeting and planning
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Add Calendar Event
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="event_title">Event Title <span class="text-danger">*</span></label>
                        <input type="text" name="event_title" id="event_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="event_date">Date <span class="text-danger">*</span></label>
                        <input type="date" name="event_date" id="event_date" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="event_time_from">From</label>
                                <input type="time" name="event_time_from" id="event_time_from" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="event_time_to">To</label>
                                <input type="time" name="event_time_to" id="event_time_to" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="event_type">Event Type</label>
                        <select name="event_type" id="event_type" class="form-control">
                            <option value="event">Event</option>
                            <option value="exam">Exam</option>
                            <option value="holiday">Holiday</option>
                            <option value="meeting">Meeting</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="event_description">Description</label>
                        <textarea name="event_description" id="event_description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentDate = new Date();

    function renderCalendar() {
        const month = currentDate.getMonth();
        const year = currentDate.getFullYear();
        
        document.getElementById('currentMonth').textContent = new Date(year, month).toLocaleString('default', { month: 'long', year: 'numeric' });
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date();
        
        let html = '<tr>';
        
        // Empty cells for days before the first day of month
        for (let i = 0; i < firstDay; i++) {
            html += '<td></td>';
        }
        
        // Days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const isToday = date.toDateString() === today.toDateString();
            const isWeekend = date.getDay() === 0 || date.getDay() === 6;
            
            html += `<td class="text-center ${isToday ? 'bg-primary text-white' : ''} ${isWeekend ? 'bg-light' : ''}" 
                         style="padding: 10px; cursor: pointer; height: 60px; vertical-align: top;"
                         onclick="selectDate(${day})">
                <div>${day}</div>
                <div style="font-size: 8px; margin-top: 2px;">
                    <span class="badge badge-danger" style="font-size: 8px;">📝</span>
                </div>
            </td>`;
            
            if ((day + firstDay) % 7 === 0 && day !== daysInMonth) {
                html += '</tr><tr>';
            }
        }
        
        // Fill remaining cells
        const remainingDays = (7 - ((daysInMonth + firstDay) % 7)) % 7;
        for (let i = 0; i < remainingDays; i++) {
            html += '<td></td>';
        }
        
        html += '</tr>';
        document.getElementById('calendarBody').innerHTML = html;
    }

    function changeMonth(delta) {
        currentDate.setMonth(currentDate.getMonth() + delta);
        renderCalendar();
    }

    function selectDate(day) {
        const month = currentDate.getMonth() + 1;
        const year = currentDate.getFullYear();
        const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        document.getElementById('event_date').value = dateStr;
        $('#addEventModal').modal('show');
    }

    renderCalendar();
</script>
@endpush
@endsection