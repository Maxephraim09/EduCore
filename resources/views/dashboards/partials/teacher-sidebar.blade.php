<ul class="nav flex-column">
    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard', 'dashboard.teacher') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-header">TEACHING</li>

    <!-- My Classes -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('classes.index') ? 'active' : '' }}" href="{{ route('classes.index') }}">
            <i class="fas fa-chalkboard"></i>
            <span>My Classes</span>
        </a>
    </li>

    <!-- My Students -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('students.index') ? 'active' : '' }}" href="{{ route('students.index') }}">
            <i class="fas fa-user-graduate"></i>
            <span>My Students</span>
        </a>
    </li>

    <!-- Subjects -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('subjects.index') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
            <i class="fas fa-book"></i>
            <span>My Subjects</span>
        </a>
    </li>

    <li class="nav-header">ASSESSMENTS</li>

    <!-- Enter Scores -->
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="toggleSubmenu(event, 'teacherScoresSubmenu')">
            <i class="fas fa-pen"></i>
            <span>Scores</span>
            <i class="fas fa-chevron-down" id="teacherScoresSubmenuIcon"></i>
        </a>
        <ul class="submenu" id="teacherScoresSubmenu">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('scores.single-entry') }}">
                    <i class="fas fa-user-edit"></i> Single Entry
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('scores.bulk-entry') }}">
                    <i class="fas fa-table"></i> Bulk Entry
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('scores.import') }}">
                    <i class="fas fa-file-import"></i> Import Scores
                </a>
            </li>
        </ul>
    </li>

    <!-- View Results -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('results.index') ? 'active' : '' }}" href="{{ route('results.index') }}">
            <i class="fas fa-file-alt"></i>
            <span>View Results</span>
        </a>
    </li>

    <!-- Exams -->
    @if(app('router')->has('question-papers.index'))
    <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('question-papers.*') ? 'active' : '' }}" href="{{ route('question-papers.index') }}">
            <i class="fas fa-file-alt"></i>
                <span>Question Papers</span>
        </a>
    </li>
    @endif

    <li class="nav-header">PERSONAL & SCHOOL TOOLS</li>

    @if(app('router')->has('admin.blog.index'))
    <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.blog.index') ? 'active' : '' }}" href="{{ route('admin.blog.index') }}">
            <i class="fas fa-blog"></i>
            <span>My Blog Posts</span>
        </a>
    </li>
    @endif

    <!-- Student Fee Status -->
    @if(app('router')->has('fee-payments.history'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('fee-payments.*') ? 'active' : '' }}" href="{{ route('fee-payments.history') }}">
            <i class="fas fa-wallet"></i>
            <span>Student Fee Status</span>
        </a>
    </li>
    @endif

    <!-- Expense Requests -->
    @if(app('router')->has('expenses.create'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('expenses.create') ? 'active' : '' }}" href="{{ route('expenses.create') }}">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Submit Expense Request</span>
        </a>
    </li>
    @endif

    <!-- My Salary -->
    @if(app('router')->has('salaries.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('salaries.*') ? 'active' : '' }}" href="{{ route('salaries.index') }}">
            <i class="fas fa-money-bill-wave"></i>
            <span>My Salary</span>
        </a>
    </li>
    @endif

    <!-- My Loans -->
    @if(app('router')->has('staff-loans.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('staff-loans.*') ? 'active' : '' }}" href="{{ route('staff-loans.index') }}">
            <i class="fas fa-hand-holding-usd"></i>
            <span>My Loan Status</span>
        </a>
    </li>
    @endif

    <!-- My Overdrafts -->
    @if(app('router')->has('staff-overdrafts.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('staff-overdrafts.*') ? 'active' : '' }}" href="{{ route('staff-overdrafts.index') }}">
            <i class="fas fa-university"></i>
            <span>My Overdraft Status</span>
        </a>
    </li>
    @endif

    <!-- School Announcements -->
    @if(app('router')->has('notifications.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
            <i class="fas fa-bullhorn"></i>
            <span>School Announcements</span>
        </a>
    </li>
    @endif

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

    <!-- Academic Schedules -->
    @if(app('router')->has('academic-calendar.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('academic-calendar.index') ? 'active' : '' }}" href="{{ route('academic-calendar.index') }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Academic Calendar</span>
        </a>
    </li>
    @endif
    @if(app('router')->has('exam-timetable.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('exam-timetable.index') ? 'active' : '' }}" href="{{ route('exam-timetable.index') }}">
            <i class="fas fa-book-open"></i>
            <span>Exam Timetable</span>
        </a>
    </li>
    @endif
    @if(app('router')->has('timetables.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('timetables.index') ? 'active' : '' }}" href="{{ route('timetables.index') }}">
            <i class="fas fa-clock"></i>
            <span>Academic Timetable</span>
        </a>
    </li>
    @endif
    @if(app('router')->has('lesson-timetables.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('lesson-timetables.index') ? 'active' : '' }}" href="{{ route('lesson-timetables.index') }}">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Lesson Timetable</span>
        </a>
    </li>
    @endif

    <li class="nav-header">ACCOUNT</li>

    <!-- Profile -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('profile.index') ? 'active' : '' }}" href="{{ route('profile.index') }}">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
        </a>
    </li>
</ul>