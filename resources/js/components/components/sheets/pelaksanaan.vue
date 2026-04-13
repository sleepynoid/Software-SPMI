<script setup lang="ts">
import {defineAsyncComponent, ref, toRefs, watch} from "vue";
import {usePelaksanaan, submitPelaksanaan, fetchPelaksanaan} from '../../stores/usePelaksanaan.js'
import {useToast} from "primevue";
import ConfirmPopup from "primevue/confirmpopup";
import {useConfirm} from "primevue/useconfirm";
const ModalLink = defineAsyncComponent({
    loader: () => import('./modal/ModalLink.vue'),
});

const props = defineProps<{
    jurusan: string,
    periode: string,
    tipeSheet: string,
    role: string,
    username: string,
}>();

const { jurusan, periode, tipeSheet, role, username } = toRefs(props);
const toast = useToast();
const confirm = useConfirm();
const loading = ref<boolean>(false);
const isEditing = ref<boolean>(false);
const oldVal = ref<string>('');
const count = ref<number>(0);

const sheetTypes = ['input', 'proses', 'output'];
const current = ref<string>(sheetTypes[0]);

watch([current, tipeSheet], async ()=> {
    loading.value = true;
    await fetchPelaksanaan(props.jurusan, props.periode, props.tipeSheet, current.value);
    loading.value = false;
    count.value = 0;
    oldVal.value = '';
}, {immediate: true})

const handleSumbitPelaksanaan = async (data) => {
    data.isUpdate = false;
    usePelaksanaan.initial(data)
    usePelaksanaan.setUserName(username.value)
    const response = await submitPelaksanaan()

    if (response === 200){
        await fetchPelaksanaan(props.jurusan, props.periode, props.tipeSheet, current.value);
        isEditing.value = false;
        toast.add({ severity: 'success', summary: 'Success Saving', detail: 'Evaluasi Saved', life: 3000 });
    }

};

const handleReset = (event, data: any) => {
    confirm.require({
        target: event.currentTarget,
        group: 'headless',
        message: 'Discard your current changes?',
        accept: () => {
            data.komentarPelaksanaan = oldVal.value;
            data.isUpdate = false;
            isEditing.value = false;
            count.value = 0;
            oldVal.value = '';
            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Changes discarded', life: 3000 });
        },
        reject: () => {
            // toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
        }
    });
};

const handleBlur = () => {
    if (!isEditing.value) {
        oldVal.value = ''
        count.value = 0
    }
}
const handleFocus = (old: string) => {
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
            <Stepper value="Input" class="p-stepper">
                <StepList>
                    <Step
                        value="Input"
                        :disabled="isEditing"
                        @click="current = 'input'"
                    />
                    <Step
                        value="Proses"
                        :disabled="isEditing"
                        @click="current = 'proses'"
                    />
                    <Step
                        value="Output"
                        :disabled="isEditing"
                        @click="current = 'output'"
                    />
                </StepList>
            </Stepper>
        </div>

        <DataTable
            :value="usePelaksanaan.list"
            showGridlines
            tableStyle="min-width: 100%"
            class="custom-table"
        >
            <ColumnGroup type="header">
                <Row>
                    <Column header="Penetapan" :colspan="3" />
                    <Column header="Pelaksanaan" :colspan="2" />
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
                        class="h-[10rem] w-[100%] flex items-center justify-center"
                    >
                        <Textarea
                            disabled
                            class="custom-textarea"
                            v-model="indicator.indicator"
                            style="resize: none; height: 9rem"
                        />
                    </span>
                </template>
            </Column>
            <Column field="indicators" class="w-[2rem]">
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
                        {{indicator.target}}
                    </div>
                </template>
            </Column>
            <Column field="indicators" class="w-[25rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="100%" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[25rem] flex items-center justify-center"
                    >
                        <FloatLabel variant="on" >
                            <Textarea
                                v-tooltip.top="{ value: 'Input Pelaksanaan', showDelay: 500, hideDelay: 300 }"
                                :disabled="isEditing && !indicator.isUpdate"
                                v-model="indicator.komentarPelaksanaan"
                                @input="isChanged(indicator)"
                                @focus="handleFocus(indicator.komentarPelaksanaan)"
                                @blur="handleBlur"
                                style="resize: none; height: 9rem; width: 25rem"
                            />
                            <label
                                for="on_label"
                                v-if="indicator.komentarPelaksanaan"
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
                        class="h-[10rem] w-[100%] flex items-center justify-center"
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

            <Column field="indicators" class="w-[5rem]">
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
                        <ButtonGroup >
                            <Button
                                icon="pi pi-check"
                                severity="info"
                                :disabled="!indicator.isUpdate"
                                raised
                                @click="handleSumbitPelaksanaan(indicator)"
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
