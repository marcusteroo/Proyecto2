<template>
    <div class="layout-topbar">
        <router-link to="/" class="layout-topbar-logo">
            <img src="/images/LogoKanFlow.svg" alt="logo" />
            <span></span>
        </router-link>

        <button class="p-link layout-menu-button layout-topbar-button" @click="onMenuToggle()">
            <i class="pi pi-bars"></i>
        </button>

        <button class="p-link layout-topbar-menu-button layout-topbar-button" @click="onTopBarMenuButton()">
            <i class="pi pi-ellipsis-v"></i>
        </button>

        <div class="layout-topbar-menu" :class="topbarMenuClasses">

            <button class="p-link layout-topbar-button layout-topbar-button-c nav-item dropdown " role="button"
                data-bs-toggle="dropdown">

                <a class="dropdown-item logout-button" :class="{ 'opacity-25': processing }" :disabled="processing" href="javascript:void(0)" @click="logout">Cerrar sessión</a>

                <span class="nav-link dropdown-toggle ms-3 me-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Hola, {{ authStore().user.name }}
                </span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useLayout } from '../composables/layout';
import useAuth from "@/composables/auth";
import {  useRouter } from "vue-router";
import { authStore } from "../store/auth";

const { onMenuToggle } = useLayout();
const { processing, logout } = useAuth();
const topbarMenuActive = ref(false);
const router = useRouter();

const onTopBarMenuButton = () => {
    topbarMenuActive.value = !topbarMenuActive.value;
};

const topbarMenuClasses = computed(() => {
    return {
        'layout-topbar-menu-mobile-active': topbarMenuActive.value
    };
});

</script>

<style lang="scss" scoped>
.layout-topbar-button-c,
.layout-topbar-button-c:hover {
    width: auto;
    background-color: rgb(255, 255, 255, 0);
    border: 0;
    border-radius: 0%;
    padding: 1em;
}
.layout-topbar{
    background-color: #1A00FF;
    height: 4rem;
    overflow: hidden;

}
.layout-topbar-logo {
    display: inline-flex;
    align-items: center;
    margin: 0;
    padding: 0;
    max-width: 200px;
    img {
        height: 12rem; /* Ajusta para que quepa en el topbar */
        width: auto;  /* Mantiene proporción */
        display: block;
    }
}
button{
    color: white!important;
}
.logout-button {
    background-color: #e74c3c; /* Rojo vibrante */
    color: #fff;
    padding: 0.5em 1em;
    border-radius: 4px;
    text-decoration: none;
    font-weight: bold;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: inline-block;
}

.logout-button:hover {
    background-color: #c0392b;
}
</style>
