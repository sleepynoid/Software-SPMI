<script setup>
import { ref } from "vue";
import { Head, useForm, Link } from '@inertiajs/vue3';
import { zodResolver } from "@primevue/forms/resolvers/zod";
import { z } from "zod";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import { Form } from "@primevue/forms";
import Button from "primevue/button";
import Message from "primevue/message";
import Select from "primevue/select";
// import ProgressSpinner from "primevue/progressspinner";

// const router = useRouter();
const toast = useToast();
const loading = ref(false);
const roles = ref([
    { name: "Pelaksanaan", value: "Pelaksanaan" },
    { name: "Evaluasi", value: "Evaluasi" },
    { name: "Peningkatan", value: "Peningkatan" },
    { name: "Pengendalian", value: "Pengendalian" },
]);
const initialValues = ref({
    name: "",
    email: "",
    password: "",
    role: "",
});

const resolver = zodResolver(
    z.object({
        name: z.string().min(1, { message: "Nama harus diisi." }),
        email: z.string().email({ message: "Format email tidak valid." }),
        password: z
            .string()
            .min(6, { message: "Password harus minimal 6 karakter." }),
        role: z
            .string()
            .min(1, { message: "Silahkan pilih role terlebih dahulu." }),
    })
);

const register = async (e) => {
    if (e.valid) {
        try {
            loading.value = true;

            const response = await axios.post("/api/register", {
                name: e.values.name,
                email: e.values.email,
                password: e.values.password,
                role: e.values.role,
            });

            loading.value = false;

            localStorage.setItem("toastMessage", "Registrasi Berhasil!");
            localStorage.setItem("toastSeverity", "success");

            await router.push("/login");
        } catch (error) {
            loading.value = false;

            let errorMessages = "";
            const errors = error.response.data.errors;

            if (typeof errors === "object" && errors !== null) {
                errorMessages = Object.values(errors).flat().join(", ");
            } else {
                errorMessages = "Terjadi kesalahan, silakan coba lagi.";
            }

            toast.add({
                severity: "error",
                summary: `Registrasi Gagal: ${errorMessages}`,
                life: 5000,
            });
        }
    }
};
</script>

<template>
    <div class="h-screen w-screen flex">
        <div
            :class="[
                { hidden: !loading },
                'absolute',
                'z-50',
                'inset-0',
                'w-full',
                'h-full',
                'bg-black/50',
                'flex',
                'items-center',
                'justify-center',
            ]"
        >
            <ProgressSpinner v-if="loading" aria-label="Loading" />
        </div>
        <Toast />

        <!-- Bagian Kiri: Gambar -->
        <div
            class="flex flex-col items-center justify-center w-1/2 bg-blue-400"
        >
            <div class="items-center justify-center">
                <Image
                    src="/image/logo-white-itats-full.webp"
                    alt="Login Image"
                    class="w-80"
                />
                <span class="text-2xl text-white text-center font-bold"
                    >LPMI - Lembaga Penjaminan Mutu Internal</span
                >
            </div>
        </div>

        <!-- Bagian Kanan: Form Register -->
        <div
            class="w-full md:w-1/2 flex items-center justify-center p-10 bg-gray-100"
        >
            <div class="w-full max-w-md">
                <h2 class="text-4xl font-bold text-gray-700 text-center">
                    Register
                </h2>
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
                        <Message
                            v-if="$form.name?.invalid"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ $form.name.error.message }}
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
                        <Message
                            v-if="$form.email?.invalid"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ $form.email.error.message }}
                        </Message>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-gray-600 font-semibold"
                            >Password</label
                        >
                        <Password
                            name="password"
                            placeholder="Masukkan password"
                            :feedback="false"
                            toggleMask
                            fluid
                        />
                        <Message
                            v-if="$form.password?.invalid"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
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
                        <Message
                            v-if="$form.role?.invalid"
                            severity="error"
                            size="small"
                            variant="simple"
                            >{{ $form.role.error.message }}</Message
                        >
                    </div>

                    <Button
                        type="submit"
                        label="Register"
                        style="width: auto"
                        severity="info"
                        raised
                    />
                    <div class="-mt-3">
                        <p class="text-gray-500 text-sm">
                            Sudah punya akun?
                            <router-link to="/login"
                                ><span class="font-semibold text-sky-500"
                                    >Silahkan login</span
                                ></router-link
                            >
                        </p>
                    </div>
                </Form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Styles remain unchanged */
</style>
