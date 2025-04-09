<script setup lang="ts">
import { ref } from 'vue';
import {useRoute} from "vue-router";
import Pelaksanaan from "../components/sheets/pelaksanaan.vue";
import Pengendalian from "../components/sheets/pengendalian.vue";
import Evaluasi from "../components/sheets/evaluasi.vue";
import Peningkatan from "../components/sheets/peningkatan.vue";
import {getUserName, getUserRole} from "../stores/commonStore.js";


const role = getUserRole();
const name = getUserName();
const route = useRoute();

const periode = ref<string>(route.params.periode.toString());
const jurusan = ref<string>(route.params.jurusan.toString());
const tipe = ref<string>(route.params.tipe.toString());

</script>

<template>
    <div class="max-h-full h-full w-full">
        <Toast />
        <Panel class="w-full overflow-x-hidden">

            <div class="pb-[3%]">
                <Pelaksanaan
                    v-if="role=== 'Pelaksanaan'"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="tipe"
                    :role="role"
                    :username="name"
                />

                <Evaluasi
                    v-else-if="role=== 'Evaluasi'"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="tipe"
                    :role="role"
                    :username="name"
                />

                <Pengendalian
                    v-else-if="role=== 'Pengendalian'"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="tipe"
                    :role="role"
                    :username="name"
                />
                <Peningkatan
                    v-else-if="role=== 'Peningkatan'"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="tipe"
                    :role="role"
                    :username="name"
                    @isUpdate=""
                />

            </div>
        </Panel>
    </div>
</template>

<style scoped>





</style>
