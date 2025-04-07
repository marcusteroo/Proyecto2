<template>
  <div class="settings-container" :class="{ 'dark-theme': preferences.theme === 'dark' }">
    <h1 class="settings-title">Configuración de la cuenta</h1>
    
    <div class="settings-tabs">
      <div class="tab-header">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          :class="['tab-button', { active: activeTab === tab.id }]"
          @click="activeTab = tab.id"
        >
          <i :class="tab.icon"></i>
          {{ tab.label }}
        </button>
      </div>
      
      <!-- Tab de Perfil -->
      <div v-if="activeTab === 'profile'" class="tab-content">
        <div class="settings-card">
          <h2>Información personal</h2>
          <form @submit.prevent="updateProfile" class="settings-form" enctype="multipart/form-data">
            <!-- Campo para subir imagen de avatar -->
            <div class="form-group avatar-upload">
              <label for="avatar" class="label-foto">Foto de perfil</label>
              <div class="avatar-container">
                <div class="current-avatar" :class="{ 'has-avatar': !!previewImage || !!profile.avatar_url }">
                  <div v-if="debugMode" class="debug-info">
                    <small>URL: {{ previewImage || profile.avatar_url }}</small>
                  </div>
                  <img 
                    v-if="previewImage || profile.avatar_url" 
                    :src="previewImage || getValidUrl(profile.avatar_url)" 
                    alt="Avatar de usuario"
                    class="avatar-image" 
                    @error="handleImageError"
                  />
                  <div v-else class="avatar-placeholder">
                    <i class="pi pi-user"></i>
                  </div>
                </div>
                <div class="avatar-actions">
                  <label for="avatar" class="avatar-upload-btn">
                    <i class="pi pi-camera"></i>
                    <span class="color-texto-cambiar-foto">Cambiar foto</span>
                  </label>
                  <button 
                    v-if="previewImage || profile.avatar_url" 
                    type="button" 
                    class="avatar-remove-btn"
                    @click="removeAvatar"
                  >
                    <i class="pi pi-trash"></i>
                    <span>Eliminar</span>
                  </button>
                </div>
                <input 
                  type="file" 
                  id="avatar" 
                  ref="avatarInput"
                  @change="onFileChange" 
                  accept="image/*" 
                  class="hidden-input"
                />
              </div>
            </div>
            
            <div class="form-group">
              <label for="name">Nombre</label>
              <input 
                id="name" 
                v-model="profile.name" 
                type="text" 
                class="form-control" 
                placeholder="Tu nombre"
              >
              <div v-if="errors.name" class="input-error">{{ errors.name[0] }}</div>
            </div>
            
            <div class="form-group">
              <label for="email">Correo electrónico</label>
              <input 
                id="email" 
                v-model="profile.email" 
                type="email" 
                class="form-control" 
                disabled
                placeholder="Tu correo electrónico"
              >
              <small class="input-help">El correo electrónico no puede modificarse</small>
            </div>
            
            <div class="form-actions">
              <button type="submit" :disabled="isLoading" class="btn-primary">
                <span v-if="isLoading" class="spinner"></span>
                <span v-if="isLoading">Guardando...</span>
                <span v-else>Actualizar perfil</span>
              </button>
            </div>
          </form>
        </div>
      </div>
      
      <!-- Tab de Contraseña -->
      <div v-if="activeTab === 'password'" class="tab-content">
        <div class="settings-card">
          <h2>Cambiar contraseña</h2>
          <form @submit.prevent="updatePassword" class="settings-form">
            <div class="form-group">
              <label for="current_password">Contraseña actual</label>
              <input 
                id="current_password" 
                v-model="password.current_password" 
                type="password" 
                class="form-control" 
                placeholder="Contraseña actual"
              >
              <div v-if="errors.current_password" class="input-error">{{ errors.current_password[0] }}</div>
            </div>
            
            <div class="form-group">
              <label for="password">Nueva contraseña</label>
              <input 
                id="password" 
                v-model="password.password" 
                type="password" 
                class="form-control" 
                placeholder="Nueva contraseña"
              >
              <div v-if="errors.password" class="input-error">{{ errors.password[0] }}</div>
            </div>
            
            <div class="form-group">
              <label for="password_confirmation">Confirmar nueva contraseña</label>
              <input 
                id="password_confirmation" 
                v-model="password.password_confirmation" 
                type="password" 
                class="form-control" 
                placeholder="Confirmar nueva contraseña"
              >
            </div>
            
            <div class="form-actions">
              <button type="submit" :disabled="isLoading" class="btn-primary">
                <span v-if="isLoading">Actualizando...</span>
                <span v-else>Cambiar contraseña</span>
              </button>
            </div>
          </form>
        </div>
      </div>
      
      <!-- Tab de Preferencias -->
      <div v-if="activeTab === 'preferences'" class="tab-content">
        <div class="settings-card">
          <h2>Preferencias del sistema</h2>
          <div class="settings-form">
            <div class="form-group">
              <label>Tema de la interfaz</label>
              <div class="theme-options">
                <button 
                  class="theme-option" 
                  :class="{ active: preferences.theme === 'light' }"
                  @click="selectTheme('light')"
                >
                  <div class="theme-preview light-preview"></div>
                  <span>Claro</span>
                </button>
                <button 
                  class="theme-option"
                  :class="{ active: preferences.theme === 'dark' }"
                  @click="selectTheme('dark')"
                >
                  <div class="theme-preview dark-preview"></div>
                  <span>Oscuro</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { authStore } from '../../../store/auth';
