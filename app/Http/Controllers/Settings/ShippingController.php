<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ShippingLocation;
use App\Repositories\Contracts\ShippingSettingsRepositoryInterface;
use App\Services\Shipping\RajaOngkirService;
use App\Services\Location\WilayahService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    protected $settingsRepo;

    public function __construct(ShippingSettingsRepositoryInterface $settingsRepository)
    {
        $this->settingsRepo = $settingsRepository;
    }

    /**
     * Display shipping methods
     */
    public function shippingMethods()
    {
        $rajaongkir = $this->settingsRepo->getValue('shipping.rajaongkir');
        $methods = $this->settingsRepo->getValue('shipping.methods');

        return view('pages.settings.shipping.shipping-methods', compact('rajaongkir', 'methods'));
    }

    /**
     * Display RajaOngkir configuration
     */
    public function rajaongkirConfig()
    {
        $config = $this->settingsRepo->getValue('shipping.rajaongkir');

        return view('pages.settings.shipping.rajaongkir-config', compact('config'));
    }

    /**
     * Update RajaOngkir configuration
     */
    public function updateRajaongkirConfig(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'account_type' => 'required|in:starter,basic,pro',
                'api_key' => 'required|string|max:255',
            ]);

            $this->settingsRepo->updateValue('shipping.rajaongkir', [
                'enabled' => $request->has('enabled'),
                'account_type' => $request->account_type,
                'api_key' => $request->api_key,
                'base_url' => $request->base_url,
                'couriers' => [
                    'jne' => ['enabled' => $request->has('courier_jne'), 'name' => 'JNE'],
                    'pos' => ['enabled' => $request->has('courier_pos'), 'name' => 'POS Indonesia'],
                    'tiki' => ['enabled' => $request->has('courier_tiki'), 'name' => 'TIKI'],
                    'rpx' => ['enabled' => $request->has('courier_rpx'), 'name' => 'RPX'],
                    'sicepat' => ['enabled' => $request->has('courier_sicepat'), 'name' => 'SiCepat'],
                    'jnt' => ['enabled' => $request->has('courier_jnt'), 'name' => 'J&T Express'],
                    'wahana' => ['enabled' => $request->has('courier_wahana'), 'name' => 'Wahana'],
                    'ninja' => ['enabled' => $request->has('courier_ninja'), 'name' => 'Ninja Express'],
                    'lion' => ['enabled' => $request->has('courier_lion'), 'name' => 'Lion Parcel'],
                    'anteraja' => ['enabled' => $request->has('courier_anteraja'), 'name' => 'AnterAja'],
                ],
            ]);

            // Return JSON response for AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'RajaOngkir configuration updated successfully',
                ], 200);
            }

            return redirect()->back()->with('success', 'RajaOngkir configuration updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update RajaOngkir configuration: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update RajaOngkir configuration');
        }
    }

    /**
     * Test RajaOngkir connection
     */
    public function testRajaongkirConnection(Request $request)
    {
        try {
            // Get current RajaOngkir settings
            $config = $this->settingsRepo->getValue('shipping.rajaongkir');

            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => 'RajaOngkir configuration not found',
                ], 400);
            }

            // Use RajaOngkirService to test connection
            $rajaongkirService = new RajaOngkirService($config);
            $result = $rajaongkirService->testConnection();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'account_type' => $result['account_type'] ?? null,
                    'http_code' => $result['http_code'] ?? null,
                    'province_count' => $result['province_count'] ?? null,
                    'request' => $result['request'] ?? null,
                    'response' => $result['response'] ?? null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'http_code' => $result['http_code'] ?? null,
                'request' => $result['request'] ?? null,
                'response' => $result['response'] ?? null,
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display origin address
     */
    public function originAddress()
    {
        $origin = $this->settingsRepo->getValue('shipping.origin');

        return view('pages.settings.shipping.origin-address', compact('origin'));
    }

    /**
     * Update origin address
     */
    public function updateOriginAddress(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'province_code' => 'required|string|max:10',
                'province_name' => 'required|string|max:255',
                'regency_code' => 'required|string|max:10',
                'regency_name' => 'required|string|max:255',
                'district_code' => 'required|string|max:15',
                'district_name' => 'required|string|max:255',
                'village_code' => 'nullable|string|max:20',
                'village_name' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:10',
                'address' => 'required|string',
                'location_name' => 'nullable|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
            ]);

            $this->settingsRepo->updateValue('shipping.origin', [
                'province_code' => $request->province_code,
                'province_name' => $request->province_name,
                'regency_code' => $request->regency_code,
                'regency_name' => $request->regency_name,
                'district_code' => $request->district_code,
                'district_name' => $request->district_name,
                'village_code' => $request->village_code,
                'village_name' => $request->village_name,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'location_name' => $request->location_name,
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
            ]);

            // Return JSON response for AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Origin address updated successfully',
                ], 200);
            }

            return redirect()->back()->with('success', 'Origin address updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update origin address: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update origin address');
        }
    }

    /**
     * Sync locations (provinces and cities) from RajaOngkir API to database
     */
    public function syncLocations(Request $request)
    {
        try {
            $config = $this->settingsRepo->getValue('shipping.rajaongkir');

            if (!$config || empty($config['api_key'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'RajaOngkir API key not configured. Please configure API settings first.',
                ], 400);
            }

            $rajaongkirService = new RajaOngkirService($config);
            $result = $rajaongkirService->syncAllLocations();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'provinces' => $result['provinces'] ?? null,
                    'cities' => $result['cities'] ?? null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'debug' => $result['debug'] ?? null,
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync locations: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    /**
     * Get provinces for dropdown (API endpoint)
     * Fetches directly from RajaOngkir API
     */
    public function getProvinces(Request $request)
    {
        try {
            $config = $this->settingsRepo->getValue('shipping.rajaongkir');

            \Log::info('=== Get Provinces Request ===', [
                'config' => $config ? array_merge($config, ['api_key' => substr($config['api_key'] ?? '', 0, 10) . '...[MASKED]']) : null,
            ]);

            if (!$config || empty($config['api_key'])) {
                \Log::error('Get provinces failed: No API key configured');
                return response()->json([
                    'success' => false,
                    'message' => 'RajaOngkir API key not configured.',
                ], 400);
            }

            $rajaongkirService = new RajaOngkirService($config);
            $result = $rajaongkirService->getProvinces();

            \Log::info('=== Get Provinces Result ===', [
                'result' => $result,
            ]);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'],
                ]);
            }

            \Log::error('Get provinces failed', [
                'result' => $result,
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Get provinces exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get provinces: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all cities for dropdown (API endpoint)
     * Fetches from database only
     */
    public function getAllCities(Request $request)
    {
        try {
            // Get all cities from database
            $cities = ShippingLocation::where('type', 'city')
                ->with('province')
                ->orderBy('name')
                ->get();

            if ($cities->count() > 0) {
                // Format to match RajaOngkir API response with province name
                $data = $cities->map(function ($city) {
                    return [
                        'city_id' => $city->rajaongkir_id,
                        'province_id' => $city->province ? $city->province->rajaongkir_id : null,
                        'city_name' => $city->name,
                        'province' => $city->province ? $city->province->name : '',
                        'type' => $city->type_name,
                        'postal_code' => $city->postal_code,
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'source' => 'database',
                ]);
            }

            // If database is empty, return message to sync first
            return response()->json([
                'success' => false,
                'message' => 'No locations found. Please sync locations first by clicking the "Sync Locations" button.',
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cities: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get cities by province (API endpoint)
     * Fetches directly from RajaOngkir API
     */
    public function getCities(Request $request, int $provinceId)
    {
        try {
            $config = $this->settingsRepo->getValue('shipping.rajaongkir');

            if (!$config || empty($config['api_key'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'RajaOngkir API key not configured.',
                ], 400);
            }

            $rajaongkirService = new RajaOngkirService($config);
            $result = $rajaongkirService->getCities($provinceId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cities: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get districts by city (API endpoint)
     * Fetches directly from RajaOngkir API
     */
    public function getDistricts(Request $request, int $cityId)
    {
        try {
            $config = $this->settingsRepo->getValue('shipping.rajaongkir');

            if (!$config || empty($config['api_key'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'RajaOngkir API key not configured.',
                ], 400);
            }

            $rajaongkirService = new RajaOngkirService($config);
            $result = $rajaongkirService->getDistricts($cityId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get districts: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate shipping cost (API endpoint)
     */
    public function calculateShippingCost(Request $request)
    {
        try {
            $validated = $request->validate([
                'origin' => 'required|integer',
                'destination' => 'required|integer',
                'weight' => 'required|integer|min:1',
                'courier' => 'required|string', // Can be single or colon-separated (jne:sicepat:tiki)
                'price' => 'nullable|string|in:lowest,highest',
            ]);

            \Log::info('=== Calculate Shipping Cost Request ===', [
                'params' => $validated,
            ]);

            $config = $this->settingsRepo->getValue('shipping.rajaongkir');

            if (!$config || empty($config['api_key'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'RajaOngkir API key not configured. Please configure API settings first.',
                ], 400);
            }

            $rajaongkirService = new RajaOngkirService($config);

            // Prepare params - handle multiple couriers for Komerce API
            $params = [
                'origin' => $request->origin,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier,
            ];

            // Add price parameter for Komerce API
            if (!empty($request->price)) {
                $params['price'] = $request->price;
            } else {
                $params['price'] = 'lowest'; // Default to lowest price
            }

            $result = $rajaongkirService->calculateCost($params);

            \Log::info('=== Calculate Shipping Cost Response ===', [
                'result' => $result,
            ]);

            if ($result['success']) {
                // Get the data - handle both old and new API formats
                $data = $result['data'] ?? [];

                // For old API: results array is nested
                // For new API: data is the array directly
                $results = $data['results'] ?? $data;

                return response()->json([
                    'success' => true,
                    'data' => $results,
                    'debug' => [
                        'raw_data' => $data,
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'debug' => $result,
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Calculate shipping cost error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate shipping cost: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get provinces from Wilayah.id API (for origin address)
     */
    public function getWilayahProvinces(Request $request)
    {
        try {
            $wilayahService = new WilayahService();
            $result = $wilayahService->getProvinces();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to fetch provinces',
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Get Wilayah provinces error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get provinces: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get regencies (cities/kabupaten) from Wilayah.id API by province code
     */
    public function getWilayahRegencies(Request $request, string $provinceCode)
    {
        try {
            $wilayahService = new WilayahService();
            $result = $wilayahService->getRegencies($provinceCode);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to fetch regencies',
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Get Wilayah regencies error', [
                'province_code' => $provinceCode,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get regencies: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get districts (kecamatan) from Wilayah.id API by regency code
     */
    public function getWilayahDistricts(Request $request, string $regencyCode)
    {
        try {
            $wilayahService = new WilayahService();
            $result = $wilayahService->getDistricts($regencyCode);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to fetch districts',
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Get Wilayah districts error', [
                'regency_code' => $regencyCode,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get districts: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get villages (kelurahan/desa) from Wilayah.id API by district code
     */
    public function getWilayahVillages(Request $request, string $districtCode)
    {
        try {
            $wilayahService = new WilayahService();
            $result = $wilayahService->getVillages($districtCode);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to fetch villages',
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Get Wilayah villages error', [
                'district_code' => $districtCode,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get villages: ' . $e->getMessage(),
            ], 500);
        }
    }
}