<ul class="nav flex-column">
    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Academic Section -->
    <li class="nav-header">ACADEMIC</li>

    <!-- Students Management -->
    @if(app('router')->has('students.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('students.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'studentSubmenu')">
            <i class="fas fa-user-graduate"></i>
            <span>Students</span>
            <i class="fas fa-chevron-down" id="studentSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="studentSubmenu">
            @if(app('router')->has('students.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('students.create') ? 'active' : '' }}" 
                   href="{{ route('students.create') }}">
                    <i class="fas fa-plus-circle"></i> Add New Student
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('students.index') ? 'active' : '' }}" 
                   href="{{ route('students.index') }}">
                    <i class="fas fa-list"></i> All Students
                </a>
            </li>
        </ul>
    </li>
    @endif

    <!-- Classes -->
    @if(app('router')->has('classes.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('classes.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'classSubmenu')">
            <i class="fas fa-chalkboard"></i>
            <span>Classes</span>
            <i class="fas fa-chevron-down" id="classSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="classSubmenu">
            @if(app('router')->has('classes.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('classes.create') ? 'active' : '' }}" 
                   href="{{ route('classes.create') }}">
                    <i class="fas fa-plus-circle"></i> Add Class
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('classes.index') ? 'active' : '' }}" 
                   href="{{ route('classes.index') }}">
                    <i class="fas fa-list"></i> All Classes
                </a>
            </li>
        </ul>
    </li>
    @endif

    <!-- Subjects -->
    @if(app('router')->has('subjects.index'))

    <li class="nav-item has-treeview {{ request()->routeIs('subjects.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'subjectSubmenu')">
            <i class="fas fa-book"></i>
            <span>Subjects</span>
            <i class="fas fa-chevron-down" id="subjectSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="subjectSubmenu">
            @if(app('router')->has('subjects.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('subjects.create') ? 'active' : '' }}" 
                   href="{{ route('subjects.create') }}">
                    <i class="fas fa-plus-circle"></i> Add Subject
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('subjects.index') ? 'active' : '' }}" 
                   href="{{ route('subjects.index') }}">
                    <i class="fas fa-list"></i> All Subjects
                </a>
            </li>
            @if(app('router')->has('subjects.assign'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('subjects.assign') ? 'active' : '' }}" 
                   href="{{ route('subjects.assign') }}">
                    <i class="fas fa-user-plus"></i> Assign Subject to Class
                </a>
            </li>
            @endif
            @if(app('router')->has('subjects.student-subjects'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('subjects.student-subjects') ? 'active' : '' }}" 
                   href="{{ route('subjects.student-subjects') }}">
                    <i class="fas fa-user-graduate"></i> Student Subjects
                </a>
            </li>
            @endif
            @if(app('router')->has('subjects.statistics'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('subjects.statistics') ? 'active' : '' }}" 
                   href="{{ route('subjects.statistics') }}">
                    <i class="fas fa-chart-bar"></i> Subject Statistics
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Academic Calendar (Route exists in your routes) -->
    @if(app('router')->has('promotions.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('promotions.*') ? 'active' : '' }}" 
           href="{{ route('promotions.index') }}">
            <i class="fas fa-graduation-cap"></i>
            <span>Promotions</span>
        </a>
    </li>
    @endif

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('academic-calendar.*') ? 'active' : '' }}" 
           href="{{ route('academic-calendar.index') }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Academic Calendar</span>
        </a>
    </li>

    <!-- Exam Timetable -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('exam-timetable.*') ? 'active' : '' }}" 
           href="{{ route('exam-timetable.index') }}">
            <i class="fas fa-clock"></i>
            <span>Exam Timetable</span>
        </a>
    </li>

    <!-- Duty Roster -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('duty-roster.*') ? 'active' : '' }}" 
           href="{{ route('duty-roster.index') }}">
            <i class="fas fa-user-clock"></i>
            <span>Duty Roster</span>
        </a>
    </li>

    <!-- Timetables (if routes exist) -->
    @if(app('router')->has('timetables.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('timetables.index') ? 'active' : '' }}" 
           href="{{ route('timetables.index') }}">
            <i class="fas fa-table"></i> Academic Timetable
        </a>
    </li>
    @endif

    @if(app('router')->has('lesson-timetables.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('lesson-timetables.index') ? 'active' : '' }}" 
           href="{{ route('lesson-timetables.index') }}">
            <i class="fas fa-clock"></i> Lesson Timetable
        </a>
    </li>
    @endif

    <!-- Assessments & Exams Section -->
    <li class="nav-header">ASSESSMENTS & EXAMS</li>

    <!-- Grading Settings -->
    @if(app('router')->has('grading.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('grading.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('grading.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'gradingSubmenu')">
            <i class="fas fa-sliders-h"></i>
            <span>Grading Settings</span>
            <i class="fas fa-chevron-down" id="gradingSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="gradingSubmenu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('grading.index') ? 'active' : '' }}" 
                   href="{{ route('grading.index') }}">
                    <i class="fas fa-eye"></i> Grading Settings
                </a>
            </li>
            @if(app('router')->has('grading.edit'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('grading.edit') ? 'active' : '' }}" 
                   href="{{ route('grading.edit') }}">
                    <i class="fas fa-edit"></i> Edit Grading
                </a>
            </li>
            @endif
            @if(app('router')->has('grading.scale'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('grading.scale') ? 'active' : '' }}" 
                   href="{{ route('grading.scale') }}">
                    <i class="fas fa-table"></i> Grade Scale
                </a>
            </li>
            @endif
            @if(app('router')->has('grading.scale.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('grading.scale.create') ? 'active' : '' }}" 
                   href="{{ route('grading.scale.create') }}">
                    <i class="fas fa-plus-circle"></i> Add Grade
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Question Papers -->
    @if(app('router')->has('question-papers.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('question-papers.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('question-papers.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'questionPaperSubmenu')">
            <i class="fas fa-file-alt"></i>
            <span>Question Papers</span>
            <i class="fas fa-chevron-down" id="questionPaperSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="questionPaperSubmenu">
            @if(app('router')->has('question-papers.manage-questions'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('question-papers.manage-questions') ? 'active' : '' }}" 
                   href="{{ route('question-papers.manage-questions') }}">
                    <i class="fas fa-plus-circle"></i> Create/Manage Questions
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('question-papers.index') ? 'active' : '' }}" 
                   href="{{ route('question-papers.index') }}">
                    <i class="fas fa-list"></i> All Question Papers
                </a>
            </li>
        </ul>
    </li>
    @endif

    <!-- Report Cards -->
    @if(app('router')->has('report-cards.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('report-cards.*') ? 'active' : '' }}"
           href="{{ route('report-cards.index') }}">
            <i class="fas fa-file-alt"></i>
            <span>Report Cards</span>
        </a>
    </li>
    @endif

    @if(app('router')->has('result-checker-pins.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('result-checker-pins.*') ? 'active' : '' }}" href="{{ route('result-checker-pins.index') }}">
            <i class="fas fa-key"></i><span>Result Checker PINs</span>
        </a>
    </li>
    @endif

    <li class="nav-header">HUMAN RESOURCES</li>

    <!-- Employees -->
    @if(app('router')->has('employees.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('employees.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'employeeSubmenu')">
            <i class="fas fa-users"></i>
            <span>Employees</span>
            <i class="fas fa-chevron-down" id="employeeSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="employeeSubmenu">
            @if(app('router')->has('employees.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('employees.create') ? 'active' : '' }}" 
                   href="{{ route('employees.create') }}">
                    <i class="fas fa-user-plus"></i> Add Employee
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('employees.index') ? 'active' : '' }}" 
                   href="{{ route('employees.index') }}">
                    <i class="fas fa-list"></i> All Employees
                </a>
            </li>
            @if(app('router')->has('employees.attendance'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('employees.attendance') ? 'active' : '' }}" 
                   href="{{ route('employees.attendance') }}">
                    <i class="fas fa-chart-line"></i> Attendance
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Salary Management -->
    @if(app('router')->has('salaries.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('salaries.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('salaries.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'salarySubmenu')">
            <i class="fas fa-coins"></i>
            <span>Salaries</span>
            <i class="fas fa-chevron-down" id="salarySubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="salarySubmenu">
            @if(app('router')->has('salaries.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('salaries.create') ? 'active' : '' }}" 
                   href="{{ route('salaries.create') }}">
                    <i class="fas fa-plus-circle"></i> Process Salary
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('salaries.index') ? 'active' : '' }}" 
                   href="{{ route('salaries.index') }}">
                    <i class="fas fa-list"></i> Salary List
                </a>
            </li>
            @if(app('router')->has('salaries.history'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('salaries.history') ? 'active' : '' }}" 
                   href="{{ route('salaries.history') }}">
                    <i class="fas fa-history"></i> Salary History
                </a>
            </li>
            @endif
            @if(app('router')->has('salaries.reports'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('salaries.reports') ? 'active' : '' }}" 
                   href="{{ route('salaries.reports') }}">
                    <i class="fas fa-file-pdf"></i> Payroll Reports
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Staff Loans -->
    @if(app('router')->has('staff-loans.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('staff-loans.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('staff-loans.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'loanSubmenu')">
            <i class="fas fa-hand-holding-usd"></i>
            <span>Staff Loans</span>
            <i class="fas fa-chevron-down" id="loanSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="loanSubmenu">
            @if(app('router')->has('staff-loans.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff-loans.create') ? 'active' : '' }}" 
                   href="{{ route('staff-loans.create') }}">
                    <i class="fas fa-plus-circle"></i> New Loan Request
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff-loans.index') ? 'active' : '' }}" 
                   href="{{ route('staff-loans.index') }}">
                    <i class="fas fa-list"></i> All Loans
                </a>
            </li>
            @if(app('router')->has('staff-loans.reports'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff-loans.reports') ? 'active' : '' }}" 
                   href="{{ route('staff-loans.reports') }}">
                    <i class="fas fa-chart-line"></i> Loan Reports
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Staff Overdrafts -->
    @if(app('router')->has('staff-overdrafts.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('staff-overdrafts.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('staff-overdrafts.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'overdraftSubmenu')">
            <i class="fas fa-credit-card"></i>
            <span>Staff Overdrafts</span>
            <i class="fas fa-chevron-down" id="overdraftSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="overdraftSubmenu">
            @if(app('router')->has('staff-overdrafts.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff-overdrafts.create') ? 'active' : '' }}" 
                   href="{{ route('staff-overdrafts.create') }}">
                    <i class="fas fa-plus-circle"></i> Create Overdraft
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff-overdrafts.index') ? 'active' : '' }}" 
                   href="{{ route('staff-overdrafts.index') }}">
                    <i class="fas fa-list"></i> All Overdrafts
                </a>
            </li>
            @if(app('router')->has('staff-overdrafts.reports'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff-overdrafts.reports') ? 'active' : '' }}" 
                   href="{{ route('staff-overdrafts.reports') }}">
                    <i class="fas fa-chart-line"></i> Overdraft Reports
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Applications Section -->
    <li class="nav-header">APPLICATIONS</li>

    <!-- Student Admission (Application System) -->
    @if(app('router')->has('application.admin.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('application.admin.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('application.admin.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'applicationSubmenu')">
            <i class="fas fa-door-open"></i>
            <span>Student Admissions</span>
            <i class="fas fa-chevron-down" id="applicationSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="applicationSubmenu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('application.admin.index') ? 'active' : '' }}" 
                   href="{{ route('application.admin.index') }}">
                    <i class="fas fa-list"></i> All Applications
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('application.admin.index') && request('status') == 'pending' ? 'active' : '' }}" 
                   href="{{ route('application.admin.index', ['status' => 'pending']) }}">
                    <i class="fas fa-clock"></i> Pending Applications
                    @php
                        $pendingCount = \App\Models\Application::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="badge badge-warning ms-1">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('application.admin.index') && request('status') == 'admitted' ? 'active' : '' }}" 
                   href="{{ route('application.admin.index', ['status' => 'admitted']) }}">
                    <i class="fas fa-user-check"></i> Admitted Students
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('application.admin.index') && request('status') == 'rejected' ? 'active' : '' }}" 
                   href="{{ route('application.admin.index', ['status' => 'rejected']) }}">
                    <i class="fas fa-times-circle"></i> Rejected Applications
                </a>
            </li>
        </ul>
    </li>
    @endif

    <!-- Recruitment (if routes exist) -->
    @if(app('router')->has('recruitment.create') || app('router')->has('recruitment.applications') || app('router')->has('recruitment.staff'))
    <li class="nav-item has-treeview {{ request()->routeIs('recruitment.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('recruitment.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'recruitmentSubmenu')">
            <i class="fas fa-user-tie"></i>
            <span>Staff Recruitment</span>
            <i class="fas fa-chevron-down" id="recruitmentSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="recruitmentSubmenu">
            @if(app('router')->has('recruitment.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('recruitment.create') ? 'active' : '' }}" 
                   href="{{ route('recruitment.create') }}">
                    <i class="fas fa-plus-circle"></i> Create Job
                </a>
            </li>
            @endif
            @if(app('router')->has('recruitment.applications'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('recruitment.applications') ? 'active' : '' }}" 
                   href="{{ route('recruitment.applications') }}">
                    <i class="fas fa-file-alt"></i> Job Applications
                </a>
            </li>
            @endif
            @if(app('router')->has('recruitment.staff'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('recruitment.staff') ? 'active' : '' }}" 
                   href="{{ route('recruitment.staff') }}">
                    <i class="fas fa-users"></i> Staff List
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    @if(app('router')->has('admin.blog.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}" href="{{ route('admin.blog.index') }}">
            <i class="fas fa-blog"></i>
            <span>Blog Management</span>
        </a>
    </li>
    @endif

    <!-- Finance Section -->
    <li class="nav-header">FINANCE</li>

    <!-- Fee Payments -->
    @if(app('router')->has('fee-payments.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('fee-payments.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('fee-payments.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'feePaymentSubmenu')">
            <i class="fas fa-money-bill-wave"></i>
            <span>Fee Payments</span>
            <i class="fas fa-chevron-down" id="feePaymentSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="feePaymentSubmenu">
            @if(app('router')->has('fee-payments.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fee-payments.create') ? 'active' : '' }}" 
                   href="{{ route('fee-payments.create') }}">
                    <i class="fas fa-plus-circle"></i> Collect Payment
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fee-payments.index') ? 'active' : '' }}" 
                   href="{{ route('fee-payments.index') }}">
                    <i class="fas fa-list"></i> All Payments
                </a>
            </li>
            @if(app('router')->has('fee-payments.history'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fee-payments.history') ? 'active' : '' }}" 
                   href="{{ route('fee-payments.history') }}">
                    <i class="fas fa-history"></i> Payment History
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Fee Structure -->
    @if(app('router')->has('fee-structure.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('fee-structure.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('fee-structure.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'feeStructureSubmenu')">
            <i class="fas fa-file-invoice"></i>
            <span>Fee Structure</span>
            <i class="fas fa-chevron-down" id="feeStructureSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="feeStructureSubmenu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fee-structure.index') ? 'active' : '' }}" 
                   href="{{ route('fee-structure.index') }}">
                    <i class="fas fa-list"></i> All Fee Structures
                </a>
            </li>
            @if(app('router')->has('fee-structure.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fee-structure.create') ? 'active' : '' }}" 
                   href="{{ route('fee-structure.create') }}">
                    <i class="fas fa-plus-circle"></i> Add Fee Structure
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Expenses -->
    @if(app('router')->has('expenses.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('expenses.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'expenseSubmenu')">
            <i class="fas fa-receipt"></i>
            <span>Expenses</span>
            <i class="fas fa-chevron-down" id="expenseSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="expenseSubmenu">
            @if(app('router')->has('expenses.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expenses.create') ? 'active' : '' }}" 
                   href="{{ route('expenses.create') }}">
                    <i class="fas fa-plus-circle"></i> Add Expense
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expenses.index') ? 'active' : '' }}" 
                   href="{{ route('expenses.index') }}">
                    <i class="fas fa-list"></i> Expense List
                </a>
            </li>
            @if(app('router')->has('expense-categories.index'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}" 
                   href="{{ route('expense-categories.index') }}">
                    <i class="fas fa-chart-pie"></i> Expense Categories
                </a>
            </li>
            @endif
            @if(app('router')->has('expenses.reports'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expenses.reports') ? 'active' : '' }}" 
                   href="{{ route('expenses.reports') }}">
                    <i class="fas fa-chart-line"></i> Expense Reports
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Other Incomes -->
    @if(app('router')->has('other-incomes.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('other-incomes.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('other-incomes.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'incomeSubmenu')">
            <i class="fas fa-chart-line"></i>
            <span>Other Incomes</span>
            <i class="fas fa-chevron-down" id="incomeSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="incomeSubmenu">
            @if(app('router')->has('other-incomes.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('other-incomes.create') ? 'active' : '' }}" 
                   href="{{ route('other-incomes.create') }}">
                    <i class="fas fa-plus-circle"></i> Add Income
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('other-incomes.index') ? 'active' : '' }}" 
                   href="{{ route('other-incomes.index') }}">
                    <i class="fas fa-list"></i> Income List
                </a>
            </li>
            @if(app('router')->has('income-categories.index'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('income-categories.*') ? 'active' : '' }}" 
                   href="{{ route('income-categories.index') }}">
                    <i class="fas fa-chart-pie"></i> Income Sources
                </a>
            </li>
            @endif
            @if(app('router')->has('other-incomes.reports'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('other-incomes.reports') ? 'active' : '' }}" 
                   href="{{ route('other-incomes.reports') }}">
                    <i class="fas fa-chart-line"></i> Income Reports
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Assets -->
    @if(app('router')->has('assets.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('assets.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'assetSubmenu')">
            <i class="fas fa-building"></i>
            <span>Assets</span>
            <i class="fas fa-chevron-down" id="assetSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="assetSubmenu">
            @if(app('router')->has('assets.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('assets.create') ? 'active' : '' }}" 
                   href="{{ route('assets.create') }}">
                    <i class="fas fa-plus-circle"></i> Add Asset
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('assets.index') ? 'active' : '' }}" 
                   href="{{ route('assets.index') }}">
                    <i class="fas fa-list"></i> Asset List
                </a>
            </li>
            @if(app('router')->has('asset-categories.index'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('asset-categories.*') ? 'active' : '' }}" 
                   href="{{ route('asset-categories.index') }}">
                    <i class="fas fa-chart-pie"></i> Asset Categories
                </a>
            </li>
            @endif
            @if(app('router')->has('assets.depreciation'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('assets.depreciation') ? 'active' : '' }}" 
                   href="{{ route('assets.depreciation') }}">
                    <i class="fas fa-chart-line"></i> Asset Depreciation
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Reports Section -->
    <li class="nav-header">REPORTS</li>

    <!-- Reports -->
    @if(app('router')->has('reports.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('reports.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'reportSubmenu')">
            <i class="fas fa-file-alt"></i>
            <span>Reports</span>
            <i class="fas fa-chevron-down" id="reportSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="reportSubmenu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}" 
                   href="{{ route('reports.index') }}">
                    <i class="fas fa-chart-line"></i> Financial Reports
                </a>
            </li>
            @if(app('router')->has('reports.fee-collection'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.fee-collection') ? 'active' : '' }}" 
                   href="{{ route('reports.fee-collection') }}">
                    <i class="fas fa-file-invoice"></i> Fee Collection Report
                </a>
            </li>
            @endif
            @if(app('router')->has('reports.salary'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.salary') ? 'active' : '' }}" 
                   href="{{ route('reports.salary') }}">
                    <i class="fas fa-file-invoice-dollar"></i> Salary Report
                </a>
            </li>
            @endif
            @if(app('router')->has('reports.profit-loss'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.profit-loss') ? 'active' : '' }}" 
                   href="{{ route('reports.profit-loss') }}">
                    <i class="fas fa-chart-bar"></i> Profit & Loss
                </a>
            </li>
            @endif
            @if(app('router')->has('reports.export-fee'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.export-fee*') ? 'active' : '' }}" 
                   href="{{ route('reports.export-fee') }}">
                    <i class="fas fa-file-export"></i> Export Fee Report (Excel)
                </a>
            </li>
            @endif
            @if(app('router')->has('reports.export-fee-pdf'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.export-fee-pdf*') ? 'active' : '' }}" 
                   href="{{ route('reports.export-fee-pdf') }}">
                    <i class="fas fa-file-pdf"></i> Export Fee Report (PDF)
                </a>
            </li>
            @endif
            @if(app('router')->has('reports.export-salary'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.export-salary*') ? 'active' : '' }}" 
                   href="{{ route('reports.export-salary') }}">
                    <i class="fas fa-file-export"></i> Export Salary Report (Excel)
                </a>
            </li>
            @endif
            @if(app('router')->has('reports.export-salary-pdf'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.export-salary-pdf*') ? 'active' : '' }}" 
                   href="{{ route('reports.export-salary-pdf') }}">
                    <i class="fas fa-file-pdf"></i> Export Salary Report (PDF)
                </a>
            </li>
            @endif
            @if(app('router')->has('reports.export-pl'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.export-pl*') ? 'active' : '' }}" 
                   href="{{ route('reports.export-pl') }}">
                    <i class="fas fa-file-export"></i> Export Profit/Loss (Excel)
                </a>
            </li>
            @endif
            @if(app('router')->has('reports.export-pl-pdf'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.export-pl-pdf*') ? 'active' : '' }}" 
                   href="{{ route('reports.export-pl-pdf') }}">
                    <i class="fas fa-file-pdf"></i> Export Profit/Loss (PDF)
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Certificates -->
    @if(app('router')->has('certificates.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('certificates.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'certificateSubmenu')">
            <i class="fas fa-award"></i>
            <span>Certificates</span>
            <i class="fas fa-chevron-down" id="certificateSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="certificateSubmenu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('certificates.index') ? 'active' : '' }}" 
                   href="{{ route('certificates.index') }}">
                    <i class="fas fa-list"></i> All Certificates
                </a>
            </li>
            @if(app('router')->has('certificates.create'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('certificates.create') ? 'active' : '' }}" 
                   href="{{ route('certificates.create') }}">
                    <i class="fas fa-plus-circle"></i> Create Certificate
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    <!-- Notifications -->
    @if(app('router')->has('notifications.index'))
    <li class="nav-item has-treeview {{ request()->routeIs('notifications.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'notificationSubmenu')">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
            <i class="fas fa-chevron-down" id="notificationSubmenuIcon"></i>
        </a>
        <ul class="nav nav-treeview submenu" id="notificationSubmenu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}" 
                   href="{{ route('notifications.index') }}">
                    <i class="fas fa-list"></i> All Notifications
                </a>
            </li>
            @can('send-notifications')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('notifications.create') ? 'active' : '' }}" 
                       href="{{ route('notifications.create') }}">
                        <i class="fas fa-paper-plane"></i> Send Notification
                    </a>
                </li>
            @endcan
        </ul>
    </li>
    @endif

    <!-- System Section -->
    <li class="nav-header">SYSTEM</li>

    <!-- Profile -->
    @if(app('router')->has('profile.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" 
           href="{{ route('profile.index') }}">
            <i class="fas fa-user-circle"></i>
            <span>My Profile</span>
        </a>
    </li>
    @endif

    <!-- Settings -->
    @if(app('router')->has('settings.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" 
           href="{{ route('settings.index') }}">
            <i class="fas fa-cog"></i>
            <span>System Settings</span>
        </a>
    </li>
    @endif
</ul>
