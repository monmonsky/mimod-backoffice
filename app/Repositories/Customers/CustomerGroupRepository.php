<?php

namespace App\Repositories\Customers;

use App\Repositories\Contracts\Customers\CustomerGroupRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CustomerGroupRepository implements CustomerGroupRepositoryInterface
{
    protected $tableName = 'customer_groups';
    protected $membersTableName = 'customer_group_members';

    private function table()
    {
        return DB::table($this->tableName);
    }

    private function membersTable()
    {
        return DB::table($this->membersTableName);
    }

    public function query()
    {
        return $this->table();
    }

    public function getAll()
    {
        return $this->table()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById($id)
    {
        return $this->table()->where('id', $id)->first();
    }

    public function findByCode($code)
    {
        return $this->table()->where('code', $code)->first();
    }

    public function create(array $data)
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $id = $this->table()->insertGetId($data);
        return $this->findById($id);
    }

    public function update($id, array $data)
    {
        $data['updated_at'] = now();
        $this->table()->where('id', $id)->update($data);
        return $this->findById($id);
    }

    public function delete($id)
    {
        return $this->table()->where('id', $id)->delete();
    }

    public function getActive()
    {
        return $this->table()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getStatistics()
    {
        $totalGroups = $this->table()->count();
        $activeGroups = $this->table()->where('is_active', true)->count();
        $totalMembers = $this->membersTable()->count();
        $avgMembersPerGroup = $totalGroups > 0 ? $totalMembers / $totalGroups : 0;

        return (object) [
            'total_groups' => $totalGroups,
            'active_groups' => $activeGroups,
            'total_members' => $totalMembers,
            'avg_members_per_group' => $avgMembersPerGroup,
        ];
    }

    public function getMembers($groupId)
    {
        return DB::table('customer_group_members as cgm')
            ->join('customers as c', 'cgm.customer_id', '=', 'c.id')
            ->where('cgm.customer_group_id', $groupId)
            ->select('c.*', 'cgm.joined_at')
            ->orderBy('cgm.joined_at', 'desc')
            ->get();
    }

    public function addMember($groupId, $customerId)
    {
        // Check if already a member
        if ($this->isMember($groupId, $customerId)) {
            return false;
        }

        $this->membersTable()->insert([
            'customer_group_id' => $groupId,
            'customer_id' => $customerId,
            'joined_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update member count
        $this->updateMemberCount($groupId);

        return true;
    }

    public function removeMember($groupId, $customerId)
    {
        $deleted = $this->membersTable()
            ->where('customer_group_id', $groupId)
            ->where('customer_id', $customerId)
            ->delete();

        // Update member count
        $this->updateMemberCount($groupId);

        return $deleted > 0;
    }

    public function isMember($groupId, $customerId)
    {
        return $this->membersTable()
            ->where('customer_group_id', $groupId)
            ->where('customer_id', $customerId)
            ->exists();
    }

    private function updateMemberCount($groupId)
    {
        $count = $this->membersTable()
            ->where('customer_group_id', $groupId)
            ->count();

        $this->table()
            ->where('id', $groupId)
            ->update(['member_count' => $count]);
    }
}
