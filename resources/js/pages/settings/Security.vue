<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { route } from '@/lib/route';
import { useToastHelper } from '@/composables/useToastHelper';

const { formSuccess, formError } = useToastHelper();

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(route('user-password.update'), {
        ...formSuccess('Password berhasil diubah'),
        ...formError(),
        onFinish: () => {
            form.reset('current_password', 'password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AppLayout title="Security">
        <Head title="Security Settings" />

        <div class="max-w-2xl space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Update Password</h2>
                <p class="text-sm text-slate-500 mb-6">Ensure your account is using a long, random password to stay secure.</p>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Current Password</label>
                        <input
                            v-model="form.current_password"
                            type="password"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        />
                        <p v-if="form.errors.current_password" class="mt-1 text-sm text-red-600">{{ form.errors.current_password }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">New Password</label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Confirm Password</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        />
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
