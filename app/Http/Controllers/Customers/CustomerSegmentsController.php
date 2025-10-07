<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Customers\CustomerSegmentRepositoryInterface;
use Illuminate\Http\Request;

class CustomerSegmentsController extends Controller
{
    protected $segmentRepo;

    public function __construct(CustomerSegmentRepositoryInterface $segmentRepository)
    {
        $this->segmentRepo = $segmentRepository;
    }

    public function index(Request $request)
    {
        $query = $this->segmentRepo->query();

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

        // Auto assign filter
        if ($request->filled('is_auto_assign')) {
            $query->where('is_auto_assign', $request->is_auto_assign === '1');
        }

        $segments = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $statistics = $this->segmentRepo->getStatistics();

        return view('pages.customers.customer-segments.index', compact('segments', 'statistics'));
    }

}
