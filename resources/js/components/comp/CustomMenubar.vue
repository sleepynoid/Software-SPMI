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
import { router, usePage } from "@inertiajs/vue3";
import { Avatar, Menubar } from "primevue";

const page = usePage();
const user = computed(() => (page.props.auth as any)?.user?.name ?? '');
const role = computed(() => (page.props.auth as any)?.user?.role ?? '');

const menu = ref(null);

const toggleMenu = (e) => {
    menu.value.toggle(e);
};

const profileMenu = computed(() => [
    {
        label: `${user.value} (${role.value})`,
        icon: "pi pi-user",
        disabled: true,
    },
    {
        label: "Logout",
        icon: "pi pi-sign-out",
        command: logout,
    },
]);

const logout = () => {
    router.post('/logout');
};

const items = computed(() => {
    const baseItems = [
        {
            label: "Home",
            icon: "pi pi-home",
            command: () => router.visit("/"),
        },
    ];

    if (role.value === "Evaluasi") {
        baseItems.splice(1, 0, {
            label: "Upload",
            icon: "pi pi-cloud-upload",
            command: () => router.visit("/import"),
        });
    }

    if (role.value === "Admin") {
        baseItems.splice(0, 1, {
            label: "User Management",
            icon: "pi pi-user-edit",
            command: () => router.visit("/admin/dashboard"),
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
