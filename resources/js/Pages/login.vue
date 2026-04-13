<script setup>
import { useForm, Head } from "@inertiajs/vue3";
import { Link } from '@inertiajs/vue3';
import Image from "primevue/image";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Button from "primevue/button";
import Message from "primevue/message";

const form = useForm({
    email: "",
    password: "",
});

const submit = () => {
    form.post('/login', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Login" />
    <div class="h-screen w-screen flex relative">
        <!-- Bagian Kiri: Gambar -->
        <div class="flex flex-col items-center justify-center w-1/2 bg-blue-400">
            <div class="items-center justify-center">
                <Image src="/image/logo-white-itats-full.webp" alt="Login Image" class="w-80" />
                <span class="text-2xl text-white text-center font-bold">LPMI - Lembaga Penjaminan Mutu Internal</span>
            </div>
        </div>

        <!-- Bagian Kanan: Form Login -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-10 bg-gray-100">
            <div class="w-full max-w-md">
                <h2 class="text-4xl font-bold text-gray-700 text-center">Login</h2>
                <p class="text-center text-gray-500 text-sm mb-8">
                    Masukkan email dan password Anda
                </p>

                <form @submit.prevent="submit" class="flex flex-col gap-5">
                    <div class="flex flex-col gap-1">
                        <label class="text-gray-600 font-semibold">Email</label>
                        <InputText
                            v-model="form.email"
                            type="email"
                            placeholder="Masukkan email"
                            class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <Message v-if="form.errors.email" severity="error" size="small" variant="simple">
                            {{ form.errors.email }}
                        </Message>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-gray-600 font-semibold">Password</label>
                        <Password
                            v-model="form.password"
                            placeholder="Masukkan password"
                            :feedback="false"
                            toggleMask
                            fluid
                        />
                        <Message v-if="form.errors.password" severity="error" size="small" variant="simple">
                            {{ form.errors.password }}
                        </Message>
                    </div>

                    <Button type="submit" :loading="form.processing" label="Masuk" severity="info" style="width: auto" />
                    <div class="-mt-3">
                        <p class="text-gray-500 text-sm">
                            Belum punya akun?
                            <Link href="/register"><span class="font-semibold text-sky-600">Daftar di sini</span></Link>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
