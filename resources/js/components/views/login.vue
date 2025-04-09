<template>
    <div class="h-screen w-screen flex relative">
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
            <ProgressSpinner
                style="width: 80px; height: 80px"
                strokeWidth="6"
                fill="transparent"
                v-if="loading"
                aria-label="Loading"
            />
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

        <!-- Bagian Kanan: Form Login -->
        <div
            class="w-full md:w-1/2 flex items-center justify-center p-10 bg-gray-100"
        >
            <div class="w-full max-w-md">
                <h2 class="text-4xl font-bold text-gray-700 text-center">
                    Login
                </h2>
                <p class="text-center text-gray-500 text-sm mb-8">
                    Masukkan email dan password Anda
                </p>

                <Form
                    v-slot="$form"
                    :initialValues="initialValues"
                    :resolver="resolver"
                    @submit="login"
                    class="flex flex-col gap-5"
                >
                    <div class="flex flex-col gap-1">
                        <label class="text-gray-600 font-semibold"
                            >Username</label
                        >
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

                    <Button
                        type="submit"
                        label="Masuk"
                        severity="info"
                        style="width: auto"
                    />
                    <div class="-mt-3">
                        <p class="text-gray-500 text-sm">
                            Belum punya akun?
                            <router-link to="/register"
                                ><span class="font-semibold text-sky-600"
                                    >Daftar di sini</span
                                ></router-link
                            >
                        </p>
                    </div>
                </Form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { zodResolver } from "@primevue/forms/resolvers/zod";
import { z } from "zod";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import Button from "primevue/button";
import Message from "primevue/message";
import ProgressSpinner from "primevue/progressspinner";
import CryptoJS from "crypto-js";

const router = useRouter();
const loading = ref(false);
const toast = useToast();

const initialValues = ref({
    email: "",
    password: "",
});

onMounted(() => {
    const toastMessage = localStorage.getItem("toastMessage");
    const toastSeverity = localStorage.getItem("toastSeverity");

    if (toastMessage) {
        toast.add({
            severity: toastSeverity || "success",
            summary: toastMessage,
            life: 3000,
        });

        localStorage.removeItem("toastMessage");
        localStorage.removeItem("toastSeverity");
    }
});

const login = async (e) => {
    if (e.valid) {
        loading.value = true;
        try {
            await axios.get('/sanctum/csrf-cookie');
            const response = await axios.post("/api/login", {
                email: e.values.email,
                password: e.values.password,
            });
            loading.value = false;

            const userName = response.data.name
            const role = response.data.userRole
            const idk = response.data.idk.toString()

            const encryptedName = CryptoJS.AES.encrypt(userName, `asx${idk}`).toString();
            const encryptedRole = CryptoJS.AES.encrypt(role, `ddx${idk}`).toString();

            localStorage.setItem("date", idk);
            localStorage.setItem("name", encryptedName);
            localStorage.setItem("userRole", encryptedRole);
            localStorage.setItem(
                "toastMessage",
                "Login Berhasil. Selamat Datang"
            );
            localStorage.setItem("toastSeverity", "success");

            await router.push("/");
        } catch (error) {
            toast.add({
                severity: "error",
                summary: "Email atau Password Salah.",
                life: 3000,
            });
            loading.value = false;
        }
    }
};

const resolver = zodResolver(
    z.object({
        email: z.string().min(1, { message: "Username harus diisi." }),
        password: z.string().min(1, { message: "Password harus diisi." }),
    })
);
</script>
