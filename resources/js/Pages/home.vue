<script setup lang="ts">
import { ref, onMounted } from "vue";
import Homepage from "../components/homepage/homepage.vue";
import Toast from "primevue/toast";
import Import from "./import.vue";
import { useToast } from "primevue";

const toast = useToast();
const page = ref("home");

onMounted(() => {
    const toastMessage = localStorage.getItem("toastMessage");
    const toastSeverity = localStorage.getItem("toastSeverity");

    if (toastMessage) {
        toast.add({
            severity: toastSeverity || "success",
            summary: "Success",
            detail: toastMessage,
            life: 3000,
        });

        localStorage.removeItem("toastMessage");
        localStorage.removeItem("toastSeverity");
    }
});
</script>

<template>
    <div class="max-h-full h-full w-full">
        <Toast />
        <div>
            <Homepage v-if="page === 'home'" />
            <Import v-if="page === 'import'" />
        </div>
    </div>
</template>

<style scoped>
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
    cursor: pointer;
}
/*
.content {
    display: flex;
    z-index: 0;
    padding: 3%;
} */

.usr {
    position: relative;
    margin-left: 60%;
}
</style>
