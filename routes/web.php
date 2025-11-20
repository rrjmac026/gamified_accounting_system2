<?php

use App\Http\Controllers\ProfileController;

// ============================================================================
// ADMIN CONTROLLERS
// ============================================================================
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\XpTransactionController;
use App\Http\Controllers\Admin\StudentContronController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\InstructorManagementController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Admin\FeedbackRecordController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\PerformanceLogController;
use App\Http\Controllers\Admin\LeaderboardController;
use App\Http\Controllers\Admin\BadgeController;

// ============================================================================
// INSTRUCTOR CONTROLLERS
// ============================================================================
use App\Http\Controllers\Instructors\InstructorController;
use App\Http\Controllers\Instructors\TaskQuestionController;
use App\Http\Controllers\Instructors\TaskController;
use App\Http\Controllers\Instructors\StudentTaskController;
use App\Http\Controllers\Instructors\TaskSubmissionController;
use App\Http\Controllers\Instructors\InstructorSectionController;
use App\Http\Controllers\Instructors\InstructorSubjectController;
use App\Http\Controllers\Instructors\StudentProgressesController;
use App\Http\Controllers\Admin\DataBackupController;
use App\Http\Controllers\Instructors\PerformanceTaskController;
use App\Http\Controllers\Instructors\PerformanceTaskAnswerSheetController;
use App\Http\Controllers\Instructors\PerformanceTaskSubmissionController;
use App\Http\Controllers\Instructors\PerformanceTaskSubmissionExportController;
use App\Http\Controllers\Instructors\InstructorFeedbackRecordController;


// ============================================================================
// STUDENT CONTROLLERS
// ============================================================================
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Students\TodoController;
use App\Http\Controllers\Students\StudentSubjectController;
use App\Http\Controllers\Students\StudentProgressController;
use App\Http\Controllers\Students\FeedbackController;
use App\Http\Controllers\Students\StudentPerformanceTaskController;

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\SystemNotificationController;


