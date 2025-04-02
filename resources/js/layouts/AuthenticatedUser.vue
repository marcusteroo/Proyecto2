<template>
    <div class="app-container" :class="{'app-theme-dark': isDarkTheme}">
      <div :class="['layout-wrapper', containerClass]">
        <!-- Barra superior mejorada -->
        <app-topbar></app-topbar>
  
        <!-- Sidebar profesional -->
        <div class="layout-sidebar" :class="{'layout-mobile-active': layoutState.staticMenuMobileActive.value}">
          <!-- Logo y título de la aplicación -->
          <div class="sidebar-brand">
            <span class="sidebar-title">KanFlow</span>
          </div>
          
          <!-- Separador -->
          <div class="sidebar-divider"></div>
  
          <!-- Información del usuario -->
          <div class="sidebar-user">
            <div class="user-greeting">
              <span class="greeting-text">Bienvenido,</span>
              <h3 class="user-name">{{ user.name }}</h3>
            </div>
            <div class="user-roles" v-if="user.roles && user.roles.length">
              <span v-for="rol in user.roles" :key="rol.id" class="user-role-badge">
                {{ rol.name }}
              </span>
            </div>
          </div>
          
          <!-- Separador -->
          <div class="sidebar-divider"></div>
  
          <!-- Menú de navegación -->
          <nav class="sidebar-nav">
            <ul class="nav-menu">
              <li class="nav-item" :class="{ 'active': route.path.startsWith('/app/kanbans') }">
                <router-link to="/app/kanbans" class="nav-link">
                  <div class="nav-link-icon">
                    <i class="pi pi-th-large"></i>
                  </div>
                  <span class="nav-link-text">Tableros</span>
                  <div class="nav-active-indicator"></div>
                </router-link>
              </li>
              
              <li class="nav-item" :class="{ 'active': route.path.startsWith('/app/flows') }">
                <router-link to="/app/flows" class="nav-link">
                  <div class="nav-link-icon">
                    <i class="pi pi-bolt"></i>
                  </div>
                  <span class="nav-link-text">Flow</span>
                  <div class="nav-active-indicator"></div>
                </router-link>
              </li>
              <li class="nav-item" :class="{ 'active': route.path.startsWith('/app/favoritos') }">
                <router-link to="/app/favoritos" class="nav-link">
                  <div class="nav-link-icon">
                    <i class="pi pi-star"></i>
                  </div>
                  <span class="nav-link-text">Favoritos</span>
                  <div class="nav-active-indicator"></div>
                </router-link>
              </li>
              <li class="nav-item" :class="{ 'active': route.path.startsWith('/app/rate') }">
                <router-link to="/app/rate" class="nav-link">
                  <div class="nav-link-icon">
                    <i class="pi pi-comment"></i>
                  </div>
                  <span class="nav-link-text">Comentarios</span>
                  <div class="nav-active-indicator"></div>
                </router-link>
              </li>
              <li class="nav-item" :class="{ 'active': route.path.startsWith('/app/settings') }">
                <router-link to="/app/settings" class="nav-link">
                  <div class="nav-link-icon">
                    <i class="pi pi-cog"></i>
                  </div>
                  <span class="nav-link-text">Configuración</span>
                  <div class="nav-active-indicator"></div>
                </router-link>
              </li>
              
            </ul>
          </nav>
  
          <!-- Pie de la barra lateral -->
          <div class="sidebar-footer">
            <div class="app-version">KanFlow v1.0</div>
          </div>
        </div>
  
        <!-- Contenedor principal -->
        <div class="content-wrapper">
          <div class="breadcrumb-container">
            <Breadcrumb :model="crumbs">
              <template #item="{ item, props }">
                <router-link
                  v-if="item.route"
                  :to="item.route"
                  custom
                  v-slot="{ href, navigate }"
                >
                  <a :href="href" v-bind="props.action" class="breadcrumb-link" @click="navigate">
                    <span :class="[item.icon, 'breadcrumb-icon']" v-if="item.icon"></span>
                    <span class="breadcrumb-text">{{ item.label }}</span>
                  </a>
                </router-link>
                <a v-else class="breadcrumb-link disabled">
                  <span class="breadcrumb-text">{{ item.label }}</span>
                </a>
              </template>
            </Breadcrumb>
          </div>
  
          <div class="content-body">
            <router-view></router-view>
          </div>
        </div>
  
        <!-- Overlay para móvil -->
        <div class="layout-mask" @click="closeMenu"></div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, watch, onMounted } from 'vue';
  import { useRoute } from 'vue-router';
  import { authStore } from '../store/auth';
  import AppTopbar from './AppTopbar.vue';
  import { useLayout } from '../composables/layout';
  import Breadcrumb from 'primevue/breadcrumb';
  
  // Acceso al store de autenticación
  const auth = authStore();
  const user = computed(() => auth.user);
  
  // Rutas
  const route = useRoute();
  
  // Tema oscuro
  const isDarkTheme = computed(() => {
    return localStorage.getItem('theme') === 'dark';
  });
  const applyTheme = (theme) => {
  // Guardar en localStorage
  localStorage.setItem('theme', theme);
  
  // Aplicar globalmente
  document.documentElement.classList.toggle('dark-theme', theme === 'dark');
  document.body.classList.toggle('dark-theme', theme === 'dark');
  
  // Configurar el layout
  if (layoutConfig && layoutConfig.darkTheme) {
    layoutConfig.darkTheme.value = theme === 'dark';
  }
  
  // Emitir evento personalizado para informar a otros componentes
  const event = new CustomEvent('themeChanged', { detail: theme });
  window.dispatchEvent(event);
};
  
  // Computar si estamos en una ruta de app
  const isAppRoute = computed(() => route.path.includes('/app/'));
  
  // Migas de pan dinámicas
  const crumbs = computed(() => {
  let pathArray = route.path.split("/");
  pathArray.shift(); // Eliminar primer elemento vacío

  // Crear array de migas de pan
  let breadcrumbs = [];
  
  // Siempre añadir "Inicio" como primera miga de pan
  breadcrumbs.push({
    label: "Inicio",
    icon: "pi pi-home", // Icono de PrimeVue para casa/inicio
    route: "/app",
    disabled: false
  });

  // Si solo tenemos '/app', no añadir más migas
  if (pathArray.length <= 1) {
    return breadcrumbs;
  }
  
  // Procesar el resto de segmentos de la URL, saltando el segmento "app"
  for (let idx = 1; idx < pathArray.length; idx++) {
    const path = pathArray[idx];
    let label = route.matched[idx]?.meta.breadCrumb || path;
    
    // Caso especial para kanban
    if (path === "kanban" && idx === 1) {
      breadcrumbs.push({
        label: "Tableros",
        route: "/app/kanbans",
        disabled: false
      });
      continue;
    }
    
    // Caso especial para ID de kanban
    if (idx > 0 && pathArray[idx-1] === "kanban" && !isNaN(path)) {
      breadcrumbs.push({
        label: `Tablero ${path}`,
        disabled: true
      });
      continue;
    }
    
    // Construir ruta acumulativa
    let currentPath = "/app";
    for (let i = 1; i <= idx; i++) {
      currentPath += "/" + pathArray[i];
    }
    
    // Añadir miga normal
    breadcrumbs.push({
      route: currentPath,
      label: label,
      disabled: idx + 1 === pathArray.length || route.matched[idx]?.meta.linked === false,
    });
  }
  
  return breadcrumbs;
});
  
  // Manejo del layout
  const { layoutConfig, layoutState, isSidebarActive } = useLayout();
  const outsideClickListener = ref(null);
  
  // Cerrar menú en móvil cuando se hace clic fuera
  const closeMenu = () => {
    layoutState.staticMenuMobileActive.value = false;
    layoutState.overlayMenuActive.value = false;
  };
  
  watch(isSidebarActive, (newVal) => {
    if (newVal) {
      bindOutsideClickListener();
    } else {
      unbindOutsideClickListener();
    }
  });
  
  const containerClass = computed(() => {
    return {
      'layout-theme-light': !isDarkTheme.value,
      'layout-theme-dark': isDarkTheme.value,
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
  
  // Observar cambios en el tema del localStorage
  onMounted(() => {
    // Inicialmente establecer el tema
    updateBodyClass();
    
    // Vigilar cambios en localStorage
    window.addEventListener('storage', (e) => {
      if (e.key === 'theme') {
        updateBodyClass();
      }
    });
  });
  
  // Actualizar clase del body según el tema
  const updateBodyClass = () => {
    if (localStorage.getItem('theme') === 'dark') {
      document.body.classList.add('dark-theme');
      document.body.classList.remove('light-theme');
    } else {
      document.body.classList.add('light-theme');
      document.body.classList.remove('dark-theme');
    }
  };
  </script>
  
  <style lang="scss">
/* Variables */
:root {
  --primary-color: #3f359b;
  --primary-light: #4D33FF;
  --primary-dark: #0f00b3;
  --accent-color: #FF4A56;
  --text-light: rgba(255, 255, 255, 0.85);
  --text-lighter: rgba(255, 255, 255, 0.65);
  --shadow-sm: 0 2px 6px rgba(0, 0, 0, 0.08);
  --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.12);
  --transition-fast: 0.2s ease;
  --transition-normal: 0.3s ease;
  --sidebar-width: 250px;
}

/* Resets y estilo base */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Inter', 'Segoe UI', Roboto, -apple-system, sans-serif;
  background-color: #f8f9fa;
  transition: background-color var(--transition-normal);
}

body.dark-theme {
  background-color: #121212;
  color: #e4e6eb;
}

/* Contenedor de la aplicación */
.app-container {
  position: relative;
  min-height: 100vh;
}

.app-theme-dark {
  --primary-color: #551FFF;
  --primary-light: #7747FF;
  background-color: #121212;
  color: #e4e6eb;
}

.layout-wrapper {
  display: flex;
  min-height: 100vh;
}

/* Sidebar principal con estilo profesional */
.layout-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: var(--sidebar-width);
  height: 100vh;
  background-color: var(--primary-color) !important;
  background-image: linear-gradient(
    to bottom,
    rgba(255, 255, 255, 0.05) 0%,
    rgba(0, 0, 0, 0.05) 100%
  ) !important;
  color: white;
  display: flex;
  flex-direction: column;
  z-index: 1000;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
  transition: transform var(--transition-normal);
  border-radius: 0 !important;
}

