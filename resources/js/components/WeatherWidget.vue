<template>
  <div class="weather-widget mt-8 border-t pt-8">
    <div v-if="weather">
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

const getWeatherEmoji = (condition) => {
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

const fetchWeather = async () => {
  try {
    const response = await axios.get('/api/weather');
    weather.value = response.data;
  } catch (error) {
    console.error('Error fetching weather data:', error);
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
