<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class EmailTestApiController extends Controller
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Test email connection with current or custom configuration
     */
    public function testConnection(Request $request)
    {
        try {
            $validated = $request->validate([
                'test_email' => 'required|email',
                'use_custom_config' => 'nullable|boolean',
                // Custom SMTP config (optional)
                'smtp_host' => 'nullable|string',
                'smtp_port' => 'nullable|integer',
                'smtp_username' => 'nullable|string',
                'smtp_password' => 'nullable|string',
                'smtp_encryption' => 'nullable|in:tls,ssl,null',
                'from_address' => 'nullable|email',
                'from_name' => 'nullable|string',
            ]);

            $testEmail = $validated['test_email'];
            $useCustomConfig = $validated['use_custom_config'] ?? false;

            // If using custom config, temporarily set mail config
            if ($useCustomConfig) {
                if (!isset($validated['smtp_host']) || !isset($validated['smtp_port'])) {
                    $result = (new ResultBuilder())
                        ->setStatus(false)
                        ->setStatusCode('422')
                        ->setMessage('SMTP host and port are required when using custom configuration');

                    return response()->json($this->response->generateResponse($result), 422);
                }

                // Temporarily set mail config for this test
                Config::set('mail.mailers.smtp', [
                    'transport' => 'smtp',
                    'host' => $validated['smtp_host'],
                    'port' => $validated['smtp_port'],
                    'username' => $validated['smtp_username'] ?? null,
                    'password' => $validated['smtp_password'] ?? null,
                    'encryption' => $validated['smtp_encryption'] ?? 'tls',
                    'timeout' => 30,
                ]);

                Config::set('mail.default', 'smtp');

                if (isset($validated['from_address'])) {
                    Config::set('mail.from.address', $validated['from_address']);
                }
                if (isset($validated['from_name'])) {
                    Config::set('mail.from.name', $validated['from_name']);
                }
            }

            // Get current mail config for response
            $currentConfig = [
                'mailer' => Config::get('mail.default'),
                'host' => Config::get('mail.mailers.smtp.host'),
                'port' => Config::get('mail.mailers.smtp.port'),
                'encryption' => Config::get('mail.mailers.smtp.encryption'),
                'username' => Config::get('mail.mailers.smtp.username'),
                'from_address' => Config::get('mail.from.address'),
                'from_name' => Config::get('mail.from.name'),
            ];

            // Send test email
            $startTime = microtime(true);

            try {
                Mail::raw('This is a test email from Minimoda. If you receive this, your email configuration is working correctly.', function ($message) use ($testEmail) {
                    $message->to($testEmail)
                        ->subject('Test Email - Minimoda Email Configuration');
                });

                $endTime = microtime(true);
                $duration = round(($endTime - $startTime) * 1000, 2); // Convert to milliseconds

                // Log activity
                logActivity('test', "Email connection test successful to {$testEmail}", 'email_test', null, [
                    'test_email' => $testEmail,
                    'duration_ms' => $duration,
                    'config' => $currentConfig,
                ]);

                $result = (new ResultBuilder())
                    ->setStatus(true)
                    ->setStatusCode('200')
                    ->setMessage('Email sent successfully')
                    ->setData([
                        'test_email' => $testEmail,
                        'duration_ms' => $duration,
                        'config_used' => $currentConfig,
                        'message' => 'Test email has been sent. Please check your inbox (and spam folder).',
                    ]);

                return response()->json($this->response->generateResponse($result), 200);
            } catch (\Exception $mailException) {
                // Get detailed error information
                $errorDetails = [
                    'message' => $mailException->getMessage(),
                    'type' => get_class($mailException),
                    'file' => basename($mailException->getFile()),
                    'line' => $mailException->getLine(),
                ];

                // Log failed test with full trace
                logActivity('test', "Email connection test failed to {$testEmail}: {$mailException->getMessage()}", 'email_test', null, [
                    'test_email' => $testEmail,
                    'error' => $mailException->getMessage(),
                    'error_type' => get_class($mailException),
                    'trace' => $mailException->getTraceAsString(),
                    'config' => $currentConfig,
                ]);

                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('500')
                    ->setMessage('Failed to send test email')
                    ->setData([
                        'error' => $mailException->getMessage(),
                        'error_type' => get_class($mailException),
                        'error_file' => basename($mailException->getFile()),
                        'error_line' => $mailException->getLine(),
                        'config_used' => $currentConfig,
                        'suggestions' => [
                            'Check SMTP host and port are correct',
                            'Verify username and password',
                            'Check if 2FA or App Password is required',
                            'Verify SSL/TLS encryption settings',
                            'Check firewall or network restrictions',
                            'For Mailtrap: use sandbox.smtp.mailtrap.io instead of smtp.mailtrap.io',
                        ],
                    ]);

                return response()->json($this->response->generateResponse($result), 500);
            }
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
                ->setMessage('Failed to test email connection: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get current email configuration (without sensitive data)
     */
    public function getConfig()
    {
        try {
            $config = [
                'default_mailer' => Config::get('mail.default'),
                'smtp' => [
                    'host' => Config::get('mail.mailers.smtp.host'),
                    'port' => Config::get('mail.mailers.smtp.port'),
                    'encryption' => Config::get('mail.mailers.smtp.encryption'),
                    'username' => Config::get('mail.mailers.smtp.username'),
                    'password_set' => !empty(Config::get('mail.mailers.smtp.password')),
                ],
                'from' => [
                    'address' => Config::get('mail.from.address'),
                    'name' => Config::get('mail.from.name'),
                ],
            ];

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Email configuration retrieved successfully')
                ->setData($config);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve email configuration: ' . $e->getMessage());

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
