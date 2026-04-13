<script setup>
import CustomMenubar from "./comp/CustomMenubar.vue";
// No longer needed

const route = useRoute();
</script>

<template>
  <div class="w-screen h-screen bg-gray-100 flex flex-col items-center px-[5%] overflow-x-hidden app">
        <CustomMenubar v-if="route.meta.showMenubar !== false" />
        <RouterView v-slot="{ Component, route }">
            <component :is="Component" />
        </RouterView>
    </div>
</template>

<style>
    .app::-webkit-scrollbar{
        display: none;
    }

    .p-stepper {
        --p-stepper-separator-size: 2px;
        --p-stepper-step-number-border-radius: 3px;
        --p-stepper-step-number-border-color: none;
        --p-stepper-step-number-shadow: none;
        width: 80%;
        margin-top: -1rem;
    }

    .custom-table {
        --p-datatable-header-cell-background: rgba(202, 240, 248, 0.4);
        --p-datatable-header-cell-text-align: center;
        padding: 5px;
    }

    .custom-textarea {
        --p-textarea-disabled-background: rgb(255,255,255);
    }
</style>

<style lang="scss">

@import 'primeicons/primeicons.css';

</style>
