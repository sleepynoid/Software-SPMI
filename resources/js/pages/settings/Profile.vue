<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, Head, usePage } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import { useToastHelper } from '@/composables/useToastHelper';

const { formSuccess, formError } = useToastHelper();
const page = usePage();
const user = (page.props.auth as any)?.user;

const form = useForm({
    nama_lengkap: user?.nama_lengkap ?? '',
    email: user?.email ?? '',
});

const submit = () => {
    form.patch(route('profile.update'), {
        ...formSuccess('Profile berhasil diperbarui'),
        ...formError(),
    });
};
</script>

<template>
    <AppLayout title="Profile">
        <Head title="Profile Settings" />

        <div class="max-w-2xl space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Profile Information</h2>
                <p class="text-sm text-slate-500 mb-6">Update your account's profile information.</p>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                        <input
                            v-model="form.nama_lengkap"
                            type="text"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        />
                        <p v-if="form.errors.nama_lengkap" class="mt-1 text-sm text-red-600">{{ form.errors.nama_lengkap }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-[#00479b] hover:bg-[#003a80] text-white font-bold px-6 py-2.5 rounded-xl transition-all disabled:opacity-50"
                    >
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
