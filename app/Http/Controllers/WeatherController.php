<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    public function getWeather()
    {
        $apiKey = config('services.openweather.key');
        $location = config('services.openweather.location');
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
                        'response' => $response->json()
                    ]);
                    return response()->json(['error' => 'Weather service unavailable'], 503);
                }
                
                $data = $response->json();
                
                // Validate the response data
                if (!isset($data['weather'][0]) || !isset($data['main']) || !isset($data['name'])) {
                    Log::error('Invalid weather data received', ['data' => $data]);
                    return response()->json(['error' => 'Invalid weather data'], 500);
                }
                
                return $data;
            } catch (\Exception $e) {
                Log::error('Failed to fetch weather data', [
                    'error' => $e->getMessage(),
                    'location' => $location
                ]);
                return response()->json(['error' => 'Failed to fetch weather data'], 500);
            }
        });
    }
}