import { useToast } from 'primevue/usetoast';
import { useLayout } from '../../../composables/layout';
import axios from 'axios';

// Estado del componente
const activeTab = ref('profile');
const isLoading = ref(false);
const errors = ref({});
const auth = authStore();
const user = auth.user;
const toast = useToast();
const { layoutConfig } = useLayout();
const avatarInput = ref(null);

// Tabs disponibles en configuración
const tabs = [
  { id: 'profile', label: 'Perfil', icon: 'pi pi-user' },
  { id: 'password', label: 'Contraseña', icon: 'pi pi-lock' },
  { id: 'preferences', label: 'Preferencias', icon: 'pi pi-cog' }
];

// Estado para el formulario de perfil
const profile = reactive({
  name: user?.name || '',
  email: user?.email || '',
  avatar: null,
  avatar_url: '',
  media: []
});

// Estado para el formulario de contraseña
const password = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
});

// Estado para preferencias del usuario
const preferences = reactive({
  theme: localStorage.getItem('theme') || 'light',
});

// Variable para almacenar la vista previa de la imagen
const previewImage = ref('');

// Añadir variable de debug
const debugMode = ref(false); // Desactiva el modo de depuración

// Función para aplicar el tema seleccionado
const applyTheme = (theme) => {
  localStorage.setItem('theme', theme);
  document.documentElement.classList.remove('dark-theme', 'light-theme');
  document.body.classList.remove('dark-theme', 'light-theme');
  document.documentElement.classList.add(theme + '-theme');
  document.body.classList.add(theme + '-theme');
  window.dispatchEvent(new CustomEvent('themeChanged', { 
    composed: true
  }));
};

// Observar cambios en el tema y aplicar clase global
watch(() => preferences.theme, (newTheme) => {
  applyTheme(newTheme);
});

// Cargar datos del usuario al montar el componente
onMounted(() => {
  loadUserData();
  applyTheme(preferences.theme);
});

