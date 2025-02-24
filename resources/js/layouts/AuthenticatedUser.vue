<template>
    <div class="desactivar-scrol">
        <div :class="['layout-wrapper', containerClass, { 'disable-scroll': isAppRoute }]">
            <!-- Topbar -->
            <app-topbar></app-topbar>

            <!-- Sidebar con estructura sencilla -->
            <div class="layout-sidebar">
                <!-- Cabecera del sidebar (usuario y roles) -->
                <div class="sidebar-header container-nombre-usuario">
                    <Avatar :image="authStore().user?.avatar" shape="circle" class="mr-2" />
                    <div class="sidebar-userinfo">
                        <span class="nombre-usuario">Bienvenido, {{ user.name }}</span>
                        <hr>
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
                    <Breadcrumb :model="crumbs">
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

            </div>

            <div class="layout-mask"></div>
        </div>

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

// Generación dinámica de migas de pan
const crumbs = computed(() => {
    let pathArray = route.path.split("/");
    pathArray.shift(); // remover primer elemento vacío

    // Siempre agregamos "Kanban" como la primera miga de pan
    let breadcrumbs = [
        {
            route: '/app/kanban',
            label: 'Kanban',
            disabled: false,
        }
    ];

    // Generamos el resto de migas basadas en meta de cada ruta
    pathArray.forEach((segment, idx) => {
        if (segment === 'app') return;

        // Tomamos la última ruta agregada
        const lastRoute = breadcrumbs[breadcrumbs.length - 1].route;
        // Si la última ruta es /app/kanban y el siguiente segmento no es 'kanban',
        // iniciamos desde /app para evitar /app/kanban/flows
        let base = lastRoute === '/app/kanban' && segment !== 'kanban'
            ? '/app'
            : lastRoute;

        breadcrumbs.push({
            route: base + '/' + segment,
            label: route.matched[idx]?.meta.breadCrumb || segment,
            disabled: idx + 1 === pathArray.length || route.matched[idx]?.meta.linked === false,
        });
    });

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
    flex-direction: row;
    min-height: 100vh;
    width: 100%;
    overflow: hidden; /* Evita el scroll */
}

/* Sidebar */
.layout-sidebar {
    width: 16rem; /* Ancho fijo */
    flex-shrink: 0; /* Evita que se reduzca */
    height: calc(100vh - 4rem); /* Ajusta la altura para no tapar el navbar */
    position: absolute; /* Cambia a posición absoluta */
    top: 4rem; /* Añade un margen superior para no tapar el navbar */
    left: 0;
    background-color: #1A00FF;
    color: white;
    padding: 1rem;
    z-index: 1; /* Asegura que la barra lateral esté detrás del navbar */
    border-radius: 0 !important;
}

@media (max-width: 767px) {
    .layout-sidebar {
        width: 100%;
        height: auto;
        position: relative; /* Cambia a posición relativa en pantallas pequeñas */
    }
}

/* Contenedor principal */
.layout-main-container {
    flex-grow: 1;
    padding: 1rem;
    height: calc(100vh - 4rem); /* Ajusta la altura para no tapar el navbar */
    overflow: hidden; /* Evita el scroll */
    margin-top: 4rem;
}
@media (max-width: 767px) {
    .layout-main-container {
        margin-left: 0; /* Elimina el margen izquierdo en pantallas pequeñas */
    }
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
    padding-top: 15px;
}

/* Lista de menú */
.menu {
    list-style: none;
    padding: 0;
    margin: 0;
}
.nombre-usuario{
    font-weight: bold;
}
.menu-item {
    margin: 0.5rem 0;
}
.desactivar-scrol {
    overflow-y: hidden!important;
    max-height: 100vh;
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
    margin-left: 20px;
}

/* Ajusta color de texto en la ruta */
.text-color {
    color: #495057 !important;
}
.disable-scroll {
    overflow: hidden !important;
}
</style>