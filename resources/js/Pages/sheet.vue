<script setup lang="ts">
import { defineProps } from 'vue';
import { Head } from "@inertiajs/vue3";
import Pelaksanaan from "../components/sheets/pelaksanaan.vue";
import Pengendalian from "../components/sheets/pengendalian.vue";
import Evaluasi from "../components/sheets/evaluasi.vue";
import Peningkatan from "../components/sheets/peningkatan.vue";
import Toast from "primevue/toast";
import Panel from "primevue/panel";

const props = defineProps<{
    jurusan: string,
    periode: string,
    tipeSheet: string,
    currentStep: string,
    role: string,
    userName: string,
    sheetData: any[]
}>();

</script>

<template>
    <Head :title="`Sheet ${role}`" />
    <div class="max-h-full h-full w-full">
        <Toast />
        <Panel class="w-full overflow-x-hidden">

            <div class="pb-[3%]">
                <Pelaksanaan
                    v-if="role === 'Pelaksanaan'"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="tipeSheet"
                    :currentStep="currentStep"
                    :role="role"
                    :username="userName"
                    :sheetData="sheetData"
                />

                <Evaluasi
                    v-else-if="role === 'Evaluasi'"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="tipeSheet"
                    :currentStep="currentStep"
                    :role="role"
                    :username="userName"
                    :sheetData="sheetData"
                />

                <Pengendalian
                    v-else-if="role === 'Pengendalian'"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="tipeSheet"
                    :currentStep="currentStep"
                    :role="role"
                    :username="userName"
                    :sheetData="sheetData"
                />
                <Peningkatan
                    v-else-if="role === 'Peningkatan'"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="tipeSheet"
                    :currentStep="currentStep"
                    :role="role"
                    :username="userName"
                    :sheetData="sheetData"
                    @isUpdate=""
                />

            </div>
        </Panel>
    </div>
</template>

<style scoped>





</style>