// Agrega esta función para mejorar el manejo de URLs
const getValidUrl = (url) => {
  if (!url) return '';
  
  // Primero corregir el protocolo si está mal formado (http:/ en vez de http://)
  let cleanUrl = url.replace(/http:\/([^\/])/, 'http://$1');
  
  // Eliminar dominios duplicados (este es el problema principal)
  if (cleanUrl.includes('127.0.0.1:8000/127.0.0.1:8000')) {
    cleanUrl = cleanUrl.replace('127.0.0.1:8000/127.0.0.1:8000', '127.0.0.1:8000');
  }
  
  // Si la URL comienza con / pero no con // (no es una URL relativa al protocolo)
  if (cleanUrl.startsWith('/') && !cleanUrl.startsWith('//')) {
    cleanUrl = window.location.origin + cleanUrl;
  }
  
  return cleanUrl;
};

// Reemplaza la función normalizeUrl existente con esta
const normalizeUrl = getValidUrl;

// Cargar datos del usuario
const loadUserData = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get('/api/user');
    if (response.data.success) {
      const userData = response.data.data;
      
      profile.name = userData.name || '';
      profile.email = userData.email || '';
      profile.media = userData.media || [];
      
      // Si hay avatar de Spatie, usarlo
      if (userData.avatar) {
        // Limpiar URL antes de asignarla
        const avatarUrl = userData.avatar_thumbnail || userData.avatar;
        profile.avatar_url = getValidUrl(avatarUrl);
        
        console.log('Avatar URL limpia cargada:', profile.avatar_url);
        
        // Intentar verificar si la URL es accesible
        fetch(profile.avatar_url, { method: 'HEAD' })
          .then(response => {
            console.log(`URL ${profile.avatar_url} accesible:`, response.ok, response.status);
          })
          .catch(error => {
            console.error(`Error verificando URL ${profile.avatar_url}:`, error);
          });
        
        previewImage.value = '';
      } else {
        profile.avatar_url = '';
      }
      
      auth.user = userData;
    }
  } catch (error) {
    console.error('Error al cargar datos del usuario:', error);
    if (toast) {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'No se pudieron cargar los datos del usuario',
        life: 3000
      });
    }
  } finally {
    isLoading.value = false;
  }
};

// Manejar el cambio del archivo de imagen
const onFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validar el tipo de archivo
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!validTypes.includes(file.type)) {
      toast.add({
        severity: 'error',
        summary: 'Formato no válido',
        detail: 'El archivo debe ser una imagen (JPG, PNG, GIF, WEBP)',
        life: 3000
      });
      if (avatarInput.value) {
        avatarInput.value.value = ''; // Limpiar el input
      }
      return;
    }
    
    // Validar el tamaño (máximo 2MB)
    if (file.size > 2 * 1024 * 1024) {
      toast.add({
        severity: 'error',
        summary: 'Archivo demasiado grande',
        detail: 'La imagen no debe superar los 2MB',
        life: 3000
      });
      if (avatarInput.value) {
        avatarInput.value.value = ''; // Limpiar el input
      }
      return;
    }
    
    console.log('Archivo seleccionado:', file.name, 'tipo:', file.type, 'tamaño:', file.size);
    profile.avatar = file;
    previewImage.value = URL.createObjectURL(file);
  }
};

// Eliminar avatar desde el servidor
const removeAvatarFromServer = async () => {
  isLoading.value = true;
  try {
    const response = await axios.delete('/api/user/avatar');
    
    if (response.data.success) {
      // Actualizar UI
      profile.avatar = null;
      profile.avatar_url = '';
      previewImage.value = '';
      
      // Actualizar el usuario en el store
      if (response.data.data) {
        auth.user = response.data.data;
      }
      
      toast.add({
        severity: 'success',
        summary: 'Avatar eliminado',
        detail: 'Tu imagen de perfil ha sido eliminada',
        life: 3000
      });
    }
  } catch (error) {
    console.error('Error al eliminar avatar:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudo eliminar la imagen de perfil',
      life: 3000
    });
  } finally {
    isLoading.value = false;
  }
};