// ============================================================================
// PUBLIC ROUTES
// ============================================================================
Route::get('/', function () {
    return view('welcome');
});

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================
Route::middleware('guest')->group(function () {
    
    // Password Reset Routes
    Route::get('forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
    
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
        
    Route::get('reset-password/{token}', [PasswordResetController::class, 'show'])
        ->name('password.reset');
    
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});


// ============================================================================
// PROFILE ROUTES (All Authenticated Users)
// ============================================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Update password route
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');
    
    // Add 2FA routes
    Route::get('/two-factor', [ProfileController::class, 'showTwoFactorForm'])
        ->name('two-factor.login');
    Route::post('/two-factor', [ProfileController::class, 'verifyTwoFactor'])
        ->name('two-factor.verify');
    Route::post('/profile/two-factor-authentication', [ProfileController::class, 'enableTwoFactor'])
        ->name('profile.enableTwoFactor');
    Route::delete('/profile/two-factor-authentication', [ProfileController::class, 'disableTwoFactor'])
        ->name('profile.disableTwoFactor');
    Route::get('/profile/two-factor', [ProfileController::class, 'showTwoFactorForm'])
     ->name('profile.twoFactorForm');

    Route::get('/profile/badges', [\App\Http\Controllers\ProfileController::class, 'badges'])
    ->name('profile.badges');

    Route::get('/notifications', [SystemNotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [SystemNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [SystemNotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::delete('/notifications/{notification}', [SystemNotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Admin broadcast
    Route::post('/notifications', [SystemNotificationController::class, 'store'])->name('notifications.store');
});

// ============================================================================
// EVALUATION ROUTES (Mixed Roles)
// ============================================================================

// Routes for students (creating evaluations)
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/evaluations/create', [EvaluationController::class, 'create'])->name('evaluations.create');
    Route::post('/evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');
});

// Routes for instructors & admins (viewing evaluations)
Route::middleware(['auth', 'role:admin,instructor'])->group(function () {
    Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('/evaluations/{evaluation}', [EvaluationController::class, 'show'])->name('evaluations.show');
});

// ============================================================================
// ADMIN ROUTES
// ============================================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Student Management
    Route::post('/students/import', [StudentManagementController::class, 'import'])->name('student.import');
    Route::get('/students/create', [StudentManagementController::class, 'create'])->name('student.create');
    Route::resource('/student', StudentManagementController::class);
    
    // Course & Subject Management
    Route::resource('/courses', CourseController::class);
    Route::resource('/subjects', SubjectController::class);
    Route::resource('sections', SectionController::class);

    Route::get('admin/sections/{section}/subjects', [SectionController::class, 'manageSubjects'])
     ->name('sections.subjects');

    Route::post('admin/sections/{section}/subjects', [SectionController::class, 'updateSubjects'])
        ->name('sections.subjects.update');
    
    // Instructor Management
    Route::resource('/instructors', InstructorManagementController::class);
    
    // Gamification & Progress
    Route::resource('badges', BadgeController::class);
    Route::resource('/xp-transactions', XpTransactionController::class);
    
    // Performance Logs Routes
    Route::get('/performance-logs', [PerformanceLogController::class, 'index'])
        ->name('performance-logs.index');
    Route::get('/performance-logs/{performanceLog}', [PerformanceLogController::class, 'show'])
        ->name('performance-logs.show');
    Route::delete('/performance-logs/{performanceLog}', [PerformanceLogController::class, 'destroy'])
        ->name('performance-logs.destroy');
    Route::get('/performance-logs/student/{student}', [PerformanceLogController::class, 'getStudentPerformance'])
        ->name('performance-logs.student');
    Route::get('/performance-logs/subject/{subject}', [PerformanceLogController::class, 'getSubjectStatistics'])
        ->name('performance-logs.subject');

    // Leaderboards
    Route::get('/leaderboards', [LeaderboardController::class, 'index'])->name('leaderboards.index');
    Route::get('/leaderboards/export', [LeaderboardController::class, 'export'])->name('leaderboards.export'); 
    Route::get('/leaderboards/{leaderboard}', [LeaderboardController::class, 'show'])->name('leaderboards.show');
    
    //Settings
    Route::prefix('backups')->name('backups.')->group(function () {
        // Main pages
        Route::get('/', [DataBackupController::class, 'index'])->name('index');
        
        // Backup operations  
        Route::post('/', [DataBackupController::class, 'store'])->name('store');
        Route::get('/{backup}/download', [DataBackupController::class, 'download'])->name('download');
        Route::delete('/{backup}', [DataBackupController::class, 'destroy'])->name('destroy');
        
        // Additional management routes
        Route::post('/cleanup', [DataBackupController::class, 'cleanup'])->name('cleanup');
        Route::get('/{backup}/status', [DataBackupController::class, 'status'])->name('status');
        
        // System diagnostics and quick actions
        Route::get('/test-system', [DataBackupController::class, 'testBackup'])->name('test');
        Route::get('/statistics', [DataBackupController::class, 'statistics'])->name('statistics');
        Route::post('/quick-backup', [DataBackupController::class, 'quickBackup'])->name('quick');
    });
    
    // Activity Logs
    Route::resource('/activity-logs', ActivityLogController::class);
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
        // Export Grades Routes
        Route::get('/reports/export-grades-excel', [ReportController::class, 'exportGradesExcel'])
            ->name('reports.export-grades-excel');
        
        Route::get('/reports/export-grades-pdf', [ReportController::class, 'exportGradesPdf'])
            ->name('reports.export-grades-pdf');
        
        // Export Feedback Routes
        Route::get('/reports/export-feedback-excel', [ReportController::class, 'exportFeedbackExcel'])
            ->name('reports.export-feedback-excel');
        
        Route::get('/reports/export-feedback-pdf', [ReportController::class, 'exportFeedbackPdf'])
            ->name('reports.export-feedback-pdf');
        
        // AJAX Route for fetching instructor sections
        Route::get('/reports/instructor/{instructor}/sections', [ReportController::class, 'getInstructorSections'])
            ->name('reports.instructor-sections');

        Route::get('/reports/export-activity-logs-excel', [ReportController::class, 'exportActivityLogsExcel'])
            ->name('reports.export-activity-logs-excel');
        Route::get('/reports/export-activity-logs-pdf', [ReportController::class, 'exportActivityLogsPdf'])
            ->name('reports.export-activity-logs-pdf');
    
    // Evaluations (Admin View)
    Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('/evaluations/{evaluation}', [EvaluationController::class, 'show'])->name('evaluations.show');
    Route::delete('/evaluations/{evaluation}', [EvaluationController::class, 'destroy'])->name('evaluations.destroy');
    
    // Feedback Records
    Route::resource('feedback-records', FeedbackRecordController::class);
    
    // // Settings
    // Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    
    // Subject instructor assignment routes
    Route::post('/subjects/{subject}/assign-instructors', [SubjectController::class, 'assignInstructors'])
        ->name('subjects.assignInstructors');
    Route::get('/subjects/{subject}/assign-instructors', [SubjectController::class, 'showAssignInstructorsForm'])
        ->name('subjects.showAssignInstructorsForm');
});



// ============================================================================
// INSTRUCTOR ROUTES
// ============================================================================
Route::middleware(['auth', 'role:instructor'])
    ->prefix('instructor')
    ->name('instructors.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');

        // Section Management
        Route::get('/sections', [InstructorSectionController::class, 'index'])->name('sections.index');
        Route::get('/sections/{section}', [InstructorSectionController::class, 'show'])->name('sections.show');

        // Subject Management
        Route::get('subjects', [InstructorSubjectController::class, 'index'])->name('subjects.index');
        Route::get('subjects/{subject}', [InstructorSubjectController::class, 'show'])->name('subjects.show');

        // Performance Task Management
        Route::get('performance-tasks', [PerformanceTaskController::class, 'index'])->name('performance-tasks.index');
        Route::get('performance-tasks/create', [PerformanceTaskController::class, 'create'])->name('performance-tasks.create');
        Route::post('performance-tasks', [PerformanceTaskController::class, 'store'])->name('performance-tasks.store');
        Route::get('performance-tasks/{task}', [PerformanceTaskController::class, 'show'])->name('performance-tasks.show');
        Route::get('performance-tasks/{task}/edit', [PerformanceTaskController::class, 'edit'])->name('performance-tasks.edit');
        Route::put('performance-tasks/{task}', [PerformanceTaskController::class, 'update'])->name('performance-tasks.update');
        Route::delete('performance-tasks/{task}', [PerformanceTaskController::class, 'destroy'])->name('performance-tasks.destroy');

        // Answer Sheets for each Performance Task
        Route::prefix('performance-task-answer-sheets')->name('performance-tasks.answer-sheets.')->group(function () {
            Route::get('/', [PerformanceTaskAnswerSheetController::class, 'index'])->name('index');
            Route::get('/{task}', [PerformanceTaskAnswerSheetController::class, 'show'])->name('show');
            Route::get('/{task}/step/{step}/edit', [PerformanceTaskAnswerSheetController::class, 'edit'])->name('edit');
            Route::put('/{task}/step/{step}', [PerformanceTaskAnswerSheetController::class, 'update'])->name('update');
            Route::post('/{task}/step/{step}', [PerformanceTaskAnswerSheetController::class, 'store'])->name('store');
        });

        Route::post('/{task}/step/{step}', [PerformanceTaskAnswerSheetController::class, 'store'])->name('performance-tasks.answer-sheets.store');

        
        // Performance Task Submissions
        // List all performance tasks with submission overview
        Route::get('/performance-tasks-submissions', [PerformanceTaskSubmissionController::class, 'index'])
            ->name('performance-tasks.submissions.index');

        Route::get('/performance-tasks/{task}/submissions/{student}/answer-sheet/{step}', 
            [PerformanceTaskSubmissionController::class, 'viewAnswerSheet'])
            ->name('performance-tasks.submissions.answer-sheet');

        // Show all student submissions for a specific task
        Route::get('/performance-tasks/{task}/submissions', [PerformanceTaskSubmissionController::class, 'show'])
            ->name('performance-tasks.submissions.show');

        // Show detailed submission for a single student on a specific task
        Route::get('/performance-tasks/{task}/submissions/student/{student}', [PerformanceTaskSubmissionController::class, 'showStudent'])
            ->name('performance-tasks.submissions.show-student');

        // Feedback routes for student submissions
        Route::get('/performance-tasks/{task}/submissions/student/{student}/step/{step}/feedback', [PerformanceTaskSubmissionController::class, 'feedbackForm'])
            ->name('performance-tasks.submissions.feedback-form');
        
        Route::post('/performance-tasks/{task}/submissions/student/{student}/step/{step}/feedback', [PerformanceTaskSubmissionController::class, 'storeFeedback'])
            ->name('performance-tasks.submissions.store-feedback');

        // Export Routes
        Route::get('/performance-tasks-submissions/export/excel', [PerformanceTaskSubmissionExportController::class, 'exportExcel'])
            ->name('performance-tasks.submissions.export.excel');
        
        Route::get('/performance-tasks-submissions/export/pdf', [PerformanceTaskSubmissionExportController::class, 'exportPdf'])
            ->name('performance-tasks.submissions.export.pdf');

        Route::resource('feedback-records', InstructorFeedbackRecordController::class)
        ->names('feedback-records')
        ->only(['index', 'show']);

});

