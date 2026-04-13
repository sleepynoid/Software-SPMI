<script setup lang="ts">
import {defineAsyncComponent, toRefs, ref} from "vue";
import {useToast} from "primevue";
import {useConfirm} from "primevue/useconfirm";
import ConfirmPopup from 'primevue/confirmpopup';
import { router } from "@inertiajs/vue3";

const ModalLink = defineAsyncComponent({
    loader: () => import('./modal/ModalLink.vue'),
});
const props = defineProps<{
    jurusan: string,
    periode: string,
    tipeSheet: string,
    currentStep: string,
    role: string,
    username: string,
    sheetData: any[]
}>();
const { jurusan, periode, tipeSheet, role, username, currentStep, sheetData } = toRefs(props);

const toast = useToast();
const confirm = useConfirm();
const loading = ref<boolean>(false);
const isEditing = ref<boolean>(false);
const adjusmentOptions = ref<string[]>(['melampaui', 'mencapai', 'belum mencapai', 'menyimpang']);
const oldVal = ref<string[]>(['','','']);
const count = ref<number[]>([0,0,0]);

const navigateStep = (stepValue: string) => {
    router.get(`/sheet/${jurusan.value}/${periode.value}/${tipeSheet.value}/${stepValue}`, {}, {
        preserveScroll: true,
        preserveState: true,
        onStart: () => loading.value = true,
        onFinish: () => {
            loading.value = false;
            count.value = [0,0,0];
            oldVal.value = ['','',''];
        }
    });
};

const handleSubmitEvaluasi = (data: any) => {
    if (!data.adjusment){
        toast.add({ severity: 'warn', summary: 'Error Saving', detail: 'Please Fill the Adjusment', life: 3000 });
        return;
    } else if (!data.komentarEvaluasi){
        toast.add({ severity: 'warn', summary: 'Error Saving', detail: 'Please Fill the Evaluasi', life: 3000 });
        return;
    } else {
        loading.value = true;
        router.post('/submitEvaluasi', {
            data: {
                idBuktiPelaksanaan: data.idBuktiPelaksanaan,
                idEvaluasi: data.idEvaluasi,
                komentarEvaluasi: data.komentarEvaluasi,
                adjusment: data.adjusment,
                userName: username.value,
                idIndikator: data.idIndikator,
                indicator: data.indicator,
            }
        }, {
            preserveScroll: true,
            onSuccess: () => {
                isEditing.value = false;
                data.isUpdate = false;
                toast.add({ severity: 'success', summary: 'Success Saving', detail: 'Evaluasi Saved', life: 3000 });
                loading.value = false;
            },
            onError: () => {
                toast.add({ severity: 'error', summary: 'Error saving', detail: 'Validation or server error', life: 3000 });
                loading.value = false;
            }
        });
    }
};

const handleReset = (event: any, data: any) => {
    confirm.require({
        target: event.currentTarget,
        group: 'headless',
        message: 'Discard your current changes?',
        accept: () => {
            oldVal.value[0] && (data.indicator = oldVal.value[0]);
            oldVal.value[1] && (data.komentarEvaluasi = oldVal.value[1]);
            oldVal.value[2] && (data.adjusment = oldVal.value[2]);
            data.isUpdate = false;
            isEditing.value = false;
            oldVal.value = ['','',''];
            count.value = [0,0,0];
            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Changes discarded', life: 3000 });
        },
        reject: () => {}
    });
};

const handleBlur = () => {
    if (!isEditing.value) {
        oldVal.value = ['','',''];
        count.value = [0,0,0];
    }
}
const handleFocus = (old: string, index: number) => {
    count.value[index] += 1;
    if (count.value[index] == 1){
        oldVal.value[index] = old;
    }
}

const isChanged = (data: any) => {
    data.isUpdate = true;
    isEditing.value = true;
}

</script>

