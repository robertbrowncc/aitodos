<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class WeatherController extends Controller
{
    public function getWeather()
    {
        // Apply rate limiting - 60 requests per minute
        $executed = RateLimiter::attempt(
            'weather_api',
            60,
            function() {
                return $this->fetchWeatherData();
            },
            60
        );

        if (!$executed) {
            return response()->json(['error' => 'Too many requests'], 429);
        }

        return $executed;
    }

    private function fetchWeatherData()
    {
        $apiKey = config('services.openweather.key');
        $location = config('services.openweather.location');
        
        // Log configuration values (excluding API key)
        Log::info('Weather API request', [
            'location' => $location,
            'has_api_key' => !empty($apiKey)
        ]);
        
        if (empty($apiKey) || empty($location)) {
            Log::error('Missing weather configuration', [
                'has_api_key' => !empty($apiKey),
                'has_location' => !empty($location)
            ]);
            return response()->json(['error' => 'Weather service not configured'], 503);
        }
        
        $cacheKey = "weather_{$location}";
        
        // Try to get weather data from cache
        return Cache::remember($cacheKey, 3600, function () use ($apiKey, $location) {
            try {
                $response = Http::timeout(5)->get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => $location,
                    'units' => 'metric',
                    'appid' => $apiKey
                ]);
                
                if (!$response->successful()) {
                    Log::error('OpenWeather API error', [
                        'status' => $response->status(),
                        'response' => $response->json(),
                        'location' => $location
                    ]);
                    return response()->json([
                        'error' => 'Weather service unavailable',
                        'status' => $response->status()
                    ], 503);
                }
                
                $data = $response->json();
                
                // Validate the response data
                if (!isset($data['weather'][0]) || !isset($data['main']) || !isset($data['name'])) {
                    Log::error('Invalid weather data received', [
                        'data' => $data,
                        'location' => $location
                    ]);
                    return response()->json([
                        'error' => 'Invalid weather data',
                        'data_received' => $data
                    ], 500);
                }
                
                return $data;
            } catch (\Exception $e) {
                Log::error('Failed to fetch weather data', [
                    'error' => $e->getMessage(),
                    'location' => $location,
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Failed to fetch weather data',
                    'message' => $e->getMessage()
                ], 500);
            }
        });
    }
}
