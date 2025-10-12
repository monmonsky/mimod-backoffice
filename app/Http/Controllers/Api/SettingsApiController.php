<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsApiController extends Controller
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get all settings or by prefix
     */
    public function index(Request $request)
    {
        try {
            $prefix = $request->query('prefix');

            $query = DB::table('settings');

            if ($prefix) {
                $query->where('key', 'LIKE', $prefix . '.%');
            }

            $settings = $query->get();

            // Transform to key-value pairs with decoded JSON
            $data = [];
            foreach ($settings as $setting) {
                $data[$setting->key] = [
                    'value' => json_decode($setting->value, true),
                    'description' => $setting->description
                ];
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Settings retrieved successfully')
                ->setData($data);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve settings: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get settings by pattern (prefix)
     */
    public function show($pattern)
    {
        try {
            $settings = DB::table('settings')
                ->where('key', 'LIKE', $pattern . '.%')
                ->get();

            if ($settings->isEmpty()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Settings not found for pattern: ' . $pattern);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Transform to key-value pairs with decoded JSON
            $data = [];
            foreach ($settings as $setting) {
                $data[$setting->key] = [
                    'value' => json_decode($setting->value, true),
                    'description' => $setting->description
                ];
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Settings retrieved successfully')
                ->setData($data);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve settings: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update setting by key or by prefix (like store)
     */
    public function update(Request $request, $key)
    {
        try {
            // Check if this is a prefix-based update (request has nested object)
            $requestData = $request->all();

            // If request doesn't have 'value' field, treat as prefix update
            if (!isset($requestData['value'])) {
                return $this->updateByPrefix($request, $key);
            }

            // Otherwise, single key update
            $validated = $request->validate([
                'value' => 'required',
                'description' => 'nullable|string',
            ]);

            // Check if setting exists
            $setting = DB::table('settings')->where('key', $key)->first();

            if (!$setting) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Setting not found: ' . $key);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Store old value for logging
            $oldValue = json_decode($setting->value, true);

            // Encode value as JSON
            $newValue = is_string($validated['value']) && $this->isJson($validated['value'])
                ? $validated['value']
                : json_encode($validated['value']);

            // Update setting
            $updateData = [
                'value' => $newValue,
                'updated_at' => now(),
            ];

            if (isset($validated['description'])) {
                $updateData['description'] = $validated['description'];
            }

            DB::table('settings')->where('key', $key)->update($updateData);

            // Get updated setting
            $updatedSetting = DB::table('settings')->where('key', $key)->first();

            // Log activity
            logActivity('update', "Updated setting: {$key}", 'setting', null, [
                'key' => $key,
                'old_value' => $oldValue,
                'new_value' => json_decode($newValue, true),
            ]);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Setting updated successfully')
                ->setData([
                    'key' => $updatedSetting->key,
                    'value' => json_decode($updatedSetting->value, true),
                    'description' => $updatedSetting->description,
                    'updated_at' => $updatedSetting->updated_at,
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
                ->setMessage('Failed to update setting: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update multiple settings at once
     */
    public function updateBulk(Request $request)
    {
        try {
            $validated = $request->validate([
                'settings' => 'required|array',
                'settings.*.key' => 'required|string',
                'settings.*.value' => 'required',
                'settings.*.description' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $updated = [];
            $notFound = [];

            foreach ($validated['settings'] as $settingData) {
                $key = $settingData['key'];
                $setting = DB::table('settings')->where('key', $key)->first();

                if (!$setting) {
                    $notFound[] = $key;
                    continue;
                }

                // Store old value
                $oldValue = json_decode($setting->value, true);

                // Encode value as JSON
                $newValue = is_string($settingData['value']) && $this->isJson($settingData['value'])
                    ? $settingData['value']
                    : json_encode($settingData['value']);

                // Update setting
                $updateData = [
                    'value' => $newValue,
                    'updated_at' => now(),
                ];

                if (isset($settingData['description'])) {
                    $updateData['description'] = $settingData['description'];
                }

                DB::table('settings')->where('key', $key)->update($updateData);

                // Log activity
                logActivity('update', "Updated setting: {$key}", 'setting', null, [
                    'key' => $key,
                    'old_value' => $oldValue,
                    'new_value' => json_decode($newValue, true),
                ]);

                $updated[] = $key;
            }

            DB::commit();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage(count($updated) . ' setting(s) updated successfully')
                ->setData([
                    'updated' => $updated,
                    'not_found' => $notFound,
                    'total_updated' => count($updated),
                    'total_not_found' => count($notFound),
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('422')
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()]);

            return response()->json($this->response->generateResponse($result), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update settings: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update settings by prefix (like GET /settings/store)
     * Supports nested structure: { "info": {...}, "contact": {...} }
     */
    private function updateByPrefix(Request $request, $prefix)
    {
        try {
            // Get all data from request
            $requestData = $request->all();

            // Get all existing settings with this prefix
            $existingSettings = DB::table('settings')
                ->where('key', 'LIKE', $prefix . '.%')
                ->get()
                ->keyBy('key');

            if ($existingSettings->isEmpty()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('No settings found for prefix: ' . $prefix);

                return response()->json($this->response->generateResponse($result), 404);
            }

            DB::beginTransaction();

            $updated = [];
            $notFound = [];

            // Loop through request data (e.g., "info", "contact", "address")
            foreach ($requestData as $key => $settingData) {
                $fullKey = $prefix . '.' . $key;

                if (!isset($existingSettings[$fullKey])) {
                    $notFound[] = $fullKey;
                    continue;
                }

                $setting = $existingSettings[$fullKey];

                // Store old value
                $oldValue = json_decode($setting->value, true);

                // Get new value and description
                $newValueData = is_array($settingData) && isset($settingData['value'])
                    ? $settingData['value']
                    : $settingData;

                $description = is_array($settingData) && isset($settingData['description'])
                    ? $settingData['description']
                    : null;

                // Encode value as JSON
                $newValue = is_string($newValueData) && $this->isJson($newValueData)
                    ? $newValueData
                    : json_encode($newValueData);

                // Update setting
                $updateData = [
                    'value' => $newValue,
                    'updated_at' => now(),
                ];

                if ($description !== null) {
                    $updateData['description'] = $description;
                }

                DB::table('settings')->where('key', $fullKey)->update($updateData);

                // Log activity
                logActivity('update', "Updated setting: {$fullKey}", 'setting', null, [
                    'key' => $fullKey,
                    'old_value' => $oldValue,
                    'new_value' => json_decode($newValue, true),
                ]);

                $updated[] = $fullKey;
            }

            DB::commit();

            // Get updated settings
            $updatedSettings = DB::table('settings')
                ->whereIn('key', $updated)
                ->get();

            $data = [];
            foreach ($updatedSettings as $setting) {
                $data[$setting->key] = [
                    'value' => json_decode($setting->value, true),
                    'description' => $setting->description,
                ];
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage(count($updated) . ' setting(s) updated successfully')
                ->setData([
                    'settings' => $data,
                    'updated' => $updated,
                    'not_found' => $notFound,
                    'total_updated' => count($updated),
                    'total_not_found' => count($notFound),
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update settings: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Check if string is valid JSON
     */
    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
