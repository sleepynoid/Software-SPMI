<script setup>

</script>

<template>
<div class="load">
    <div class="loader"></div>
    <p>logging in...</p>
</div>
</template>

<style scoped>
.load{
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 99;
    background-color: rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;


}

.loader {
    width: 50px;
    --b: 8px;
    aspect-ratio: 1;
    border-radius: 50%;
    background: #514b82;
    -webkit-mask:
        repeating-conic-gradient(#0000 0deg,#000 1deg 70deg,#0000 71deg 90deg),
        radial-gradient(farthest-side,#0000 calc(100% - var(--b) - 1px),#000 calc(100% - var(--b)));
    -webkit-mask-composite: destination-in;
    mask-composite: intersect;
    animation: l5 1s infinite;
}
@keyframes l5 {to{transform: rotate(.5turn)}}
</style>
