import './bootstrap';
import { createApp } from "vue";
import app from './components/app.vue';
import router from "./router";
import 'bootstrap/dist/css/bootstrap.css';
import bootsrap from 'bootstrap/dist/js/bootstrap.bundle.js';


createApp(app).use(router).use(bootsrap).mount('#app');
