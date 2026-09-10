<ul class="nav flex-column">
    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard.parent') ? 'active' : '' }}" href="{{ route('dashboard.parent') }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-header">ACADEMIC</li>

    <!-- My Children -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard.parent') ? 'active' : '' }}" href="{{ route('dashboard.parent') }}">
            <i class="fas fa-child"></i>
            <span>My Children</span>
        </a>
    </li>

    <!-- Academic Progress -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('results.*') ? 'active' : '' }}" href="{{ route('results.index') }}">
            <i class="fas fa-chart-line"></i>
            <span>Academic Progress</span>
        </a>
    </li>

    <!-- Report Cards -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('report-cards.*') ? 'active' : '' }}" href="{{ route('report-cards.index') }}">
            <i class="fas fa-print"></i>
            <span>Report Cards</span>
        </a>
    </li>

    <!-- Academic Schedule -->
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

    <li class="nav-header">FINANCE</li>

    <!-- Fee Structure -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('fee-structure.*') ? 'active' : '' }}" href="{{ route('fee-structure.index') }}">
            <i class="fas fa-file-invoice"></i>
            <span>Fee Structure</span>
        </a>
    </li>

    <!-- Pay Fees Online -->
    @if(auth()->user()->hasPermission('collect-payments'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('fee-payments.create') ? 'active' : '' }}" href="{{ route('fee-payments.create') }}">
            <i class="fas fa-credit-card"></i>
            <span>Pay Fees</span>
        </a>
    </li>
    @endif

    <!-- Payment History -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('fee-payments.history') ? 'active' : '' }}" href="{{ route('fee-payments.history') }}">
            <i class="fas fa-history"></i>
            <span>Payment History</span>
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

    <!-- Submit Inquiry -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('inquiries.create') ? 'active' : '' }}" href="{{ route('inquiries.create') }}">
            <i class="fas fa-paper-plane"></i>
            <span>Submit Inquiry</span>
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