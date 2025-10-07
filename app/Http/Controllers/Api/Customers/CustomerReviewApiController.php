<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\Customers\ProductReviewRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerReviewApiController extends Controller
{
    protected $reviewRepo;
    protected $responseBuilder;
    protected $response;

    public function __construct(ProductReviewRepositoryInterface $reviewRepository)
    {
        $this->reviewRepo = $reviewRepository;
        $this->responseBuilder = new ResultBuilder;
        $this->response = new Response;
    }

    public function index(Request $request)
    {
        try {
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

            $perPage = $request->get('per_page', 20);
            $reviews = $query->orderBy('pr.created_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();

            $statistics = $this->reviewRepo->getStatistics();

            $this->responseBuilder->setMessage("Product reviews retrieved successfully.");
            $this->responseBuilder->setData([
                'reviews' => $reviews,
                'statistics' => $statistics
            ]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function show($id)
    {
        try {
            $review = DB::table('product_reviews as pr')
                ->join('products as p', 'pr.product_id', '=', 'p.id')
                ->join('customers as c', 'pr.customer_id', '=', 'c.id')
                ->leftJoin('users as u', 'pr.approved_by', '=', 'u.id')
                ->select(
                    'pr.*',
                    'p.name as product_name',
                    'c.name as customer_name',
                    'c.email as customer_email',
                    'u.name as approved_by_name'
                )
                ->where('pr.id', $id)
                ->first();

            if (!$review) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Product review not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->responseBuilder->setMessage("Product review retrieved successfully.");
            $this->responseBuilder->setData(['review' => $review]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $userId = auth()->id();
            $review = $this->reviewRepo->approve($id, $userId);

            DB::commit();

            $this->responseBuilder->setMessage("Review approved successfully.");
            $this->responseBuilder->setData(['review' => $review]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function respond(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'admin_response' => 'required|string',
            ]);

            if ($validator->fails()) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setStatusCode('422');
                $this->responseBuilder->setMessage($validator->errors()->first());
                $this->responseBuilder->setData(['errors' => $validator->errors()]);
                return response()->json($this->response->generateResponse($this->responseBuilder), 422);
            }

            DB::beginTransaction();

            $validated = $validator->validated();
            $userId = auth()->id();
            $review = $this->reviewRepo->respond($id, $validated['admin_response'], $userId);

            DB::commit();

            $this->responseBuilder->setMessage("Response submitted successfully.");
            $this->responseBuilder->setData(['review' => $review]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->reviewRepo->delete($id);

            $this->responseBuilder->setMessage("Review deleted successfully.");
            $this->responseBuilder->setData([]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
