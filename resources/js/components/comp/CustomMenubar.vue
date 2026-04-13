<template>
    <div class="w-full pb-6">
        <Menubar :model="items" class="shadow-xl">
            <template #start>
                <div class="text-sky-500 text-2xl font-bold">LPMI</div>
            </template>
            <template #center="{ item, props }">
                <a v-ripple class="flex items-center justify-center" v-bind="props.action">
                    <span>{{ item.label }}</span>
                </a>
            </template>
            <template #end>
                <div class="flex items-center mr-2">
                    <Avatar
                        image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png"
                        shape="circle"
                        @click="toggleMenu"
                        style="cursor: pointer;"
                    />
                    <TieredMenu ref="menu" id="overlay_tmenu" :model="profileMenu" popup />
                </div>
            </template>
        </Menubar>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { Avatar, Menubar } from "primevue";
import CryptoJS from "crypto-js";
import {getUserName, getUserRole} from "../stores/commonStore.js";
import axios from "axios";

// const router = useRouter();
const user = getUserName();
const role = getUserRole();


const loading = ref(false);
const menu = ref(null);

const toggleMenu = (e) => {
    menu.value.toggle(e);
};

const profileMenu = computed(() => [
    {
        label: `${user} (${role})`,
        icon: "pi pi-user",
        disabled: true,
    },
    {
        label: "Logout",
        icon: "pi pi-sign-out",
        command: logout,
    },
]);

const logout = async () => {
    try {
        loading.value = true;
        const response = await axios.post(
            "/api/logout",
            {},
            {
                withCredentials: true,
            }
        );
        loading.value = false;

        if (response.data.success) {
            localStorage.removeItem("token");
            localStorage.removeItem("userRole");
            router.push("/login");
        } else {
            console.error("Logout failed");
        }
    } catch (error) {
        console.error("Error during logout:", error);
    }
};

const items = computed(() => {
    const baseItems = [
        {
            label: "Home",
            icon: "pi pi-home",
            command: () => router.push("/"),
        },
    ];

    if (role === "Evaluasi") {
        baseItems.splice(1, 0, {
            label: "Upload",
            icon: "pi pi-cloud-upload",
            command: () => router.push("/import"),
        });
    }

    if (role === "Admin") {
        baseItems.splice(0, 1, {
            label: "User Management",
            icon: "pi pi-user-edit",
            command: () => router.push("/admin/dashboard"),
        });
    }

    return baseItems;
});
</script>

<style scoped>
.navbar {
    width: 100vw;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    padding: 15px;
    background: whitesmoke;
}
</style>
