<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NytBestSellersController extends Controller
{
    public function index(Request $request)
    {
        // Validate incoming request parameters
        $validated = $request->validate([
            'author' => 'nullable|string',
            'isbn' => 'nullable|array',
            'isbn.*' => 'string',
            'title' => 'nullable|string',
            'offset' => 'nullable|integer|min:0',
        ]);

        // Build the query parameters
        $query = array_merge($validated, [
            'api-key' => config('services.nyt.key'),
        ]);

        try {
            // Cache the response for 60 minutes to avoid redundant API calls
            $cacheKey = 'nyt_best_sellers_' . md5(json_encode($query));
            $data = Cache::remember($cacheKey, 60, function () use ($query) {
                $response = Http::get(config('services.nyt.base_url') . '/lists/best-sellers/history.json', $query);

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch data from NYT API: ' . $response->body(), $response->status());
                }

                return $response->json();
            });

            return response()->json($data);
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }
}
