<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomerProfileController extends Controller
{
    protected $customerRepository;
    protected $response;

    public function __construct(CustomerRepositoryInterface $customerRepository, Response $response)
    {
        $this->customerRepository = $customerRepository;
        $this->response = $response;
    }

    /**
     * Get customer profile
     */
    public function show(Request $request)
    {
        try {
            $customer = $request->customer;

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Profile retrieved successfully.')
                ->setData([
                    'id' => $customer->id,
                    'customer_code' => $customer->customer_code,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'date_of_birth' => $customer->date_of_birth,
                    'gender' => $customer->gender,
                    'segment' => $customer->segment,
                    'is_vip' => $customer->is_vip,
                    'loyalty_points' => $customer->loyalty_points,
                    'total_orders' => $customer->total_orders,
                    'total_spent' => $customer->total_spent,
                    'average_order_value' => $customer->average_order_value,
                    'last_order_at' => $customer->last_order_at,
                    'last_login_at' => $customer->last_login_at,
                    'email_verified_at' => $customer->email_verified_at,
                    'preferences' => $customer->preferences ? json_decode($customer->preferences, true) : null,
                    'status' => $customer->status,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve profile: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update customer profile
     */
    public function update(Request $request)
    {
        try {
            $customer = $request->customer;

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20|unique:customers,phone,' . $customer->id,
                'date_of_birth' => 'sometimes|date|before:today',
                'gender' => 'sometimes|in:male,female,other',
                'preferences' => 'sometimes|array',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $updateData = $request->only(['name', 'phone', 'date_of_birth', 'gender']);

            if ($request->has('preferences')) {
                $updateData['preferences'] = json_encode($request->preferences);
            }

            $updatedCustomer = $this->customerRepository->update($customer->id, $updateData);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Profile updated successfully.')
                ->setData([
                    'id' => $updatedCustomer->id,
                    'customer_code' => $updatedCustomer->customer_code,
                    'name' => $updatedCustomer->name,
                    'email' => $updatedCustomer->email,
                    'phone' => $updatedCustomer->phone,
                    'date_of_birth' => $updatedCustomer->date_of_birth,
                    'gender' => $updatedCustomer->gender,
                    'preferences' => $updatedCustomer->preferences ? json_decode($updatedCustomer->preferences, true) : null,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update profile: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            $customer = $request->customer;

            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            // Verify current password
            if (!Hash::check($request->current_password, $customer->password)) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Current password is incorrect.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            // Update password
            $this->customerRepository->update($customer->id, [
                'password' => $request->new_password,
            ]);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Password changed successfully.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to change password: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request)
    {
        try {
            $customer = $request->customer;

            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpeg,jpg,png|max:2048', // 2MB max
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $file = $request->file('avatar');
            $filename = 'customer_' . $customer->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('customers/avatars', $filename, 'ftp');

            // Generate URL
            $url = env('FTP_URL') . '/' . $path;

            // Delete old avatar if exists
            if (isset($customer->avatar) && $customer->avatar) {
                $oldPath = str_replace(env('FTP_URL') . '/', '', $customer->avatar);
                if (Storage::disk('ftp')->exists($oldPath)) {
                    Storage::disk('ftp')->delete($oldPath);
                }
            }

            // Update customer avatar
            $this->customerRepository->update($customer->id, [
                'avatar' => $url,
            ]);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Avatar uploaded successfully.')
                ->setData([
                    'avatar_url' => $url,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to upload avatar: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete avatar
     */
    public function deleteAvatar(Request $request)
    {
        try {
            $customer = $request->customer;

            if (!isset($customer->avatar) || !$customer->avatar) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('No avatar found.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            // Delete avatar file
            $path = str_replace(env('FTP_URL') . '/', '', $customer->avatar);
            if (Storage::disk('ftp')->exists($path)) {
                Storage::disk('ftp')->delete($path);
            }

            // Update customer record
            $this->customerRepository->update($customer->id, [
                'avatar' => null,
            ]);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Avatar deleted successfully.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete avatar: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete account (soft delete)
     */
    public function deleteAccount(Request $request)
    {
        try {
            $customer = $request->customer;

            $validator = Validator::make($request->all(), [
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Validation failed')
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            // Verify password
            if (!Hash::check($request->password, $customer->password)) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Password is incorrect.')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 400);
            }

            // Soft delete customer
            $this->customerRepository->update($customer->id, [
                'status' => 'inactive',
                'deleted_at' => now(),
            ]);

            // Revoke all tokens
            $this->customerRepository->revokeAllCustomerTokens($customer->id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Account deleted successfully.')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete account: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get loyalty points
     */
    public function getLoyaltyPoints(Request $request)
    {
        try {
            $customer = $request->customer;

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Loyalty points retrieved successfully.')
                ->setData([
                    'loyalty_points' => $customer->loyalty_points,
                    'customer_code' => $customer->customer_code,
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve loyalty points: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get loyalty history
     */
    public function getLoyaltyHistory(Request $request)
    {
        try {
            $customer = $request->customer;
            $limit = $request->input('limit', 20);

            $history = $this->customerRepository->getLoyaltyHistory($customer->id, $limit);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Loyalty history retrieved successfully.')
                ->setData([
                    'current_points' => $customer->loyalty_points,
                    'history' => $history,
                    'total_records' => count($history),
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve loyalty history: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
