<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import { useToastHelper } from '@/composables/useToastHelper';

const { formError } = useToastHelper();

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post(route('login.store'), {
        ...formError(),
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen bg-[#f8fafc] flex items-center justify-center p-4">
        <Head title="Login SPMI" />
        <Toast />

        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
            <div class="bg-[#00479b] p-8 text-white text-center relative overflow-hidden">
                <div class="relative z-10 flex flex-col items-center">
                    <img :src="'/images/itats-icon.png'" alt="ITATS Icon" class="h-16 mb-4 object-contain" />
                    <h1 class="text-3xl font-bold mb-1 tracking-tight">SPMI ONLINE</h1>
                    <p class="text-blue-100 text-sm opacity-90">SISTEM PENJAMINAN MUTU INTERNAL</p>
                    <p class="text-[10px] text-blue-200 mt-2 tracking-widest uppercase">Institut Teknologi Adhi Tama Surabaya</p>
                </div>
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
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
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
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                                placeholder="••••••••"
                            />
                        </div>
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-[#00479b] hover:bg-[#003a80] text-white font-bold py-3.5 rounded-xl transition-all shadow-lg disabled:opacity-50 disabled:shadow-none flex items-center justify-center gap-2"
                    >
                        <i v-if="form.processing" class="pi pi-spin pi-spinner"></i>
                        Masuk ke Sistem
                    </button>
                </form>

                <div class="mt-10 pt-6 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-500">
                        Butuh bantuan login? <a href="#" class="text-[#f36f21] font-bold hover:underline">Hubungi Admin LPM</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
