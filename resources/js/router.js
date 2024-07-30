import {createRouter, createWebHistory} from "vue-router";
import Home from './components/homepage.vue';
import Sheet from './components/sheet.vue';
import Upload from './components/upload.vue';
import Importexcel from './components/import.vue';
import NotFound from './components/notFound.vue';

const routes =[
    {
        path: '/',
        component: Home
    },
    {
        path: '/sheet',
        component: Sheet
    },
    {
        path: '/import',
        component: Importexcel
    },
    // {
    //     path: '/upload',
    //     component: Upload
    // },

    // {
    //     path: '/:pathMatch(.*)*',
    //     component: NotFound
    // }
]

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router