/* Título de la aplicación (sin logo) */
.sidebar-brand {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 68px;
  padding: 0 22px;
  position: relative;
}

.sidebar-brand:after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 1px;
  background: linear-gradient(
    to right,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0) 100%
  );
}

/* Ocultar el logo */
.sidebar-logo {
  display: none;
}

.sidebar-title {
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-align: center;
}

/* Separador */
.sidebar-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
  margin: 0 16px;
}

/* Información del usuario */
.sidebar-user {
  padding: 20px;
}

.user-greeting {
  margin-bottom: 8px;
}

.greeting-text {
  font-size: 13px;
  color: var(--text-lighter);
}

.user-name {
  font-size: 16px;
  font-weight: 600;
  margin: 4px 0;
}

.user-roles {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.user-role-badge {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  padding: 3px 10px;
  font-size: 11px;
  font-weight: 500;
}

/* Navegación */
.sidebar-nav {
  flex: 1;
  overflow-y: auto;
  padding: 12px 10px;
}

.nav-menu {
  list-style-type: none;
}

.nav-item {
  margin: 4px 0;
  opacity: 0;
  animation: fadeInLeft 0.4s forwards;
}

.nav-item:nth-child(1) { animation-delay: 0.1s; }
.nav-item:nth-child(2) { animation-delay: 0.2s; }
.nav-item:nth-child(3) { animation-delay: 0.3s; }
.nav-item:nth-child(4) { animation-delay: 0.4s; }
.nav-item:nth-child(5) { animation-delay: 0.5s; }

@keyframes fadeInLeft {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.nav-link {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  border-radius: 8px;
  color: var(--text-light);
  text-decoration: none;
  transition: background-color var(--transition-fast);
  position: relative;
}

.nav-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.nav-item.active .nav-link {
  background-color: rgba(255, 255, 255, 0.15);
  font-weight: 600;
  color: white;
}

.nav-link-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  margin-right: 12px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.1);
  transition: background-color var(--transition-fast), transform var(--transition-fast);
}

