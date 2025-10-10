<?php

namespace App\Http\Controllers\Api\AccessControl;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\UserActivityRepositoryInterface;
use Illuminate\Http\Request;

class UserActivityApiController extends Controller
{
    protected $activityRepo;
    protected $response;

    public function __construct(UserActivityRepositoryInterface $activityRepo, Response $response)
    {
        $this->activityRepo = $activityRepo;
        $this->response = $response;
    }

    /**
     * Get all user activities with filters
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'user_id' => $request->get('user_id'),
                'action' => $request->get('action'),
                'subject_type' => $request->get('subject_type'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
                'search' => $request->get('search'),
                'per_page' => $request->get('per_page', 20),
            ];

            // Add current user priority for filtering
            $currentUser = currentUser();
            if ($currentUser && isset($currentUser['role_priority'])) {
                $filters['current_user_priority'] = $currentUser['role_priority'];
            }

            $activities = $this->activityRepo->getAll($filters);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('User activities retrieved successfully')
                ->setData($activities);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve user activities: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single user activity by ID
     */
    public function show($id)
    {
        try {
            $activity = $this->activityRepo->findById($id);

            if (!$activity) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Activity not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Decode properties if exists
            if ($activity->properties) {
                $activity->properties = json_decode($activity->properties, true);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('User activity retrieved successfully')
                ->setData($activity);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve user activity: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get user activity statistics
     */
    public function statistics()
    {
        try {
            $stats = $this->activityRepo->getStatistics();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Statistics retrieved successfully')
                ->setData($stats);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve statistics: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get activities by current user
     */
    public function myActivities(Request $request)
    {
        try {
            $userId = userId();

            if (!$userId) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('401')
                    ->setMessage('Unauthorized')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 401);
            }

            $filters = [
                'user_id' => $userId,
                'action' => $request->get('action'),
                'subject_type' => $request->get('subject_type'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
                'per_page' => $request->get('per_page', 20),
            ];

            $activities = $this->activityRepo->getAll($filters);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Your activities retrieved successfully')
                ->setData($activities);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve your activities: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Export user activities
     */
    public function export(Request $request)
    {
        try {
            $filters = [
                'user_id' => $request->get('user_id'),
                'action' => $request->get('action'),
                'subject_type' => $request->get('subject_type'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
            ];

            // Add current user priority for filtering
            $currentUser = currentUser();
            if ($currentUser && isset($currentUser['role_priority'])) {
                $filters['current_user_priority'] = $currentUser['role_priority'];
            }

            $activities = $this->activityRepo->exportLogs($filters);

            // Convert to CSV format
            $csv = "ID,User Name,User Email,Action,Subject Type,Subject ID,Description,IP Address,Created At\n";

            foreach ($activities as $activity) {
                $csv .= implode(',', [
                    $activity->id,
                    '"' . str_replace('"', '""', $activity->user_name) . '"',
                    '"' . str_replace('"', '""', $activity->user_email) . '"',
                    '"' . str_replace('"', '""', $activity->action) . '"',
                    '"' . str_replace('"', '""', $activity->subject_type ?? '') . '"',
                    $activity->subject_id ?? '',
                    '"' . str_replace('"', '""', $activity->description ?? '') . '"',
                    '"' . str_replace('"', '""', $activity->ip_address ?? '') . '"',
                    '"' . str_replace('"', '""', $activity->created_at) . '"',
                ]) . "\n";
            }

            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="user-activities-' . date('Y-m-d-His') . '.csv"');

        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to export activities: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Clear all activity logs
     */
    public function clear()
    {
        try {
            $deletedCount = $this->activityRepo->clearLogs();

            logActivity('delete', "Cleared all user activity logs ({$deletedCount} records)", 'user_activity', null);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('All activity logs cleared successfully')
                ->setData(['deleted_count' => $deletedCount]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to clear activity logs: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
