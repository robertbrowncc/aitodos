import './bootstrap';
import { createApp } from 'vue';
import router from './router';
import App from './components/App.vue';
import axios from 'axios';

// Set the base URL for all axios requests
axios.defaults.baseURL = 'https://aitodos.test';

// Add CSRF token and other required headers to all requests
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

const app = createApp(App);
app.use(router);
app.mount('#app');
