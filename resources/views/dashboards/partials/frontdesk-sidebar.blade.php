<ul class="nav flex-column">
    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard.frontdesk') ? 'active' : '' }}" href="{{ route('dashboard.frontdesk') }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-header">STUDENTS</li>

    <!-- Add Student -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('students.create') ? 'active' : '' }}" href="{{ route('students.create') }}">
            <i class="fas fa-user-plus"></i>
            <span>Add Student</span>
        </a>
    </li>

    <!-- All Students -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('students.index') ? 'active' : '' }}" href="{{ route('students.index') }}">
            <i class="fas fa-list"></i>
            <span>All Students</span>
        </a>
    </li>

    <li class="nav-header">FINANCE</li>

    <!-- Collect Payment -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('fee-payments.create') ? 'active' : '' }}" href="{{ route('fee-payments.create') }}">
            <i class="fas fa-credit-card"></i>
            <span>Collect Payment</span>
        </a>
    </li>

    <!-- Fee Structure -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('fee-structure.index') ? 'active' : '' }}" href="{{ route('fee-structure.index') }}">
            <i class="fas fa-file-invoice"></i>
            <span>Fee Structure</span>
        </a>
    </li>
    <li class="nav-header">ACADEMICS</li>

    @if(app('router')->has('admin.blog.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}" href="{{ route('admin.blog.index') }}">
            <i class="fas fa-blog"></i>
            <span>Blog Management</span>
        </a>
    </li>
    @endif

    <!-- Academic Calendar -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('academic-calendar.index') ? 'active' : '' }}" href="{{ route('academic-calendar.index') }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Academic Calendar</span>
        </a>
    </li>

    <!-- Lesson Timetable -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('lesson-timetables.index') ? 'active' : '' }}" href="{{ route('lesson-timetables.index') }}">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Lesson Timetable</span>
        </a>
    </li>

    <!-- Exam Timetable -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('exam-timetable.index') ? 'active' : '' }}" href="{{ route('exam-timetable.index') }}">
            <i class="fas fa-clock"></i>
            <span>Exam Timetable</span>
        </a>
    </li>

    <!-- Duty Roster -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('duty-roster.index') ? 'active' : '' }}" href="{{ route('duty-roster.index') }}">
            <i class="fas fa-users-cog"></i>
            <span>Duty Roster</span>
        </a>
    </li>

    <!-- Student Reports -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('results.*') ? 'active' : '' }}" href="{{ route('results.index') }}">
            <i class="fas fa-file-alt"></i>
            <span>Student Reports</span>
        </a>
    </li>

    <!-- Report Cards -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('report-cards.*') ? 'active' : '' }}" href="{{ route('report-cards.index') }}">
            <i class="fas fa-print"></i>
            <span>Report Cards</span>
        </a>
    </li>

    <li class="nav-header">COMMUNICATION</li>

    <!-- Notifications -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
        </a>
    </li>

    <!-- Inquiries -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('inquiries.index') ? 'active' : '' }}" href="{{ route('inquiries.index') }}">
            <i class="fas fa-comments"></i>
            <span>Inquiries</span>
        </a>
    </li>

    <!-- New Inquiry -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('inquiries.create') ? 'active' : '' }}" href="{{ route('inquiries.create') }}">
            <i class="fas fa-plus-circle"></i>
            <span>New Inquiry</span>
        </a>
    </li>

    <li class="nav-header">ACCOUNT</li>

    <!-- Profile -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
        </a>
    </li>
</ul>