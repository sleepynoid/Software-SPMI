<script setup>
import {ref} from "vue";
import Modal from "@/components/sheets/modal.vue";

const props = defineProps({
    data: Object,
});

const popupTriggers = ref(false);
const selectedIndicator = ref(null);
const tipeLink = ref(null);
const komentar = ref(null);
const openPopup = (indicator, tipe, komen) => {
    selectedIndicator.value = indicator;
    tipeLink.value = tipe;
    komentar.value = komen;
    togglePopup();
};
const togglePopup = () => {
    popupTriggers.value = !popupTriggers.value;
};

</script>

<template>
    <div class="bodi">
        <table class="tb">
        <thead>
        <tr>
            <th colspan="3"><h3 class="font-poppin">Penetapan</h3></th>
            <th rowspan="2"><h3 class="font-poppin">Pelaksanaan</h3></th>
            <th rowspan="2"><h3 class="font-poppin">Evaluasi</h3></th>
            <th colspan="4"><h3 class="font-poppin">Pengendalian</h3></th>
        </tr>
        <tr>
            <th rowspan=""><h3 class="font-poppin">Standar</h3></th>
            <th><h3 class="font-poppin">Indicator</h3></th>
            <th><h3 class="font-poppin">Target</h3></th>
            <th><h3 class="font-poppin w10">Temuan</h3></th>
            <th><h3 class="font-poppin w10">Akar Masalah</h3></th>
            <th><h3 class="font-poppin w10">RTL</h3></th>
            <th><h3 class="font-poppin w10">Pelaksanaan RTL</h3></th>
        </tr>
        </thead>
        <tbody>
        <template v-for="(standar, index) in props.data" :key="index">
            <tr>
                <td :rowspan="standar.indicators.length + 1">{{ standar.standar }}</td>
            </tr>
            <tr v-for="indicator in standar.indicators" :key="indicator.id">
                <td>{{ indicator.indicator }}</td>
                <td>{{ indicator.target }}</td>
<!--             Pelaksanaan            -->
                <td>
                    <button
                    v-if="indicator.idBukti !== ''"
                    @click="openPopup(indicator.idBukti, 'Pelaksanaan', indicator.bukti)"
                    :title="indicator.bukti">
                        Link
                    </button>
                </td>
<!--             Evaluasi            -->
                <td>
                    <button
                    v-if="indicator.evaluasi !== ''"
                    @click="openPopup(indicator.idBukti, 'Evaluasi', indicator.evaluasi)"
                    :title="indicator.evaluasi">
                        Link
                    </button>
                </td>
                    <td>
                        <textarea></textarea>
                    </td>
                    <td>
                        <textarea></textarea>
                    </td>
                    <td>
                        <textarea></textarea>
                    </td>
                    <td>
                        <textarea></textarea>
                    </td>
            </tr>
        </template>
        </tbody>
    </table>
    </div>
    <Modal
        v-if="popupTriggers"
        :togglePopup="togglePopup"
        :idBukti="selectedIndicator"
        :tipe="tipeLink"
        :role=" 'Pengendalian' "
        :komentar="komentar"
    />
</template>

<style scoped>
.w10{
  width: 10rem;
}

.bodi{
    overflow-x: auto;
    padding-right: 2%;
    margin-top: 1rem;
}

.tb{
    //width: 100%;
}
</style>