.nav-item.active .nav-link-icon {
  background-color: white;
  color: var(--primary-color);
  transform: scale(1.05);
}

.nav-link-text {
  flex: 1;
  font-size: 14px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.nav-active-indicator {
  position: absolute;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 3px;
  height: 70%;
  background-color: white;
  border-radius: 3px 0 0 3px;
  opacity: 0;
}

.nav-item.active .nav-active-indicator {
  opacity: 1;
}

/* Footer del sidebar */
.sidebar-footer {
  padding: 16px 20px;
  text-align: center;
  margin-top: auto;
}

.app-version {
  font-size: 11px;
  opacity: 0.6;
  letter-spacing: 0.5px;
}

/* Contenedor principal */
.content-wrapper {
  flex: 1;
  margin-left: var(--sidebar-width);
  min-height: 100vh;
  background-color: #f8f9fa;
  transition: margin var(--transition-normal), background-color var(--transition-normal);
  width: calc(100% - var(--sidebar-width));
  display: flex;
  flex-direction: column;
  padding-top: 64px; /* Altura del topbar */
}

.app-theme-dark .content-wrapper {
  background-color: #121212;
}

/* Migas de pan */
.breadcrumb-container {
  padding: 12px 24px;
  border-bottom: 1px solid #e9ecef;
  background: white;
  box-shadow: var(--shadow-sm);
  transition: background-color var(--transition-normal), border-color var(--transition-normal);
}

.app-theme-dark .breadcrumb-container {
  background-color: #1e1e2e;
  border-bottom-color: #2d2d3a;
}

.breadcrumb-link {
  display: inline-flex;
  align-items: center;
  padding: 6px 8px;
  color: #495057;
  text-decoration: none;
  border-radius: 4px;
  transition: background-color var(--transition-fast), color var(--transition-fast);
}

.breadcrumb-link:not(.disabled):hover {
  background: rgba(0, 0, 0, 0.04);
}

.app-theme-dark .breadcrumb-link {
  color: #e4e6eb;
}

.app-theme-dark .breadcrumb-link:not(.disabled):hover {
  background: rgba(255, 255, 255, 0.08);
}

.breadcrumb-icon {
  margin-right: 6px;
  color: var(--primary-color);
}

.breadcrumb-text {
  font-size: 14px;
}

.breadcrumb-link.disabled {
  color: #6c757d;
  pointer-events: none;
}

.app-theme-dark .breadcrumb-link.disabled {
  color: #adb5bd;
}

/* Tema oscuro para migas de pan */
.dark-theme .breadcrumb-container,
body.dark-theme .breadcrumb-container,
.app-theme-dark .breadcrumb-container {
  background-color: #1e1e2e !important;
  border-bottom-color: #2d2d3a !important;
}

.dark-theme .breadcrumb-link,
body.dark-theme .breadcrumb-link,
.app-theme-dark .breadcrumb-link {
  color: #e4e6eb !important;
}

.dark-theme .breadcrumb-link:not(.disabled):hover,
body.dark-theme .breadcrumb-link:not(.disabled):hover,
.app-theme-dark .breadcrumb-link:not(.disabled):hover {
  background: rgba(255, 255, 255, 0.08) !important;
}

/* Contenido principal */
.content-body {
  flex: 1;
  padding: 24px;
  overflow-y: auto;
}

/* Overlay para móvil */
.layout-mask {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(2px);
  z-index: 999;
}

/* Media queries para responsive */
@media (max-width: 991px) {
  .layout-sidebar {
    transform: translateX(-100%);
    box-shadow: none;
  }
  
  .layout-sidebar.layout-mobile-active,
  .layout-mobile-active .layout-sidebar {
    transform: translateX(0);
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
  }
  
  .content-wrapper {
    margin-left: 0;
    width: 100%;
  }
  
  .layout-mask {
    display: none;
  }
  
  .layout-mobile-active ~ .layout-mask,
  .layout-mobile-active + .layout-mask {
    display: block;
  }
}

/* Tema oscuro para migas de pan (reglas adicionales para PrimeVue) */
html.dark-theme .breadcrumb-container,
body.dark-theme .breadcrumb-container,
.dark-theme .breadcrumb-container,
.layout-theme-dark .breadcrumb-container {
  background-color: #1e1e2e !important;
  border-bottom-color: #2d2d3a !important;
}

html.dark-theme .p-breadcrumb,
body.dark-theme .p-breadcrumb,
.dark-theme .p-breadcrumb,
.layout-theme-dark .p-breadcrumb {
  background-color: transparent !important;
  border: none !important;
}

html.dark-theme .breadcrumb-link,
body.dark-theme .breadcrumb-link,
.dark-theme .breadcrumb-link,
.layout-theme-dark .breadcrumb-link {
  color: #e4e6eb !important;
}

html.dark-theme .p-breadcrumb .p-menuitem-link .p-menuitem-text,
body.dark-theme .p-breadcrumb .p-menuitem-link .p-menuitem-text,
.dark-theme .p-breadcrumb .p-menuitem-link .p-menuitem-text,
.layout-theme-dark .p-breadcrumb .p-menuitem-link .p-menuitem-text {
  color: #e4e6eb !important;
}

html.dark-theme .p-breadcrumb .p-breadcrumb-chevron,
body.dark-theme .p-breadcrumb .p-breadcrumb-chevron,
.dark-theme .p-breadcrumb .p-breadcrumb-chevron,
.layout-theme-dark .p-breadcrumb .p-breadcrumb-chevron {
  color: #adb5bd !important;
}

html.dark-theme .breadcrumb-link:not(.disabled):hover,
body.dark-theme .breadcrumb-link:not(.disabled):hover,
.dark-theme .breadcrumb-link:not(.disabled):hover,
.layout-theme-dark .breadcrumb-link:not(.disabled):hover {
  background: rgba(255, 255, 255, 0.08) !important;
}
.layout-sidebar,
.dark-theme .layout-sidebar,
html.dark-theme .layout-sidebar,
body.dark-theme .layout-sidebar,
:root .layout-sidebar,
.app-container .layout-sidebar,
.app-theme-dark .layout-sidebar,
.layout-wrapper .layout-sidebar,
[class*="layout-"] .layout-sidebar {
  background-color: #3f359b !important;
  background-image: none !important; 
}
.layout-sidebar .nav-link-icon{
  color: white !important;
}
.layout-sidebar .nav-item.active .nav-link-icon {
  color: rgb(20, 6, 102) !important; 
}
.layout-sidebar .nav-link-text{
  color: white !important;
}

</style>