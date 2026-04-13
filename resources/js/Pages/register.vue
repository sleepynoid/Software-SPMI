<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { zodResolver } from "@primevue/forms/resolvers/zod";
import { z } from "zod";
import { Form } from "@primevue/forms";
import Image from "primevue/image";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Button from "primevue/button";
import Message from "primevue/message";
import Select from "primevue/select";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";

const toast = useToast();

const roles = [
    { name: "Pelaksanaan", value: "Pelaksanaan" },
    { name: "Evaluasi", value: "Evaluasi" },
    { name: "Pengendalian", value: "Pengendalian" },
    { name: "Peningkatan", value: "Peningkatan" },
];

const initialValues = {
    name: "",
    email: "",
    password: "",
    role: "",
};

const resolver = zodResolver(
    z.object({
        name: z.string().min(1, { message: "Nama harus diisi." }),
        email: z.string().email({ message: "Format email tidak valid." }),
        password: z.string().min(6, { message: "Password harus minimal 6 karakter." }),
        role: z.string().min(1, { message: "Silahkan pilih role terlebih dahulu." }),
    })
);

const form = useForm({
    name: "",
    email: "",
    password: "",
    role: "",
});

const register = (e) => {
    if (!e.valid) return;

    form.name     = e.values.name;
    form.email    = e.values.email;
    form.password = e.values.password;
    form.role     = e.values.role;

    form.post('/register', {
        preserveScroll: true,
        onError: (errors) => {
            const messages = Object.values(errors).join(', ');
            toast.add({
                severity: 'error',
                summary: 'Registrasi Gagal',
                detail: messages,
                life: 5000,
            });
        },
    });
};
</script>

<template>
    <Head title="Register" />
    <Toast />
    <div class="h-screen w-screen flex">
        <!-- Bagian Kiri: Gambar -->
        <div class="flex flex-col items-center justify-center w-1/2 bg-blue-400">
            <div class="items-center justify-center">
                <Image src="/image/logo-white-itats-full.webp" alt="Login Image" class="w-80" />
                <span class="text-2xl text-white text-center font-bold">LPMI - Lembaga Penjaminan Mutu Internal</span>
            </div>
        </div>

        <!-- Bagian Kanan: Form Register -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-10 bg-gray-100">
            <div class="w-full max-w-md">
                <h2 class="text-4xl font-bold text-gray-700 text-center">Register</h2>
                <p class="text-center text-gray-500 text-sm mb-8">
                    Isi form di bawah ini untuk mendaftar.
                </p>

                <Form
                    v-slot="$form"
                    :initialValues="initialValues"
                    :resolver="resolver"
                    @submit="register"
                    class="flex flex-col gap-5"
                >
                    <div class="flex flex-col gap-1">
                        <label class="text-gray-600 font-semibold">Nama</label>
                        <InputText
                            name="name"
                            type="text"
                            placeholder="Masukkan nama"
                            class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <Message v-if="$form.name?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.name.error.message }}
                        </Message>
                        <Message v-if="form.errors.name" severity="error" size="small" variant="simple">
                            {{ form.errors.name }}
                        </Message>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-gray-600 font-semibold">Email</label>
                        <InputText
                            name="email"
                            type="text"
                            placeholder="Masukkan email"
                            class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <Message v-if="$form.email?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.email.error.message }}
                        </Message>
                        <Message v-if="form.errors.email" severity="error" size="small" variant="simple">
                            {{ form.errors.email }}
                        </Message>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-gray-600 font-semibold">Password</label>
                        <Password
                            name="password"
                            placeholder="Masukkan password"
                            :feedback="false"
                            toggleMask
                            fluid
                        />
                        <Message v-if="$form.password?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.password.error.message }}
                        </Message>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-gray-600 font-semibold">Role</label>
                        <Select
                            name="role"
                            :options="roles"
                            optionLabel="name"
                            optionValue="value"
                            placeholder="Pilih role"
                            fluid
                        />
                        <Message v-if="$form.role?.invalid" severity="error" size="small" variant="simple">
                            {{ $form.role.error.message }}
                        </Message>
                    </div>

                    <Button
                        type="submit"
                        label="Register"
                        :loading="form.processing"
                        style="width: auto"
                        severity="info"
                        raised
                    />
                    <div class="-mt-3">
                        <p class="text-gray-500 text-sm">
                            Sudah punya akun?
                            <Link href="/login"><span class="font-semibold text-sky-500">Silahkan login</span></Link>
                        </p>
                    </div>
                </Form>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
