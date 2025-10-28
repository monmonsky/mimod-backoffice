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

            // Build prompt
            $prompt = $this->buildPrompt($name, $description, $brandName, $categoriesStr, $tagsStr, $ageRange);

            // Try AI providers with fallback: Anthropic -> Gemini -> Template
            $seoData = null;
            $provider = null;

            // 1. Try Anthropic Claude (Priority 1)
            try {
                $seoData = $this->generateWithAnthropic($prompt);
                $provider = 'Anthropic Claude';
            } catch (\Exception $e) {
                Log::warning('Anthropic API failed, trying Gemini...', ['error' => $e->getMessage()]);
            }

            // 2. Try Gemini (Priority 2)
            if (!$seoData) {
                try {
                    $seoData = $this->generateWithGemini($prompt);
                    $provider = 'Google Gemini';
                } catch (\Exception $e) {
                    Log::warning('Gemini API failed, using template...', ['error' => $e->getMessage()]);
                }
            }

            // 3. Fallback to template
            if (!$seoData) {
                Log::info('Using fallback template-based SEO generation');
                $seoData = $this->generateTemplateSeo($name, $description, $brandName, $categoriesStr, $tagsStr, $ageRange);
                $provider = 'Template';
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage("SEO generated successfully (provider: {$provider})")
                ->setData($seoData);

            return response()->json($this->response->generateResponse($result), 200);

        } catch (\Exception $e) {
            Log::error('AI SEO Generation Error: ' . $e->getMessage());

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

    /**
     * Generate SEO using template (fallback when AI fails)
     */
    private function generateSeoFallback($name, $description, $brandName, $categories, $tags, $ageRange)
    {
        // Truncate description for meta
        $shortDesc = mb_strlen($description) > 100 ? mb_substr($description, 0, 100) . '...' : $description;

        // Build SEO Title (max 60 chars)
        $title = $name;
        if (!empty($brandName)) {
            $title .= " - {$brandName}";
        }
        $title .= " | Minimoda";
        if (mb_strlen($title) > 60) {
            $title = mb_substr($name, 0, 40) . "... | Minimoda";
        }

        // Build Meta Description (120-160 chars)
        $metaDesc = "Beli {$name}";
        if (!empty($brandName)) {
            $metaDesc .= " dari {$brandName}";
        }
        $metaDesc .= ". {$shortDesc}";
        if (!empty($ageRange)) {
            $metaDesc .= " Untuk usia {$ageRange}.";
        }
        $metaDesc .= " Belanja sekarang di Minimoda!";

        if (mb_strlen($metaDesc) > 160) {
            $metaDesc = mb_substr($metaDesc, 0, 157) . "...";
        }

        // Build Keywords
        $keywords = [$name];

        if (!empty($brandName)) {
            $keywords[] = $brandName;
        }

        if (!empty($categories)) {
            $catArray = explode(', ', $categories);
            $keywords = array_merge($keywords, array_slice($catArray, 0, 3));
        }

        if (!empty($tags)) {
            $tagArray = explode(', ', $tags);
            $keywords = array_merge($keywords, array_slice($tagArray, 0, 3));
        }

        $keywords[] = 'pakaian anak';
        $keywords[] = 'fashion anak';
        $keywords[] = 'baju anak berkualitas';

        // Remove duplicates and limit to 10
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, 10);

        return [
            'title' => $title,
            'description' => $metaDesc,
            'keywords' => implode(', ', $keywords)
        ];
    }

    /**
     * Generate SEO with Anthropic Claude API
     */
    private function generateWithAnthropic($prompt)
    {
        $apiKey = config('services.anthropic.api_key');

        if (!$apiKey) {
            throw new \Exception('Anthropic API key not configured');
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-haiku-20240307',
                'max_tokens' => 1024,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

        if ($response->failed()) {
            Log::error('Anthropic API Request Failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('Anthropic API request failed: ' . $response->body());
        }

        $result = $response->json();
        $generatedText = $result['content'][0]['text'] ?? '';

        if (empty($generatedText)) {
            throw new \Exception('Empty response from Anthropic API');
        }

        return $this->parseGeneratedSeo($generatedText);
    }

    /**
     * Generate SEO with Google Gemini API
     */
    private function generateWithGemini($prompt)
    {
        $apiKey = config('services.gemini.api_key');

        if (!$apiKey || $apiKey === 'your_gemini_api_key_here') {
            throw new \Exception('Gemini API key not configured');
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
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

        if ($response->failed()) {
            Log::error('Gemini API Request Failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('Gemini API request failed: ' . $response->body());
        }

        $result = $response->json();
        $generatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($generatedText)) {
            throw new \Exception('Empty response from Gemini API');
        }

        return $this->parseGeneratedSeo($generatedText);
    }

    /**
     * Generate SEO using template
     */
    private function generateTemplateSeo($name, $description, $brandName, $categories, $tags, $ageRange)
    {
        return $this->generateSeoFallback($name, $description, $brandName, $categories, $tags, $ageRange);
    }
}
