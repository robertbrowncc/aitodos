<template>
  <div class="p-4 bg-blue-50 rounded-xl h-full">
    <div v-if="error" class="text-red-500">
      <p>{{ error }}</p>
      <button 
        v-if="canRetry"
        @click="fetchWeather" 
        class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
      >
        Retry
      </button>
    </div>
    <div v-else-if="weather && weather.weather && weather.weather[0]">
      <h3 class="text-lg font-semibold text-blue-700 mb-3">
        🌡️ Current Weather
      </h3>
      <div class="space-y-2">
        <div class="flex items-center justify-between p-2 bg-white rounded-lg shadow-sm">
          <span class="font-medium text-gray-700">{{ weather.name }}</span>
          <span class="text-sm text-blue-600">
            {{ getWeatherEmoji(weather.weather[0].description) }} {{ Math.round(weather.main.temp) }}°C
          </span>
        </div>
        <div class="flex items-center justify-between p-2 bg-white rounded-lg shadow-sm">
          <span class="font-medium text-gray-700">Feels Like</span>
          <span class="text-sm text-blue-600">{{ Math.round(weather.main.feels_like) }}°C</span>
        </div>
        <div class="flex items-center justify-between p-2 bg-white rounded-lg shadow-sm">
          <span class="font-medium text-gray-700">Condition</span>
          <span class="text-sm text-blue-600 capitalize">{{ weather.weather[0].description }}</span>
        </div>
        <div class="flex items-center justify-between p-2 bg-white rounded-lg shadow-sm">
          <span class="font-medium text-gray-700">Humidity</span>
          <span class="text-sm text-blue-600">{{ weather.main.humidity }}%</span>
        </div>
      </div>
    </div>
    <div v-else class="flex items-center justify-center h-full">
      <p class="text-gray-500">Loading weather data...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const weather = ref(null);
const error = ref(null);
const canRetry = ref(true);

const getWeatherEmoji = (condition) => {
  if (!condition) return '🌡️';
  
  const weatherMap = {
    'clear sky': '☀️',
    'few clouds': '🌤️',
    'scattered clouds': '⛅',
    'broken clouds': '☁️',
    'shower rain': '🌦️',
    'rain': '🌧️',
    'thunderstorm': '⛈️',
    'snow': '🌨️',
    'mist': '🌫️',
    'moderate rain': '🌧️',
    'light rain': '🌦️',
    'heavy rain': '⛈️',
    'overcast clouds': '☁️',
    'haze': '🌫️',
    'fog': '🌫️',
    'drizzle': '🌦️'
  };
  
  return weatherMap[condition.toLowerCase()] || '🌡️';
};

const validateWeatherData = (data) => {
  if (!data) return false;
  if (!data.weather || !Array.isArray(data.weather) || data.weather.length === 0) return false;
  if (!data.main || !data.main.temp || !data.main.feels_like || !data.main.humidity) return false;
  if (!data.name) return false;
  return true;
};

const fetchWeather = async () => {
  try {
    error.value = null;
    canRetry.value = true;
    const response = await axios.get('/api/weather', {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    });
    
    if (validateWeatherData(response.data)) {
      weather.value = response.data;
    } else {
      error.value = 'Invalid weather data received';
      console.error('Invalid weather data:', response.data);
    }
  } catch (err) {
    if (err.response?.status === 429) {
      error.value = 'Too many requests. Please try again in a minute.';
      canRetry.value = false;
      setTimeout(() => {
        canRetry.value = true;
      }, 60000);
    } else {
      error.value = 'Failed to load weather data';
      console.error('Error fetching weather data:', err);
    }
    weather.value = null;
  }
};

onMounted(() => {
  fetchWeather();
});
</script>

<style scoped>
.weather-widget {
  max-width: 300px;
  margin: 0 auto;
}
</style>
