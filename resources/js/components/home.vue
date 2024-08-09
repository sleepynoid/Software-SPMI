<script setup>
import { ref } from 'vue';
import Homepage from "@/components/homepage.vue";
import Login from "@/components/login/login.vue";

let user = localStorage.getItem('token');

const page = ref('home');
</script>

<template>
    <div class="c1">
        <div class="topbar">
            <h2>SPMI</h2>
            <div class="menu">
                <strong @click="page = 'login'">Login</strong>
                <strong @click="page = 'home'">Home</strong>
                <strong>Register</strong>
                <strong>About</strong>
            </div>
            <div class="search">
                <!-- Search content -->
            </div>
            <div v-if="user" class="login">logout</div>
        </div>

        <div class="content">
            <Login v-if="page === 'login'"/>
            <Homepage v-if="page === 'home'"/>
        </div>
    </div>
</template>


<style scoped>
.custom-router-link {
    text-decoration: none;
    color: inherit;
}

.c1{
    width: 100vw;
    /* //height: 200vh; */
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    padding: 3%;
    background: whitesmoke;
}

.topbar{
    width: 100%;
    height: 4rem;
    background: white;
    display: flex;
    align-items: center;
    padding: 0rem 2rem 0rem 2rem;
    border-radius: 1rem;
    box-shadow: 0 10px 20px 1px lightgray;
}

.menu{
    display: flex;
    gap: 3rem;
    margin: 0 2.5rem 0 2.5rem;

    strong{
        cursor: pointer;
    }
}

.search{
    width: 35%;
    height: 70%;
    background: lightgray;
    border-radius: 10rem;
    margin-right: 15rem;
}

.login{
    width: 7%;
    height: 50%;
    background: #FFDA76;
    border-radius: 10rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.content{
    display: flex;
    z-index: 0;
    padding: 3%;
}
</style>
