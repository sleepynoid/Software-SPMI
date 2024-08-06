import {createRouter, createWebHistory} from "vue-router";
import Home from './components/homepage.vue';
import Sheet from './components/sheets/sheet.vue';
import Importexcel from './components/upload/import.vue';
import Login from './components/login/login.vue';
import Register from './components/login/register.vue';
import NotFound from './components/notFound.vue';

const routes =[
    {
        path: '/',
        component: Home
    },
    {
        path: '/sheet/:jurusan/:periode',
        name: 'Sheet',
        component: Sheet
    },
    {
        path: '/import',
        component: Importexcel
    },
    {
        path: '/login',
        component: Login
    },
    {
        path: '/register',
        component: Register
    },

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
