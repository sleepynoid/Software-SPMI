<script setup>
const props = defineProps({
    togglePopup: Function,
    idS: String
});
</script>

<template>
    <div class="popup">
        <div class="popup-inner">
            <slot/>
            <h2>{{idS}}</h2>
            <input type="text" name="" id="">

            <br>
            <button class="popup-close" @click="props.togglePopup">CLose</button>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.popup{
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 99;
    background-color: rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: center;

}

.popup-inner{
    position: relative;
    margin-bottom: 20rem;
    background: #FFF;
    padding: 32px;
}
</style>
