<ul class="nav flex-column">
    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-header">ACADEMIC</li>

    <!-- My Results -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('results.*') ? 'active' : '' }}" href="{{ route('results.index') }}">
            <i class="fas fa-file-alt"></i>
            <span>My Results</span>
        </a>
    </li>

    <!-- Report Cards -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('report-cards.*') ? 'active' : '' }}" href="{{ route('report-cards.index') }}">
            <i class="fas fa-print"></i>
            <span>Report Cards</span>
        </a>
    </li>

    <li class="nav-header">CBT</li>

    <!-- Available Exams -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('cbt.student.exams') ? 'active' : '' }}" href="{{ route('cbt.student.exams') }}">
            <i class="fas fa-laptop"></i>
            <span>Available Exams</span>
            @php
                $availableCount = \App\Models\CbtExam::where('status', 'published')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->count();
            @endphp
            @if($availableCount > 0)
                <span class="badge badge-success ms-1">{{ $availableCount }}</span>
            @endif
        </a>
    </li>

    <!-- My CBT Results -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('cbt.student.*') ? 'active' : '' }}" href="{{ route('cbt.student.exams') }}">
            <i class="fas fa-check-circle"></i>
            <span>My CBT Results</span>
        </a>
    </li>

    <li class="nav-header">FINANCE</li>

    <!-- Fee Status -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('fee-payments.*') ? 'active' : '' }}" href="{{ route('fee-payments.history') }}">
            <i class="fas fa-money-bill-wave"></i>
            <span>Fee Status</span>
        </a>
    </li>

    <li class="nav-header">COMMUNICATION</li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('inquiries.index') ? 'active' : '' }}" href="{{ route('inquiries.index') }}">
            <i class="fas fa-comments"></i>
            <span>Inquiries</span>
        </a>
    </li>
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