<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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
                $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => $location,
                    'units' => 'metric',
                    'appid' => $apiKey
                ]);
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return response()->json(['error' => 'Failed to fetch weather data'], 500);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to fetch weather data'], 500);
            }
        });
    }
}
