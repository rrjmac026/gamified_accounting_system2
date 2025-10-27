<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Loggable
{
    /**
     * Get identifying attributes for the model
     */
    protected function getModelIdentifiers(Model $model): array
    {
        // First check if model has defined what fields to log
        if (method_exists($model, 'getLoggableAttributes')) {
            return $model->getLoggableAttributes();
        }

        // Default identifier fields by model type
        $defaultIdentifiers = [
            'User' => ['name', 'email', 'role'],
            'Student' => ['user.name', 'student_id', 'course.course_code', 'year_level', 'section.section_name'],
            'Instructor' => ['user.name', 'department', 'employee_id'],
            'Course' => ['course_code', 'course_name', 'department'],
            'Section' => ['section_name', 'course.course_code', 'year_level', 'instructor.user.name'],
            'Task' => ['title', 'type', 'due_date', 'points'],
            'TaskQuestion' => ['question', 'task.title', 'type', 'points'],
            'TaskSubmission' => ['student.user.name', 'task.title', 'status', 'submitted_at'],
            'TaskAnswer' => ['student.user.name', 'task_question.question', 'answer', 'is_correct'],
            'Badge' => ['name', 'description', 'points_required', 'type'],
            'StudentBadge' => ['student.user.name', 'badge.name', 'earned_at', 'progress'],
            'StudentSubject' => ['student.user.name', 'subject.subject_code', 'semester', 'academic_year'],
            'StudentTask' => ['student.user.name', 'task.title', 'status', 'score'],
            'QuizScore' => ['student.user.name', 'task.title', 'score', 'total_score'],
            'LearningMaterial' => ['title', 'type', 'course.course_code', 'file_path'],
            'Evaluation' => ['student.user.name', 'instructor.user.name', 'course.course_code', 'rating'],
            'FeedbackRecord' => ['student.user.name', 'task.title', 'feedback', 'rating'],
            'XpTransaction' => ['student.user.name', 'points', 'reason', 'type'],
            'Leaderboard' => ['student.user.name', 'total_points', 'rank', 'course.course_code'],
            'SystemNotification' => ['title', 'message', 'type', 'recipient_type'],
            'SystemSetting' => ['key', 'value', 'category', 'description'],
            'GamificationSetting' => ['setting_key', 'setting_value', 'course.course_code', 'is_active'],
            'DataBackup' => ['backup_name', 'file_path', 'size', 'status'],
            'ErrorRecord' => ['error_type', 'message', 'user.name', 'occurred_at'],
            'PerformanceLog' => ['action', 'execution_time', 'memory_usage', 'user.name'],
            'ActivityLog' => ['action', 'model_type', 'user.name', 'performed_at'],
        ];

        $modelName = class_basename($model);
        
        // Get the default identifiers for this model type, or use some common fields
        $identifiers = $defaultIdentifiers[$modelName] ?? ['id', 'name', 'title', 'code'];

        $attributes = [];
        foreach ($identifiers as $identifier) {
            if (Str::contains($identifier, '.')) {
                // Handle nested relationship attributes (e.g., user.name, subject.subject_name)
                $value = $this->getNestedAttribute($model, $identifier);
                if ($value !== null) {
                    $attributes[$identifier] = $value;
                }
            } else {
                // Handle direct attributes
                if (isset($model->{$identifier})) {
                    $attributes[$identifier] = $model->{$identifier};
                }
            }
        }

        return array_filter($attributes);
    }

    /**
     * Get nested attribute value from model relationships
     */
    protected function getNestedAttribute(Model $model, string $path)
    {
        $parts = explode('.', $path);
        $current = $model;

        foreach ($parts as $part) {
            if ($current && isset($current->{$part})) {
                $current = $current->{$part};
            } else {
                return null;
            }
        }

        return $current;
    }

    /**
     * Generate a human-readable description for the activity
     */
    protected function generateActivityDescription(string $action, string $modelType, Model $model = null, array $additionalContext = []): string
    {
        if (!$model) {
            return ucfirst($action) . ' ' . $modelType;
        }

        $identifiers = $this->getModelIdentifiers($model);
        $modelName = class_basename($model);

        // Create a descriptive string based on model type and identifiers
        switch ($modelName) {
            case 'User':
                return ucfirst($action) . " user: " . ($identifiers['name'] ?? 'Unknown') . " (" . ($identifiers['email'] ?? 'Unknown') . ")";
                
            case 'Student':
                $name = $identifiers['user.name'] ?? $identifiers['name'] ?? 'Unknown';
                $course = $identifiers['course.course_code'] ?? '';
                $year = $identifiers['year_level'] ?? '';
                $section = $identifiers['section.section_name'] ?? '';
                return ucfirst($action) . " student: {$name}" . 
                       ($course ? " - {$course}" : '') .
                       ($year ? " Year {$year}" : '') .
                       ($section ? " Section {$section}" : '');
                       
            case 'Instructor':
                $name = $identifiers['user.name'] ?? $identifiers['name'] ?? 'Unknown';
                $dept = $identifiers['department'] ?? '';
                $empId = $identifiers['employee_id'] ?? '';
                return ucfirst($action) . " instructor: {$name}" .
                       ($empId ? " (ID: {$empId})" : '') .
                       ($dept ? " - {$dept}" : '');
                       
            case 'Course':
                $courseCode = $identifiers['course_code'] ?? 'Unknown';
                $courseName = $identifiers['course_name'] ?? 'Unknown';
                return ucfirst($action) . " course: {$courseCode} - {$courseName}";
                
            case 'Subject':
                $code = $identifiers['subject_code'] ?? '';
                $name = $identifiers['subject_name'] ?? '';
                $semester = $identifiers['semester'] ?? '';
                $ay = $identifiers['academic_year'] ?? '';
                return ucfirst($action) . " subject: {$code} - {$name}" .
                       ($semester && $ay ? " ({$semester} {$ay})" : '');
                       
            case 'Task':
            case 'Assignment':
            case 'Quiz':
            case 'Activity':
                $title = $identifiers['title'] ?? 'Unknown';
                $subject = $identifiers['subject.subject_name'] ?? '';
                $type = $identifiers['type'] ?? strtolower($modelName);
                return ucfirst($action) . " {$type}: {$title}" . ($subject ? " in {$subject}" : '');
                
            case 'Badge':
                $name = $identifiers['name'] ?? 'Unknown Badge';
                $pointsRequired = $identifiers['points_required'] ?? null;
                return ucfirst($action) . " badge: {$name}" . ($pointsRequired !== null ? " ({$pointsRequired} points)" : '');
                
            case 'Grade':
                $student = $identifiers['student.user.name'] ?? 'Unknown Student';
                $subject = $identifiers['subject.subject_name'] ?? 'Unknown Subject';
                $score = $identifiers['score'] ?? '';
                $total = $identifiers['total_points'] ?? '';
                return ucfirst($action) . " grade for {$student} in {$subject}" .
                       ($score && $total ? " ({$score}/{$total})" : '');
                       
            case 'Enrollment':
                $student = $identifiers['student.user.name'] ?? 'Unknown Student';
                $subject = $identifiers['subject.subject_name'] ?? 'Unknown Subject';
                return ucfirst($action) . " enrollment: {$student} in {$subject}";
                
            case 'Announcement':
                $title = $identifiers['title'] ?? 'Unknown';
                $subject = $identifiers['subject.subject_name'] ?? '';
                return ucfirst($action) . " announcement: {$title}" . ($subject ? " in {$subject}" : '');
                
            default:
                // Generic fallback - use the first available identifier
                $firstIdentifier = reset($identifiers);
                if ($firstIdentifier) {
                    return ucfirst($action) . " {$modelName}: {$firstIdentifier}";
                }
                return ucfirst($action) . " {$modelName} (ID: {$model->id})";
        }
    }

    /**
     * Log an activity
     *
     * @param string $action The action performed (e.g., 'created', 'updated', 'deleted')
     * @param string $modelType The model type (e.g., 'Course', 'User')
     * @param int|null $modelId The model ID
     * @param array $additionalDetails Additional details to store
     * @param int|null $userId Override the user ID (optional)
     * @return ActivityLog
     */
    protected function logActivity(
        string $action,
        string $modelType,
        ?int $modelId = null,
        array $additionalDetails = [],
        ?int $userId = null
    ): ActivityLog {
        // Get the model instance if modelId is provided
        $model = null;
        if ($modelId && class_exists("App\\Models\\{$modelType}")) {
            $modelClass = "App\\Models\\{$modelType}";
            $model = $modelClass::find($modelId);
        }

        // Build the details array
        $details = [
            'timestamp' => now()->toISOString(),
            'performed_by' => Auth::user()?->only(['id', 'name', 'email']) ?? ['id' => $userId],
        ];

        // Add model identifiers and description if available
        if ($model) {
            $identifiers = $this->getModelIdentifiers($model);
            $details['model_identifiers'] = $identifiers;
            $details['description'] = $this->generateActivityDescription($action, $modelType, $model);
        } else {
            $details['description'] = ucfirst($action) . ' ' . $modelType;
        }

        // Add any additional details
        $details = array_merge($details, $additionalDetails);

        // Create the activity log
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'performed_at' => now(),
        ]);
    }

    /**
     * Log multiple activities at once
     *
     * @param array $activities Array of activity data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function logMultipleActivities(array $activities)
    {
        $logs = collect();
        
        foreach ($activities as $activity) {
            $logs->push($this->logActivity(
                $activity['action'],
                $activity['model_type'],
                $activity['model_id'] ?? null,
                $activity['details'] ?? [],
                $activity['user_id'] ?? null
            ));
        }

        return $logs;
    }

    /**
     * Log model creation
     *
     * @param string $modelType
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $additionalDetails
     * @return ActivityLog
     */
    protected function logCreated(string $modelType, $model, array $additionalDetails = []): ActivityLog
    {
        // Get the important identifiers for this model
        $identifiers = $this->getModelIdentifiers($model);
        
        $details = array_merge([
            'model_data' => $identifiers, // Use identifiers instead of full toArray()
            'full_model_data' => $model->toArray(), // Keep full data for reference
        ], $additionalDetails);

        return $this->logActivity('created', $modelType, $model->id, $details);
    }

    /**
     * Log model update
     *
     * @param string $modelType
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $changes
     * @param array $additionalDetails
     * @return ActivityLog
     */
    protected function logUpdated(string $modelType, $model, array $changes = [], array $additionalDetails = []): ActivityLog
    {
        $modelChanges = $changes ?: $model->getChanges();
        
        // Create a more readable changes summary
        $changesSummary = [];
        foreach ($modelChanges as $field => $newValue) {
            $oldValue = $model->getOriginal($field);
            $changesSummary[] = "{$field}: '{$oldValue}' → '{$newValue}'";
        }

        $details = array_merge([
            'changes' => $modelChanges,
            'changes_summary' => $changesSummary,
            'original' => $model->getOriginal(),
        ], $additionalDetails);

        return $this->logActivity('updated', $modelType, $model->id, $details);
    }

    /**
     * Log model deletion
     *
     * @param string $modelType
     * @param int $modelId
     * @param array $modelData
     * @param array $additionalDetails
     * @return ActivityLog
     */
    protected function logDeleted(string $modelType, int $modelId, array $modelData = [], array $additionalDetails = []): ActivityLog
    {
        $details = array_merge([
            'deleted_data' => $modelData,
        ], $additionalDetails);

        return $this->logActivity('deleted', $modelType, $modelId, $details);
    }

    /**
     * Log model restoration (if using soft deletes)
     *
     * @param string $modelType
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $additionalDetails
     * @return ActivityLog
     */
    protected function logRestored(string $modelType, $model, array $additionalDetails = []): ActivityLog
    {
        return $this->logActivity('restored', $modelType, $model->id, $additionalDetails);
    }

    /**
     * Log bulk operations
     *
     * @param string $action
     * @param string $modelType
     * @param array $modelIds
     * @param array $additionalDetails
     * @return ActivityLog
     */
    protected function logBulkOperation(string $action, string $modelType, array $modelIds, array $additionalDetails = []): ActivityLog
    {
        $details = array_merge([
            'affected_ids' => $modelIds,
            'count' => count($modelIds),
            'description' => ucfirst($action) . ' ' . count($modelIds) . ' ' . Str::plural($modelType),
        ], $additionalDetails);

        return $this->logActivity("bulk_{$action}", $modelType, null, $details);
    }

    /**
     * Log custom activity with automatic model detection
     *
     * @param string $action
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @param array $details
     * @return ActivityLog
     */
    protected function logModelActivity(string $action, $model = null, array $details = []): ActivityLog
    {
        if ($model) {
            $modelType = class_basename($model);
            $modelId = $model->id;
        } else {
            $modelType = null;
            $modelId = null;
        }

        return $this->logActivity($action, $modelType, $modelId, $details);
    }

    /**
     * Log with context (useful for debugging)
     *
     * @param string $action
     * @param string $modelType
     * @param int|null $modelId
     * @param array $details
     * @param string $context
     * @return ActivityLog
     */
    protected function logWithContext(string $action, string $modelType, ?int $modelId, array $details, string $context): ActivityLog
    {
        $details['context'] = $context;
        $details['controller'] = get_class($this);
        $details['method'] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'] ?? 'unknown';
        
        return $this->logActivity($action, $modelType, $modelId, $details);
    }

    /**
     * Log a simplified activity (for quick logging)
     *
     * @param string $action
     * @param array $additionalDetails
     * @return ActivityLog
     */
    public function logSimpleActivity(string $action, array $additionalDetails = []): ActivityLog
    {
        return $this->logActivity($action, null, null, $additionalDetails);
    }

    /**
     * Enhanced helper method for logging with automatic model detection and rich details
     *
     * @param string $action
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $additionalDetails
     * @return ActivityLog
     */
    protected function logRichActivity(string $action, Model $model, array $additionalDetails = []): ActivityLog
    {
        $modelType = class_basename($model);
        
        // Get comprehensive details
        $details = array_merge([
            'model_identifiers' => $this->getModelIdentifiers($model),
            'action_summary' => $this->generateActivityDescription($action, $modelType, $model),
        ], $additionalDetails);

        return $this->logActivity($action, $modelType, $model->id, $details);
    }
}