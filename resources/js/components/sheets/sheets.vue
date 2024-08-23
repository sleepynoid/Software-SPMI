<script setup>
import { defineAsyncComponent, ref } from "vue";
import CustomButton from "@/components/comp/custom-button.vue";
import {debounce} from "lodash";

const Modal = defineAsyncComponent({
    loader: () => import('../sheets/modal.vue'),
});

const props = defineProps({
    data: Object,
    role: String,
});

const emit = defineEmits(['submit-data', 'update']);

const formData = ref([]);

const save = debounce((idIndikator, bukti, idP) => {
    const newData = { idIndikator, bukti, idPelaksanaan: idP };
    const index = formData.value.findIndex(item => item.id === idIndikator);
    if (index !== -1) {
        formData.value.splice(index, 1, newData);
    } else {
        formData.value.push(newData);
    }
    emit('update', true);

}, 500);


function submit(){
    emit('submit-data', formData.value)
}

const popupTriggers = ref(false);
const selectedIndicator = ref(null);
const tipeLink = ref(null);

const togglePopup = () => {
    popupTriggers.value = !popupTriggers.value;
};

const openPopup = (indicator, tipe) => {
    selectedIndicator.value = indicator;
    tipeLink.value = tipe;
    togglePopup();
};
</script>

<template>
    <br />
    <custom-button v-once @click="submit">Save</custom-button>
    <div class="table">
        <table :class="props.role">
            <thead>
            <tr>
                <th colspan="3"><h3 class="font-poppin">Penetapan</h3></th>
                <th colspan="2"><h3 class="font-poppin">Pelaksanaan</h3></th>
            </tr>
            <tr>
                <th><h3 class="font-poppin" style="width: 10rem">Standar</h3></th>
                <th><h3 class="font-poppin" style="width: 10rem">Indikator</h3></th>
                <th><h3 class="font-poppin">Target</h3></th>
                <th><h3 class="xd">Komentar</h3></th>
                <th><h3 class="font-poppin">Link Bukti</h3></th>
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

                    <td>
                        <textarea v-model="indicator.bukti" @input="save(indicator.id, indicator.bukti, indicator.idPelaksanaan)"></textarea>
                    </td>
                    <td>
                        <button
                                v-if="indicator.idBukti !== ''"
                                class="pop"
                                @click="openPopup(indicator.idBukti, 'Pelaksanaan')">
                            Link
                        </button>
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
            :role="role"
    />
</template>

<style scoped>
.table {
    overflow-x: auto;
    padding-right: 2%;
}



.xd{
    width: 30rem;
}

.Pelaksanaan {
    width: 92vw;
    margin-top: 1rem;
}

textarea {
    width: 100%;
    box-sizing: border-box;
}

.pop {
    width: 80%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
