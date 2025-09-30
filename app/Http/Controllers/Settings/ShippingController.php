<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Repositories\Settings\ShippingSettingsRepository;
use App\Services\Shipping\RajaOngkirService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    protected $settingsRepo;

    public function __construct(ShippingSettingsRepository $settingsRepository)
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
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
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
                'origin_city_id' => 'required|integer',
                'origin_city_name' => 'required|string|max:255',
                'origin_province_id' => 'required|integer',
                'origin_province_name' => 'required|string|max:255',
            ]);

            $this->settingsRepo->updateValue('shipping.origin', [
                'city_id' => $request->origin_city_id,
                'city_name' => $request->origin_city_name,
                'province_id' => $request->origin_province_id,
                'province_name' => $request->origin_province_name,
                'district_id' => $request->origin_district_id,
                'district_name' => $request->origin_district_name,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
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
     * Get provinces for dropdown (API endpoint)
     */
    public function getProvinces(Request $request)
    {
        try {
            $config = $this->settingsRepo->getValue('shipping.rajaongkir');

            if (!$config || !($config['enabled'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'RajaOngkir not configured or disabled',
                ], 400);
            }

            $rajaongkirService = new RajaOngkirService($config);
            $result = $rajaongkirService->getProvinces();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']['results'] ?? [],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get provinces: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get cities by province (API endpoint)
     */
    public function getCities(Request $request, int $provinceId)
    {
        try {
            $config = $this->settingsRepo->getValue('shipping.rajaongkir');

            if (!$config || !($config['enabled'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'RajaOngkir not configured or disabled',
                ], 400);
            }

            $rajaongkirService = new RajaOngkirService($config);
            $result = $rajaongkirService->getCities($provinceId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']['results'] ?? [],
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
     * Calculate shipping cost (API endpoint)
     */
    public function calculateShippingCost(Request $request)
    {
        try {
            $validated = $request->validate([
                'origin' => 'required|integer',
                'destination' => 'required|integer',
                'weight' => 'required|integer',
                'courier' => 'required|string',
            ]);

            $config = $this->settingsRepo->getValue('shipping.rajaongkir');

            if (!$config || !($config['enabled'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'RajaOngkir not configured or disabled',
                ], 400);
            }

            $rajaongkirService = new RajaOngkirService($config);
            $result = $rajaongkirService->calculateCost([
                'origin' => $request->origin,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier,
            ]);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']['results'] ?? [],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate shipping cost: ' . $e->getMessage(),
            ], 500);
        }
    }
}