import { useToast } from 'primevue/usetoast';
import { router, usePage } from '@inertiajs/vue3';
import { watch } from 'vue';

let routerHandlersRegistered = false;

export function useToastHelper() {
    const toast = useToast();
    const page = usePage();

    watch(
        () => (page.props as any)?.flash,
        (flash) => {
            if (flash?.success) {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: flash.success, life: 3000 });
            }
            if (flash?.error) {
                toast.add({ severity: 'error', summary: 'Gagal', detail: flash.error, life: 5000 });
            }
        },
        { immediate: true },
    );

    if (!routerHandlersRegistered) {
        routerHandlersRegistered = true;

        router.on('error', (event) => {
            const statusCode = event.detail?.statusCode;
            if (statusCode === 419) {
                toast.add({ severity: 'warn', summary: 'Sesi Berakhir', detail: 'Silakan refresh halaman.', life: 5000 });
            } else if (statusCode === 403) {
                toast.add({ severity: 'error', summary: 'Akses Ditolak', detail: 'Anda tidak memiliki akses.', life: 5000 });
            } else if (statusCode === 404) {
                toast.add({ severity: 'warn', summary: 'Tidak Ditemukan', detail: 'Halaman tidak ditemukan.', life: 3000 });
            } else if (statusCode && statusCode >= 500) {
                toast.add({ severity: 'error', summary: 'Server Error', detail: 'Terjadi kesalahan server.', life: 5000 });
            }
        });

        router.on('invalid', (event) => {
            const errors = event.detail?.errors;
            if (errors && Object.keys(errors).length > 0) {
                const firstError = Object.values(errors)[0] as string;
                toast.add({ severity: 'warn', summary: 'Validasi Gagal', detail: firstError, life: 5000 });
            }
        });
    }

    function formSuccess(message = 'Berhasil disimpan') {
        return {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: message, life: 3000 });
            },
        };
    }

    function formError() {
        return {
            onError: (errors: Record<string, string>) => {
                const first = Object.values(errors)[0];
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: (first as string) || 'Terjadi kesalahan.',
                    life: 5000,
                });
            },
        };
    }

    return { toast, formSuccess, formError };
}
