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
            <div class="form-group">
              <label for="avatar">Foto de perfil</label>
              <input type="file" id="avatar" @change="onFileChange" accept="image/*">
              <img v-if="previewImage" :src="previewImage">
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

// Tabs disponibles en configuración (sin notificaciones)
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
  avatar_url: ''
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

// Función para aplicar el tema seleccionado
const applyTheme = (theme) => {
  localStorage.setItem('theme', theme);
  document.documentElement.classList.remove('dark-theme', 'light-theme');
  document.body.classList.remove('dark-theme', 'light-theme');
  document.documentElement.classList.add(theme + '-theme');
  document.body.classList.add(theme + '-theme');
  window.dispatchEvent(new CustomEvent('themeChanged', { 
    detail: theme,
    bubbles: true, 
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

// Cargar datos del usuario
const loadUserData = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get('/api/user');
    if (response.data.success) {
      const userData = response.data.data;
      profile.name = userData.name || '';
      profile.email = userData.email || '';
      profile.avatar_url = userData.avatar || '';
      if (profile.avatar_url) {
        previewImage.value = profile.avatar_url;
      }
      auth.user = userData;
    }
  } catch (error) {
    console.error('Error al cargar datos del usuario:', error);
  } finally {
    isLoading.value = false;
  }
};

// Manejar el cambio del archivo de imagen
const onFileChange = (event) => {
  const file = event.target.files[0];
  profile.avatar = file;
  previewImage.value = URL.createObjectURL(file);
};

// Actualizar perfil (incluye subida de imagen si se selecciona)
const updateProfile = async () => {
  isLoading.value = true;
  errors.value = {};
  try {
    const formData = new FormData();
    formData.append('name', profile.name);
    formData.append('email', profile.email);
    if (profile.avatar) {
      formData.append('avatar', profile.avatar);
    }
    const response = await axios.put('/api/user', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    if (response.data.success) {
      auth.user = response.data.data;
      profile.avatar_url = response.data.data.avatar;
      if (profile.avatar_url) {
        previewImage.value = profile.avatar_url;
      }
      if (toast) {
        toast.add({ 
          severity: 'success', 
          summary: 'Perfil actualizado', 
          detail: 'Tu información personal ha sido actualizada correctamente', 
          life: 3000 
        });
      } else {
        alert('Perfil actualizado correctamente');
      }
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
    } else {
      if (toast) {
        toast.add({ 
          severity: 'error', 
          summary: 'Error', 
          detail: 'No se pudo actualizar el perfil', 
          life: 3000 
        });
      } else {
        alert('Error al actualizar el perfil');
      }
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
    background-color: #1A00FF;
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
    background-color: #4D33FF;
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
  }
  </style>