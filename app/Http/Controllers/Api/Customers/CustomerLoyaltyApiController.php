<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\Customers\LoyaltyProgramRepositoryInterface;
use App\Repositories\Contracts\Customers\LoyaltyTransactionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerLoyaltyApiController extends Controller
{
    protected $programRepo;
    protected $transactionRepo;
    protected $responseBuilder;
    protected $response;

    public function __construct(
        LoyaltyProgramRepositoryInterface $programRepository,
        LoyaltyTransactionRepositoryInterface $transactionRepository
    ) {
        $this->programRepo = $programRepository;
        $this->transactionRepo = $transactionRepository;
        $this->responseBuilder = new ResultBuilder;
        $this->response = new Response;
    }

    public function index(Request $request)
    {
        try {
            // Get programs
            $programs = $this->programRepo->getAll();

            // Get transactions with customer info
            $query = DB::table('loyalty_transactions as lt')
                ->join('customers as c', 'lt.customer_id', '=', 'c.id')
                ->select('lt.*', 'c.name as customer_name', 'c.email as customer_email');

            // Filter by transaction type
            if ($request->filled('transaction_type')) {
                $query->where('lt.transaction_type', $request->transaction_type);
            }

            // Filter by customer
            if ($request->filled('customer_search')) {
                $search = $request->customer_search;
                $query->where(function($q) use ($search) {
                    $q->where('c.name', 'ILIKE', "%{$search}%")
                      ->orWhere('c.email', 'ILIKE', "%{$search}%");
                });
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->where('lt.created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('lt.created_at', '<=', $request->date_to . ' 23:59:59');
            }

            $perPage = $request->get('per_page', 20);
            $transactions = $query->orderBy('lt.created_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();

            // Get statistics
            $statistics = $this->programRepo->getStatistics();

            $this->responseBuilder->setMessage("Loyalty data retrieved successfully.");
            $this->responseBuilder->setData([
                'programs' => $programs,
                'transactions' => $transactions,
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

    // Program CRUD
    public function showProgram($id)
    {
        try {
            $program = $this->programRepo->findById($id);

            if (!$program) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Loyalty program not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->responseBuilder->setMessage("Loyalty program retrieved successfully.");
            $this->responseBuilder->setData(['program' => $program]);
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

    public function storeProgram(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:loyalty_programs,code',
                'description' => 'nullable|string',
                'points_per_currency' => 'required|numeric|min:0',
                'currency_per_point' => 'required|numeric|min:0',
                'min_points_redeem' => 'required|integer|min:0',
                'points_expiry_days' => 'nullable|integer|min:0',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'is_active' => 'boolean',
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
            $validated['is_active'] = $request->has('is_active');

            $program = $this->programRepo->create($validated);

            DB::commit();

            logActivity('create', "Created loyalty program: {$program->name}", 'loyalty_program', $program->id);

            $this->responseBuilder->setMessage("Loyalty program created successfully.");
            $this->responseBuilder->setData(['program' => $program]);
            return response()->json($this->response->generateResponse($this->responseBuilder), 201);
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

    public function updateProgram(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:loyalty_programs,code,' . $id,
                'description' => 'nullable|string',
                'points_per_currency' => 'required|numeric|min:0',
                'currency_per_point' => 'required|numeric|min:0',
                'min_points_redeem' => 'required|integer|min:0',
                'points_expiry_days' => 'nullable|integer|min:0',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'is_active' => 'boolean',
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
            $validated['is_active'] = $request->has('is_active');

            $program = $this->programRepo->update($id, $validated);

            DB::commit();

            logActivity('update', "Updated loyalty program: {$program->name}", 'loyalty_program', (int)$id);

            $this->responseBuilder->setMessage("Loyalty program updated successfully.");
            $this->responseBuilder->setData(['program' => $program]);
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

    public function destroyProgram($id)
    {
        try {
            $program = $this->programRepo->findById($id);
            $programName = $program ? $program->name : "ID: {$id}";

            $this->programRepo->delete($id);

            logActivity('delete', "Deleted loyalty program: {$programName}", 'loyalty_program', (int)$id);

            $this->responseBuilder->setMessage("Loyalty program deleted successfully.");
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

    // Transaction CRUD
    public function storeTransaction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'transaction_type' => 'required|in:earn,redeem,adjust,expire',
                'points' => 'required|integer',
                'description' => 'required|string',
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
            $customerId = $validated['customer_id'];
            $type = $validated['transaction_type'];
            $points = abs($validated['points']);
            $description = $validated['description'];

            switch ($type) {
                case 'earn':
                    $transaction = $this->transactionRepo->earnPoints($customerId, $points, null, $description);
                    break;
                case 'redeem':
                    $transaction = $this->transactionRepo->redeemPoints($customerId, $points, $description);
                    break;
                case 'adjust':
                    // Points can be positive or negative for adjustments
                    $transaction = $this->transactionRepo->adjustPoints($customerId, $validated['points'], $description);
                    break;
                case 'expire':
                    $transaction = $this->transactionRepo->expirePoints($customerId, $points, $description);
                    break;
            }

            DB::commit();

            logActivity('create', "Created loyalty transaction: {$type} - {$points} points for customer ID: {$customerId}", 'loyalty_transaction', $transaction->id ?? null);

            $this->responseBuilder->setMessage("Loyalty transaction created successfully.");
            $this->responseBuilder->setData(['transaction' => $transaction]);
            return response()->json($this->response->generateResponse($this->responseBuilder), 201);
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

    public function getCustomerBalance($customerId)
    {
        try {
            $balance = $this->transactionRepo->getCustomerBalance($customerId);

            $this->responseBuilder->setMessage("Customer balance retrieved successfully.");
            $this->responseBuilder->setData(['balance' => $balance]);
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
