<script setup>
import {ref, watch} from "vue";

const props = defineProps({
  data: Array,
  wid: Number,
})
const jur = ref('a');
const emit = defineEmits(['response']);

watch(jur, (newValue) => {
  emit('response', newValue);
});
</script>

<template>
  <select v-model="jur" :style="{width: props.wid + '%'}">
    <option v-for="d in props.data">{{d}}</option>
  </select>
</template>

<style scoped>

</style>