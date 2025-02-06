<template>
    <div class="popup">
        <div class="popup-inner">
            <slot />
            <button class="popup-close">Cerrar</button>

        </div>
    </div>
</template>
<script>
export default{
    setup() {
        const popupTriggers = ref({
            buttonTrigger: false,
            timedTrigger: false
        });
        return {
            Popup,
            popupTriggers
        }
    }
}
</script>
<style lang="scsss" scoped>

</style>