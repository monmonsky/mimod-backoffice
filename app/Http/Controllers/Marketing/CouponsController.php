<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Marketing\CouponRepositoryInterface;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    protected $couponRepo;

    public function __construct(CouponRepositoryInterface $couponRepository)
    {
        $this->couponRepo = $couponRepository;
    }

    public function index(Request $request)
    {
        $query = $this->couponRepo->query();

        // Filter by code or name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'ILIKE', "%{$search}%")
                  ->orWhere('name', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', now());
            } elseif ($request->status === 'upcoming') {
                $query->where('start_date', '>', now());
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = 'desc';

        if (in_array($sortBy, ['code', 'name'])) {
            $sortDirection = 'asc';
        }

        $coupons = $query->orderBy($sortBy, $sortDirection)
            ->paginate(20)
            ->withQueryString();

        $statistics = $this->couponRepo->getStatistics();

        return view('pages.marketing.coupons.index', compact('coupons', 'statistics'));
    }
}
