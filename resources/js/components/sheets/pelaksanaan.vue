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

const save = debounce((idIndikator, bukti, idP, idBuk) => {
    const newData = { idIndikator: idIndikator, bukti: bukti, idPelaksanaan: idP };
    const index = formData.value.findIndex(item => item.idIndikator === idIndikator);
    if (index !== -1) {
        if (bukti !== ''){
            formData.value.splice(index, 1, newData);
            return;
        }
        if (bukti === '' && idBuk !== ''){
            formData.value.splice(index, 1, newData);
            return;
        }
        formData.value.splice(index, 1);
    } else {
        formData.value.push(newData);
    }

    if (formData.value.length > 0){
        emit('update', true);
    } else {
        emit('update', false);
    }

    console.log(formData.value)

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
        <table class="Pelaksanaan">
            <thead>
            <tr>
                <th colspan="3"><h4 class="font-poppin">Penetapan</h4></th>
                <th colspan="2"><h4 class="font-poppin">Pelaksanaan</h4></th>
            </tr>
            <tr>
                <th><div class="font-poppin th">Standar</div></th>
                <th><div class="font-poppin th">Indikator</div></th>
                <th><div class="font-poppin">Target</div></th>
                <th><div class="xd">Komentar</div></th>
                <th><div class="font-poppin link">Link Bukti</div></th>
            </tr>
            </thead>
            <tbody>
            <template v-for="(standar, index) in props.data" :key="index">
                <tr>
                    <td :rowspan="standar.indicators.length + 1">{{ standar.standar }}</td>
                </tr>
                <tr v-for="data in standar.indicators" :key="data.id">
                    <td>{{ data.indicator }}</td>
                    <td>{{ data.target }}</td>

                    <td>
                        <textarea v-model="data.bukti" @input="save(data.id, data.bukti, data.idPelaksanaan, data.idBukti)"></textarea>
                    </td>
                    <td>
                        <button
                                v-if="data.idBukti !== ''"
                                class="pop"
                                @click="openPopup(data.idBukti, 'Pelaksanaan')">
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
    height: 5rem;
    box-sizing: border-box;
}

.pop {
    width: 80%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
