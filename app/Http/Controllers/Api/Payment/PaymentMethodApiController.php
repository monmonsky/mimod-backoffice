<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\Payment\PaymentMethodRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodApiController extends Controller
{
    protected $paymentMethodRepo;
    protected $response;

    public function __construct(
        PaymentMethodRepositoryInterface $paymentMethodRepo,
        Response $response
    ) {
        $this->paymentMethodRepo = $paymentMethodRepo;
        $this->response = $response;
    }

    /**
     * Get all payment methods
     */
    public function index(Request $request)
    {
        try {
            $activeOnly = $request->input('active_only', false);

            $paymentMethods = $activeOnly
                ? $this->paymentMethodRepo->getAllActive()
                : $this->paymentMethodRepo->getAll();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Payment methods retrieved successfully')
                ->setData($paymentMethods);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve payment methods: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single payment method by ID
     */
    public function show($id)
    {
        try {
            $paymentMethod = $this->paymentMethodRepo->findById($id);

            if (!$paymentMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Payment method not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get configs (non-encrypted values only for security)
            $configs = $this->paymentMethodRepo->getConfig($id);
            $paymentMethod->configs = $configs;

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Payment method retrieved successfully')
                ->setData($paymentMethod);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve payment method: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new payment method
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:100|unique:payment_methods,code',
                'name' => 'required|string|max:200',
                'type' => 'required|string|max:50',
                'provider' => 'nullable|string|max:50',
                'logo_url' => 'nullable|string',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'fee_percentage' => 'nullable|numeric|min:0|max:100',
                'fee_fixed' => 'nullable|numeric|min:0',
                'min_amount' => 'nullable|numeric|min:0',
                'max_amount' => 'nullable|numeric|min:0',
                'expired_duration' => 'nullable|integer|min:1',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $paymentMethod = $this->paymentMethodRepo->create($validator->validated());

            logActivity(
                'create',
                "Created payment method: {$paymentMethod->name}",
                'payment_method',
                (int) $paymentMethod->id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Payment method created successfully')
                ->setData($paymentMethod);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create payment method: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update payment method
     */
    public function update(Request $request, $id)
    {
        try {
            $paymentMethod = $this->paymentMethodRepo->findById($id);

            if (!$paymentMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Payment method not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validator = Validator::make($request->all(), [
                'code' => 'sometimes|required|string|max:100|unique:payment_methods,code,' . $id,
                'name' => 'sometimes|required|string|max:200',
                'type' => 'sometimes|required|string|max:50',
                'provider' => 'nullable|string|max:50',
                'logo_url' => 'nullable|string',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'fee_percentage' => 'nullable|numeric|min:0|max:100',
                'fee_fixed' => 'nullable|numeric|min:0',
                'min_amount' => 'nullable|numeric|min:0',
                'max_amount' => 'nullable|numeric|min:0',
                'expired_duration' => 'nullable|integer|min:1',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $updatedPaymentMethod = $this->paymentMethodRepo->update($id, $validator->validated());

            logActivity(
                'update',
                "Updated payment method: {$updatedPaymentMethod->name}",
                'payment_method',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Payment method updated successfully')
                ->setData($updatedPaymentMethod);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update payment method: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete payment method
     */
    public function destroy($id)
    {
        try {
            $paymentMethod = $this->paymentMethodRepo->findById($id);

            if (!$paymentMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Payment method not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->paymentMethodRepo->delete($id);

            logActivity(
                'delete',
                "Deleted payment method: {$paymentMethod->name}",
                'payment_method',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Payment method deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete payment method: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Toggle payment method active status
     */
    public function toggleActive($id)
    {
        try {
            $updatedPaymentMethod = $this->paymentMethodRepo->toggleActive($id);

            $status = $updatedPaymentMethod->is_active ? 'activated' : 'deactivated';

            logActivity(
                'update',
                "Payment method {$status}: {$updatedPaymentMethod->name}",
                'payment_method',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage("Payment method {$status} successfully")
                ->setData($updatedPaymentMethod);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to toggle payment method status: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update payment method configuration
     */
    public function updateConfig(Request $request, $id)
    {
        try {
            $paymentMethod = $this->paymentMethodRepo->findById($id);

            if (!$paymentMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Payment method not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validator = Validator::make($request->all(), [
                'configs' => 'required|array',
                'configs.*.key' => 'required|string',
                'configs.*.value' => 'required|string',
                'configs.*.is_encrypted' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            foreach ($request->configs as $config) {
                $this->paymentMethodRepo->setConfig(
                    $id,
                    $config['key'],
                    $config['value'],
                    $config['is_encrypted'] ?? false
                );
            }

            logActivity(
                'update',
                "Updated configuration for payment method: {$paymentMethod->name}",
                'payment_method',
                (int) $id
            );

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Payment method configuration updated successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update payment method configuration: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
