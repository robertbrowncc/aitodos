import './bootstrap';
import { createApp } from 'vue';
import router from './router';
import App from './components/App.vue';
import axios from 'axios';

// Set the base URL for all axios requests based on environment
const baseURL = import.meta.env.VITE_APP_URL;
console.log('Using base URL:', baseURL); // For debugging

// If no base URL is set, use the current origin
if (!baseURL) {
    console.warn('VITE_APP_URL not set, falling back to window.location.origin');
}

axios.defaults.baseURL = baseURL || window.location.origin;

// Add CSRF token and other required headers to all requests
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

const app = createApp(App);
app.use(router);
app.mount('#app');
