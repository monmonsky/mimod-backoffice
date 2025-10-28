<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\Payment\PaymentMethodRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodConfigApiController extends Controller
{
    protected $paymentMethodRepository;
    protected $response;

    public function __construct(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        Response $response
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->response = $response;
    }

    /**
     * Get all configs for a payment method (merged provider + method-specific)
     * GET /api/payment-methods/{id}/configs
     */
    public function index($paymentMethodId)
    {
        try {
            $paymentMethod = $this->paymentMethodRepository->findById($paymentMethodId);

            if (!$paymentMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Payment method not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get merged configs (provider + method-specific)
            $configs = $this->paymentMethodRepository->getConfig($paymentMethodId);

            // Get payment method info to show provider details
            $data = [
                'payment_method' => [
                    'id' => $paymentMethod->id,
                    'code' => $paymentMethod->code,
                    'name' => $paymentMethod->name,
                    'provider' => $paymentMethod->provider,
                ],
                'configs' => $configs,
            ];

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Configs retrieved successfully')
                ->setData($data);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve configs: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get specific config by key
     * GET /api/payment-methods/{id}/configs/{key}
     */
    public function show($paymentMethodId, $key)
    {
        try {
            $paymentMethod = $this->paymentMethodRepository->findById($paymentMethodId);

            if (!$paymentMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Payment method not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $value = $this->paymentMethodRepository->getConfig($paymentMethodId, $key);

            if ($value === null) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage("Config key '{$key}' not found")
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Config retrieved successfully')
                ->setData([
                    'key' => $key,
                    'value' => $value
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve config: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create or update single config
     * PUT /api/payment-methods/{id}/configs/{key}
     */
    public function update(Request $request, $paymentMethodId, $key)
    {
        try {
            $paymentMethod = $this->paymentMethodRepository->findById($paymentMethodId);

            if (!$paymentMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Payment method not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'value' => 'required|string',
                'is_encrypted' => 'boolean',
            ]);

            $this->paymentMethodRepository->setConfig(
                $paymentMethodId,
                $key,
                $validated['value'],
                $validated['is_encrypted'] ?? false
            );

            // Return updated value
            $updatedValue = $this->paymentMethodRepository->getConfig($paymentMethodId, $key);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Config updated successfully')
                ->setData([
                    'key' => $key,
                    'value' => $updatedValue
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation error')
                ->setData($e->errors());
            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update config: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Bulk create/update configs
     * POST /api/payment-methods/{id}/configs
     */
    public function bulkUpdate(Request $request, $paymentMethodId)
    {
        try {
            $paymentMethod = $this->paymentMethodRepository->findById($paymentMethodId);

            if (!$paymentMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Payment method not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $validated = $request->validate([
                'configs' => 'required|array',
                'configs.*.key' => 'required|string',
                'configs.*.value' => 'required|string',
                'configs.*.is_encrypted' => 'boolean',
            ]);

            foreach ($validated['configs'] as $config) {
                $this->paymentMethodRepository->setConfig(
                    $paymentMethodId,
                    $config['key'],
                    $config['value'],
                    $config['is_encrypted'] ?? false
                );
            }

            // Get updated configs
            $updatedConfigs = $this->paymentMethodRepository->getConfig($paymentMethodId);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Configs updated successfully')
                ->setData($updatedConfigs);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation error')
                ->setData($e->errors());
            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update configs: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete specific config
     * DELETE /api/payment-methods/{id}/configs/{key}
     */
    public function destroy($paymentMethodId, $key)
    {
        try {
            $paymentMethod = $this->paymentMethodRepository->findById($paymentMethodId);

            if (!$paymentMethod) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Payment method not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            // Check if config exists
            $value = $this->paymentMethodRepository->getConfig($paymentMethodId, $key);
            if ($value === null) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage("Config key '{$key}' not found")
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            // Delete config override
            $this->paymentMethodRepository->deleteConfigOverride($paymentMethodId, $key);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Config deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete config: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get all global configs (list all payment_method_configs)
     * GET /api/payment-method-configs
     */
    public function getAllGlobalConfigs()
    {
        try {
            $globalConfigs = DB::table('payment_method_configs')
                ->orderBy('provider')
                ->get();

            $resultData = $globalConfigs->map(function($config) {
                // Get config items for this global config
                $items = DB::table('payment_method_config_items')
                    ->where('payment_method_config_id', $config->id)
                    ->get()
                    ->mapWithKeys(function($item) {
                        $value = $item->is_encrypted
                            ? \Illuminate\Support\Facades\Crypt::decryptString($item->value)
                            : $item->value;
                        return [$item->key => $value];
                    });

                // Get count of payment methods using this config
                $methodCount = DB::table('payment_methods')
                    ->where('payment_method_config_id', $config->id)
                    ->count();

                return [
                    'id' => $config->id,
                    'name' => $config->name,
                    'provider' => $config->provider,
                    'description' => $config->description,
                    'method_count' => $methodCount,
                    'configs' => $items,
                    'created_at' => $config->created_at,
                    'updated_at' => $config->updated_at,
                ];
            });

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Global configs retrieved successfully')
                ->setData($resultData);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve global configs: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single global config by ID
     * GET /api/payment-method-configs/{id}
     */
    public function getGlobalConfig($configId)
    {
        try {
            $config = DB::table('payment_method_configs')
                ->where('id', $configId)
                ->first();

            if (!$config) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Global config not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            // Get config items
            $items = DB::table('payment_method_config_items')
                ->where('payment_method_config_id', $config->id)
                ->get()
                ->mapWithKeys(function($item) {
                    $value = $item->is_encrypted
                        ? \Illuminate\Support\Facades\Crypt::decryptString($item->value)
                        : $item->value;
                    return [$item->key => $value];
                });

            // Get payment methods using this config
            $methods = DB::table('payment_methods')
                ->select('id', 'code', 'name', 'is_active')
                ->where('payment_method_config_id', $config->id)
                ->get();

            $resultData = [
                'id' => $config->id,
                'name' => $config->name,
                'provider' => $config->provider,
                'description' => $config->description,
                'configs' => $items,
                'methods' => $methods,
                'created_at' => $config->created_at,
                'updated_at' => $config->updated_at,
            ];

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Global config retrieved successfully')
                ->setData($resultData);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve global config: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new global config
     * POST /api/payment-method-configs
     */
    public function createGlobalConfig(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:payment_method_configs,name',
                'provider' => 'required|string|max:50',
                'description' => 'nullable|string',
                'configs' => 'required|array|min:1',
                'configs.*.key' => 'required|string|max:100',
                'configs.*.value' => 'required|string',
                'configs.*.is_encrypted' => 'boolean',
            ]);

            DB::beginTransaction();

            // Create global config
            $configId = DB::table('payment_method_configs')->insertGetId([
                'name' => $validated['name'],
                'provider' => $validated['provider'],
                'description' => $validated['description'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert config items
            foreach ($validated['configs'] as $configItem) {
                $value = ($configItem['is_encrypted'] ?? false)
                    ? \Illuminate\Support\Facades\Crypt::encryptString($configItem['value'])
                    : $configItem['value'];

                DB::table('payment_method_config_items')->insert([
                    'payment_method_config_id' => $configId,
                    'key' => $configItem['key'],
                    'value' => $value,
                    'is_encrypted' => $configItem['is_encrypted'] ?? false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            // Return the created config
            return $this->getGlobalConfig($configId);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation error')
                ->setData($e->errors());
            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create global config: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update global config
     * PUT /api/payment-method-configs/{id}
     */
    public function updateGlobalConfig(Request $request, $configId)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:100|unique:payment_method_configs,name,' . $configId,
                'provider' => 'sometimes|string|max:50',
                'description' => 'nullable|string',
                'configs' => 'sometimes|array|min:1',
            ]);

            $config = DB::table('payment_method_configs')->where('id', $configId)->first();
            if (!$config) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Global config not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            DB::beginTransaction();

            // Update global config basic info
            $updateData = [];
            if (isset($validated['name'])) $updateData['name'] = $validated['name'];
            if (isset($validated['provider'])) $updateData['provider'] = $validated['provider'];
            if (isset($validated['description'])) $updateData['description'] = $validated['description'];

            if (!empty($updateData)) {
                $updateData['updated_at'] = now();
                DB::table('payment_method_configs')->where('id', $configId)->update($updateData);
            }

            // Update config items if provided
            if (isset($validated['configs'])) {
                // Check if configs is in object format {key: value} or array format [{key: x, value: y}]
                $configsArray = [];

                // Detect format
                $firstKey = array_key_first($validated['configs']);
                if (is_numeric($firstKey) && isset($validated['configs'][0]['key']) && isset($validated['configs'][0]['value'])) {
                    // Array format: [{key: "api_key", value: "xxx"}, ...]
                    $configsArray = $validated['configs'];
                } else {
                    // Object format: {api_key: "xxx", account_type: "yyy"}
                    foreach ($validated['configs'] as $key => $value) {
                        $configsArray[] = [
                            'key' => $key,
                            'value' => $value,
                            'is_encrypted' => false
                        ];
                    }
                }

                foreach ($configsArray as $configItem) {
                    $value = ($configItem['is_encrypted'] ?? false)
                        ? \Illuminate\Support\Facades\Crypt::encryptString($configItem['value'])
                        : $configItem['value'];

                    $existing = DB::table('payment_method_config_items')
                        ->where('payment_method_config_id', $configId)
                        ->where('key', $configItem['key'])
                        ->first();

                    if ($existing) {
                        // Update existing
                        DB::table('payment_method_config_items')
                            ->where('id', $existing->id)
                            ->update([
                                'value' => $value,
                                'is_encrypted' => $configItem['is_encrypted'] ?? false,
                                'updated_at' => now(),
                            ]);
                    } else {
                        // Insert new
                        DB::table('payment_method_config_items')->insert([
                            'payment_method_config_id' => $configId,
                            'key' => $configItem['key'],
                            'value' => $value,
                            'is_encrypted' => $configItem['is_encrypted'] ?? false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            // Return updated config
            return $this->getGlobalConfig($configId);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation error')
                ->setData($e->errors());
            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update global config: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete global config
     * DELETE /api/payment-method-configs/{id}
     */
    public function deleteGlobalConfig($configId)
    {
        try {
            $config = DB::table('payment_method_configs')->where('id', $configId)->first();
            if (!$config) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Global config not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            // Check if any payment methods are using this config
            $methodCount = DB::table('payment_methods')
                ->where('payment_method_config_id', $configId)
                ->count();

            if ($methodCount > 0) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Cannot delete global config that is being used by ' . $methodCount . ' payment method(s). Please unassign methods first.')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 400);
            }

            DB::beginTransaction();

            // Delete config items first (cascade will handle this, but explicit is better)
            DB::table('payment_method_config_items')
                ->where('payment_method_config_id', $configId)
                ->delete();

            // Delete global config
            DB::table('payment_method_configs')->where('id', $configId)->delete();

            DB::commit();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Global config deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete global config: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete specific config item from global config
     * DELETE /api/payment-method-configs/{id}/items/{key}
     */
    public function deleteGlobalConfigItem($configId, $key)
    {
        try {
            $config = DB::table('payment_method_configs')->where('id', $configId)->first();
            if (!$config) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Global config not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $deleted = DB::table('payment_method_config_items')
                ->where('payment_method_config_id', $configId)
                ->where('key', $key)
                ->delete();

            if ($deleted === 0) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage("Config item '{$key}' not found")
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Config item deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete config item: ' . $e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
