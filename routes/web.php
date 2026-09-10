<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\AccountantDashboardController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\FrontdeskDashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FeePaymentController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\IncomeCategoryController;
use App\Http\Controllers\OtherIncomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffLoanController;
use App\Http\Controllers\StaffOverdraftController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassCategoryController;
use App\Http\Controllers\ScoresEntryController;
use App\Http\Controllers\ScoresSelectorController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\QuestionPaperController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CbtExamController;
use App\Http\Controllers\CbtQuestionController;
use App\Http\Controllers\CbtStudentController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Admin\PaymentVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GradingController;
use App\Http\Controllers\GradeScaleController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResultCheckerController;
use App\Http\Controllers\BlogController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== PUBLIC ROUTES ====================
// Landing/Welcome page - Entry point of the website
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/result-checker', [ResultCheckerController::class, 'verifyForm'])->name('result-checker.form');
Route::post('/result-checker', [ResultCheckerController::class, 'verify'])->name('result-checker.verify');
Route::get('/result-checker/qr/{registration_number}/{pin}', [ResultCheckerController::class, 'verifyQr'])->name('result-checker.qr');
Route::get('/result-checker/{pin}/download', [ResultCheckerController::class, 'download'])->name('result-checker.download');

// Public receipt verification (QR points here)
Route::get('/verify/receipt/{id}', [FeePaymentController::class, 'verifyReceipt'])->name('fee-payments.verify');

// Public blog
Route::get('/blog', [BlogController::class, 'publicIndex'])->name('blog.public.index');
Route::get('/blog/post/{post:slug}', [BlogController::class, 'publicShow'])->name('blog.public.show');
Route::post('/blog/post/{post:slug}/comments', [BlogController::class, 'comment'])->name('blog.comments.store');
Route::post('/blog/subscribe', [BlogController::class, 'subscribe'])->name('blog.subscribe');

// Normal staff/student authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

// Applicant (temporary) portal for admissions
Route::get('/apply', [\App\Http\Controllers\ApplicantPortalController::class, 'showLanding'])->name('application.portal.index');
Route::get('/apply/register', [\App\Http\Controllers\ApplicantPortalController::class, 'showRegister'])->name('application.register');
Route::post('/apply/register', [\App\Http\Controllers\ApplicantPortalController::class, 'register'])->name('application.register.store');
Route::get('/apply/login', [\App\Http\Controllers\ApplicantPortalController::class, 'showLogin'])->name('application.login');
Route::post('/apply/login', [\App\Http\Controllers\ApplicantPortalController::class, 'login'])->name('application.login.store');

// Applicant-protected routes (requires applicant login)
Route::middleware('auth:applicant')->group(function () {
    Route::get('/apply/dashboard', [\App\Http\Controllers\ApplicantPortalController::class, 'dashboard'])->name('application.portal.dashboard');
    Route::get('/apply/profile', [\App\Http\Controllers\ApplicantPortalController::class, 'profile'])->name('application.portal.profile');
    Route::get('/apply/application', [\App\Http\Controllers\ApplicantPortalController::class, 'applicationOverview'])->name('application.portal.application');
    Route::get('/apply/admission', [\App\Http\Controllers\ApplicantPortalController::class, 'admission'])->name('application.portal.admission');
    Route::get('/apply/registration', [\App\Http\Controllers\ApplicantPortalController::class, 'registration'])->name('application.portal.registration');
    Route::get('/apply/payment-history', [\App\Http\Controllers\ApplicantPortalController::class, 'paymentHistory'])->name('application.portal.payment-history');
    Route::get('/apply/enquiry', [\App\Http\Controllers\ApplicantPortalController::class, 'enquiry'])->name('application.portal.enquiry');
    Route::post('/apply/enquiry', [\App\Http\Controllers\ApplicantPortalController::class, 'sendEnquiry'])->name('application.portal.enquiry.send');
    Route::post('/apply/logout', [\App\Http\Controllers\ApplicantPortalController::class, 'logout'])->name('application.portal.logout');

    Route::get('/apply/select-category', [\App\Http\Controllers\ApplicantPortalController::class, 'selectCategory'])->name('application.select-category');
    Route::post('/apply/select-category', [\App\Http\Controllers\ApplicantPortalController::class, 'postSelectCategory'])->name('application.select-category.store');
    Route::post('/apply/pay', [\App\Http\Controllers\ApplicantPortalController::class, 'payForApplication'])->name('application.pay');
    Route::post('/apply/pay/manual', [\App\Http\Controllers\ApplicantPortalController::class, 'manualPaymentIntent'])->name('application.pay.manual');
    Route::post('/apply/pay/manual/verify/{tx}', [\App\Http\Controllers\ApplicantPortalController::class, 'manualPaymentVerify'])->name('application.pay.manual.verify');
    Route::get('/apply/form', [ApplicationController::class, 'showApplicationForm'])->name('application.form');
    Route::post('/apply/submit', [ApplicationController::class, 'submitApplication'])->name('application.submit');
});

