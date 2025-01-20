<template>
    <h1>Hola</h1>
    <h2>{{ test }}</h2>
    <input v-model="test">
</template>
<script setup>
    import { ref } from "vue";
    const test= ref('hola')
</script>
<style scoped>
</style>