<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const isDark = ref(false);

onMounted(() => {
    isDark.value = localStorage.getItem('theme') === 'dark';
    applyTheme();
});

function toggleTheme() {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
    applyTheme();
}

function applyTheme() {
    if (isDark.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}
</script>

<template>
    <AppLayout title="Appearance">
        <Head title="Appearance Settings" />

        <div class="max-w-2xl space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Appearance</h2>
                <p class="text-sm text-slate-500 mb-6">Toggle dark mode.</p>
                <div class="flex items-center gap-4">
                    <button
                        @click="toggleTheme"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-6 py-2.5 rounded-xl transition-all flex items-center gap-2"
                    >
                        <i :class="isDark ? 'pi pi-sun' : 'pi pi-moon'"></i>
                        {{ isDark ? 'Light Mode' : 'Dark Mode' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
