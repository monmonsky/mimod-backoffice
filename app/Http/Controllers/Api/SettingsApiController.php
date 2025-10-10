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
                ->setMessage('Failed to retrieve settings')
                ->setError($e->getMessage());

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
                ->setMessage('Failed to retrieve settings')
                ->setError($e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
