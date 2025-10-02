<?php

namespace App\Repositories\AccessControl;

use App\Repositories\Contracts\UserActivityRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserActivityRepository implements UserActivityRepositoryInterface
{
    protected $tableName = 'user_activities';

    /**
     * Get fresh query builder instance
     */
    private function table()
    {
        return DB::table($this->tableName);
    }

    /**
     * Get all activities with filters
     */
    public function getAll($filters = [])
    {
        $query = $this->table()
            ->join('users', 'user_activities.user_id', '=', 'users.id')
            ->leftJoin('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
            ->select(
                'user_activities.*',
                'users.name as user_name',
                'users.email as user_email',
                'roles.priority as user_role_priority'
            )
            ->orderBy('user_activities.created_at', 'desc');

        // Filter by role priority (user can only see their level or below)
        if (!empty($filters['current_user_priority'])) {
            $query->where(function($q) use ($filters) {
                $q->where('roles.priority', '<=', $filters['current_user_priority'])
                  ->orWhereNull('roles.priority'); // Include users without role
            });
        }

        // Apply filters
        if (!empty($filters['user_id'])) {
            $query->where('user_activities.user_id', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $query->where('user_activities.action', $filters['action']);
        }

        if (!empty($filters['subject_type'])) {
            $query->where('user_activities.subject_type', $filters['subject_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('user_activities.created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('user_activities.created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('users.name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('users.email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('user_activities.description', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Find activity by ID
     */
    public function findById($id)
    {
        return $this->table()
            ->join('users', 'user_activities.user_id', '=', 'users.id')
            ->select(
                'user_activities.*',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->where('user_activities.id', $id)
            ->first();
    }

    /**
     * Create new activity log
     */
    public function create(array $data)
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // Convert properties to JSON if it's an array
        if (isset($data['properties']) && is_array($data['properties'])) {
            $data['properties'] = json_encode($data['properties']);
        }

        $id = $this->table()->insertGetId($data);

        return $this->findById($id);
    }

    /**
     * Clear all logs
     */
    public function clearLogs()
    {
        return $this->table()->delete();
    }

    /**
     * Get statistics
     */
    public function getStatistics()
    {
        $total = $this->table()->count();

        $today = $this->table()
            ->whereDate('created_at', today())
            ->count();

        $this_week = $this->table()
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $this_month = $this->table()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total' => $total,
            'today' => $today,
            'this_week' => $this_week,
            'this_month' => $this_month,
        ];
    }

    /**
     * Export logs
     */
    public function exportLogs($filters = [])
    {
        $query = $this->table()
            ->join('users', 'user_activities.user_id', '=', 'users.id')
            ->leftJoin('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
            ->select(
                'user_activities.id',
                'users.name as user_name',
                'users.email as user_email',
                'user_activities.action',
                'user_activities.subject_type',
                'user_activities.subject_id',
                'user_activities.description',
                'user_activities.ip_address',
                'user_activities.created_at'
            )
            ->orderBy('user_activities.created_at', 'desc');

        // Filter by role priority (user can only see their level or below)
        if (!empty($filters['current_user_priority'])) {
            $query->where(function($q) use ($filters) {
                $q->where('roles.priority', '<=', $filters['current_user_priority'])
                  ->orWhereNull('roles.priority'); // Include users without role
            });
        }

        // Apply same filters as getAll
        if (!empty($filters['user_id'])) {
            $query->where('user_activities.user_id', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $query->where('user_activities.action', $filters['action']);
        }

        if (!empty($filters['subject_type'])) {
            $query->where('user_activities.subject_type', $filters['subject_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('user_activities.created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('user_activities.created_at', '<=', $filters['date_to']);
        }

        return $query->get();
    }
}
