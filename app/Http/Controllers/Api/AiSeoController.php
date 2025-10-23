<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiSeoController extends Controller
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function generateSeo(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'brand_name' => 'nullable|string',
            'categories' => 'nullable|array',
            'tags' => 'nullable|array',
            'age_min' => 'nullable|integer',
            'age_max' => 'nullable|integer',
        ]);

        try {
            // Prepare data
            $name = $validated['name'];
            $description = $validated['description'];
            $brandName = $validated['brand_name'] ?? '';
            $categories = $validated['categories'] ?? [];
            $tags = $validated['tags'] ?? [];
            $ageMin = $validated['age_min'] ?? 0;
            $ageMax = $validated['age_max'] ?? 0;

            // Build age range string
            $ageRange = '';
            if ($ageMin > 0 && $ageMax > 0) {
                $ageRange = "{$ageMin}-{$ageMax} tahun";
            }

            // Build categories string
            $categoriesStr = !empty($categories) ? implode(', ', $categories) : 'General';

            // Build tags string
            $tagsStr = !empty($tags) ? implode(', ', $tags) : '';

            // Build prompt for Gemini
            $prompt = $this->buildPrompt($name, $description, $brandName, $categoriesStr, $tagsStr, $ageRange);

            // Call Gemini API
            $apiKey = config('services.gemini.api_key');

            if (!$apiKey || $apiKey === 'your_gemini_api_key_here') {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('500')
                    ->setMessage('Gemini API key not configured. Please set GEMINI_API_KEY in .env file')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 500);
            }

            // Use the correct Gemini API endpoint - gemini-2.5-flash (verified working)
            $httpResponse = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 1024,
                    ]
                ]);

            if ($httpResponse->failed()) {
                throw new \Exception('Failed to generate SEO from AI: ' . $httpResponse->body());
            }

            $apiResult = $httpResponse->json();

            // Extract text from response
            $generatedText = $apiResult['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($generatedText)) {
                throw new \Exception('AI response is empty');
            }

            // Parse JSON from generated text
            $seoData = $this->parseGeneratedSeo($generatedText);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('SEO generated successfully')
                ->setData($seoData);

            return response()->json($this->response->generateResponse($result), 200);

        } catch (\Exception $e) {
            Log::error('AI SEO Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to generate SEO: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Build prompt for Gemini AI
     */
    private function buildPrompt($name, $description, $brandName, $categories, $tags, $ageRange)
    {
        $prompt = "Generate SEO metadata untuk produk e-commerce dengan detail berikut:\n\n";
        $prompt .= "Nama Produk: {$name}\n";
        $prompt .= "Brand: {$brandName}\n";
        $prompt .= "Deskripsi: {$description}\n";
        $prompt .= "Kategori: {$categories}\n";

        if (!empty($tags)) {
            $prompt .= "Tags: {$tags}\n";
        }

        if (!empty($ageRange)) {
            $prompt .= "Rentang Usia: {$ageRange}\n";
        }

        $prompt .= "\nRequirements:\n";
        $prompt .= "1. SEO Title: Maksimal 60 karakter, include brand dan main keywords, menarik untuk diklik\n";
        $prompt .= "2. Meta Description: 120-160 karakter, persuasif dan include call-to-action (misal: 'Beli sekarang', 'Dapatkan diskon', dll)\n";
        $prompt .= "3. Keywords: 5-10 relevant keywords, comma-separated, fokus pada search intent\n\n";
        $prompt .= "PENTING: Return HANYA valid JSON dalam format ini, tanpa markdown atau text tambahan:\n";
        $prompt .= '{"title": "...", "description": "...", "keywords": "..."}';
        $prompt .= "\n\nBahasa: Indonesian (Bahasa Indonesia)";

        return $prompt;
    }

    /**
     * Parse generated SEO from AI response
     */
    private function parseGeneratedSeo($text)
    {
        // Remove markdown code blocks if any
        $text = preg_replace('/```json\s*/', '', $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        // Try to decode JSON
        $decoded = json_decode($text, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return [
                'title' => $decoded['title'] ?? '',
                'description' => $decoded['description'] ?? '',
                'keywords' => $decoded['keywords'] ?? '',
            ];
        }

        // Fallback if JSON parsing fails
        throw new \Exception('Failed to parse AI response. Invalid JSON format. Response: ' . substr($text, 0, 200));
    }
}