// Eliminar avatar
const removeAvatar = () => {
  profile.avatar = null;
  previewImage.value = '';
  
  if (profile.avatar_url) {
    // Si hay un avatar en el servidor, eliminarlo
    removeAvatarFromServer();
  } else {
    profile.avatar_url = '';
  }
  
  if (avatarInput.value) {
    avatarInput.value.value = '';
  }
};

// Subir avatar al servidor
const uploadAvatar = async () => {
  if (!profile.avatar) return;
  
  try {
    console.log("Subiendo avatar con método específico");
    
    const formData = new FormData();
    formData.append('avatar', profile.avatar);
    
    const response = await axios.post('/api/user/avatar', formData, {
      headers: { 
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    });
    
    if (response.data.success || response.status === 200) {
      console.log("Avatar actualizado correctamente");
      
      // Limpiar el input de archivo
      if (avatarInput.value) {
        avatarInput.value.value = '';
      }
      
      // Importante: Actualizar el store de autenticación con los nuevos datos
      if (response.data.data) {
        auth.user = response.data.data;
      }
      
      // Limpiar la vista previa y recargar datos del usuario del servidor
      profile.avatar = null;
      previewImage.value = '';
      
      // Forzar la recarga de los datos del usuario desde el servidor
      await loadUserData();
      
      // Agregar parámetro para forzar recarga del navegador (evitar caché)
      if (profile.avatar_url) {
        profile.avatar_url = `${profile.avatar_url}?t=${new Date().getTime()}`;
      }
    }
  } catch (error) {
    console.error('Error al subir avatar:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: error.response?.data?.message || 'No se pudo actualizar la imagen de perfil',
      life: 3000
    });
    throw error;
  }
};

// Actualizar perfil
const updateProfile = async () => {
  isLoading.value = true;
  errors.value = {};
  
  try {
    // Primero guardamos la información básica del perfil (sin avatar)
    const profileData = {
      name: profile.name
    };
    
    // Verificar qué contiene formData
    console.log('Enviando datos:', {
      name: profile.name,
      email: profile.email,
      hasAvatar: !!profile.avatar,
      removeAvatar: !profile.avatar && !profile.avatar_url && previewImage.value === ''
    });
    
    // Actualizar datos del perfil
    const profileResponse = await axios.put('/api/user', profileData);
    
    // Si hay avatar para subir, hacerlo en una solicitud separada
    if (profile.avatar) {
      console.log("Avatar a subir:", {
        name: profile.avatar.name,
        type: profile.avatar.type,
        size: profile.avatar.size
      });
      
      await uploadAvatar();
    } 
    // Si se quiere eliminar el avatar
    else if (!profile.avatar_url && previewImage.value === '') {
      // Ya tenemos un método para esto: removeAvatarFromServer();
    }
    
    // Recargar datos del usuario
    await loadUserData();
    
    // Mostrar mensaje de éxito
    toast.add({ 
      severity: 'success', 
      summary: 'Perfil actualizado', 
      detail: 'Tu información personal ha sido actualizada correctamente', 
      life: 3000 
    });
    
  } catch (error) {
    console.error('Error completo:', error);
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
    } else {
      toast.add({ 
        severity: 'error', 
        summary: 'Error', 
        detail: error.response?.data?.message || 'No se pudo actualizar el perfil', 
        life: 3000 
      });
    }
  } finally {
    isLoading.value = false;
  }
};

// Actualizar contraseña
const updatePassword = async () => {
  isLoading.value = true;
  errors.value = {};
  try {
    const response = await axios.put('/api/password', password);
    // Limpiar el formulario de contraseña
    password.current_password = '';
    password.password = '';
    password.password_confirmation = '';
    if (toast) {
      toast.add({ 
        severity: 'success', 
        summary: 'Contraseña actualizada', 
        detail: 'Tu contraseña ha sido cambiada correctamente', 
        life: 3000 
      });
    } else {
      alert('Contraseña actualizada correctamente');
    }
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      if (toast) {
        toast.add({ 
          severity: 'error', 
          summary: 'Error', 
          detail: error.response?.data?.message || 'No se pudo actualizar la contraseña', 
          life: 3000 
        });
      } else {
        alert('Error al actualizar la contraseña');
      }
    }
  } finally {
    isLoading.value = false;
  }
};

