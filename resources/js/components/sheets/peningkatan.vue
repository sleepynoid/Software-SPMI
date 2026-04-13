<script setup lang="ts">
import {ref, toRefs, defineAsyncComponent} from "vue";
import ConfirmPopup from 'primevue/confirmpopup';
import {useToast} from "primevue";
import { useConfirm } from "primevue/useconfirm";
import { router } from "@inertiajs/vue3";

const ModalShow = defineAsyncComponent({
    loader: () => import('./modal/ModalShow.vue'),
});
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
const oldVal = ref<string>('');
const count = ref<number>(0);

const navigateStep = (stepValue: string) => {
    router.get(`/sheet/${jurusan.value}/${periode.value}/${tipeSheet.value}/${stepValue}`, {}, {
        preserveScroll: true,
        preserveState: true,
        onStart: () => loading.value = true,
        onFinish: () => {
            loading.value = false;
            count.value = 0;
            oldVal.value = '';
        }
    });
};

const handleSubmitPeningkatan = (data: any) => {
    loading.value = true;
    router.post('/submitPeningkatan', {
        data: {
            idBuktiPengendalian: data.idBuktiPengendalian,
            komenPeningkatan: data.komenPeningkatan,
            userName: username.value
        }
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
            data.isUpdate = false;
            toast.add({ severity: 'success', summary: 'Success Saving', detail: 'Peningkatan Saved', life: 3000 });
            loading.value = false;
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error saving', detail: 'Validation or server error', life: 3000 });
            loading.value = false;
        }
    });
};

const handleReset = (event: any, data: any) => {
    confirm.require({
        target: event.currentTarget,
        group: 'headless',
        message: 'Discard your current changes?',
        accept: () => {
            data.komenPeningkatan = oldVal.value;
            data.isUpdate = false;
            isEditing.value = false;
            count.value = 0;
            oldVal.value = '';
            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Changes discarded', life: 3000 });
        },
        reject: () => {}
    });
};

const handleBlur = () => {
    if (!isEditing.value) {
        oldVal.value = ''
        count.value = 0
    }
}
const handleFocus = (old:string) => {
    count.value += 1;
    if (count.value == 1){
        oldVal.value = old;
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
            tableStyle="min-width: 110vw"
            class="custom-table"
        >
            <ColumnGroup type="header">
                <Row>
                    <Column header="Penetapan" :colspan="3" />
                    <Column header="Pelaksanaan" :rowspan="2" />
                    <Column header="Evaluasi" :rowspan="2" />
                    <Column header="Pengendalian" :rowspan="2" />
                    <Column header="Peningkatan" :colspan="2" />
                    <Column header="Save" :rowspan="2" />
                </Row>
                <Row>
                    <Column header="Standar"/>
                    <Column header="Indikator"/>
                    <Column header="Target"/>
                    <Column header="Komentar"/>
                    <Column header="Link"/>
                </Row>
            </ColumnGroup>
            <Column field="standar" header="Standar" class="min-w-[5rem] max-w-[5rem] h-[5rem]">
                <template #body="{ data }">
              <span v-if="loading">
                  <Skeleton width="100%" height="16px" />
              </span>
                    <span
                        class="w-[5rem]"
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
                      disabled
                      class="custom-textarea"
                      v-model="indicator.indicator"
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
            <Column field="indicators" class="w-[5rem]">
                <template #body="slotProps">
              <span v-if="loading">
                  <Skeleton width="100%" height="16px" />
              </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[100%] flex items-center justify-start"
                    >
                        <ModalShow
                            v-if="indicator.idBukti"
                            :idBukti = indicator.idBukti
                            :tipeLink="'Pelaksanaan'"
                            :comment="indicator.bukti"
                            :editor="indicator.editorPelaksanaan"
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
                        class="h-[10rem] w-[100%] flex items-center justify-center"
                    >
                        <ModalShow
                            v-if="indicator.idBuktiEvaluasi"
                            :idBukti = indicator.idBuktiEvaluasi
                            :tipeLink="'Evaluasi'"
                            :comment="indicator.evaluasi"
                            :editor="indicator.editorEval"
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
                        class="h-[10rem] w-[100%] flex items-center justify-center"
                    >
                        <ModalShow
                            v-if="indicator.idBuktiPengendalian"
                            :idBukti = indicator.idBuktiPengendalian
                            :tipeLink="'Pengendalian'"
                            :editor="indicator.editorPengendali"
                            :pengendalian="{
                                temuan: indicator.temuan,
                                akarMasalah: indicator.akar_masalah,
                                rtl: indicator.rtl,
                                pelaksanaanRtl: indicator.pelaksanaanRtl
                            }"
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
                      v-tooltip.top="{ value: 'Input Peningkatan', showDelay: 500, hideDelay: 300 }"
                      :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiPengendalian"
                      v-model="indicator.komenPeningkatan"
                      @input="isChanged(indicator)"
                      @focus="handleFocus(indicator.komenPeningkatan)"
                      @blur="handleBlur"
                      style="resize: none; height: 9rem; width: 20rem"
                  />
                            <label
                                for="on_label"
                                v-if="indicator.komenPeningkatan"
                            >Last Edited by: {{indicator.editorPeningkatan}}</label>
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
                            v-if="indicator.idPeningkatan"
                            :idBukti = indicator.idPeningkatan
                            :tipeLink="'Peningkatan'"
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
                                v-tooltip.top="{ value: 'Save Change', showDelay: 500, hideDelay: 300 }"
                                icon="pi pi-check"
                                severity="info"
                                :disabled="!indicator.isUpdate"
                                raised
                                @click="handleSubmitPeningkatan(indicator)"
                            />
                            <Button
                                icon="pi pi-times"
                                severity="danger"
                                :disabled="!indicator.isUpdate"
                                raised
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


</style>
