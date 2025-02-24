<template>
    <div class="layout-wrapper" :class="containerClass">
        <!-- Topbar -->
        <app-topbar></app-topbar>

        <!-- Sidebar con estructura sencilla -->
        <div class="layout-sidebar">
            <!-- Cabecera del sidebar (usuario y roles) -->
            <div class="sidebar-header container-nombre-usuario">
                <Avatar :image="authStore().user?.avatar" shape="circle" class="mr-2" />
                <div class="sidebar-userinfo">
                    <span class="nombre-usuario">Bienvenido, {{ user.name }}</span>
                    <span class="user-roles">
                        <span v-for="rol in user.roles" :key="rol.id" class="rol"> {{ rol.name }} </span>
                    </span>
                </div>
            </div>

            <!-- Lista de enlaces (remplazando PrimeVue Menu) -->
            <ul class="menu">
                <li v-for="menuItem in items" :key="menuItem.label" class="menu-item">
                    <router-link
                        :to="menuItem.to"
                        class="menu-link"
                        :class="{ selected: isSelected(menuItem) }"
                    >
                        <i :class="menuItem.icon" class="menu-icon"></i>
                        <span class="menu-label">{{ menuItem.label }}</span>
                    </router-link>
                </li>
            </ul>
        </div>

        <!-- Contenido principal -->
        <div class="layout-main-container">
            <div class="card mb-2 bread">
                <Breadcrumb :home="home" :model="crumbs">
                    <template #item="{ item, props }">
                        <router-link
                            v-if="item.route"
                            :to="item.route"
                            custom
                            v-slot="{ href, navigate }"
                        >
                            <a :href="href" v-bind="props.action" class="btn btn-link" @click="navigate">
                                <span :class="[item.icon, 'text-color']" />
                                <span class="text-primary font-semibold">{{ item.label }}</span>
                            </a>
                        </router-link>
                        <a v-else :href="item.url" :target="item.target" v-bind="props.action">
                            <span class="text-color">{{ item.label }}</span>
                        </a>
                    </template>
                </Breadcrumb>
            </div>

            <div class="layout-main">
                <router-view></router-view>
            </div>

            <app-footer></app-footer>
        </div>

        <div class="layout-mask"></div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import { authStore } from '../store/auth';
import AppTopbar from './AppTopbar.vue';
import AppFooter from './AppFooter.vue';
import { useLayout } from '../composables/layout';
import Breadcrumb from 'primevue/breadcrumb';

// Acceso al store de autenticación
const auth = authStore();
const user = computed(() => auth.user);

// Rutas y router
const route = useRoute();
const router = useRouter();

// Elemento "home" para el Breadcrumb
const home = ref({
    icon: 'pi pi-home',
    route: '/app',
});

// Generación dinámica de migas de pan
const crumbs = computed(() => {
    let pathArray = route.path.split("/");
    pathArray.shift();

    let breadcrumbs = pathArray.reduce((breadcrumbArray, path, idx) => {
        breadcrumbArray.push({
            route: breadcrumbArray[idx - 1]
                ? "" + breadcrumbArray[idx - 1].route + "/" + path
                : "/" + path,
            label: route.matched[idx]?.meta.breadCrumb || path,
            disabled: idx + 1 === pathArray.length || route.matched[idx]?.meta.linked === false,
        });
        return breadcrumbArray;
    }, []);

    return breadcrumbs;
});

// Items del menú
const items = ref([
    {
        label: 'Kanban',
        icon: 'pi pi-th-large',
        to: '/app/kanban',
    },
    {
        label: 'Automatizaciones',
        icon: 'pi pi-cog',
        to: '/app/flows',
    },
    {
        label: 'Search',
        icon: 'pi pi-search',
        to: '/app/search',
    }
]);

// Función para marcar el ítem seleccionado
const isSelected = (item) => {
  // Si quieres que un ítem esté seleccionado con rutas hijas:
  return route.path.startsWith(item.to)
  // Si solo quieres exactitud:
  // return route.path === item.to
}

// Manejo de layout y eventos de clic fuera del menú
const { layoutConfig, layoutState, isSidebarActive } = useLayout();
const outsideClickListener = ref(null);

watch(isSidebarActive, (newVal) => {
    if (newVal) {
        bindOutsideClickListener();
    } else {
        unbindOutsideClickListener();
    }
});

const containerClass = computed(() => {
    return {
        'layout-theme-light': layoutConfig.darkTheme.value === 'light',
        'layout-theme-dark': layoutConfig.darkTheme.value === 'dark',
        'layout-overlay': layoutConfig.menuMode.value === 'overlay',
        'layout-static': layoutConfig.menuMode.value === 'static',
        'layout-static-inactive':
            layoutState.staticMenuDesktopInactive.value && layoutConfig.menuMode.value === 'static',
        'layout-overlay-active': layoutState.overlayMenuActive.value,
        'layout-mobile-active': layoutState.staticMenuMobileActive.value,
        'p-input-filled': layoutConfig.inputStyle.value === 'filled',
        'p-ripple-disabled': !layoutConfig.ripple.value,
    };
});

const bindOutsideClickListener = () => {
    if (!outsideClickListener.value) {
        outsideClickListener.value = (event) => {
            if (isOutsideClicked(event)) {
                layoutState.overlayMenuActive.value = false;
                layoutState.staticMenuMobileActive.value = false;
                layoutState.menuHoverActive.value = false;
            }
        };
        document.addEventListener('click', outsideClickListener.value);
    }
};

const unbindOutsideClickListener = () => {
    if (outsideClickListener.value) {
        document.removeEventListener('click', outsideClickListener.value);
        outsideClickListener.value = null;
    }
};

const isOutsideClicked = (event) => {
    const sidebarEl = document.querySelector('.layout-sidebar');
    const topbarEl = document.querySelector('.layout-menu-button');

    return !(
        sidebarEl?.contains(event.target) ||
        topbarEl?.contains(event.target)
    );
};

</script>

<style lang="scss">
/* Layout general */
.layout-wrapper {
    display: flex;
    min-height: 100vh;
}

.layout-main-container {
    flex: 1;
}

/* Sidebar */
.layout-sidebar {
    width: 16rem;
    background-color: #1A00FF;
    color: #fff;
    padding: 1rem;
}
.layout-main-container {
    flex: 1;
    /* Si haces position: fixed en .layout-sidebar,
       agrégale margin-left: 16rem para que no quede superpuesto */
}
/* Cabecera del Sidebar (usuario y roles) */
.sidebar-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.sidebar-userinfo {
    display: flex;
    flex-direction: column;
}

/* Lista de menú */
.menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-item {
    margin: 0.5rem 0;
}

.menu-link {
    display: flex;
    align-items: center;
    color: #fff;
    text-decoration: none;
    padding: 0.5rem;
    transition: background-color 0.3s;
    border-radius: 4px;
    font-weight: bold;
    background-color: transparent;
}

/* Hover */
.menu-link:hover {
    background-color: #4D33FF;
}

/* Estado seleccionado */
.selected {
    background-color: #4D33FF;
}

/* Ícono en el menú */
.menu-icon {
    margin-right: 0.5rem;
}

/* Estilos de Breadcrumb */
.bread {
    padding: .1rem;
}

/* Ajusta color de texto en la ruta */
.text-color {
    color: #495057 !important;
}
</style>