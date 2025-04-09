<script setup lang="ts">
import {ref, toRefs, watch} from "vue";
import {onClickOutside, useMagicKeys} from "@vueuse/core";
import {deleteLink, fetchLink, LinkPayload, submitLink, useLink} from "../../../stores/useLink";
import {useToast} from "primevue";
import {useConfirm} from "primevue/useconfirm";
import ConfirmPopup from "primevue/confirmpopup";
import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';

const props = defineProps<{
    idBukti: string,
    tipeLink: string,
    comment: string,
    editor: string,
    pengendalian?: any,
}>();

const {escape} = useMagicKeys()
const toast = useToast();
const confirm = useConfirm();

const { idBukti, tipeLink, comment, pengendalian, editor } = toRefs(props);
const isModal = ref<boolean>(false);
const loading = ref<boolean>(true);
const modal = ref<any>(null);
const list = ref<any[]>([]);


watch(escape, (v) => {
    if (v) {
        isModal.value = false;
    }
})
watch(isModal, async ()=> {
    if (isModal){
        list.value = await fetchLink(props.idBukti, props.tipeLink)
    }
})

const openLink = (link) => {
    window.open(link, "_blank")
}

onClickOutside(modal, () => (
    isModal.value = false
));

</script>

<template>
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
    <Button
        @click="isModal = true"
        severity="contrast"
        variant="outlined"
        class="min-w-[100%]"
        raised
    >show</Button>

    <Teleport to="#modal">
        <Transition name="modal">
            <div class="modal-bg" v-show="isModal">
                <div class="Modal" ref="modal">
                    <button @click="isModal = false" class="close-btn">x</button>
                    <div class="flex flex-col gap-5">
                        <h1 class="text-2xl font-bold">Bukti {{tipeLink}}</h1>

                        <Accordion value="0" v-if="pengendalian" class="accordioncolor">
                            <AccordionPanel value="0">
                                <AccordionHeader>
                                    <span class="flex items-center gap-2 w-full">
                                        <span class="font-bold whitespace-nowrap">Temuan</span>
                                        <Badge :value="editor"/>
                                    </span>
                                </AccordionHeader>
                                <AccordionContent>
                                    <ScrollPanel class="h-fit max-h-[140px] min-h-[30px]">
                                        <p class="m-0">{{pengendalian?.temuan!}}</p>
                                    </ScrollPanel>
                                </AccordionContent>
                            </AccordionPanel>
                            <AccordionPanel value="1">
                                <AccordionHeader>Akar Masalah</AccordionHeader>
                                <AccordionContent>
                                    <ScrollPanel class="h-fit max-h-[140px] min-h-[30px]">
                                        <p class="m-0">{{pengendalian?.akarMasalah!}}</p>
                                    </ScrollPanel>
                                </AccordionContent>
                            </AccordionPanel>
                            <AccordionPanel value="2">
                                <AccordionHeader>RTL</AccordionHeader>
                                <AccordionContent>
                                    <ScrollPanel class="h-fit max-h-[140px] min-h-[30px]">
                                        <p class="m-0">{{pengendalian?.rtl!}}</p>
                                    </ScrollPanel>
                                </AccordionContent>
                            </AccordionPanel>
                            <AccordionPanel value="3">
                                <AccordionHeader>Pelaksanaan RTL</AccordionHeader>
                                <AccordionContent>
                                    <ScrollPanel class="h-fit max-h-[140px] min-h-[30px]">
                                        <p class="m-0">{{pengendalian?.pelaksanaanRtl!}}</p>
                                    </ScrollPanel>
                                </AccordionContent>
                            </AccordionPanel>
                        </Accordion>

                        <Accordion value="0" v-else>
                            <AccordionPanel value="0">
                                <AccordionHeader>
                                    <span class="flex items-center gap-2 w-full">
                                        <span class="font-bold whitespace-nowrap">Komentar</span>
                                        <Badge :value="editor"/>
                                    </span>
                                </AccordionHeader>
                                <AccordionContent>
                                    <ScrollPanel class="h-fit max-h-[140px]">
                                        <p class="m-0">{{comment}}</p>
                                    </ScrollPanel>
                                </AccordionContent>
                            </AccordionPanel>
                        </Accordion>

                        <Fieldset legend="Link Bukti">
                            <DataTable
                                :value="list"
                                scrollable showGridlines stripedRows
                                scrollHeight="300px"
                                tableStyle="min-width: 100%"
                                class="tablecolor"
                            >
                                <Column field="judul_link" header="Keterangan Link" class="w-[80%]">
                                    <template #body="{ data }">
                                        <span>{{data?.judul_link!}}</span>
                                    </template>
                                </Column>
                                <Column field="name" header="Link">
                                    <template #body="{ data }">
                                        <Button
                                            v-tooltip.top="{ value: `${data?.link!}`, showDelay: 500, hideDelay: 300 }"
                                            @click="openLink(data?.link!)"
                                        >
                                            Visit
                                        </Button>
                                    </template>
                                </Column>
                                <Column field="category" header="Action">
                                    <template #body="{ data }">
                                        <ButtonGroup >
                                            <Button
                                                v-tooltip.top="{ value: `edit`, showDelay: 500, hideDelay: 300 }"
                                                icon="pi pi-pen-to-square"
                                                severity="info"
                                                raised
                                                disabled
                                            />
                                            <Button
                                                v-tooltip.top="{ value: `delete`, showDelay: 500, hideDelay: 300 }"
                                                icon="pi pi-trash"
                                                severity="danger"
                                                raised
                                                disabled
                                            />
                                        </ButtonGroup>
                                    </template>
                                </Column>
                            </DataTable>
                        </Fieldset>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>

.tablecolor{
    --p-datatable-header-cell-background: rgb(233, 196, 106);
}

.accordioncolor{
    --p-accordion-header-color: #023047;
    --p-accordion-header-active-color: #023047;
    --p-accordion-header-hover-color: #219ebc;
}

.modal-bg {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 100;
    background-color: rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.Modal {
    position: relative;
    background: #FFF;
    width: 70vw;
    height: 80vh;
    max-height: 80vh;
    overflow-y: auto;
    padding: 32px;
    border-radius: 5px;
    box-shadow: 0 10px 5px 2px rgba(0, 0, 0, 0.1);

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: none;
        border: none;
        cursor: pointer;
    }
}

.Modal::-webkit-scrollbar{
    display: none;
}

.modal-enter-active,
.modal-leave-active {
    transition: all 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(1.1);
}
</style>