// Student Progress Routes (Instructor Side)
Route::middleware(['auth', 'role:instructor'])->group(function () {
    Route::get('/instructor/progress', [StudentProgressesController::class, 'index'])
        ->name('instructors.progress.index');
    Route::get('/instructor/progress/{student}', [StudentProgressesController::class, 'show'])
        ->name('instructors.progress.show');
});

// ============================================================================
// STUDENT ROUTES
// ============================================================================
Route::middleware(['auth', 'role:student'])->prefix('students')->name('students.')->group(function () {
    
    // Dashboard & Main Views
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/assignments', [StudentController::class, 'viewAssignments'])->name('assignments');
    
    // Progress & Achievements
    Route::get('/progress', [StudentProgressController::class, 'progress'])->name('progress');
    Route::get('/achievements', [StudentProgressController::class, 'achievements'])->name('achievements');
    
    // Todo Management
    Route::prefix('todo')->group(function () {
        Route::get('/{status?}', [TodoController::class, 'index'])
            ->where('status', 'missing|assigned|late|submitted|graded')
            ->name('todo.index');
    });
    
    // Subject Management
    Route::get('/subjects', [\App\Http\Controllers\Students\StudentSubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/{id}', [\App\Http\Controllers\Students\StudentSubjectController::class, 'show'])->name('subjects.show');
    
    // Performance Tasks
    Route::get('/performance-tasks', [StudentPerformanceTaskController::class, 'index'])
        ->name('performance-tasks.index');

    Route::get('/performance-tasks/progress/{taskId?}', [StudentPerformanceTaskController::class, 'progress'])
        ->name('performance-tasks.progress');

    Route::get('/performance-tasks/{id}', [StudentPerformanceTaskController::class, 'show'])
        ->name('performance-tasks.show');

    Route::get('/performance-tasks/{id}/step/{step}', [StudentPerformanceTaskController::class, 'step'])
        ->name('performance-tasks.step');

    Route::post('/performance-tasks/{id}/step/{step}/save', [StudentPerformanceTaskController::class, 'saveStep'])
        ->name('performance-tasks.save-step');

    Route::get('/performance-tasks/{id}/my-progress', [StudentPerformanceTaskController::class, 'myProgress'])
        ->name('performance-tasks.my-progress');
    
    Route::get('/performance-tasks/{id}/step/{step}/feedback', [StudentPerformanceTaskController::class, 'viewFeedback'])
        ->name('performance-tasks.view-feedback');

    Route::post('/performance-tasks/submit', [StudentPerformanceTaskController::class, 'submit'])
        ->name('performance-tasks.submit');
        
    Route::get('performance-tasks/{id}/step/{step}/answers', [StudentPerformanceTaskController::class, 'showAnswers'])
        ->name('performance-tasks.show-answers');
    
    // Feedback Management
    Route::prefix('feedback')->name('feedback.')->group(function () {
        Route::get('/', [FeedbackController::class, 'index'])->name('index');
        Route::get('/create', [FeedbackController::class, 'create'])->name('create');
        Route::post('/', [FeedbackController::class, 'store'])->name('store');
        Route::post('/mark-all-read', [FeedbackController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/task/{taskId}', [FeedbackController::class, 'byTask'])->name('by-task');
        Route::get('/{feedback}', [FeedbackController::class, 'show'])->name('show');
        Route::post('/{feedback}/mark-read', [FeedbackController::class, 'markAsRead'])->name('mark-read');
        Route::get('/{feedback}/export', [FeedbackController::class, 'export'])->name('export');
        Route::delete('/{feedback}', [FeedbackController::class, 'destroy'])->name('destroy');
    });
    
    // Evaluation Management (Student View)
    Route::get('/evaluations/create', [EvaluationController::class, 'create'])->name('evaluations.create');
    Route::post('/evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');
    Route::get('/my-evaluations', [EvaluationController::class, 'myEvaluations'])->name('evaluations.index');
    
    // Hide the Leaderboard name
    Route::patch('/profile/leaderboard-privacy', [ProfileController::class, 'updateLeaderboardPrivacy'])
         ->name('updateLeaderboardPrivacy');
});


require __DIR__.'/auth.php';
