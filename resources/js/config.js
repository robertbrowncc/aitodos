export default {
    weather: {
        apiKey: import.meta.env.VITE_WEATHER_API_KEY,
        location: import.meta.env.VITE_WEATHER_LOCATION,
        apiUrl: 'https://api.openweathermap.org/data/2.5/weather',
        corsProxy: import.meta.env.VITE_APP_ENV === 'local' ? 'https://cors-anywhere.herokuapp.com' : ''
    }
};
