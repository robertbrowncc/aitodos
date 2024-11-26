<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function getWeather()
    {
        $apiKey = config('services.openweather.key');
        $location = config('services.openweather.location');
        
        try {
            $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'q' => $location,
                'units' => 'metric',
                'appid' => $apiKey
            ]);
            
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch weather data'], 500);
        }
    }
}
