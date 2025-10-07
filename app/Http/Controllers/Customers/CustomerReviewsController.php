<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Customers\ProductReviewRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerReviewsController extends Controller
{
    protected $reviewRepo;

    public function __construct(ProductReviewRepositoryInterface $reviewRepository)
    {
        $this->reviewRepo = $reviewRepository;
    }

    public function index(Request $request)
    {
        // Build query with joins
        $query = DB::table('product_reviews as pr')
            ->join('products as p', 'pr.product_id', '=', 'p.id')
            ->join('customers as c', 'pr.customer_id', '=', 'c.id')
            ->select(
                'pr.*',
                'p.name as product_name',
                'c.name as customer_name',
                'c.email as customer_email'
            );

        // Filter by product
        if ($request->filled('product_name')) {
            $query->where('p.name', 'ILIKE', "%{$request->product_name}%");
        }

        // Filter by customer
        if ($request->filled('customer_name')) {
            $query->where(function($q) use ($request) {
                $q->where('c.name', 'ILIKE', "%{$request->customer_name}%")
                  ->orWhere('c.email', 'ILIKE', "%{$request->customer_name}%");
            });
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('pr.rating', $request->rating);
        }

        // Filter by approval status
        if ($request->filled('is_approved')) {
            $query->where('pr.is_approved', $request->is_approved === '1');
        }

        // Filter by verified purchase
        if ($request->filled('is_verified')) {
            $query->where('pr.is_verified_purchase', $request->is_verified === '1');
        }

        $reviews = $query->orderBy('pr.created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $statistics = $this->reviewRepo->getStatistics();

        return view('pages.customers.customer-reviews.index', compact('reviews', 'statistics'));
    }

}