// Seleccionar tema
const selectTheme = (theme) => {
  preferences.theme = theme;
  localStorage.setItem('theme', theme);
  applyTheme(theme);
  if (toast) {
    toast.add({
      severity: 'info',
      summary: 'Tema cambiado',
      detail: `Has cambiado al tema ${theme === 'dark' ? 'oscuro' : 'claro'}`,
      life: 2000
    });
  }
};

// Mejorar el manejo de errores en la carga de imagen
const handleImageError = (e) => {
  console.error('Error al cargar imagen de avatar:', e);
  
  // Mostrar placeholder directamente sin reintentos
  e.target.style.display = 'none';
  
  // Mostrar avatar placeholder
  const parent = e.target.parentNode;
  if (!parent.querySelector('.avatar-placeholder')) {
    const placeholder = document.createElement('div');
    placeholder.className = 'avatar-placeholder';
    const icon = document.createElement('i');
    icon.className = 'pi pi-user';
    placeholder.appendChild(icon);
    parent.appendChild(placeholder);
  }
};
</script>

  
  <style scoped>
  .settings-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .settings-title {
    font-size: 28px;
    margin-bottom: 30px;
    color: #333;
    text-align: center;
  }
  
  .settings-tabs {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0,0,0,0.08);
    overflow: hidden;
  }
  
  .tab-header {
    display: flex;
    background-color: #f5f7f9;
    border-bottom: 1px solid #e1e4e8;
  }
  
  .tab-button {
    padding: 16px 24px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 15px;
    color: #586069;
    font-weight: 500;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .tab-button:hover {
    background-color: #e9ecef;
    color: #1A00FF;
  }
  
  .tab-button.active {
    color: #1A00FF;
    border-bottom: 3px solid #1A00FF;
  }
  
  .tab-button i {
    font-size: 16px;
  }
  
  .tab-content {
    padding: 30px;
  }
  
  .settings-card {
    background: white;
    border-radius: 4px;
  }
  
  .settings-card h2 {
    margin-top: 0;
    margin-bottom: 24px;
    font-size: 20px;
    color: #24292e;
  }
  
  .settings-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 500px;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  
  .form-group label {
    font-weight: 500;
    color: #24292e;
    font-size: 14px;
  }
  
  .form-control {
    padding: 10px 12px;
    border: 1px solid #d1d5da;
    border-radius: 4px;
    font-size: 16px;
    transition: border-color 0.2s;
  }
  
  .form-control:focus {
    border-color: #1A00FF;
    outline: none;
    box-shadow: 0 0 0 3px rgba(26, 0, 255, 0.2);
  }
  
  .form-control:disabled {
    background-color: #f6f8fa;
    cursor: not-allowed;
  }
  
  .input-error {
    color: #d73a49;
    font-size: 14px;
    margin-top: 4px;
  }
  
  .input-help {
    color: #586069;
    font-size: 12px;
    margin-top: 4px;
  }
  
  .form-actions {
    margin-top: 10px;
  }
  
  .btn-primary {
    background-color: #106EBE;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 10px 16px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }
  
  .btn-primary:hover {
    background-color: #1067b3;
  }
  
  .btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }
  
  .spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 0.8s linear infinite;
  }
  
  @keyframes spin {
    to { transform: rotate(360deg); }
  }
  
  /* Estilos para los selectores de tema */
  .theme-options {
    display: flex;
    gap: 20px;
    margin-top: 10px;
  }
  
  .theme-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    background: none;
    border: none;
    cursor: pointer;
  }
  
  .theme-preview {
    width: 100px;
    height: 60px;
    border-radius: 6px;
    border: 2px solid #d1d5da;
    transition: border-color 0.2s;
  }
  
  .theme-option.active .theme-preview {
    border-color: #1A00FF;
    box-shadow: 0 0 0 2px rgba(26, 0, 255, 0.3);
  }
  
  .light-preview {
    background-color: white;
  }
  
  .dark-preview {
    background-color: #212529;
  }
  
  /* Estilos para modo oscuro */
  :host-context(.dark-theme) .settings-container,
  .dark-theme .settings-container {
    background-color: #1e1e2e;
    color: #e4e6eb;
  }
  
  :host-context(.dark-theme) .settings-title,
  .dark-theme .settings-title {
    color: #e4e6eb;
  }
  
  :host-context(.dark-theme) .settings-tabs,
  .dark-theme .settings-tabs {
    background-color: #292a36;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
  }
  
  :host-context(.dark-theme) .tab-header,
  .dark-theme .tab-header {
    background-color: #24252d;
    border-bottom-color: #383a46;
  }
  
  :host-context(.dark-theme) .tab-button,
  .dark-theme .tab-button {
    color: #b5bac1;
  }
  
  :host-context(.dark-theme) .tab-button:hover,
  .dark-theme .tab-button:hover {
    background-color: #383a46;
  }
  
  :host-context(.dark-theme) .settings-card,
  .dark-theme .settings-card {
    background-color: #292a36;
  }
  
  :host-context(.dark-theme) .settings-card h2,
  .dark-theme .settings-card h2 {
    color: #e4e6eb;
  }
  
  :host-context(.dark-theme) .form-group label,
  .dark-theme .form-group label {
    color: #e4e6eb;
  }
  
  :host-context(.dark-theme) .form-control,
  .dark-theme .form-control {
    background-color: #1e1e2e;
    border-color: #383a46;
    color: #e4e6eb;
  }
  
  :host-context(.dark-theme) .form-control:disabled,
  .dark-theme .form-control:disabled {
    background-color: #292a36;
  }
  
  :host-context(.dark-theme) .input-help,
  .dark-theme .input-help {
    color: #b5bac1;
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .tab-header {
      flex-direction: column;
    }
    
    .tab-button {
      justify-content: center;
      border-bottom: 1px solid #e1e4e8;
    }
    
    .tab-button.active {
      border-bottom: 1px solid #e1e4e8;
      border-left: 3px solid #1A00FF;
    }
    .avatar-container{
      align-items: center;
    }
  }
  
  /* Nuevos estilos para el avatar */
