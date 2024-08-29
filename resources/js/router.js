import {createRouter, createWebHistory} from "vue-router";
import Home from './components/homepage/home.vue';
import Sheet from './components/sheets/sheet.vue';
import Importexcel from './components/upload/import.vue';
import Register from './components/login/register.vue';
import Login from './components/login/login.vue';
import NotFound from './components/notFound.vue';

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
    {
        path: '/',
        component: Home,
        meta: {
            enterClass: 'animate__animated animate__fadeInLeft',
            leaveClass: 'animate__animated animate__fadeOutRight'
        },
    },
    {
        path: '/sheet/:jurusan/:periode',
        name: 'Sheet',
        component: Sheet,
        meta: {
            enterClass: 'animate__animated animate__fadeInLeft',
            leaveClass: 'animate__animated animate__fadeOutRight'
        },
    },
    {
        path: '/import',
        component: Importexcel,
        meta: {
            enterClass: 'animate__animated animate__fadeInLeft',
            leaveClass: 'animate__animated animate__fadeOutRight'
        },
    },
    {
        path: '/login',
        component: Login,
        meta: {
            enterClass: 'animate__animated animate__fadeInLeft',
            leaveClass: 'animate__animated animate__fadeOutRight'
        },
    },
    {
        path: '/register',
        component: Register,
        meta: {
            enterClass: 'animate__animated animate__fadeInLeft',
            leaveClass: 'animate__animated animate__fadeOutRight'
        },
    },

        {
            path: '/:pathMatch(.*)*',
            component: NotFound,
            meta: {
                enterClass: 'animate__animated animate__fadeInLeft',
                leaveClass: 'animate__animated animate__fadeOutRight'
            },
        },
    ]
})

export default router
