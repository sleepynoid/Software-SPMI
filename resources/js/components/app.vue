<script setup>
</script>

<template>
        <RouterView v-slot="{ Component, route }">
            <transition
                :enter-active-class="route.meta.enterClass"
                :leave-active-class="route.meta.leaveClass"
            >
                <component :is="Component"/>
            </transition>
        </RouterView>
</template>

<style>
.h1{
    position: absolute;
    top: 2rem;
    left: 3rem;
}


</style>
