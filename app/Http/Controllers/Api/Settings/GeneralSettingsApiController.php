<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\GeneralSettingsRepositoryInterface;
use Illuminate\Http\Request;

class GeneralSettingsApiController extends Controller
{
    protected $settingsRepo;
    protected $response;

    public function __construct(
        GeneralSettingsRepositoryInterface $settingsRepository,
        Response $response
    ) {
        $this->settingsRepo = $settingsRepository;
        $this->response = $response;
    }

    /**
     * Get all general settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $settings = $this->settingsRepo->getByPrefix('store.');

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('General settings retrieved successfully')
                ->setData($settings->toArray());

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve general settings: ' . $e->getMessage())
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
     * Get store information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStoreInfo()
    {
        try {
            $storeInfo = $this->settingsRepo->getValue('store.info');
            $storeContact = $this->settingsRepo->getValue('store.contact');
            $storeAddress = $this->settingsRepo->getValue('store.address');
            $storeSocial = $this->settingsRepo->getValue('store.social');

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Store information retrieved successfully')
                ->setData([
                    'info' => $storeInfo,
                    'contact' => $storeContact,
                    'address' => $storeAddress,
                    'social' => $storeSocial
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve store information: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get email settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmailSettings()
    {
        try {
            $smtpSettings = $this->settingsRepo->getValue('email.smtp');
            $notifications = $this->settingsRepo->getValue('email.notifications');

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Email settings retrieved successfully')
                ->setData([
                    'smtp' => $smtpSettings,
                    'notifications' => $notifications
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve email settings: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get SEO settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSeoSettings()
    {
        try {
            $seoBasic = $this->settingsRepo->getValue('seo.basic');
            $seoOpengraph = $this->settingsRepo->getValue('seo.opengraph');
            $seoTwitter = $this->settingsRepo->getValue('seo.twitter');

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('SEO settings retrieved successfully')
                ->setData([
                    'basic' => $seoBasic,
                    'opengraph' => $seoOpengraph,
                    'twitter' => $seoTwitter
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve SEO settings: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get system configuration
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSystemConfig()
    {
        try {
            $general = $this->settingsRepo->getValue('system.general');
            $security = $this->settingsRepo->getValue('system.security');
            $maintenance = $this->settingsRepo->getValue('system.maintenance');

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('System configuration retrieved successfully')
                ->setData([
                    'general' => $general,
                    'security' => $security,
                    'maintenance' => $maintenance
                ]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve system configuration: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
