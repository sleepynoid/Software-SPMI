<script setup>
import { ref } from "vue";
import Homepage from "@/components/homepage/homepage.vue";
import About from "@/components/homepage/about.vue";
import Register from "@/components/login/register.vue";
import { useRouter } from "vue-router";

let token = localStorage.getItem("token");
const page = ref("home");
const router = useRouter();

const logout = async () => {
    try {
        const response = await axios.post(
            "/api/logout",
            {},
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );

        console.log('respons: ',response);

        if (response.data.success) {
            localStorage.removeItem("token");
            router.push("/login");
        } else {
            console.error("Logout failed");
        }
    } catch (error) {
        console.error("Error during logout:", error);
    }
};
</script>

<template>
    <h1 v-if="token != undefined">{{ token }}</h1>
    <div class="c1">
        <div class="topbar">
            <h2>SPMI</h2>
            <div class="menu">
                <strong>
                    <router-link to="/login">Login</router-link>
                </strong>
                <strong @click="page = 'home'">Home</strong>
                <strong @click="page = 'reg'">Register</strong>
                <strong @click="page = 'about'">About</strong>
            </div>
            <div class="search">
                <!-- Search content -->
            </div>
            <button v-if="token" class="login" @click="logout">Logout</button>
        </div>

        <div class="content">
            <Homepage v-if="page === 'home'" />
            <Register v-if="page === 'reg'" />
            <About v-if="page === 'about'" />
        </div>
    </div>
</template>

<style scoped>
.c1 {
    width: 100vw;
    /* //height: 200vh; */
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    padding: 3%;
    background: whitesmoke;
}

.topbar {
    width: 100%;
    height: 4rem;
    background: white;
    display: flex;
    align-items: center;
    padding: 0rem 2rem 0rem 2rem;
    border-radius: 1rem;
    box-shadow: 0 10px 20px 1px lightgray;
}

.menu {
    display: flex;
    gap: 3rem;
    margin: 0 2.5rem 0 2.5rem;

    strong {
        cursor: pointer;
    }
}

.search {
    width: 35%;
    height: 70%;
    background: lightgray;
    border-radius: 10rem;
    margin-right: 15rem;
}

.login {
    width: 7%;
    height: 50%;
    background: #ffda76;
    border-radius: 10rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.content {
    display: flex;
    z-index: 0;
    padding: 3%;
}
</style>
