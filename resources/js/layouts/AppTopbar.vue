<template>
    <header class="app-topbar">
      <!-- Botón de menú móvil -->
      <button class="menu-toggle-btn" @click.stop="toggleMobileMenu($event)">
        <i class="pi pi-bars"></i>
      </button>
  
      <!-- Logotipo para móvil -->
      <div class="mobile-logo">
        <router-link to="/" class="logo-link">
          <img src="/images/LogoKanFlow.svg" alt="KanFlow" class="logo-image" />
        </router-link>
      </div>
  
      <!-- Espacio medio -->
      <div class="topbar-spacer"></div>
  
      <!-- Menú derecho -->
      <div class="topbar-menu" :class="topbarMenuClasses">
        <!-- Menú de perfil -->
        <div class="profile-menu">
          <button class="profile-button" @click.stop="toggleProfileMenu($event)">
            <span class="user-welcome">Hola, {{ authStore().user.name }}</span>
            <div class="user-avatar">{{ getUserInitials() }}</div>
            <i class="pi pi-chevron-down chevron-icon" :class="{'rotate': profileMenuOpen}"></i>
          </button>
          
          <!-- Menú desplegable -->
          <div class="dropdown-menu" :class="{'show': profileMenuOpen}">
            <router-link to="/app/settings" class="dropdown-item" @click="closeProfileMenu">
              <i class="pi pi-user"></i>
              <span>Mi perfil</span>
            </router-link>
            <div class="dropdown-divider"></div>
            <button @click="handleLogout" class="dropdown-item logout-item" :disabled="processing">
              <i class="pi pi-sign-out"></i>
              <span>{{ processing ? 'Cerrando sesión...' : 'Cerrar sesión' }}</span>
            </button>
          </div>
        </div>
      </div>
    </header>
  </template>
  
  <script setup>
  import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
  import { useLayout } from '../composables/layout';
  import useAuth from "@/composables/auth";
  import { useRouter } from "vue-router";
  import { authStore } from "../store/auth";
  
  const router = useRouter();
  const { processing, logout } = useAuth();
  const { layoutState } = useLayout();
  
  const topbarMenuActive = ref(false);
  const profileMenuOpen = ref(false);
  
  // Función para obtener iniciales de usuario para avatar
  const getUserInitials = () => {
    const name = authStore().user.name || '';
    const parts = name.split(' ');
    
    if (parts.length >= 2) {
      return `${parts[0][0]}${parts[1][0]}`.toUpperCase();
    } else if (name.length > 0) {
      return name[0].toUpperCase();
    } else {
      return 'U';
    }
  };
  
  // Toggle menú móvil
  const toggleMobileMenu = (event) => {
    console.log("Toggling mobile menu");
    event.stopPropagation();
    layoutState.staticMenuMobileActive.value = !layoutState.staticMenuMobileActive.value;
  };
  
  // Toggle menú de usuario
  const toggleProfileMenu = (event) => {
    event.stopPropagation();
    profileMenuOpen.value = !profileMenuOpen.value;
    
    // Solo agregar el listener cuando el menú está abierto
    if (profileMenuOpen.value) {
      // Pequeño retraso para evitar que el mismo clic que abre el menú lo cierre
      setTimeout(() => {
        document.addEventListener('click', handleOutsideClick);
      }, 10);
    } else {
      document.removeEventListener('click', handleOutsideClick);
    }
  };
  
  // Cerrar menú de perfil
  const closeProfileMenu = () => {
    profileMenuOpen.value = false;
    document.removeEventListener('click', handleOutsideClick);
  };
  
  // Función para logout
  const handleLogout = () => {
    closeProfileMenu();
    logout();
  };
  
  const topbarMenuClasses = computed(() => {
    return {
      'topbar-menu-active': topbarMenuActive.value
    };
  });
  
  // Cerrar menú al hacer click fuera
  const handleOutsideClick = (event) => {
    const profileButton = document.querySelector('.profile-button');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    // Si el clic no fue en el botón ni en el menú, cerrar el menú
    if (profileButton && dropdownMenu && 
        !profileButton.contains(event.target) && 
        !dropdownMenu.contains(event.target)) {
      profileMenuOpen.value = false;
      document.removeEventListener('click', handleOutsideClick);
    }
  };
  
  // No es necesario añadir el listener al montar, lo hacemos solo cuando se abre el menú
  onBeforeUnmount(() => {
    document.removeEventListener('click', handleOutsideClick);
  });
  </script>
  
  <style lang="scss">
  .app-topbar {
    height: 64px;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    padding: 0 24px;
    position: fixed;
    top: 0;
    right: 0;
    left: var(--sidebar-width);
    z-index: 999;
    transition: left var(--transition-normal);
  }
  
  /* Botón de menú responsivo */
  .menu-toggle-btn {
    display: none;
    background: transparent;
    border: none;
    color: #444;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    transition: background-color var(--transition-fast);
    align-items: center;
    justify-content: center;
  }
  
  .menu-toggle-btn:hover {
    background-color: rgba(0, 0, 0, 0.04);
  }
  
  /* Logo para móvil */
  .mobile-logo {
    display: none;
  }
  
  .logo-link {
    display: flex;
    align-items: center;
    text-decoration: none;
  }
  
  .logo-image {
    height: 32px;
  }
  
  /* Espaciador flexible */
  .topbar-spacer {
    flex: 1;
  }
  
  /* Menú derecho */
  .topbar-menu {
    display: flex;
    align-items: center;
  }
  
  /* Menú de perfil */
  .profile-menu {
    position: relative;
  }
  
  .profile-button {
    display: flex;
    align-items: center;
    background: transparent;
    border: none;
    padding: 8px 16px;
    border-radius: 24px;
    cursor: pointer;
    transition: background-color var(--transition-fast);
  }
  
  .profile-button:hover {
    background-color: rgba(0, 0, 0, 0.04);
  }
  
  .user-welcome {
    margin-right: 12px;
    font-weight: 500;
    color: #444;
  }
  
  .user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    margin-right: 8px;
  }
  
  .chevron-icon {
    font-size: 12px;
    color: #666;
    transition: transform var(--transition-fast);
  }
  
  .chevron-icon.rotate {
    transform: rotate(180deg);
  }
  
  /* Dropdown menu */
  .dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 200px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    overflow: hidden;
    display: none;
    animation: slideDown 0.2s ease forwards;
    z-index: 1000;
  }
  
  /* Solo mostrar cuando tiene la clase .show */
  .dropdown-menu.show {
    display: block;
  }
  
  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .dropdown-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    color: #333;
    text-decoration: none;
    transition: background-color var(--transition-fast);
    cursor: pointer;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    font-size: 14px;
  }
  
  .dropdown-item:hover {
    background-color: rgba(0, 0, 0, 0.04);
  }
  
  .dropdown-item i {
    margin-right: 12px;
    font-size: 16px;
  }
  
  .dropdown-divider {
    height: 1px;
    background-color: #eee;
    margin: 4px 0;
  }
  
  .logout-item {
    color: #e74c3c;
  }
  
  .logout-item:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
  
  /* Responsivo */
  @media (max-width: 991px) {
    .app-topbar {
      left: 0;
      padding: 0 16px;
    }
    
    .menu-toggle-btn {
      display: flex;
    }
    
    .mobile-logo {
      display: block;
      margin-left: 16px;
    }
    
    .user-welcome {
      display: none;
    }
  }
  
  /* Tema oscuro */
  .layout-theme-dark .app-topbar,
  .dark-theme .app-topbar {
    background-color: #1e1e2e;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  }
  
  .layout-theme-dark .menu-toggle-btn,
  .dark-theme .menu-toggle-btn {
    color: #e4e6eb;
  }
  
  .layout-theme-dark .menu-toggle-btn:hover,
  .dark-theme .menu-toggle-btn:hover {
    background-color: rgba(255, 255, 255, 0.08);
  }
  
  .layout-theme-dark .profile-button,
  .dark-theme .profile-button {
    color: #e4e6eb;
  }
  
  .layout-theme-dark .profile-button:hover,
  .dark-theme .profile-button:hover {
    background-color: rgba(255, 255, 255, 0.08);
  }
  
  .layout-theme-dark .user-welcome,
  .dark-theme .user-welcome {
    color: #e4e6eb;
  }
  
  .layout-theme-dark .chevron-icon,
  .dark-theme .chevron-icon {
    color: #adb5bd;
  }
  
  .layout-theme-dark .dropdown-menu,
  .dark-theme .dropdown-menu {
    background-color: #292a36;
  }
  
  .layout-theme-dark .dropdown-item,
  .dark-theme .dropdown-item {
    color: #e4e6eb;
  }
  
  .layout-theme-dark .dropdown-item:hover,
  .dark-theme .dropdown-item:hover {
    background-color: rgba(255, 255, 255, 0.08);
  }
  
  .layout-theme-dark .dropdown-divider,
  .dark-theme .dropdown-divider {
    background-color: #383a46;
  }
  
  </style>