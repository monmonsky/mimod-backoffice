<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserActivityRepositoryInterface;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    protected $activityRepo;

    public function __construct(UserActivityRepositoryInterface $activityRepository)
    {
        $this->activityRepo = $activityRepository;
    }

    /**
     * Display user activities list
     */
    public function index(Request $request)
    {
        // Get current user's role priority
        $currentUser = currentUser();
        $currentUserPriority = null;

        if ($currentUser && isset($currentUser['role_priority'])) {
            $currentUserPriority = $currentUser['role_priority'];
        }

        $filters = [
            'user_id' => $request->user_id,
            'action' => $request->action,
            'subject_type' => $request->subject_type,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'search' => $request->search,
            'per_page' => $request->per_page ?? 20,
            'current_user_priority' => $currentUserPriority,
        ];

        $activities = $this->activityRepo->getAll($filters);
        $statistics = $this->activityRepo->getStatistics();

        return view('pages.access-control.user-activities.index', compact('activities', 'statistics', 'filters'));
    }

    /**
     * Show activity detail
     */
    public function show($id)
    {
        $activity = $this->activityRepo->findById($id);

        if (!$activity) {
            abort(404, 'Activity not found');
        }

        // Decode properties JSON
        if ($activity->properties) {
            $activity->properties = json_decode($activity->properties, true);
        }

        // Return JSON for AJAX request
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $activity,
            ]);
        }

        return view('pages.access-control.user-activities.show', compact('activity'));
    }

    /**
     * Clear all activity logs
     */
    public function clear(Request $request)
    {
        try {
            $this->activityRepo->clearLogs();

            // Log this action using helper
            logActivity('clear_logs', 'Cleared all user activity logs', 'UserActivity');

            return response()->json([
                'success' => true,
                'message' => 'All activity logs cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear logs: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        try {
            // Get current user's role priority
            $currentUser = currentUser();
            $currentUserPriority = null;

            if ($currentUser && isset($currentUser['role_priority'])) {
                $currentUserPriority = $currentUser['role_priority'];
            }

            $filters = [
                'user_id' => $request->user_id,
                'action' => $request->action,
                'subject_type' => $request->subject_type,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'current_user_priority' => $currentUserPriority,
            ];

            $activities = $this->activityRepo->exportLogs($filters);

            // Log this action using helper
            logActivity(
                'export',
                'Exported user activity logs (' . count($activities) . ' records)',
                'UserActivity',
                null,
                ['record_count' => count($activities), 'filters' => $filters]
            );

            // Create CSV
            $filename = 'user_activities_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($activities) {
                $file = fopen('php://output', 'w');

                // CSV Headers
                fputcsv($file, [
                    'ID',
                    'User Name',
                    'User Email',
                    'Action',
                    'Subject Type',
                    'Subject ID',
                    'Description',
                    'IP Address',
                    'Date & Time',
                ]);

                // CSV Data
                foreach ($activities as $activity) {
                    fputcsv($file, [
                        $activity->id,
                        $activity->user_name,
                        $activity->user_email,
                        $activity->action,
                        $activity->subject_type ?? '-',
                        $activity->subject_id ?? '-',
                        $activity->description ?? '-',
                        $activity->ip_address ?? '-',
                        $activity->created_at,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export logs: ' . $e->getMessage(),
            ], 500);
        }
    }
}