.avatar-upload {
  margin-bottom: 30px;
}

.avatar-container {
  display: flex;
  flex-direction: column;
  gap: 15px;
  
}

.current-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  overflow: hidden;
  background-color: #f0f2f5;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #d1d5da;
}

.dark-theme .current-avatar {
  background-color: #2d2d3a;
  border-color: #383a46;
}

.avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-placeholder i {
  font-size: 48px;
  color: #a0a0a0;
}

.dark-theme .avatar-placeholder i {
  color: #6c6c7a;
}

.avatar-actions {
  display: flex;
  gap: 10px;
}

.hidden-input {
  display: none;
}

.avatar-upload-btn, .avatar-remove-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  border-radius: 4px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}

.avatar-upload-btn {
  background-color: #106EBE;
  color: white;
  border: none;
}

.avatar-upload-btn:hover {
  background-color: #0e5ea7;
}

.avatar-remove-btn {
  background-color: transparent;
  color: #d73a49;
  border: 1px solid #d73a49;
}

.avatar-remove-btn:hover {
  background-color: rgba(215, 58, 73, 0.1);
}

.dark-theme .avatar-remove-btn {
  color: #ff6b6b;
  border-color: #ff6b6b;
}

.dark-theme .avatar-remove-btn:hover {
  background-color: rgba(255, 107, 107, 0.1);
}

.spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
.label-foto{
  padding-bottom: 10px;
}
.color-texto-cambiar-foto, .pi-camera{
  color: white;
}
  </style>