// Public callback for applicant payments
Route::get('/apply/pay/callback', [\App\Http\Controllers\ApplicantPortalController::class, 'paymentCallback'])->name('application.pay.callback');

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ==================== STUDENT APPLICATION SYSTEM ====================
// Public Routes - No login required.
Route::prefix('apply')->name('application.')->group(function () {
    Route::get('/success/{applicationNumber}', [ApplicationController::class, 'applicationSuccess'])->name('success');

    Route::get('/payment/{applicationNumber}', [ApplicationController::class, 'paymentForm'])->name('payment');
    Route::post('/payment/{applicationNumber}', [ApplicationController::class, 'processPayment'])->name('process-payment');
    Route::get('/payment/success/{applicationNumber}', [ApplicationController::class, 'paymentSuccess'])->name('payment.success');

    Route::get('/status', [ApplicationController::class, 'showStatusForm'])->name('status.form');
    Route::post('/status', [ApplicationController::class, 'checkStatus'])->name('status.check');

    Route::get('/admission-letter/{admissionNumber}', [ApplicationController::class, 'admissionLetter'])->name('admission-letter');
    Route::get('/admission-letter/{admissionNumber}/download', [ApplicationController::class, 'downloadAdmissionLetter'])->name('admission-letter.download');
});

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware(['auth', 'activity'])->group(function () {
    
    // ==================== DASHBOARD ====================
    // Main dashboard route with role-based redirection
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [UserController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/{user}/activate', [UserController::class, 'activate'])->name('activate');
        Route::post('/{user}/deactivate', [UserController::class, 'deactivate'])->name('deactivate');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::post('/{id}/restore', [UserController::class, 'restore'])->name('restore');
    });

    // ==================== BLOG ====================
    Route::prefix('admin/blog')->name('admin.blog.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->middleware('permission:view-blog')->name('index');
        Route::get('/create', [BlogController::class, 'create'])->middleware('permission:create-blog-posts')->name('create');
        Route::post('/', [BlogController::class, 'store'])->middleware('permission:create-blog-posts')->name('store');
        Route::get('/categories', [BlogController::class, 'categories'])->middleware('permission:manage-blog')->name('categories');
        Route::post('/categories', [BlogController::class, 'storeCategory'])->middleware('permission:manage-blog')->name('categories.store');
        Route::put('/categories/{category}', [BlogController::class, 'updateCategory'])->middleware('permission:manage-blog')->name('categories.update');
        Route::delete('/categories/{category}', [BlogController::class, 'destroyCategory'])->middleware('permission:manage-blog')->name('categories.destroy');
        Route::post('/tags', [BlogController::class, 'storeTag'])->middleware('permission:manage-blog')->name('tags.store');
        Route::get('/comments', [BlogController::class, 'comments'])->middleware('permission:manage-blog')->name('comments');
        Route::get('/subscribers', [BlogController::class, 'subscribers'])->middleware('permission:manage-blog')->name('subscribers');
        Route::get('/presentation', [BlogController::class, 'presentation'])->middleware('permission:manage-blog')->name('presentation');
        Route::put('/presentation', [BlogController::class, 'updatePresentation'])->middleware('permission:manage-blog')->name('presentation.update');
        Route::post('/ads', [BlogController::class, 'storeAd'])->middleware('permission:manage-blog')->name('ads.store');
        Route::put('/ads/{ad}', [BlogController::class, 'updateAd'])->middleware('permission:manage-blog')->name('ads.update');
        Route::delete('/ads/{ad}', [BlogController::class, 'destroyAd'])->middleware('permission:manage-blog')->name('ads.destroy');
        Route::post('/comments/{comment}/approve', [BlogController::class, 'approveComment'])->middleware('permission:manage-blog')->name('comments.approve');
        Route::post('/comments/{comment}/reject', [BlogController::class, 'rejectComment'])->middleware('permission:manage-blog')->name('comments.reject');
        Route::delete('/comments/{comment}', [BlogController::class, 'destroyComment'])->middleware('permission:manage-blog')->name('comments.destroy');
        Route::post('/{post}/approve', [BlogController::class, 'approve'])->middleware('permission:approve-blog-posts')->name('approve');
        Route::post('/{post}/reject', [BlogController::class, 'reject'])->middleware('permission:approve-blog-posts')->name('reject');
        Route::get('/{post}/edit', [BlogController::class, 'edit'])->middleware('permission:create-blog-posts')->name('edit');
        Route::match(['put', 'patch'], '/{post}', [BlogController::class, 'update'])->middleware('permission:create-blog-posts')->name('update');
        Route::delete('/{post}', [BlogController::class, 'destroy'])->middleware('permission:manage-blog')->name('destroy');
    });

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [UserController::class, 'changePassword'])->name('profile.password');
    Route::post('/profile/security', [UserController::class, 'updateSecurity'])->name('profile.security');
    
    // Role-specific dashboard routes (direct access)
    Route::get('/dashboard/admin', [AdminDashboardController::class, 'index'])->name('dashboard.admin');
    Route::get('/dashboard/teacher', [TeacherDashboardController::class, 'index'])->name('dashboard.teacher');
    Route::get('/dashboard/student', [StudentDashboardController::class, 'index'])->name('dashboard.student');
    Route::get('/dashboard/accountant', [AccountantDashboardController::class, 'index'])->name('dashboard.accountant');
    Route::get('/dashboard/parent', [ParentDashboardController::class, 'index'])->name('dashboard.parent');
    Route::get('/dashboard/frontdesk', [FrontdeskDashboardController::class, 'index'])->name('dashboard.frontdesk');

    // Static admin pages with existing Blade views.
    Route::view('/academic-calendar', 'academic-calendar.index')->name('academic-calendar.index');
    Route::view('/exam-timetable', 'exam-timetable.index')->name('exam-timetable.index');
    Route::view('/duty-roster', 'duty-roster.index')->name('duty-roster.index');
    Route::view('/academic-timetable', 'academic-timetable.index')->name('timetables.index');
    Route::view('/lesson-timetable', 'lesson-timetable.index')->name('lesson-timetables.index');
    Route::view('/exam-settings', 'exam-settings.index')->name('exam-settings.index');

    // ==================== ACADEMIC YEAR & TERMS ====================
    Route::middleware(['auth'])->prefix('academic-years')->name('academic-years.')->group(function () {
        Route::get('/', [AcademicYearController::class, 'index'])->name('index');
        Route::get('/create', [AcademicYearController::class, 'create'])->name('create');
        Route::post('/', [AcademicYearController::class, 'store'])->name('store');
        Route::get('/{academicYear}', [AcademicYearController::class, 'show'])->name('show');
        Route::get('/{academicYear}/edit', [AcademicYearController::class, 'edit'])->name('edit');
        Route::put('/{academicYear}', [AcademicYearController::class, 'update'])->name('update');
        Route::post('/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])->name('set-current');
        Route::delete('/{academicYear}', [AcademicYearController::class, 'destroy'])->name('destroy');

        // Terms
        Route::get('/{academicYear}/terms', [TermController::class, 'index'])->name('terms.index');
        Route::get('/{academicYear}/terms/create', [TermController::class, 'create'])->name('terms.create');
        Route::post('/{academicYear}/terms', [TermController::class, 'store'])->name('terms.store');
        Route::get('/terms/{term}/edit', [TermController::class, 'edit'])->name('terms.edit');
        Route::put('/terms/{term}', [TermController::class, 'update'])->name('terms.update');
        Route::post('/terms/{term}/set-current', [TermController::class, 'setCurrent'])->name('terms.set-current');
        Route::delete('/terms/{term}', [TermController::class, 'destroy'])->name('terms.destroy');
    });

    // ==================== STUDENT MANAGEMENT ====================
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])
            ->middleware('permission:view-students,view-own-student')
            ->name('index');
        Route::get('/create', [StudentController::class, 'create'])
            ->middleware('permission:create-students')
            ->name('create');
        Route::post('/', [StudentController::class, 'store'])
            ->middleware('permission:create-students')
            ->name('store');
        Route::get('/{student}', [StudentController::class, 'show'])
            ->middleware('permission:view-students,view-own-student')
            ->name('show');
        Route::get('/{student}/edit', [StudentController::class, 'edit'])
            ->middleware('permission:edit-students')
            ->name('edit');
        Route::match(['put', 'patch'], '/{student}', [StudentController::class, 'update'])
            ->middleware('permission:edit-students')
            ->name('update');
        Route::delete('/{student}', [StudentController::class, 'destroy'])
            ->middleware('permission:delete-students')
            ->name('destroy');
    });

    // ==================== EMPLOYEE MANAGEMENT ====================
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])
            ->middleware('permission:view-employees')
            ->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])
            ->middleware('permission:create-employees')
            ->name('create');
        Route::post('/', [EmployeeController::class, 'store'])
            ->middleware('permission:create-employees')
            ->name('store');
        Route::get('/attendance', [EmployeeController::class, 'attendance'])
            ->middleware('permission:view-employees')
            ->name('attendance');
        Route::get('/attendance/report', [EmployeeController::class, 'getAttendanceReport'])
            ->middleware('permission:view-employees')
            ->name('attendance.report');
        Route::post('/attendance/record', [EmployeeController::class, 'recordAttendance'])
            ->middleware('permission:create-employees')
            ->name('attendance.record');
        Route::post('/attendance/bulk', [EmployeeController::class, 'bulkAttendance'])
            ->middleware('permission:create-employees')
            ->name('attendance.bulk');
        Route::get('/{employee}', [EmployeeController::class, 'show'])
            ->middleware('permission:view-employees')
            ->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])
            ->middleware('permission:edit-employees')
            ->name('edit');
        Route::get('/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])
            ->middleware('permission:edit-employees')
            ->name('toggle-status');
        Route::match(['put', 'patch'], '/{employee}', [EmployeeController::class, 'update'])
            ->middleware('permission:edit-employees')
            ->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])
            ->middleware('permission:delete-employees')
            ->name('destroy');
    });

    // ==================== FEE PAYMENTS ====================
    Route::prefix('fee-payments')->name('fee-payments.')->group(function () {
        Route::get('/', [FeePaymentController::class, 'index'])
            ->middleware('permission:view-fees')
            ->name('index');
        Route::get('/create', [FeePaymentController::class, 'create'])
            ->middleware('permission:collect-payments')
            ->name('create');
        Route::post('/initialize', [FeePaymentController::class, 'initializePayment'])
            ->middleware('permission:collect-payments')
            ->name('initialize');
        Route::get('/callback', [FeePaymentController::class, 'handleCallback'])
            ->middleware('permission:collect-payments')
            ->name('callback');
        Route::get('/receipt/{id}', [FeePaymentController::class, 'showReceipt'])
            ->middleware('permission:view-fees')
            ->name('receipt');
        Route::get('/history/{studentId?}', [FeePaymentController::class, 'paymentHistory'])
            ->middleware('permission:view-fees')
            ->name('history');
    });

    // ==================== PROMOTIONS ====================
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/', [PromotionController::class, 'index'])->name('index');
        Route::get('/settings', [PromotionController::class, 'settings'])->name('settings');
        Route::post('/settings', [PromotionController::class, 'storeSettings'])->name('settings.store');
        Route::post('/settings/remove', [PromotionController::class, 'removeMapping'])->name('settings.remove');
        Route::post('/promote', [PromotionController::class, 'promote'])->name('promote');
    });

    // ==================== FEE STRUCTURE ====================
    Route::prefix('fee-structure')->name('fee-structure.')->group(function () {
        Route::get('/', [FeeStructureController::class, 'index'])
            ->middleware('permission:view-fee-structure')
            ->name('index');
        Route::post('/', [FeeStructureController::class, 'store'])
            ->middleware('permission:edit-fee-structure')
            ->name('store');
        Route::match(['put', 'patch'], '/{fee_structure}', [FeeStructureController::class, 'update'])
            ->middleware('permission:edit-fee-structure')
            ->name('update');
        Route::delete('/{fee_structure}', [FeeStructureController::class, 'destroy'])
            ->middleware('permission:edit-fee-structure')
            ->name('destroy');
    });

    // ==================== SALARY MANAGEMENT ====================
    Route::prefix('salaries')->name('salaries.')->group(function () {
        Route::get('/', [SalaryController::class, 'index'])
            ->middleware('permission:view-salaries,view-own-salary')
            ->name('index');
        Route::get('/create', [SalaryController::class, 'create'])
            ->middleware('permission:process-salaries')
            ->name('create');
        Route::post('/calculate', [SalaryController::class, 'calculate'])
            ->middleware('permission:process-salaries')
            ->name('calculate');
        Route::post('/', [SalaryController::class, 'store'])
            ->middleware('permission:process-salaries')
            ->name('store');
        Route::get('/history/{employeeId?}', [SalaryController::class, 'history'])
            ->middleware('permission:view-salaries,view-own-salary')
            ->name('history');
        Route::get('/reports', [SalaryController::class, 'reports'])
            ->middleware('permission:view-salary-reports')
            ->name('reports');
        Route::get('/{salary}', [SalaryController::class, 'show'])
            ->middleware('permission:view-salaries,view-own-salary')
            ->name('show');
        Route::get('/{salary}/payslip', [SalaryController::class, 'payslip'])
            ->middleware('permission:view-salaries,view-own-salary')
            ->name('payslip');
        Route::delete('/{salary}', [SalaryController::class, 'destroy'])
            ->middleware('permission:process-salaries')
            ->name('destroy');
    });

    // ==================== STAFF LOANS ====================
    Route::prefix('staff-loans')->name('staff-loans.')->group(function () {
        Route::get('/', [StaffLoanController::class, 'index'])
            ->middleware('permission:view-loans,view-own-loan')
            ->name('index');
        Route::get('/create', [StaffLoanController::class, 'create'])
            ->middleware('permission:approve-loans,apply-loans')
            ->name('create');
        Route::post('/calculate', [StaffLoanController::class, 'calculateLoan'])
            ->middleware('permission:approve-loans')
            ->name('calculate');
        Route::post('/', [StaffLoanController::class, 'store'])
            ->middleware('permission:approve-loans,apply-loans')
            ->name('store');
        Route::get('/reports', [StaffLoanController::class, 'reports'])
            ->middleware('permission:view-financial-reports')
            ->name('reports');
        Route::get('/{loan}', [StaffLoanController::class, 'show'])
            ->middleware('permission:view-loans,view-own-loan')
            ->name('show');
        Route::get('/{loan}/edit', [StaffLoanController::class, 'edit'])
            ->middleware('permission:approve-loans')
            ->name('edit');
        Route::match(['put', 'patch'], '/{loan}', [StaffLoanController::class, 'update'])
            ->middleware('permission:approve-loans')
            ->name('update');
        Route::delete('/{loan}', [StaffLoanController::class, 'destroy'])
            ->middleware('permission:approve-loans')
            ->name('destroy');
        Route::post('/{loan}/payments', [StaffLoanController::class, 'recordPayment'])
            ->middleware('permission:record-loan-payments')
            ->name('payments.store');
    });

    // ==================== STAFF OVERDRAFTS ====================
    Route::prefix('staff-overdrafts')->name('staff-overdrafts.')->group(function () {
        Route::get('/', [StaffOverdraftController::class, 'index'])
            ->middleware('permission:view-overdrafts,view-own-overdraft')
            ->name('index');
        Route::get('/create', [StaffOverdraftController::class, 'create'])
            ->middleware('permission:approve-overdrafts,apply-overdrafts')
            ->name('create');
        Route::post('/calculate', [StaffOverdraftController::class, 'calculateOverdraft'])
            ->middleware('permission:approve-overdrafts')
            ->name('calculate');
        Route::post('/', [StaffOverdraftController::class, 'store'])
            ->middleware('permission:approve-overdrafts,apply-overdrafts')
            ->name('store');
        Route::get('/reports', [StaffOverdraftController::class, 'reports'])
            ->middleware('permission:view-financial-reports')
            ->name('reports');
        Route::get('/auto-expire', [StaffOverdraftController::class, 'autoExpireOverdrafts'])
            ->middleware('permission:approve-overdrafts')
            ->name('auto-expire');
        Route::get('/{overdraft}', [StaffOverdraftController::class, 'show'])
            ->middleware('permission:view-overdrafts,view-own-overdraft')
            ->name('show');
        Route::get('/{overdraft}/edit', [StaffOverdraftController::class, 'edit'])
            ->middleware('permission:approve-overdrafts')
            ->name('edit');
        Route::match(['put', 'patch'], '/{overdraft}', [StaffOverdraftController::class, 'update'])
            ->middleware('permission:approve-overdrafts')
            ->name('update');
        Route::delete('/{overdraft}', [StaffOverdraftController::class, 'destroy'])
            ->middleware('permission:approve-overdrafts')
            ->name('destroy');
        Route::post('/{overdraft}/usage', [StaffOverdraftController::class, 'recordUsage'])
            ->middleware('permission:record-overdraft-transactions')
            ->name('usage.store');
        Route::get('/{overdraft}/statement', [StaffOverdraftController::class, 'statement'])
            ->middleware('permission:view-overdrafts')
            ->name('statement');
    });

    // ==================== EXPENSES ====================
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])
            ->middleware('permission:view-expenses')
            ->name('index');
        Route::get('/create', [ExpenseController::class, 'create'])
            ->middleware('permission:create-expenses')
            ->name('create');
        Route::post('/', [ExpenseController::class, 'store'])
            ->middleware('permission:create-expenses')
            ->name('store');
        Route::get('/reports', [ExpenseController::class, 'reports'])
            ->middleware('permission:view-financial-reports')
            ->name('reports');
        Route::get('/export/excel', [ExpenseController::class, 'exportExcel'])
            ->middleware('permission:view-financial-reports')
            ->name('export.excel');
        Route::get('/export/pdf', [ExpenseController::class, 'exportPdf'])
            ->middleware('permission:view-financial-reports')
            ->name('export.pdf');
        Route::get('/email-report', [ExpenseController::class, 'emailReport'])
            ->middleware('permission:view-financial-reports')
            ->name('email.report');
        Route::get('/{expense}', [ExpenseController::class, 'show'])
            ->middleware('permission:view-expenses')
            ->name('show');
        Route::get('/{expense}/approve', [ExpenseController::class, 'approve'])
            ->middleware('permission:approve-expenses')
            ->name('approve');
        Route::get('/{expense}/reject', [ExpenseController::class, 'reject'])
            ->middleware('permission:approve-expenses')
            ->name('reject');
        Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])
            ->middleware('permission:edit-expenses')
            ->name('edit');
        Route::match(['put', 'patch'], '/{expense}', [ExpenseController::class, 'update'])
            ->middleware('permission:edit-expenses')
            ->name('update');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])
            ->middleware('permission:edit-expenses')
            ->name('destroy');
    });

    // Expense Categories
    Route::resource('expense-categories', ExpenseCategoryController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('permission:edit-expenses');

    // ==================== ASSETS ====================
    Route::prefix('assets')->name('assets.')->group(function () {
        Route::get('/', [AssetController::class, 'index'])
            ->middleware('permission:view-assets')
            ->name('index');
        Route::get('/create', [AssetController::class, 'create'])
            ->middleware('permission:create-assets')
            ->name('create');
        Route::post('/', [AssetController::class, 'store'])
            ->middleware('permission:create-assets')
            ->name('store');
        Route::get('/depreciation', [AssetController::class, 'depreciation'])
            ->middleware('permission:view-assets')
            ->name('depreciation');
        Route::get('/{asset}/calculate-depreciation', [AssetController::class, 'calculateDepreciation'])
            ->middleware('permission:view-assets')
            ->name('calculate-depreciation');
        Route::get('/{asset}', [AssetController::class, 'show'])
            ->middleware('permission:view-assets')
            ->name('show');
        Route::get('/{asset}/edit', [AssetController::class, 'edit'])
            ->middleware('permission:edit-assets')
            ->name('edit');
        Route::match(['put', 'patch'], '/{asset}', [AssetController::class, 'update'])
            ->middleware('permission:edit-assets')
            ->name('update');
        Route::delete('/{asset}', [AssetController::class, 'destroy'])
            ->middleware('permission:delete-assets')
            ->name('destroy');
    });

    // Asset Categories
    Route::resource('asset-categories', AssetCategoryController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('permission:edit-assets');

    // ==================== OTHER INCOMES ====================
    Route::prefix('other-incomes')->name('other-incomes.')->group(function () {
        Route::get('/', [OtherIncomeController::class, 'index'])
            ->middleware('permission:view-incomes')
            ->name('index');
        Route::get('/create', [OtherIncomeController::class, 'create'])
            ->middleware('permission:create-incomes')
            ->name('create');
        Route::post('/', [OtherIncomeController::class, 'store'])
            ->middleware('permission:create-incomes')
            ->name('store');
        Route::get('/reports', [OtherIncomeController::class, 'reports'])
            ->middleware('permission:view-financial-reports')
            ->name('reports');
        Route::get('/export/excel', [OtherIncomeController::class, 'exportExcel'])
            ->middleware('permission:view-financial-reports')
            ->name('export.excel');
        Route::get('/export/pdf', [OtherIncomeController::class, 'exportPdf'])
            ->middleware('permission:view-financial-reports')
            ->name('export.pdf');
        Route::get('/{other_income}', [OtherIncomeController::class, 'show'])
            ->middleware('permission:view-incomes')
            ->name('show');
        Route::get('/{other_income}/edit', [OtherIncomeController::class, 'edit'])
            ->middleware('permission:create-incomes')
            ->name('edit');
        Route::match(['put', 'patch'], '/{other_income}', [OtherIncomeController::class, 'update'])
            ->middleware('permission:create-incomes')
            ->name('update');
        Route::delete('/{other_income}', [OtherIncomeController::class, 'destroy'])
            ->middleware('permission:create-incomes')
            ->name('destroy');
    });

    // Income Categories
    Route::resource('income-categories', IncomeCategoryController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('permission:create-incomes');

    // ==================== REPORTS ====================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])
            ->middleware('permission:view-financial-reports')
            ->name('index');
        Route::get('/fee-collection', [ReportController::class, 'feeCollection'])
            ->middleware('permission:view-fee-reports')
            ->name('fee-collection');
        Route::get('/salary', [ReportController::class, 'salaryReport'])
            ->middleware('permission:view-salary-reports')
            ->name('salary');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])
            ->middleware('permission:view-pl-reports')
            ->name('profit-loss');
        Route::get('/export-fee', [ReportController::class, 'exportFeeReport'])
            ->middleware('permission:view-fee-reports')
            ->name('export-fee');
        Route::get('/export-fee-pdf', [ReportController::class, 'exportFeeReport'])
            ->middleware('permission:view-fee-reports')
            ->name('export-fee-pdf');
        Route::get('/export-salary', [ReportController::class, 'exportSalaryReport'])
            ->middleware('permission:view-salary-reports')
            ->name('export-salary');
        Route::get('/export-salary-pdf', [ReportController::class, 'exportSalaryReport'])
            ->middleware('permission:view-salary-reports')
            ->name('export-salary-pdf');
        Route::get('/export-pl', [ReportController::class, 'exportProfitLoss'])
            ->middleware('permission:view-pl-reports')
            ->name('export-pl');
        Route::get('/export-pl-pdf', [ReportController::class, 'exportProfitLoss'])
            ->middleware('permission:view-pl-reports')
            ->name('export-pl-pdf');
    });

    // ==================== CLASS MANAGEMENT ====================
    Route::prefix('classes')->name('classes.')->group(function () {
        Route::get('/', [ClassController::class, 'index'])->name('index');
        Route::get('/create', [ClassController::class, 'create'])->name('create');
        Route::post('/', [ClassController::class, 'store'])->name('store');
        Route::get('/{id}', [ClassController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ClassController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{id}', [ClassController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClassController::class, 'destroy'])->name('destroy');
        
        Route::get('/{id}/students', [ClassController::class, 'students'])->name('students');
        Route::get('/{id}/subjects', [ClassController::class, 'subjects'])->name('subjects');
        Route::get('/{id}/assign-students', [ClassController::class, 'showAssignStudentsForm'])->name('assign-students');
        Route::get('/{id}/assign-subject', [ClassController::class, 'showAssignSubjectForm'])->name('assign-subject');
        Route::get('/{id}/exams', [ClassController::class, 'exams'])->name('exams');
        Route::get('/{id}/results', [ClassController::class, 'results'])->name('results');
        Route::post('/{id}/assign-subject', [ClassController::class, 'assignSubject'])->name('assign-subject.post');
        Route::delete('/{classId}/remove-subject/{subjectId}', [ClassController::class, 'removeSubject'])->name('remove-subject');
        Route::post('/{id}/assign-students', [ClassController::class, 'assignStudents'])->name('assign-students.post');
        Route::delete('/{classId}/remove-student/{studentId}', [ClassController::class, 'removeStudent'])->name('remove-student');
    });

    Route::prefix('class-categories')->name('class-categories.')->group(function () {
        Route::get('/', [ClassCategoryController::class, 'index'])->name('index');
        Route::get('/create', [ClassCategoryController::class, 'create'])->name('create');
        Route::post('/', [ClassCategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ClassCategoryController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{id}', [ClassCategoryController::class, 'update'])->name('update');
        Route::get('/{id}/assign', [ClassCategoryController::class, 'assignForm'])->name('assign');
        Route::post('/{id}/assign', [ClassCategoryController::class, 'assignClass'])->name('assign.post');
    });

    // ==================== SUBJECT MANAGEMENT ====================
    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::get('/create', [SubjectController::class, 'create'])->name('create');
        Route::post('/', [SubjectController::class, 'store'])->name('store');

        // Static routes must stay before dynamic /{id} routes.
        Route::get('/assign', [SubjectController::class, 'assignForm'])->name('assign');
        Route::post('/assign', [SubjectController::class, 'assignSubject'])->name('assign.store');
        Route::get('/assign/class/{classId}', [SubjectController::class, 'getAssignedSubjects'])->name('assign.get');
        Route::delete('/assign/{assignmentId}', [SubjectController::class, 'removeAssignment'])->name('assign.remove');
        Route::post('/assign/bulk', [SubjectController::class, 'bulkAssign'])->name('assign.bulk');

        Route::get('/student-subjects', [SubjectController::class, 'studentSubjects'])->name('student-subjects');
        Route::post('/student-subjects', [SubjectController::class, 'assignStudentSubject'])->name('student-subjects.assign');
        Route::delete('/student-subjects/{studentId}/{subjectId}', [SubjectController::class, 'removeStudentSubject'])->name('student-subjects.remove');
        Route::get('/student-subjects/student/{studentId}', [SubjectController::class, 'getStudentSubjects'])->name('student-subjects.get');
        Route::get('/student-subjects/available/{classId}/{subjectId}', [SubjectController::class, 'getAvailableStudents'])->name('student-subjects.available');
        Route::post('/student-subjects/bulk', [SubjectController::class, 'bulkAssignSubject'])->name('student-subjects.bulk');
        Route::post('/student-subjects/bulk-remove', [SubjectController::class, 'bulkRemoveSubject'])->name('student-subjects.bulk-remove');
        Route::get('/student-subjects/missing/{classId}', [SubjectController::class, 'getMissingStudents'])->name('student-subjects.missing');

        Route::get('/statistics', [SubjectController::class, 'statistics'])->name('statistics');
        Route::get('/report', [SubjectController::class, 'report'])->name('report');
        Route::get('/export', [SubjectController::class, 'export'])->name('export');

        Route::get('/{id}', [SubjectController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SubjectController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{id}', [SubjectController::class, 'update'])->name('update');
        Route::delete('/{id}', [SubjectController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/classes', [SubjectController::class, 'classes'])->name('classes');
        Route::get('/{id}/teachers', [SubjectController::class, 'teachers'])->name('teachers');
        Route::get('/{id}/exams', [SubjectController::class, 'exams'])->name('exams');
        Route::get('/{id}/assign-class', [SubjectController::class, 'showAssignClassForm'])->name('assign-class');
        Route::post('/{id}/assign-class', [SubjectController::class, 'assignClass'])->name('assign-class.post');
        Route::get('/{id}/performance', [SubjectController::class, 'performance'])->name('performance');
        Route::get('/{id}/students', [SubjectController::class, 'studentsList'])->name('students');
    });

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('application.admin.')->group(function () {
    Route::get('/applications', [ApplicationController::class, 'adminApplications'])->name('index');
    Route::get('/applications/{id}', [ApplicationController::class, 'adminApplicationDetails'])->name('details');
    Route::put('/applications/{id}', [ApplicationController::class, 'adminUpdateStatus'])->name('update');
    Route::post('/applications/bulk-action', [ApplicationController::class, 'adminBulkAction'])->name('bulk-action');

    // Manual payment verification UI
    Route::get('/manual-transactions', [PaymentVerificationController::class, 'index'])->name('payments.manual.index');
    Route::post('/manual-transactions/{id}/verify', [PaymentVerificationController::class, 'verify'])->name('payments.manual.verify');
    Route::post('/manual-transactions/{id}/reject', [PaymentVerificationController::class, 'reject'])->name('payments.manual.reject');
});

    // ==================== EXAM QUESTIONS MANAGEMENT ====================
    Route::middleware(['auth'])->prefix('question-papers')->name('question-papers.')->group(function () {
        Route::get('/', [QuestionPaperController::class, 'index'])->name('index');
        Route::get('/manage-questions', [QuestionPaperController::class, 'manageQuestions'])->name('manage-questions');
        Route::post('/store-questions', [QuestionPaperController::class, 'storeQuestions'])->name('store-questions');
        Route::get('/get-by-filters', [QuestionPaperController::class, 'getByFilters'])->name('get-by-filters');

        Route::get('/{questionPaper}', [QuestionPaperController::class, 'show'])->name('show');
        Route::post('/{questionPaper}/publish', [QuestionPaperController::class, 'publish'])->name('publish');
        Route::post('/{questionPaper}/unpublish', [QuestionPaperController::class, 'unpublish'])->name('unpublish');
        Route::post('/{questionPaper}/duplicate', [QuestionPaperController::class, 'duplicate'])->name('duplicate');
        Route::get('/{questionPaper}/export', [QuestionPaperController::class, 'export'])->name('export');
        Route::get('/{questionPaper}/questions', [QuestionPaperController::class, 'getQuestions'])->name('get-questions');
        Route::get('/{questionPaper}/stats', [QuestionPaperController::class, 'getStats'])->name('get-stats');
        Route::delete('/{questionPaper}', [QuestionPaperController::class, 'destroy'])->name('destroy');
    });

    // ==================== RESULTS ====================
    Route::prefix('results')->name('results.')->group(function () {
        Route::get('/', [ResultController::class, 'index'])->name('index');
        Route::match(['get', 'post'], '/view', [ResultController::class, 'viewResults'])->name('view');
        Route::get('/report-card/{examId}/{studentId}', [ResultController::class, 'generateReportCard'])->name('report-card');
        Route::get('/bulk-upload/{examId}', [ResultController::class, 'bulkUploadForm'])->name('bulk-upload');
        Route::post('/bulk-upload/{examId}', [ResultController::class, 'bulkUpload'])->name('bulk-upload.store');
        Route::get('/export/{examId}/{classId}', [ResultController::class, 'exportResults'])->name('export');
    });

// ==================== GRADING MANAGEMENT ====================
Route::middleware(['auth'])->prefix('grading')->name('grading.')->group(function () {
    // Main grading index (view/edit)
    Route::get('/', [GradingController::class, 'index'])->name('index');
    
    // Edit grading
    Route::get('/edit', [GradingController::class, 'edit'])->name('edit');
    
    // Update grading
    Route::put('/update', [GradingController::class, 'update'])->name('update');
    
    // AJAX Endpoints
    Route::get('/get-grade/{score}', [GradingController::class, 'getGrade'])->name('get-grade');
    Route::get('/stats', [GradingController::class, 'getStats'])->name('stats');

    // ==================== GRADE SCALE MANAGEMENT ====================
    // Grade Scale index
    Route::get('/scale', [GradeScaleController::class, 'index'])->name('scale');
    
    // Create grade scale
    Route::get('/scale/create', [GradeScaleController::class, 'create'])->name('scale.create');
    Route::post('/scale', [GradeScaleController::class, 'store'])->name('scale.store');
    
    // Edit grade scale
    Route::get('/scale/{gradeScale}/edit', [GradeScaleController::class, 'edit'])->name('scale.edit');
    Route::put('/scale/{gradeScale}', [GradeScaleController::class, 'update'])->name('scale.update');
    
    // Delete grade scale
    Route::delete('/scale/{gradeScale}', [GradeScaleController::class, 'destroy'])->name('scale.destroy');
    
    // AJAX Endpoints for Grade Scale
    Route::get('/scale/active', [GradeScaleController::class, 'getActive'])->name('scale.active');
    Route::post('/scale/{gradeScale}/toggle', [GradeScaleController::class, 'toggleStatus'])->name('scale.toggle');
});

    // ==================== REPORT CARDS ====================
    Route::prefix('report-cards')->name('report-cards.')->group(function () {
        Route::get('/', [ReportCardController::class, 'index'])->name('index');
        Route::get('/settings', [ReportCardController::class, 'settings'])->name('settings');
        Route::get('/generate', [ReportCardController::class, 'generateRedirect'])->name('generate.redirect');
        Route::post('/generate', [ReportCardController::class, 'generate'])->name('generate');
        Route::get('/view/{examId}/{studentId}', [ReportCardController::class, 'viewSingle'])->name('view');
        Route::get('/download/{examId}/{studentId}', [ReportCardController::class, 'downloadPdf'])->name('download');
        Route::get('/bulk', [ReportCardController::class, 'bulkIndex'])->name('bulk.index');
        Route::get('/bulk/{examId}/{classId}', [ReportCardController::class, 'bulkGenerate'])->name('bulk');
        Route::post('/publish', [ReportCardController::class, 'publish'])->name('publish');
        Route::post('/unpublish', [ReportCardController::class, 'unpublish'])->name('unpublish');
        Route::post('/bulk-publish', [ReportCardController::class, 'bulkPublish'])->name('bulk-publish');
        Route::post('/bulk-unpublish', [ReportCardController::class, 'bulkUnpublish'])->name('bulk-unpublish');
    });

    Route::prefix('result-checker-pins')->name('result-checker-pins.')->group(function () {
        Route::get('/', [ResultCheckerController::class, 'index'])->name('index');
        Route::post('/generate', [ResultCheckerController::class, 'generate'])->name('generate');
    });

    // ==================== API ROUTES ====================
    Route::middleware(['auth', 'throttle:60,1'])->get('/api/students-by-class/{classId}', function($classId) {
        $user = auth()->user();
        abort_unless(
            $user->hasPermission('view-students') || $user->hasPermission('view-own-student'),
            403
        );

        $classQuery = \App\Models\ClassModel::whereKey($classId);
        if ($user->role === 'teacher') {
            $classQuery->where('class_teacher_id', getCurrentEmployeeId() ?? 0);
        }

        abort_unless($classQuery->exists(), 403);

        return \App\Models\Student::where('class_id', $classId)
            ->where('is_active', true)
            ->select('id', 'admission_number', 'first_name', 'last_name')
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'admission_number' => $student->admission_number,
                    'full_name' => $student->full_name,
                ];
            });
    })->name('api.students-by-class');


    // ==================== NOTIFICATIONS ====================
    Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/create', [NotificationController::class, 'create'])
            ->middleware('permission:send-notifications')
            ->name('create');
        Route::post('/', [NotificationController::class, 'store'])
            ->middleware('permission:send-notifications')
            ->name('store');
        Route::post('/mark-read/{id}', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });


    // ==================== RECRUITMENT (NEW IMPLEMENTATION) ====================
    // Public jobs (no auth)
    Route::prefix('recruitment')->name('recruitment.jobs.')->group(function () {
        Route::get('/jobs', [\App\Http\Controllers\PublicJobController::class, 'index'])->name('index');
        Route::get('/jobs/{id}', [\App\Http\Controllers\PublicJobController::class, 'show'])->name('show');
        Route::get('/jobs/{id}/apply', [\App\Http\Controllers\PublicJobController::class, 'apply'])->name('apply');
        Route::post('/jobs/{id}/apply', [\App\Http\Controllers\PublicJobController::class, 'storeApplication'])->name('apply.store');
    });

    // Applicant dashboard
    Route::middleware(['auth'])->prefix('applicant')->name('applicant.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ApplicantDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/applications', [\App\Http\Controllers\ApplicantDashboardController::class, 'applications'])->name('applications');
        Route::get('/applications/{id}/status', [\App\Http\Controllers\ApplicantDashboardController::class, 'applicationStatus'])->name('application.status');
        Route::get('/applications/{id}/edit', [\App\Http\Controllers\ApplicantDashboardController::class, 'editApplication'])->name('application.edit');
        Route::put('/applications/{id}', [\App\Http\Controllers\ApplicantDashboardController::class, 'updateApplication'])->name('application.update');
        Route::patch('/applications/{id}', [\App\Http\Controllers\ApplicantDashboardController::class, 'updateApplication'])->name('application.update.patch');
    });

    // Admin recruitment
    Route::middleware(['auth', 'permission:manage-recruitment'])->prefix('admin/recruitment')->name('admin.recruitment.')->group(function () {
        // Jobs
        Route::get('/jobs', [\App\Http\Controllers\AdminRecruitmentController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/create', [\App\Http\Controllers\AdminRecruitmentController::class, 'create'])->name('jobs.create');
        Route::post('/jobs', [\App\Http\Controllers\AdminRecruitmentController::class, 'store'])->name('jobs.store');
        Route::get('/jobs/{id}', [\App\Http\Controllers\AdminRecruitmentController::class, 'show'])->name('jobs.show');
        Route::get('/jobs/{id}/edit', [\App\Http\Controllers\AdminRecruitmentController::class, 'edit'])->name('jobs.edit');
        Route::put('/jobs/{id}', [\App\Http\Controllers\AdminRecruitmentController::class, 'update'])->name('jobs.update');
        Route::patch('/jobs/{id}', [\App\Http\Controllers\AdminRecruitmentController::class, 'update'])->name('jobs.update.patch');
        Route::delete('/jobs/{id}', [\App\Http\Controllers\AdminRecruitmentController::class, 'destroy'])->name('jobs.destroy');
        Route::post('/jobs/{id}/toggle-status', [\App\Http\Controllers\AdminRecruitmentController::class, 'toggleStatus'])->name('jobs.toggle-status');

        // Applications
        Route::get('/applications', [\App\Http\Controllers\AdminRecruitmentController::class, 'applications'])->name('applications');
        Route::get('/applications/{id}', [\App\Http\Controllers\AdminRecruitmentController::class, 'applicationDetails'])->name('application.details');
        Route::put('/applications/{id}/status', [\App\Http\Controllers\AdminRecruitmentController::class, 'updateApplicationStatus'])->name('application.status');
        Route::patch('/applications/{id}/status', [\App\Http\Controllers\AdminRecruitmentController::class, 'updateApplicationStatus'])->name('application.status.patch');
        Route::get('/applications/{id}/download-cv', [\App\Http\Controllers\AdminRecruitmentController::class, 'downloadCV'])->name('application.download-cv');
        Route::get('/applications/{id}/download-certificates', [\App\Http\Controllers\AdminRecruitmentController::class, 'downloadCertificates'])->name('application.download-certificates');

        // Settings
        Route::get('/settings', [\App\Http\Controllers\AdminRecruitmentController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\AdminRecruitmentController::class, 'updateSettings'])->name('settings.update');
    });

    // ==================== (deprecated placeholder) ====================
    // NOTE: Old RecruitmentController routes remain removed and replaced by new recruitment workflow above.



    // ==================== VISITORS ====================
    Route::middleware(['auth'])->prefix('visitors')->name('visitors.')->group(function () {
        Route::get('/', [\App\Http\Controllers\VisitorController::class, 'index'])
            ->middleware('permission:view-visitors')
            ->name('index');
        Route::get('/create', [\App\Http\Controllers\VisitorController::class, 'create'])
            ->middleware('permission:create-visitors')
            ->name('create');
        Route::post('/', [\App\Http\Controllers\VisitorController::class, 'store'])
            ->middleware('permission:create-visitors')
            ->name('store');
        Route::get('/{visitor}', [\App\Http\Controllers\VisitorController::class, 'show'])
            ->middleware('permission:view-visitors')
            ->name('show');
        Route::post('/{visitor}/checkout', [\App\Http\Controllers\VisitorController::class, 'checkOut'])
            ->middleware('permission:checkout-visitors')
            ->name('checkout');
    });

    // ==================== INQUIRIES ====================
    Route::middleware(['auth'])->prefix('inquiries')->name('inquiries.')->group(function () {
        Route::get('/', [\App\Http\Controllers\InquiryController::class, 'index'])
            ->name('index');
        Route::get('/create', [\App\Http\Controllers\InquiryController::class, 'create'])
            ->middleware('permission:create-inquiries')
            ->name('create');
        Route::post('/', [\App\Http\Controllers\InquiryController::class, 'store'])
            ->middleware('permission:create-inquiries')
            ->name('store');
        Route::get('/{inquiry}', [\App\Http\Controllers\InquiryController::class, 'show'])
            ->name('show');
        Route::post('/{inquiry}/respond', [\App\Http\Controllers\InquiryController::class, 'respond'])
            ->middleware('permission:respond-inquiries')
            ->name('respond');
    });


    // ==================== CERTIFICATES ====================
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/create', [CertificateController::class, 'create'])->name('create');
        Route::post('/', [CertificateController::class, 'store'])->name('store');
        Route::get('/{id}', [CertificateController::class, 'show'])->name('show');
        Route::get('/generate/{id}', [CertificateController::class, 'generate'])->name('generate');
        Route::delete('/{id}', [CertificateController::class, 'destroy'])->name('destroy');
    });

    // ==================== SCORES ENTRY ====================
    Route::prefix('scores')->name('scores.')->group(function () {
        // Main scores page
        Route::get('/', [ScoresEntryController::class, 'index'])->name('index');

        // Single Entry
        Route::get('/single-entry', [ScoresEntryController::class, 'singleEntry'])->name('single-entry');
        Route::post('/save-single', [ScoresEntryController::class, 'saveSingleEntry'])->name('save-single');
        Route::get('/get-student-scores', [ScoresEntryController::class, 'getStudentScores'])->name('get-student-scores');
        
        // Bulk Entry
        Route::get('/bulk-entry', [ScoresEntryController::class, 'bulkEntry'])->name('bulk-entry');
        Route::post('/save-bulk', [ScoresEntryController::class, 'saveBulkEntry'])->name('save-bulk');
        Route::get('/get-bulk-scores', [ScoresEntryController::class, 'getBulkScores'])->name('get-bulk-scores');
        
        // Import
        Route::get('/import', [ScoresEntryController::class, 'importScores'])->name('import');
        Route::post('/process-import', [ScoresEntryController::class, 'processImport'])->name('process-import');
        Route::get('/download-template', [ScoresEntryController::class, 'downloadTemplate'])->name('download-template');
        
        // Additional Features
        Route::get('/auto-fill', [ScoresEntryController::class, 'autoFillScores'])->name('auto-fill');
        Route::get('/calculate-positions', [ScoresEntryController::class, 'calculatePositions'])->name('calculate-positions');
        Route::post('/publish', [ScoresEntryController::class, 'publishResults'])->name('publish');
        Route::post('/lock', [ScoresEntryController::class, 'lockScores'])->name('lock');
        Route::get('/export', [ScoresEntryController::class, 'exportScores'])->name('export');
        
        // Progress and Management
        Route::get('/get-progress', [ScoresEntryController::class, 'getProgress'])->name('get-progress');
        Route::get('/get-subject-students', [ScoresEntryController::class, 'getSubjectStudents'])->name('get-subject-students');
    });

    // ==================== SCORE SHEETS (API) ====================
    Route::prefix('score-sheets')->name('score-sheets.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ScoreController::class, 'index'])->name('index');
        Route::post('/{id}/toggle', [\App\Http\Controllers\ScoreController::class, 'toggle'])->name('toggle');
        Route::post('/{id}/enter-scores', [\App\Http\Controllers\ScoreController::class, 'enterScores'])->name('enter-scores');
    });

    // ==================== CBT MANAGEMENT ====================
    Route::prefix('cbt')->name('cbt.')->middleware(['auth'])->group(function () {
        // Exam routes
        Route::resource('exams', CbtExamController::class);
        
        // Exam workflow routes
        Route::post('/exams/{id}/submit-for-approval', [CbtExamController::class, 'submitForApproval'])->name('exams.submit-for-approval');
        Route::post('/exams/{id}/approve', [CbtExamController::class, 'approve'])->name('exams.approve');
        Route::post('/exams/{id}/publish', [CbtExamController::class, 'publish'])->name('exams.publish');
        Route::post('/exams/{id}/close', [CbtExamController::class, 'close'])->name('exams.close');
        Route::post('/exams/{id}/auto-convert', [CbtExamController::class, 'autoConvertToTermExam'])->name('exams.auto-convert');
        
        // Question routes
        Route::get('/exams/{examId}/questions/create', [CbtQuestionController::class, 'create'])->name('questions.create');
        Route::post('/exams/{examId}/questions', [CbtQuestionController::class, 'store'])->name('questions.store');
        Route::get('/questions/{id}/edit', [CbtQuestionController::class, 'edit'])->name('questions.edit');
        Route::put('/questions/{id}', [CbtQuestionController::class, 'update'])->name('questions.update');
        Route::delete('/questions/{id}', [CbtQuestionController::class, 'destroy'])->name('questions.destroy');
        Route::post('/questions/reorder', [CbtQuestionController::class, 'reorder'])->name('questions.reorder');
    });

    // ==================== CBT STUDENT ROUTES ====================
    Route::prefix('cbt/student')->name('cbt.student.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', [CbtStudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/exams', [CbtStudentController::class, 'exams'])->name('exams');
        Route::get('/exams/{id}/take', [CbtStudentController::class, 'takeExam'])->name('take-exam');
        Route::post('/exams/{id}/start', [CbtStudentController::class, 'startExam'])->name('start-exam');
        Route::post('/exams/{id}/submit', [CbtStudentController::class, 'submitExam'])->name('submit-exam');
        Route::get('/exams/{id}/results', [CbtStudentController::class, 'results'])->name('results');
        Route::post('/exams/save-progress', [CbtStudentController::class, 'saveProgress'])->name('save-progress');
        Route::get('/get-question', [CbtStudentController::class, 'getQuestion'])->name('get-question');
        Route::get('/get-saved-answers', [CbtStudentController::class, 'getSavedAnswers'])->name('get-saved-answers');
    });

    // Profile Routes
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
    Route::put('/security', [ProfileController::class, 'updateSecurity'])->name('update-security');
});

    // ==================== SETTINGS ====================
    Route::prefix('settings')->name('settings.')->group(function () {
        // Main Settings Page
        Route::get('/', [SettingsController::class, 'index'])
            ->middleware('permission:view-settings')
            ->name('index');
        
        // School Settings
        Route::post('/update-school', [SettingsController::class, 'updateSchoolSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-school');
        
        // Academic Settings
        Route::post('/update-academic', [SettingsController::class, 'updateAcademicSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-academic');
        
        // Localization Settings
        Route::post('/update-localization', [SettingsController::class, 'updateLocalizationSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-localization');
        
        // Social Media Settings
        Route::post('/update-social', [SettingsController::class, 'updateSocialSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-social');
        
        // Paystack Settings
        Route::post('/update-paystack', [SettingsController::class, 'updatePaystackSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-paystack');
        
        // Bank Settings
        Route::post('/update-bank', [SettingsController::class, 'updateBankSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-bank');
        
        // SMS Settings
        Route::post('/update-sms', [SettingsController::class, 'updateSmsSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-sms');
        
        // Email Settings
        Route::post('/update-email', [SettingsController::class, 'updateEmailSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-email');

        // Admissions Settings
        Route::post('/update-admissions', [SettingsController::class, 'updateAdmissionsSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-admissions');
        
        // Security Settings
        Route::post('/update-security', [SettingsController::class, 'updateSecuritySettings'])
            ->middleware('permission:edit-settings')
            ->name('update-security');
        
        // SEO Settings
        Route::post('/update-seo', [SettingsController::class, 'updateSeoSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-seo');
        
        // Theme Settings
        Route::post('/update-theme', [SettingsController::class, 'updateThemeSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-theme');
        
        // Performance Settings
        Route::post('/update-performance', [SettingsController::class, 'updatePerformanceSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-performance');
        
        // Maintenance Settings
        Route::post('/update-maintenance', [SettingsController::class, 'updateMaintenanceSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-maintenance');
        
        // API Settings
        Route::post('/update-api', [SettingsController::class, 'updateApiSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-api');
        
        // Logging Settings
        Route::post('/update-logging', [SettingsController::class, 'updateLoggingSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-logging');
        
        // Cookie & GDPR Settings
        Route::post('/update-cookie', [SettingsController::class, 'updateCookieSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-cookie');
        
        // Backup Settings
        Route::post('/update-backup', [SettingsController::class, 'updateBackupSettings'])
            ->middleware('permission:manage-backups')
            ->name('update-backup');
        
        // Backup Actions
        Route::get('/backup', [SettingsController::class, 'backup'])
            ->middleware('permission:manage-backups')
            ->name('backup');
        Route::post('/restore', [SettingsController::class, 'restore'])
            ->middleware('permission:manage-backups')
            ->name('restore');
        Route::get('/backups/list', [SettingsController::class, 'listBackups'])
            ->middleware('permission:manage-backups')
            ->name('backups.list');
        Route::delete('/backups/{filename}', [SettingsController::class, 'deleteBackup'])
            ->middleware('permission:manage-backups')
            ->name('backups.delete');
        
        // General Settings
        Route::post('/update-general', [SettingsController::class, 'updateGeneralSettings'])
            ->middleware('permission:edit-settings')
            ->name('update-general');
        
        // Cache
        Route::get('/clear-cache', [SettingsController::class, 'clearCache'])
            ->middleware('permission:edit-settings')
            ->name('clear-cache');
        
        // Logs
        Route::get('/logs', [SettingsController::class, 'logs'])
            ->middleware('permission:view-logs')
            ->name('logs');
        Route::post('/clear-logs', [SettingsController::class, 'clearLogs'])
            ->middleware('permission:view-logs')
            ->name('clear-logs');
        
        // Test Routes
        Route::post('/test-sms', [SettingsController::class, 'testSms'])
            ->middleware('permission:edit-settings')
            ->name('test-sms');
        Route::post('/test-email', [SettingsController::class, 'testEmail'])
            ->middleware('permission:edit-settings')
            ->name('test-email');
    });

}); // This closes the main auth middleware group

