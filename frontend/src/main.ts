import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';

// 👇 Add FontAwesome CSS
import '@fortawesome/fontawesome-free/css/all.min.css';

// Your existing Tailwind & custom styles
import './assets/styles/tailwind.css';
import './assets/styles/index.css';

const app = createApp(App);
app.use(createPinia());
app.use(router);
app.mount('#app');
