<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\PaymentSettingsRepositoryInterface;
use Illuminate\Http\Request;

class PaymentSettingsApiController extends Controller
{
    protected $settingsRepo;
    protected $response;

    public function __construct(
        PaymentSettingsRepositoryInterface $settingsRepository,
        Response $response
    ) {
        $this->settingsRepo = $settingsRepository;
        $this->response = $response;
    }

    /**
     * Get all payment settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $settings = $this->settingsRepo->getAllPaymentSettings();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Payment settings retrieved successfully')
                ->setData($settings->toArray());

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve payment settings: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get specific setting by key
     *
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($key)
    {
        try {
            $value = $this->settingsRepo->getValue($key);

            if ($value === null) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('204')
                    ->setMessage('Setting not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Setting retrieved successfully')
                ->setData([
                    'key' => $key,
                    'value' => $value
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve setting: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update setting
     *
     * @param \Illuminate\Http\Request $request
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $key)
    {
        try {
            $validated = $request->validate([
                'value' => 'required|array'
            ]);

            $this->settingsRepo->updateValue($key, $validated['value']);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Setting updated successfully')
                ->setData([
                    'key' => $key,
                    'value' => $validated['value']
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()]);

            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update setting: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get tax settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaxSettings()
    {
        try {
            $taxConfig = $this->settingsRepo->getValue('payment.tax');

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Tax settings retrieved successfully')
                ->setData([
                    'tax' => $taxConfig
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve tax settings: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get Midtrans configuration
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMidtransConfig()
    {
        try {
            $midtransApi = $this->settingsRepo->getValue('payment.midtrans.api');
            $midtransMethods = $this->settingsRepo->getValue('payment.midtrans.methods');
            $midtransTransaction = $this->settingsRepo->getValue('payment.midtrans.transaction');

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Midtrans configuration retrieved successfully')
                ->setData([
                    'api' => $midtransApi,
                    'methods' => $midtransMethods,
                    'transaction' => $midtransTransaction
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve Midtrans configuration: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get payment methods
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentMethods()
    {
        try {
            $paymentMethods = $this->settingsRepo->getValue('payment.methods');

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Payment methods retrieved successfully')
                ->setData([
                    'methods' => $paymentMethods
                ]);

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
}
