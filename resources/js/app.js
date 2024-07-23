import './bootstrap';
import { createApp } from "vue";
import app from './components/app.vue';
import router from "./router";
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min';


createApp(app).use(router).mount('#app');
