<script setup lang="ts">
import { onMounted, watch } from "vue";
import Homepage from "../components/homepage/homepage.vue";
import Toast from "primevue/toast";
import { useToast } from "primevue";
import { usePage } from "@inertiajs/vue3";

const toast = useToast();
const page = usePage();

// Tampilkan flash message dari Inertia (misal setelah redirect)
watch(
    () => page.props.flash,
    (flash: any) => {
        if (flash?.success) {
            toast.add({ severity: 'success', summary: 'Sukses', detail: flash.success, life: 3000 });
        }
        if (flash?.error) {
            toast.add({ severity: 'error', summary: 'Error', detail: flash.error, life: 4000 });
        }
    },
    { immediate: true }
);
</script>

<template>
    <div class="max-h-full h-full w-full">
        <Toast />
        <Homepage />
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