<template>
    <div class="w-full h-full">
        <Toast />
        <ConfirmPopup group="headless">
            <template #container="{ message, acceptCallback, rejectCallback }">
                <div class="rounded p-4">
                    <span>{{ message?.message! }}</span>
                    <div class="flex items-center gap-2 mt-4">
                        <Button label="Yes" severity="danger" @click="acceptCallback" size="small"></Button>
                        <Button label="Cancel" outlined @click="rejectCallback" severity="secondary" size="small" text></Button>
                    </div>
                </div>
            </template>
        </ConfirmPopup>
        <div class="w-full card flex justify-center">
            <Stepper :value="currentStep" class="p-stepper">
                <StepList>
                    <Step
                        value="input"
                        :disabled="isEditing"
                        @click="navigateStep('input')"
                    />
                    <Step
                        value="proses"
                        :disabled="isEditing"
                        @click="navigateStep('proses')"
                    />
                    <Step
                        value="output"
                        :disabled="isEditing"
                        @click="navigateStep('output')"
                    />
                </StepList>
            </Stepper>
        </div>

        <DataTable
            :value="sheetData"
            showGridlines
            tableStyle="min-width: 130vw"
            class="custom-table"
        >
            <ColumnGroup type="header">
                <Row>
                    <Column header="Penetapan" :colspan="3" />
                    <Column header="Pelaksanaan" :colspan="2" />
                    <Column header="Evaluasi" :colspan="3" />
                    <Column header="Save" :rowspan="2" />
                </Row>
                <Row>
                    <Column header="Standar"/>
                    <Column header="Indikator"/>
                    <Column header="Target"/>
                    <Column header="Komentar"/>
                    <Column header="Link"/>
                    <Column header="Komentar Evaluasi"/>
                    <Column header="Adjusment"/>
                    <Column header="Link"/>
                </Row>
            </ColumnGroup>
            <Column field="standar" header="Standar" class="min-w-[10rem] max-w-[10rem] h-[5rem]">
                <template #body="{ data }">
                    <span v-if="loading">
                        <Skeleton width="100%" height="16px" />
                    </span>
                    <span
                        class="w-[10rem]"
                        v-else
                    >
                        {{ data?.standar! }}
                    </span>
                </template>
            </Column>
            <Column field="indicators" class="w-[20rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="100%" height="16px" />
                    </span>
                    <span
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[20rem] flex items-center justify-center"
                    >
                        <Textarea
                            :disabled="isEditing && !indicator.isUpdate"
                            v-model="indicator.indicator"
                            @focus="handleFocus(indicator.indicator, 0)"
                            @input="isChanged(indicator)"
                            @blur="handleBlur"
                            style="resize: none; height: 9rem; width: 100%;"
                        />
                    </span>
                </template>
            </Column>
            <Column field="indicators" class="w-[3rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="100%" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[3rem] flex items-center justify-center"
                    >
                        {{indicator.target}}
                    </div>
                </template>
            </Column>
            <Column field="indicators" class="w-[20rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="100%" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[20rem] flex items-center justify-start"
                    >
                        <FloatLabel variant="on">
                            <Textarea
                                disabled
                                class="custom-textarea"
                                v-model="indicator.bukti"
                                style="resize: none; height: 9rem; width: 20rem"
                            />
                            <label
                                for="on_label"
                                v-if="indicator.komentarEvaluasi"
                            >Last Edited by: {{indicator.editorPelaksanaan}}</label>
                        </FloatLabel>
                    </div>
                </template>
            </Column>
            <Column field="indicators" class="w-[3rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="100%" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[3rem] flex items-center justify-center"
                    >
                        <ModalLink
                            v-if="indicator.idBuktiPelaksanaan"
                            :idBukti = indicator.idBuktiPelaksanaan
                            :tipeLink="'Pelaksanaan'"
                            :role="role"
                        />
                    </div>
                </template>
            </Column>
            <Column field="indicators" class="w-[20rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="100%" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[20rem] flex items-center justify-center"
                    >

                        <FloatLabel variant="on" >
                            <Textarea
                                v-tooltip.top="{ value: 'Input Evaluasi', showDelay: 500, hideDelay: 300 }"
                                :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiPelaksanaan"
                                v-model="indicator.komentarEvaluasi"
                                @input="isChanged(indicator)"
                                @focus="handleFocus(indicator.komentarEvaluasi, 1)"
                                @blur="handleBlur"

                                style="resize: none; height: 9rem; width: 20rem"
                            />
                            <label
                                for="on_label"
                                v-if="indicator.komentarEvaluasi"
                            >Last Edited by: {{indicator.editorEval}}</label>
                        </FloatLabel>

                    </div>
                </template>
            </Column>
            <Column field="indicators" class="w-[10rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="100%" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[10rem] flex items-center justify-center"
                    >
                        <Select
                            style="width: 10rem"
                            :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiPelaksanaan"
                            :options="adjusmentOptions"
                            v-model="indicator.adjusment"
                            @change="isChanged(indicator)"
                            @focus="handleFocus(indicator.adjusment, 2)"
                            @blur="handleBlur"
                            checkmark :highlightOnSelect="false"
                        />
                    </div>
                </template>
            </Column>
            <Column field="indicators" class="w-[3rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="100%" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[3rem] flex items-center justify-center"
                    >
                        <ModalLink
                            v-if="indicator.idBuktiEval"
                            :idBukti = indicator.idBuktiEval
                            :tipeLink="'Evaluasi'"
                            :role="role"
                        />
                    </div>
                </template>
            </Column>

            <Column field="indicators" class="w-[5rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="100%" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[5rem] flex items-center justify-center"
                    >
                        <ButtonGroup >
                            <Button
                                icon="pi pi-check"
                                severity="info"
                                :disabled="!indicator.isUpdate"
                                raised
                                @click="handleSubmitEvaluasi(indicator)"
                            />
                            <Button
                                icon="pi pi-times"
                                severity="danger"
                                raised
                                :disabled="!indicator.isUpdate"
                                @click="handleReset($event ,indicator)"
                            />
                        </ButtonGroup>
                    </div>
                </template>
            </Column>
        </DataTable>

  </div>
</template>

<style scoped>

textarea {
    width: 100%;
    box-sizing: border-box;
}

</style>
