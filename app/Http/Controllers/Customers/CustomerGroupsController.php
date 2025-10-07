<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Customers\CustomerGroupRepositoryInterface;
use App\Repositories\Contracts\Customers\CustomerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerGroupsController extends Controller
{
    protected $groupRepo;
    protected $customerRepo;

    public function __construct(
        CustomerGroupRepositoryInterface $groupRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->groupRepo = $groupRepository;
        $this->customerRepo = $customerRepository;
    }

    public function index(Request $request)
    {
        $query = $this->groupRepo->query();

        // Name filter
        if ($request->filled('name')) {
            $query->where('name', 'ILIKE', "%{$request->name}%");
        }

        // Code filter
        if ($request->filled('code')) {
            $query->where('code', 'ILIKE', "%{$request->code}%");
        }

        // Active filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        $groups = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $statistics = $this->groupRepo->getStatistics();

        return view('pages.customers.customer-groups.index', compact('groups', 'statistics'));
    }

    public function members(Request $request, $id)
    {
        $group = $this->groupRepo->findById($id);

        if (!$group) {
            abort(404, 'Group not found');
        }

        // Get members with pagination
        $members = DB::table('customer_group_members as cgm')
            ->join('customers as c', 'cgm.customer_id', '=', 'c.id')
            ->where('cgm.customer_group_id', $id)
            ->select('c.*', 'cgm.joined_at')
            ->orderBy('cgm.joined_at', 'desc')
            ->paginate(20);

        // Get all customers for adding
        $allCustomers = $this->customerRepo->getAll();

        return view('pages.customers.customer-groups.members', compact('group', 'members', 'allCustomers'));
    }
}
