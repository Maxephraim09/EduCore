<ul class="nav flex-column">
    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard.accountant') ? 'active' : '' }}" href="{{ route('dashboard.accountant') }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-header">FINANCE</li>

    <!-- Fee Payments -->
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="toggleSubmenu(event, 'accountantFeeSubmenu')">
            <i class="fas fa-money-bill-wave"></i>
            <span>Fee Payments</span>
            <i class="fas fa-chevron-down" id="accountantFeeSubmenuIcon"></i>
        </a>
        <ul class="submenu" id="accountantFeeSubmenu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fee-payments.create') ? 'active' : '' }}" href="{{ route('fee-payments.create') }}">
                    <i class="fas fa-plus-circle"></i> Collect Payment
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fee-payments.history') ? 'active' : '' }}" href="{{ route('fee-payments.history') }}">
                    <i class="fas fa-history"></i> Payment History
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fee-structure.*') ? 'active' : '' }}" href="{{ route('fee-structure.index') }}">
                    <i class="fas fa-file-invoice"></i> Fee Structure
                </a>
            </li>
        </ul>
    </li>

    <!-- Expenses -->
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="toggleSubmenu(event, 'accountantExpenseSubmenu')">
            <i class="fas fa-receipt"></i>
            <span>Expenses</span>
            <i class="fas fa-chevron-down" id="accountantExpenseSubmenuIcon"></i>
        </a>
        <ul class="submenu" id="accountantExpenseSubmenu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expenses.create') ? 'active' : '' }}" href="{{ route('expenses.create') }}">
                    <i class="fas fa-plus-circle"></i> Add Expense
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expenses.index') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                    <i class="fas fa-list"></i> Expense List
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}" href="{{ route('expense-categories.index') }}">
                    <i class="fas fa-chart-pie"></i> Expense Categories
                </a>
            </li>
        </ul>
    </li>

    <!-- Other Incomes -->
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="toggleSubmenu(event, 'accountantIncomeSubmenu')">
            <i class="fas fa-chart-line"></i>
            <span>Other Incomes</span>
            <i class="fas fa-chevron-down" id="accountantIncomeSubmenuIcon"></i>
        </a>
        <ul class="submenu" id="accountantIncomeSubmenu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('other-incomes.create') ? 'active' : '' }}" href="{{ route('other-incomes.create') }}">
                    <i class="fas fa-plus-circle"></i> Add Income
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('other-incomes.index') ? 'active' : '' }}" href="{{ route('other-incomes.index') }}">
                    <i class="fas fa-list"></i> Income List
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('income-categories.*') ? 'active' : '' }}" href="{{ route('income-categories.index') }}">
                    <i class="fas fa-chart-pie"></i> Income Sources
                </a>
            </li>
        </ul>
    </li>

    <!-- Salary -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('salaries.*') ? 'active' : '' }}" href="{{ route('salaries.index') }}">
            <i class="fas fa-money-bill-alt"></i>
            <span>Salary</span>
        </a>
    </li>

    <!-- Staff Loans -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('staff-loans.*') ? 'active' : '' }}" href="{{ route('staff-loans.index') }}">
            <i class="fas fa-hand-holding-usd"></i>
            <span>Staff Loans</span>
        </a>
    </li>

    <!-- Staff Overdrafts -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('staff-overdrafts.*') ? 'active' : '' }}" href="{{ route('staff-overdrafts.index') }}">
            <i class="fas fa-wallet"></i>
            <span>Staff Overdrafts</span>
        </a>
    </li>

    <li class="nav-header">REPORTS</li>

    <!-- Financial Reports -->
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="toggleSubmenu(event, 'accountantReportSubmenu')">
            <i class="fas fa-file-alt"></i>
            <span>Reports</span>
            <i class="fas fa-chevron-down" id="accountantReportSubmenuIcon"></i>
        </a>
        <ul class="submenu" id="accountantReportSubmenu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.fee-collection') ? 'active' : '' }}" href="{{ route('reports.fee-collection') }}">
                    <i class="fas fa-file-invoice"></i> Fee Collection Report
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.salary') ? 'active' : '' }}" href="{{ route('reports.salary') }}">
                    <i class="fas fa-file-invoice-dollar"></i> Salary Report
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.profit-loss') ? 'active' : '' }}" href="{{ route('reports.profit-loss') }}">
                    <i class="fas fa-chart-bar"></i> Profit & Loss
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.export-fee*') ? 'active' : '' }}" href="{{ route('reports.export-fee') }}">
                    <i class="fas fa-file-export"></i> Export Fee Report (Excel)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.export-fee-pdf*') ? 'active' : '' }}" href="{{ route('reports.export-fee-pdf') }}">
                    <i class="fas fa-file-pdf"></i> Export Fee Report (PDF)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.export-salary*') ? 'active' : '' }}" href="{{ route('reports.export-salary') }}">
                    <i class="fas fa-file-export"></i> Export Salary Report (Excel)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.export-salary-pdf*') ? 'active' : '' }}" href="{{ route('reports.export-salary-pdf') }}">
                    <i class="fas fa-file-pdf"></i> Export Salary Report (PDF)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.export-pl*') ? 'active' : '' }}" href="{{ route('reports.export-pl') }}">
                    <i class="fas fa-file-export"></i> Export Profit/Loss (Excel)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.export-pl-pdf*') ? 'active' : '' }}" href="{{ route('reports.export-pl-pdf') }}">
                    <i class="fas fa-file-pdf"></i> Export Profit/Loss (PDF)
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-header">COMMUNICATION</li>
    @if(app('router')->has('notifications.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
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
            <span>New Inquiry</span>
        </a>
    </li>

    <li class="nav-header">ACADEMICS</li>
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
    @if(app('router')->has('duty-roster.index'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('duty-roster.index') ? 'active' : '' }}" href="{{ route('duty-roster.index') }}">
            <i class="fas fa-users"></i>
            <span>Duty Roster</span>
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
        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
        </a>
    </li>
</ul>