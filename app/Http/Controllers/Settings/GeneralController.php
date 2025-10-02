<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\GeneralSettingsRepositoryInterface;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    protected $settingsRepo;

    public function __construct(GeneralSettingsRepositoryInterface $settingsRepository)
    {
        $this->settingsRepo = $settingsRepository;
    }

    /**
     * Display general store information settings
     */
    public function storeInfo()
    {
        $storeInfo = $this->settingsRepo->getValue('store.info');
        $storeContact = $this->settingsRepo->getValue('store.contact');
        $storeAddress = $this->settingsRepo->getValue('store.address');
        $storeSocial = $this->settingsRepo->getValue('store.social');
        $operatingHours = $this->settingsRepo->getValue('store.operating_hours');

        return view('pages.settings.generals.store-info', compact(
            'storeInfo',
            'storeContact',
            'storeAddress',
            'storeSocial',
            'operatingHours'
        ));
    }

    /**
     * Upload store logo
     */
    public function uploadStoreLogo(Request $request)
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048',
            ]);

            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                $oldLogo = $this->settingsRepo->getValue('store.info')['logo'] ?? null;
                if ($oldLogo && \Storage::disk('public')->exists($oldLogo)) {
                    \Storage::disk('public')->delete($oldLogo);
                }

                // Store new logo
                // Path pattern: storage/app/public/settings/store/logo/{filename}
                // Accessible via: /storage/settings/store/logo/{filename}
                $path = $request->file('logo')->store('settings/store/logo', 'public');

                // Update settings
                $storeInfo = $this->settingsRepo->getValue('store.info') ?? [];
                $storeInfo['logo'] = $path;
                $this->settingsRepo->updateValue('store.info', $storeInfo);

                // Log activity
                logActivity('update', 'Updated store logo', 'Settings');

                return response()->json([
                    'success' => true,
                    'path' => $path,
                    'url' => \Storage::disk('public')->url($path),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete store logo
     */
    public function deleteStoreLogo(Request $request)
    {
        try {
            $storeInfo = $this->settingsRepo->getValue('store.info') ?? [];
            $logo = $storeInfo['logo'] ?? null;

            if ($logo && \Storage::disk('public')->exists($logo)) {
                \Storage::disk('public')->delete($logo);
            }

            $storeInfo['logo'] = null;
            $this->settingsRepo->updateValue('store.info', $storeInfo);

            // Log activity
            logActivity('delete', 'Deleted store logo', 'Settings');

            return response()->json([
                'success' => true,
                'message' => 'Logo deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update store information settings
     */
    public function updateStoreInfo(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'store_name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string',
            ]);

            // Get current logo from settings
            $currentLogo = $this->settingsRepo->getValue('store.info')['logo'] ?? null;

            // Update store.info
            $this->settingsRepo->updateValue('store.info', [
                'name' => $request->store_name,
                'tagline' => $request->tagline,
                'description' => $request->description,
                'logo' => $currentLogo, // Keep existing logo
                'favicon' => $request->favicon,
            ]);

            // Update store.contact
            $this->settingsRepo->updateValue('store.contact', [
                'email' => $request->email,
                'phone' => $request->phone,
                'whatsapp' => $request->whatsapp,
            ]);

            // Update store.address
            $this->settingsRepo->updateValue('store.address', [
                'province_code' => $request->province_code,
                'province_name' => $request->province_name,
                'regency_code' => $request->regency_code,
                'regency_name' => $request->regency_name,
                'district_code' => $request->district_code,
                'district_name' => $request->district_name,
                'village_code' => $request->village_code,
                'village_name' => $request->village_name,
                'street' => $request->street,
                'postal_code' => $request->postal_code,
                'country' => $request->country ?? 'Indonesia',
            ]);

            // Update store.social
            $this->settingsRepo->updateValue('store.social', [
                'facebook' => $request->facebook,
                'instagram' => $request->instagram,
                'twitter' => $request->twitter,
                'tiktok' => $request->tiktok,
                'youtube' => $request->youtube,
            ]);

            // Update operating hours
            $this->settingsRepo->updateValue('store.operating_hours', [
                'hours' => $request->operating_hours,
            ]);

            // Log activity
            logActivity('update', 'Updated store information settings', 'Settings');

            // Return JSON response for AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Store information updated successfully',
                ], 200);
            }

            return redirect()->back()->with('success', 'Store information updated successfully');
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
                    'message' => 'Failed to update store information: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update store information');
        }
    }

    /**
     * Display email settings
     */
    public function emailSettings()
    {
        $smtpSettings = $this->settingsRepo->getValue('email.smtp');
        $notifications = $this->settingsRepo->getValue('email.notifications');

        return view('pages.settings.generals.email-settings', compact('smtpSettings', 'notifications'));
    }

    /**
     * Test email connection
     */
    public function testEmailConnection(Request $request)
    {
        try {
            // Validate test email
            $validated = $request->validate([
                'test_email' => 'required|email',
            ]);

            // Get current SMTP settings
            $smtpSettings = $this->settingsRepo->getValue('email.smtp');

            if (!$smtpSettings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email settings not configured',
                ], 400);
            }

            // Configure mail temporarily
            $config = [
                'transport' => 'smtp',
                'host' => $smtpSettings['host'] ?? '',
                'port' => $smtpSettings['port'] ?? 587,
                'encryption' => $smtpSettings['encryption'] ?? 'tls',
                'username' => $smtpSettings['username'] ?? '',
                'password' => $smtpSettings['password'] ?? '',
                'timeout' => null,
            ];

            // Set mail config
            config(['mail.mailers.smtp' => $config]);
            config(['mail.from.address' => $smtpSettings['from_email'] ?? 'noreply@minimoda.com']);
            config(['mail.from.name' => $smtpSettings['from_name'] ?? 'Minimoda']);

            // Send test email
            $testEmail = $request->test_email;

            \Mail::raw('This is a test email from Minimoda Backoffice.' . "\n\n" .
                'If you receive this email, your SMTP configuration is working correctly.' . "\n\n" .
                'SMTP Configuration:' . "\n" .
                '- Host: ' . ($smtpSettings['host'] ?? 'not set') . "\n" .
                '- Port: ' . ($smtpSettings['port'] ?? 'not set') . "\n" .
                '- Encryption: ' . ($smtpSettings['encryption'] ?? 'not set') . "\n" .
                '- From: ' . ($smtpSettings['from_email'] ?? 'not set') . "\n\n" .
                'Sent at: ' . now()->format('Y-m-d H:i:s'),
                function($message) use ($testEmail) {
                    $message->to($testEmail)
                        ->subject('Test Email - SMTP Configuration');
                }
            );

            // Log activity
            logActivity('test', 'Sent test email to: ' . $testEmail, 'Settings');

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $testEmail,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email address',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update email settings
     */
    public function updateEmailSettings(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'smtp_host' => 'nullable|string|max:255',
                'smtp_port' => 'nullable|integer',
                'smtp_username' => 'nullable|string|max:255',
                'smtp_encryption' => 'nullable|string|in:tls,ssl',
                'from_email' => 'required|email',
                'from_name' => 'required|string|max:255',
            ]);

            // Update SMTP settings
            $this->settingsRepo->updateValue('email.smtp', [
                'host' => $request->smtp_host,
                'port' => $request->smtp_port,
                'username' => $request->smtp_username,
                'password' => $request->smtp_password,
                'encryption' => $request->smtp_encryption,
                'from_email' => $request->from_email,
                'from_name' => $request->from_name,
            ]);

            // Update notification settings
            $this->settingsRepo->updateValue('email.notifications', [
                'order_confirmation' => $request->has('order_confirmation'),
                'order_shipped' => $request->has('order_shipped'),
                'order_delivered' => $request->has('order_delivered'),
                'welcome_email' => $request->has('welcome_email'),
                'password_reset' => $request->has('password_reset'),
                'newsletter' => $request->has('newsletter'),
            ]);

            // Log activity
            logActivity('update', 'Updated email settings', 'Settings');

            // Return JSON response for AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email settings updated successfully',
                ], 200);
            }

            return redirect()->back()->with('success', 'Email settings updated successfully');
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
                    'message' => 'Failed to update email settings: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update email settings');
        }
    }

    /**
     * Display SEO meta settings
     */
    public function seoMeta()
    {
        $seoBasic = $this->settingsRepo->getValue('seo.basic');
        $seoOpengraph = $this->settingsRepo->getValue('seo.opengraph');
        $seoTwitter = $this->settingsRepo->getValue('seo.twitter');

        return view('pages.settings.generals.seo-meta', compact('seoBasic', 'seoOpengraph', 'seoTwitter'));
    }

    /**
     * Update SEO meta settings
     */
    public function updateSeoMeta(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'site_title' => 'required|string|max:255',
                'meta_description' => 'required|string',
            ]);

            // Update SEO basic settings
            $this->settingsRepo->updateValue('seo.basic', [
                'site_title' => $request->site_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'google_analytics_id' => $request->google_analytics_id,
                'google_search_console' => $request->google_search_console,
                'facebook_pixel_id' => $request->facebook_pixel_id,
            ]);

            // Update Open Graph settings
            $this->settingsRepo->updateValue('seo.opengraph', [
                'og_title' => $request->og_title,
                'og_type' => $request->og_type,
                'og_description' => $request->og_description,
                'og_image' => $request->og_image,
            ]);

            // Update Twitter Card settings
            $this->settingsRepo->updateValue('seo.twitter', [
                'twitter_card' => $request->twitter_card,
                'twitter_site' => $request->twitter_site,
                'twitter_title' => $request->twitter_title,
                'twitter_description' => $request->twitter_description,
                'twitter_image' => $request->twitter_image,
            ]);

            // Log activity
            logActivity('update', 'Updated SEO meta settings', 'Settings');

            // Return JSON response for AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'SEO settings updated successfully',
                ], 200);
            }

            return redirect()->back()->with('success', 'SEO settings updated successfully');
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
                    'message' => 'Failed to update SEO settings: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update SEO settings');
        }
    }

    /**
     * Display system configuration
     */
    public function systemConfig()
    {
        $generalSettings = $this->settingsRepo->getValue('system.general');
        $securitySettings = $this->settingsRepo->getValue('system.security');
        $maintenanceSettings = $this->settingsRepo->getValue('system.maintenance');

        return view('pages.settings.generals.system-config', compact('generalSettings', 'securitySettings', 'maintenanceSettings'));
    }

    /**
     * Update system configuration
     */
    public function updateSystemConfig(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'timezone' => 'required|string',
                'currency' => 'required|string|max:10',
                'currency_symbol' => 'required|string|max:10',
            ]);

            // Update general settings
            $this->settingsRepo->updateValue('system.general', [
                'timezone' => $request->timezone,
                'date_format' => $request->date_format,
                'time_format' => $request->time_format,
                'default_language' => $request->default_language,
                'currency' => $request->currency,
                'currency_symbol' => $request->currency_symbol,
                'currency_position' => $request->currency_position,
                'decimal_separator' => $request->decimal_separator,
                'thousand_separator' => $request->thousand_separator,
            ]);

            // Update security settings
            $this->settingsRepo->updateValue('system.security', [
                'min_password_length' => $request->min_password_length,
                'session_timeout' => $request->session_timeout,
                'max_login_attempts' => $request->max_login_attempts,
                'lockout_duration' => $request->lockout_duration,
                'require_uppercase' => $request->has('require_uppercase'),
                'require_number' => $request->has('require_number'),
                'require_special_char' => $request->has('require_special_char'),
                'enable_2fa' => $request->has('enable_2fa'),
            ]);

            // Update maintenance settings
            $this->settingsRepo->updateValue('system.maintenance', [
                'maintenance_mode' => $request->has('maintenance_mode'),
                'maintenance_message' => $request->maintenance_message,
                'maintenance_end_time' => $request->maintenance_end_time,
            ]);

            // Log activity
            logActivity('update', 'Updated system configuration', 'Settings');

            // Return JSON response for AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'System configuration updated successfully',
                ], 200);
            }

            return redirect()->back()->with('success', 'System configuration updated successfully');
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
                    'message' => 'Failed to update system configuration: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update system configuration');
        }
    }
}