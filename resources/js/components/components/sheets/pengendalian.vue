<script setup lang="ts">
import {defineAsyncComponent,ref, toRefs, watch} from "vue";
import {useToast} from "primevue";
import {fetchPengendalian, submitPengendalian, usePengendalian} from "../../stores/usePengendalian";
import {useConfirm} from "primevue/useconfirm";
import ConfirmPopup from "primevue/confirmpopup";

const ModalLink = defineAsyncComponent({
    loader: () => import('./modal/modalLink.vue'),
});
const ModalShow = defineAsyncComponent({
    loader: () => import('./modal/modalShow.vue'),
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
const oldVal = ref<string[]>(['', '', '', '']);
const count = ref<number[]>([0,0,0,0]);

const sheetTypes = ['input', 'proses', 'output'];
const current = ref<string>(sheetTypes[0]);

watch([current, tipeSheet], async ()=> {
    loading.value = true;
    await fetchPengendalian(props.jurusan, props.periode, props.tipeSheet, current.value);
    loading.value = false;
    count.value = [0,0,0,0];
    oldVal.value = ['', '', '', ''];
}, {immediate: true})

const handleSubmitPengendalian = async (data) => {
    if (!data.rtl){
        toast.add({ severity: 'warn', summary: 'Error Saving', detail: 'Please Fill the Adjusment', life: 3000 });
        return;
    } else if (!data.temuan){
        toast.add({ severity: 'warn', summary: 'Error Saving', detail: 'Please Fill the Evaluasi', life: 3000 });
        return;
    } else {
        data.isUpdate = false;
        usePengendalian.initial(data)
        usePengendalian.setUserName(username.value);
        const response = await submitPengendalian()

        if (response === 200){
            await fetchPengendalian(props.jurusan, props.periode, props.tipeSheet, current.value);
            isEditing.value = false;
            toast.add({ severity: 'success', summary: 'Success Saving', detail: 'Evaluasi Saved', life: 3000 });
        }
    }
};

const handleReset = (event, data: any) => {
    confirm.require({
        target: event.currentTarget,
        group: 'headless',
        message: 'Discard your current changes?',
        accept: () => {
            oldVal.value[0] && (data.temuan = oldVal.value[0]);
            oldVal.value[1] && (data.akarMasalah = oldVal.value[1]);
            oldVal.value[2] && (data.rtl = oldVal.value[2]);
            oldVal.value[3] && (data.pelaksanaanRtl = oldVal.value[3]);
            data.isUpdate = false;
            isEditing.value = false;
            count.value = [0,0,0,0];
            oldVal.value = ['', '', '', ''];
            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Changes discarded', life: 3000 });
        },
        reject: () => {
            // toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
        }
    });
};

const handleBlur = () => {
    if (!isEditing.value) {
        count.value = [0,0,0,0];
        oldVal.value = ['', '', '', ''];
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
};

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
          :value="usePengendalian.list"
          showGridlines
          tableStyle="min-width: 130vw"
          class="custom-table"
        >
          <ColumnGroup type="header">
            <Row>

              <Column header="Penetapan" :colspan="3" />
              <Column header="Pelaksanaan" :rowspan="2" />
              <Column header="Evaluasi" :rowspan="2" />
              <Column header="Pengendalian" :colspan="5" />
              <Column header="Save" :rowspan="2" />
            </Row>
            <Row>
              <Column header="Standar"/>
              <Column header="Indikator"/>
              <Column header="Target"/>
              <Column header="Temuan"/>
              <Column header="Akar Masalah"/>
              <Column header="RTL"/>
              <Column header="Pelaksanaan RTL"/>
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
                      v-tooltip.top="{ value: 'Input Temuan', showDelay: 500, hideDelay: 300 }"
                      :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiEvaluasi"
                      v-model="indicator.temuan"
                      @input="isChanged(indicator)"
                      @focus="handleFocus(indicator.temuan, 0)"
                      @blur="handleBlur"
                      style="resize: none; height: 9rem; width: 20rem"
                  />
                  <label
                      for="on_label"
                      v-if="indicator.temuan"
                  >Last Edited by: {{indicator.editorPengendali}}</label>
                </FloatLabel>
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
                      v-tooltip.top="{ value: 'Input Akar Masalah', showDelay: 500, hideDelay: 300 }"
                      :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiEvaluasi"
                      v-model="indicator.akarMasalah"
                      @input="isChanged(indicator)"
                      @focus="handleFocus(indicator.akarMasalah, 1)"
                      @blur="handleBlur"
                      style="resize: none; height: 9rem; width: 20rem"
                  />
                  <label
                      for="on_label"
                      v-if="indicator.akarMasalah"
                  >Last Edited by: {{indicator.editorPengendali}}</label>
                </FloatLabel>
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
                      v-tooltip.top="{ value: 'Input RTL', showDelay: 500, hideDelay: 300 }"
                      :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiEvaluasi"
                      v-model="indicator.rtl"
                      @input="isChanged(indicator)"
                      @focus="handleFocus(indicator.rtl, 2)"
                      @blur="handleBlur"
                      style="resize: none; height: 9rem; width: 20rem"
                  />
                  <label
                      for="on_label"
                      v-if="indicator.rtl"
                  >Last Edited by: {{indicator.editorPengendali}}</label>
                </FloatLabel>
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
                      v-tooltip.top="{ value: 'Input Pelaksanaan RTL', showDelay: 500, hideDelay: 300 }"
                      :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiEvaluasi"
                      v-model="indicator.pelaksanaanRtl"
                      @input="isChanged(indicator)"
                      @focus="handleFocus(indicator.pelaksanaanRtl, 3)"
                      @blur="handleBlur"
                      style="resize: none; height: 9rem; width: 20rem"
                  />
                  <label
                      for="on_label"
                      v-if="indicator.pelaksanaanRtl"
                  >Last Edited by: {{indicator.editorPengendali}}</label>
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
                      v-if="indicator.idBPengendalian"
                      :idBukti = indicator.idBPengendalian
                      :tipeLink="'Pengendalian'"
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
                          @click="handleSubmitPengendalian(indicator)"
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

</style>
