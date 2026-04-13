<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, usePage } from "@inertiajs/vue3";

const page = usePage();
const auth = computed(() => (page.props.auth as any));
const name = computed(() => auth.value?.user?.name ?? '');

const props = defineProps<{
    jurusan: string,
    periode: string,
    tipeSheet: string,
}>();

const roleUser = ['Pelaksanaan','Evaluasi', 'Pengendalian', 'Peningkatan'];
const role = ref<string>(roleUser[0]);
</script>

<template>
    <div class="max-h-full h-full w-full">
        <Toast />
        <Select v-model="role" :options="roleUser" placeholder="Select a Role" class="w-full md:w-50 h-10 mb-3" />
        <Panel class="w-full overflow-x-hidden">
            <div class="pb-[3%]">
                <p class="text-gray-500 text-sm mb-2">Viewing as: <strong>{{ name }}</strong></p>
                <component
                    :is="role"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="tipeSheet"
                    :role="role"
                    :username="name"
                />
            </div>
        </Panel>
    </div>
</template>

<style scoped>
</style>
