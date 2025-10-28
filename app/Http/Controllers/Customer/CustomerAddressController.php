<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerAddressController extends Controller
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get all customer addresses
     */
    public function index(Request $request)
    {
        try {
            $customer = $request->customer;

            $addresses = DB::table('customer_addresses')
                ->where('customer_id', $customer->id)
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Addresses retrieved successfully.')
                ->setData([
                    'addresses' => $addresses,
                    'total' => count($addresses),
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve addresses: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single address
     */
    public function show(Request $request, $id)
    {
        try {
            $customer = $request->customer;

            $address = DB::table('customer_addresses')
                ->where('id', $id)
                ->where('customer_id', $customer->id)
                ->first();

            if (!$address) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Address not found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Address retrieved successfully.')
                ->setData($address);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve address: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new address
     */
    public function store(Request $request)
    {
        try {
            $customer = $request->customer;

            $validator = Validator::make($request->all(), [
                'label' => 'required|string|max:50',
                'recipient_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address_line' => 'required|string',
                'city' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'postal_code' => 'required|string|max:10',
                'country' => 'nullable|string|max:100',
                'is_default' => 'nullable|boolean',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $isDefault = $request->input('is_default', false);

            // If this is set as default, unset other defaults
            if ($isDefault) {
                DB::table('customer_addresses')
                    ->where('customer_id', $customer->id)
                    ->update(['is_default' => false]);
            }

            // Create address
            $addressId = DB::table('customer_addresses')->insertGetId([
                'customer_id' => $customer->id,
                'label' => $request->label,
                'recipient_name' => $request->recipient_name,
                'phone' => $request->phone,
                'address_line' => $request->address_line,
                'city' => $request->city,
                'province' => $request->province,
                'postal_code' => $request->postal_code,
                'country' => $request->input('country', 'Indonesia'),
                'is_default' => $isDefault,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $address = DB::table('customer_addresses')->where('id', $addressId)->first();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Address created successfully.')
                ->setData($address);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create address: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update address
     */
    public function update(Request $request, $id)
    {
        try {
            $customer = $request->customer;

            $address = DB::table('customer_addresses')
                ->where('id', $id)
                ->where('customer_id', $customer->id)
                ->first();

            if (!$address) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Address not found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validator = Validator::make($request->all(), [
                'label' => 'sometimes|string|max:50',
                'recipient_name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20',
                'address_line' => 'sometimes|string',
                'city' => 'sometimes|string|max:100',
                'province' => 'sometimes|string|max:100',
                'postal_code' => 'sometimes|string|max:10',
                'country' => 'nullable|string|max:100',
                'is_default' => 'nullable|boolean',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $updateData = $request->only([
                'label', 'recipient_name', 'phone', 'address_line',
                'city', 'province', 'postal_code', 'country',
                'is_default', 'latitude', 'longitude', 'notes'
            ]);

            // If this is set as default, unset other defaults
            if (isset($updateData['is_default']) && $updateData['is_default']) {
                DB::table('customer_addresses')
                    ->where('customer_id', $customer->id)
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $updateData['updated_at'] = now();

            DB::table('customer_addresses')
                ->where('id', $id)
                ->update($updateData);

            $updatedAddress = DB::table('customer_addresses')->where('id', $id)->first();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Address updated successfully.')
                ->setData($updatedAddress);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update address: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete address
     */
    public function destroy(Request $request, $id)
    {
        try {
            $customer = $request->customer;

            $address = DB::table('customer_addresses')
                ->where('id', $id)
                ->where('customer_id', $customer->id)
                ->first();

            if (!$address) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Address not found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            DB::table('customer_addresses')->where('id', $id)->delete();

            // If deleted address was default, set another as default
            if ($address->is_default) {
                $newDefault = DB::table('customer_addresses')
                    ->where('customer_id', $customer->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($newDefault) {
                    DB::table('customer_addresses')
                        ->where('id', $newDefault->id)
                        ->update(['is_default' => true]);
                }
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Address deleted successfully.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete address: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Set address as default
     */
    public function setDefault(Request $request, $id)
    {
        try {
            $customer = $request->customer;

            $address = DB::table('customer_addresses')
                ->where('id', $id)
                ->where('customer_id', $customer->id)
                ->first();

            if (!$address) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Address not found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Unset all defaults for this customer
            DB::table('customer_addresses')
                ->where('customer_id', $customer->id)
                ->update(['is_default' => false]);

            // Set this as default
            DB::table('customer_addresses')
                ->where('id', $id)
                ->update(['is_default' => true, 'updated_at' => now()]);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Default address updated successfully.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to set default address: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
