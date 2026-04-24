<script setup>
import { useForm, Head } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen bg-[#f8fafc] flex items-center justify-center p-4">
        <Head title="Login SPMI" />
        
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
            <div class="bg-primary-600 p-8 text-white text-center relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-3xl font-bold mb-2">SPMI Online</h1>
                    <p class="text-primary-100">Sistem Penjaminan Mutu Internal</p>
                </div>
                <!-- Subtle background decoration -->
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-black/10 rounded-full blur-3xl"></div>
            </div>

            <div class="p-8 pb-10">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email Institusi</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="pi pi-envelope"></i>
                            </span>
                            <input 
                                v-model="form.email"
                                type="email" 
                                required
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all"
                                placeholder="nama@spmi.ac.id"
                            />
                        </div>
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Kata Sandi</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="pi pi-lock"></i>
                            </span>
                            <input 
                                v-model="form.password"
                                type="password" 
                                required
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all"
                                placeholder="••••••••"
                            />
                        </div>
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                            <input type="checkbox" class="rounded text-primary-600 focus:ring-primary-500 border-slate-300">
                            Ingat saya
                        </label>
                        <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Lupa sandi?</a>
                    </div>

                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-primary-200 disabled:opacity-50 disabled:shadow-none flex items-center justify-center gap-2"
                    >
                        <i v-if="form.processing" class="pi pi-spin pi-spinner"></i>
                        Masuk ke Sistem
                    </button>
                </form>

                <div class="mt-10 pt-6 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-500">
                        Butuh bantuan login? <a href="#" class="text-primary-600 font-medium">Hubungi Admin LPM</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
