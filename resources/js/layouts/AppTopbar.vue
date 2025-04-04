<template>
    <header class="app-topbar">
      <!-- Botón de menú móvil -->
      <button class="menu-toggle-btn" @click.stop="toggleMobileMenu($event)">
        <i class="pi pi-bars"></i>
      </button>

  
      <!-- Espacio medio -->
      <div class="topbar-spacer"></div>
  
      <!-- Menú derecho -->
      <div class="topbar-menu" :class="topbarMenuClasses">
        <!-- Menú de perfil -->
        <div class="profile-menu">
          <button class="profile-button" @click.stop="toggleProfileMenu($event)">
            <span class="user-welcome">Hola, {{ authStore().user.name }}</span>
            <!-- Avatar personalizado, mostrando imagen si existe -->
            <div class="user-avatar" v-if="!userAvatarUrl">{{ getUserInitials() }}</div>
            <div class="user-avatar" v-else>
              <img 
                :src="normalizeUrl(userAvatarUrl)" 
                alt="Avatar" 
                class="avatar-image"
                @error="handleAvatarError" 
              />
            </div>
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
import { ref, computed, onMounted, watch, onBeforeUnmount } from 'vue';
import { useLayout } from '../composables/layout';
import useAuth from "@/composables/auth";
import { useRouter } from "vue-router";
import { authStore } from "../store/auth";
  
const router = useRouter();
const { processing, logout } = useAuth();
const { layoutState } = useLayout();
const auth = authStore();
  
const topbarMenuActive = ref(false);
const profileMenuOpen = ref(false);
const userAvatar = ref('');
const userAvatarUrl = ref('');

/**
 * Función para obtener la URL de la conversión (thumbnail) a partir de la URL original
 */
const getThumbnailUrl = (originalUrl) => {
  if (!originalUrl) return '';
  
  // Si ya es una URL de conversión, devolverla tal cual
  if (originalUrl.includes('/conversions/')) {
    return originalUrl;
  }
  
  // Intentar construir la ruta a la conversión
  try {
    // Ejemplo: URL original = http://127.0.0.1:8000/storage/4/file.jpg
    // URL conversión = http://127.0.0.1:8000/storage/4/conversions/thumbnail.jpg
    
    // Separar la URL por "/"
    const parts = originalUrl.split('/');
    
    // Encontrar el índice del ID de media (que suele ser un número)
    // Esto depende de tu estructura de URL
    let mediaIdIndex = -1;
    for (let i = parts.length - 2; i >= 0; i--) {
      if (!isNaN(parts[i])) {
        mediaIdIndex = i;
        break;
      }
    }
    
    if (mediaIdIndex === -1) return originalUrl;
    
    // Reconstruir la URL hasta el ID de media
    const basePath = parts.slice(0, mediaIdIndex + 1).join('/');
    
    // Agregar la ruta de conversión
    return `${basePath}/conversions/thumbnail.jpg`;
    
  } catch (error) {
    console.error('Error al generar URL de thumbnail:', error);
    return originalUrl;
  }
};

// Observar cambios en el usuario para actualizar el avatar
watch(() => auth.user, (newUser) => {
  if (newUser && newUser.avatar) {
    userAvatar.value = newUser.avatar;
    
    // Intentar usar la URL de la conversión de thumbnail si está disponible
    if (newUser.avatar_thumbnail) {
      userAvatarUrl.value = newUser.avatar_thumbnail;
    } else {
      // Si no hay thumbnail específico, intentar derivarlo de la URL principal
      userAvatarUrl.value = getThumbnailUrl(newUser.avatar);
    }
    
    // Asegurar que la URL comienza con la URL base correcta
    if (userAvatarUrl.value && userAvatarUrl.value.startsWith('/')) {
      userAvatarUrl.value = `${window.location.origin}${userAvatarUrl.value}`;
    } else if (userAvatarUrl.value && !userAvatarUrl.value.startsWith('http')) {
      userAvatarUrl.value = `${window.location.origin}/${userAvatarUrl.value}`;
    }
    
    console.log('Avatar URL en AppTopbar:', userAvatarUrl.value);
  } else {
    userAvatar.value = '';
    userAvatarUrl.value = '';
  }
}, { immediate: true, deep: true });

// Función para obtener iniciales de usuario para avatar
const getUserInitials = () => {
  const name = authStore().user.name || '';
  const parts = name.split(' ');
  
  if (parts.length >= 2) {
    return `${parts[0][0]}${parts[1][0]}`.toUpperCase();
  }
  
  return name.substring(0, 2).toUpperCase();
};
  
// Cargar avatar al iniciar
onMounted(() => {
  if (auth.user && auth.user.avatar) {
    userAvatar.value = auth.user.avatar;
  }
});
  
// Toggle menú móvil
const toggleMobileMenu = (event) => {
  event.preventDefault();
  layoutState.sidebarVisible = !layoutState.sidebarVisible;
  topbarMenuActive.value = false;
};
  
// Toggle menú de usuario
const toggleProfileMenu = (event) => {
  event.preventDefault();
  profileMenuOpen.value = !profileMenuOpen.value;
  
  if (profileMenuOpen.value) {
    document.addEventListener('click', handleOutsideClick);
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
  logout().then(() => {
    router.push('/login');
  });
};
  
const topbarMenuClasses = computed(() => {
  return {
    'topbar-menu-active': topbarMenuActive.value
  };
});
  
// Cerrar menú al hacer click fuera
const handleOutsideClick = (event) => {
  if (profileMenuOpen.value && !event.target.closest('.profile-menu')) {
    closeProfileMenu();
  }
};
  
// No es necesario añadir el listener al montar, lo hacemos solo cuando se abre el menú
onBeforeUnmount(() => {
  document.removeEventListener('click', handleOutsideClick);
});

// Añadir función de normalización de URL
const normalizeUrl = (url) => {
  if (!url) return '';
  
  // Eliminar dobles barras excepto después del protocolo
  let normalizedUrl = url.replace(/(https?:\/\/)|(\/\/+)/g, (match, protocol) => {
    return protocol || '/';
  });
  
  console.log('URL normalizada en AppTopbar:', normalizedUrl);
  return normalizedUrl;
};

// Función para manejar errores de carga
const handleAvatarError = (e) => {
  console.error('Error al cargar avatar en AppTopbar:', e.target.src);
  userAvatarUrl.value = ''; // Mostrar iniciales en su lugar
};
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
    overflow: hidden; /* Para que las imágenes no se salgan del círculo */
  }
  
  .avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
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