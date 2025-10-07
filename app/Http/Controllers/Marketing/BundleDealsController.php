<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Marketing\BundleDealRepositoryInterface;
use Illuminate\Http\Request;

class BundleDealsController extends Controller
{
    protected $bundleRepo;

    public function __construct(BundleDealRepositoryInterface $bundleRepository)
    {
        $this->bundleRepo = $bundleRepository;
    }

    public function index(Request $request)
    {
        $query = $this->bundleRepo->query();

        // Filter by name
        if ($request->filled('search')) {
            $query->where('name', 'ILIKE', "%{$request->search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
            } elseif ($request->status === 'upcoming') {
                $query->where('is_active', true)
                      ->where('start_date', '>', now());
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', now());
            }
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = 'desc';

        if (in_array($sortBy, ['name'])) {
            $sortDirection = 'asc';
        }

        $bundles = $query->orderBy($sortBy, $sortDirection)
            ->paginate(20)
            ->withQueryString();

        $statistics = $this->bundleRepo->getStatistics();

        return view('pages.marketing.bundle-deals.index', compact('bundles', 'statistics'));
    }
}
