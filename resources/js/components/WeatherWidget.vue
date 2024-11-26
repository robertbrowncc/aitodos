<template>
  <div class="weather-widget mt-8 border-t pt-8">
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
      <h3 class="text-xl font-semibold mb-4 text-gray-700">
        Current Weather in {{ weather.name }} {{ getWeatherEmoji(weather.weather[0].description) }}
      </h3>
      <div class="space-y-2">
        <p class="text-lg"><span class="font-medium">Temperature:</span> {{ Math.round(weather.main.temp) }}°C</p>
        <p class="text-lg"><span class="font-medium">Feels like:</span> {{ Math.round(weather.main.feels_like) }}°C</p>
        <p class="text-lg capitalize"><span class="font-medium">Condition:</span> {{ weather.weather[0].description }}</p>
        <p class="text-lg"><span class="font-medium">Humidity:</span> {{ weather.main.humidity }}%</p>
      </div>
    </div>
    <div v-else>
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
    const response = await axios.get('/api/weather');
    
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
