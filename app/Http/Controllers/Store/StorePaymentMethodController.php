<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Payment\PaymentMethodRepositoryInterface;
use Illuminate\Http\Request;

class StorePaymentMethodController extends Controller
{
    protected $paymentMethodRepo;

    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepo)
    {
        $this->paymentMethodRepo = $paymentMethodRepo;
    }

    /**
     * Get all active payment methods for customer checkout
     */
    public function index(Request $request)
    {
        try {
            $orderAmount = $request->input('order_amount', 0);

            // Get active payment methods that are valid for the order amount
            $paymentMethods = $this->paymentMethodRepo->getAllActiveForCustomer($orderAmount);

            // Remove sensitive data and format for frontend
            $formattedMethods = $paymentMethods->map(function($method) use ($orderAmount) {
                // Calculate fee for this order
                $fee = $method->fee_fixed + ($orderAmount * $method->fee_percentage / 100);

                return [
                    'id' => $method->id,
                    'code' => $method->code,
                    'name' => $method->name,
                    'type' => $method->type,
                    'provider' => $method->provider,
                    'logo_url' => $method->logo_url,
                    'description' => $method->description,
                    'instructions' => $method->instructions,
                    'fee_amount' => round($fee, 0),
                    'fee_display' => $this->formatFeeDisplay($method),
                    'min_amount' => $method->min_amount,
                    'max_amount' => $method->max_amount,
                    'sort_order' => $method->sort_order,
                ];
            });

            return response()->json([
                'status' => true,
                'statusCode' => '200',
                'message' => 'Payment methods retrieved successfully',
                'data' => $formattedMethods
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'statusCode' => '500',
                'message' => 'Failed to retrieve payment methods: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get single payment method detail
     */
    public function show($id)
    {
        try {
            $paymentMethod = $this->paymentMethodRepo->findById($id);

            if (!$paymentMethod) {
                return response()->json([
                    'status' => false,
                    'statusCode' => '404',
                    'message' => 'Payment method not found',
                    'data' => []
                ], 404);
            }

            if (!$paymentMethod->is_active) {
                return response()->json([
                    'status' => false,
                    'statusCode' => '400',
                    'message' => 'Payment method is not available',
                    'data' => []
                ], 400);
            }

            return response()->json([
                'status' => true,
                'statusCode' => '200',
                'message' => 'Payment method retrieved successfully',
                'data' => [
                    'id' => $paymentMethod->id,
                    'code' => $paymentMethod->code,
                    'name' => $paymentMethod->name,
                    'type' => $paymentMethod->type,
                    'provider' => $paymentMethod->provider,
                    'logo_url' => $paymentMethod->logo_url,
                    'description' => $paymentMethod->description,
                    'instructions' => $paymentMethod->instructions,
                    'fee_display' => $this->formatFeeDisplay($paymentMethod),
                    'min_amount' => $paymentMethod->min_amount,
                    'max_amount' => $paymentMethod->max_amount,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'statusCode' => '500',
                'message' => 'Failed to retrieve payment method: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Format fee display text
     */
    private function formatFeeDisplay($paymentMethod)
    {
        $feePercentage = (float) $paymentMethod->fee_percentage;
        $feeFixed = (float) $paymentMethod->fee_fixed;

        if ($feePercentage > 0 && $feeFixed > 0) {
            return $feePercentage . '% + Rp ' . number_format($feeFixed, 0, ',', '.');
        } elseif ($feePercentage > 0) {
            return $feePercentage . '%';
        } elseif ($feeFixed > 0) {
            return 'Rp ' . number_format($feeFixed, 0, ',', '.');
        } else {
            return 'Gratis';
        }
    }
}
