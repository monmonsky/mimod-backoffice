<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Marketing\FlashSaleRepositoryInterface;
use Illuminate\Http\Request;

class FlashSalesController extends Controller
{
    protected $flashSaleRepo;

    public function __construct(FlashSaleRepositoryInterface $flashSaleRepository)
    {
        $this->flashSaleRepo = $flashSaleRepository;
    }

    public function index(Request $request)
    {
        $query = $this->flashSaleRepo->query();

        // Filter by name
        if ($request->filled('search')) {
            $query->where('name', 'ILIKE', "%{$request->search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where('start_time', '<=', now())
                      ->where('end_time', '>=', now());
            } elseif ($request->status === 'upcoming') {
                $query->where('is_active', true)
                      ->where('start_time', '>', now());
            } elseif ($request->status === 'expired') {
                $query->where('end_time', '<', now());
            }
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'priority');
        $sortDirection = 'desc';

        if (in_array($sortBy, ['name'])) {
            $sortDirection = 'asc';
        }

        $flashSales = $query->orderBy($sortBy, $sortDirection)
            ->paginate(20)
            ->withQueryString();

        $statistics = $this->flashSaleRepo->getStatistics();

        return view('pages.marketing.flash-sales.index', compact('flashSales', 'statistics'));
    }
}
