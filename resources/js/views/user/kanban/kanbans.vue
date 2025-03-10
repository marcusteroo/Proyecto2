<template>
    <div class="board-page" :class="{'dark-theme': isDarkTheme}">
      <div class="board-header">
        <h1>Mis tableros</h1>
        <div class="view-selector">
          <button class="view-btn active">
            <i class="pi pi-th-large"></i> Vista de cuadrícula
          </button>
        </div>
      </div>
  
      <div class="boards-container">
        <!-- Tableros del usuario -->
        <div class="boards-section">
          <h2 class="section-title">Tus tableros</h2>
          <div class="boards-grid">
            <!-- Tarjeta para crear nuevo tablero -->
            <div class="board-card create-board" @click="showCreateDialog = true">
              <div class="create-board-content">
                <i class="pi pi-plus create-icon"></i>
                <h3>Crear nuevo tablero</h3>
              </div>
            </div>
  
            <!-- Lista de tableros existentes -->
            <div 
                v-for="tablero in tableros" 
                :key="tablero.id_tablero" 
                class="board-card"
                :style="{ backgroundColor: tablero.color_fondo || getRandomColor(tablero.id_tablero) }"
                @click="$router.push(`/app/kanban/${tablero.id_tablero}`)"
                >
                <div class="board-card-overlay"></div>
                <div class="board-card-content">
                    <h3 class="board-title">{{ tablero.nombre }}</h3>
                    <p class="board-meta">Creado {{ formatDate(tablero.created_at) }}</p>
                </div>

                <div class="board-actions">
                    <button @click.stop="$router.push(`/app/kanban/${tablero.id_tablero}`)" class="board-action-btn">
                    <i class="pi pi-external-link"></i>
                    </button>
                    <button @click.stop="confirmDelete(tablero)" class="board-action-btn danger">
                    <i class="pi pi-trash"></i>
                    </button>
                </div>
                </div>
          </div>
        </div>
      </div>
  
      <!-- Dialog para crear nuevo tablero -->
      <div v-if="showCreateDialog" class="dialog-overlay" @click.self="showCreateDialog = false">
        <div class="dialog-card">
          <div class="dialog-header">
            <h3>Crear nuevo tablero</h3>
            <button @click="showCreateDialog = false" class="close-btn">
              <i class="pi pi-times"></i>
            </button>
          </div>
          <div class="dialog-body">
            <div class="form-group">
              <label for="boardTitle">Título del tablero</label>
              <input
                v-model="newBoard.nombre"
                id="boardTitle"
                type="text"
                class="form-control"
                placeholder="Añade un título para tu tablero"
                @keyup.enter="createBoard"
                ref="boardTitleInput"
              />
              <small v-if="errorMessage" class="error-text">{{ errorMessage }}</small>
            </div>
            
            <div class="form-group">
              <label>Color de fondo</label>
              <div class="background-colors">
                <div 
                  v-for="(color, index) in backgroundColors" 
                  :key="index"
                  class="color-option"
                  :style="{ backgroundColor: color }"
                  :class="{ active: newBoard.background === color }"
                  @click="newBoard.background = color"
                ></div>
              </div>
            </div>
          </div>
          <div class="dialog-footer">
            <button @click="showCreateDialog = false" class="btn btn-cancel">Cancelar</button>
            <button @click="createBoard" class="btn btn-primary" :disabled="isLoading">
              <i v-if="isLoading" class="pi pi-spinner pi-spin"></i>
              <span v-else>Crear tablero</span>
            </button>
          </div>
        </div>
      </div>
  
      <!-- Dialog de confirmación para eliminar tablero -->
      <div v-if="deleteDialog.show" class="dialog-overlay" @click.self="deleteDialog.show = false">
        <div class="dialog-card confirm-dialog">
          <div class="dialog-header">
            <h3>Eliminar tablero</h3>
            <button @click="deleteDialog.show = false" class="close-btn">
              <i class="pi pi-times"></i>
            </button>
          </div>
          <div class="dialog-body">
            <p>¿Estás seguro de que quieres eliminar el tablero <strong>{{ deleteDialog.tablero?.nombre }}</strong>?</p>
            <p class="warning-text">Esta acción no se puede deshacer y se perderán todas las tareas asociadas.</p>
          </div>
          <div class="dialog-footer">
            <button @click="deleteDialog.show = false" class="btn btn-cancel">Cancelar</button>
            <button @click="deleteBoard" class="btn btn-danger" :disabled="isLoading">
              <i v-if="isLoading" class="pi pi-spinner pi-spin"></i>
              <span v-else>Eliminar</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import { onMounted, ref, reactive, nextTick, watch } from 'vue';
  import { authStore } from "@/store/auth";
  
  export default {
    setup() {
      // Estado de datos y UI
      const tableros = ref([]);
      const isLoading = ref(false);
      const errorMessage = ref('');
      const isDarkTheme = ref(localStorage.getItem('theme') === 'dark');
      
      // Estado para el dialog de creación
      const showCreateDialog = ref(false);
      const newBoard = reactive({
        nombre: '',
        background: '#0079bf'
      });
      
      // Estado para el dialog de eliminación
      const deleteDialog = reactive({
        show: false,
        tablero: null
      });
      
      // Referencia para autofoco
      const boardTitleInput = ref(null);
      
      // Colores de fondo predefinidos estilo Trello
      const backgroundColors = [
        '#0079bf', // Azul
        '#d29034', // Naranja
        '#519839', // Verde
        '#b04632', // Rojo
        '#89609e', // Morado
        '#cd5a91', // Rosa
        '#4bbf6b', // Verde claro
        '#00aecc', // Azul cielo
      ];
      
      // Cargar tableros al montar el componente
      onMounted(async () => {
        await fetchTableros();
        
        // Escuchar cambios en el tema
        window.addEventListener('storage', (e) => {
          if (e.key === 'theme') {
            isDarkTheme.value = e.newValue === 'dark';
          }
        });
      });
      
      // Obtener tableros desde el backend
      const fetchTableros = async () => {
        isLoading.value = true;
        try {
            const response = await axios.get('/api/tableros');
            
            if (Array.isArray(response.data)) {
            tableros.value = response.data;
            console.log('Tableros cargados:', tableros.value);
            } else {
            console.error('La respuesta del servidor no es un array:', response.data);
            tableros.value = [];
            }
        } catch (error) {
            console.error('Error al obtener los tableros:', error);
            tableros.value = [];
        } finally {
            isLoading.value = false;
        }
        };
      
      // Crear nuevo tablero
      // Modificar la función createBoard
        const createBoard = async () => {
        if (!newBoard.nombre.trim()) {
            errorMessage.value = 'El título del tablero es obligatorio';
            return;
        }
        
        isLoading.value = true;
        errorMessage.value = '';
        
        try {
            // Obtener el ID del usuario autenticado del store (asegúrate de tener el authStore importado)
            const auth = authStore();
            const userId = auth.user?.id || 1; // Usar 1 como fallback
            
            await axios.post('/api/tableros', {
            nombre: newBoard.nombre,
            id_creador: userId,
            color_fondo: newBoard.background // Guardar el color seleccionado
            });
            
            // Cerrar el diálogo y limpiar el formulario
            showCreateDialog.value = false;
            newBoard.nombre = '';
            
            // Recargar la lista de tableros
            await fetchTableros();
        } catch (error) {
            console.error('Error al crear el tablero:', error);
            errorMessage.value = 'No se pudo crear el tablero. Inténtalo de nuevo.';
        } finally {
            isLoading.value = false;
        }
        };
      
      // Confirmar eliminación de tablero
      const confirmDelete = (tablero) => {
        deleteDialog.tablero = tablero;
        deleteDialog.show = true;
      };
      
      // Eliminar tablero
      const deleteBoard = async () => {
        if (!deleteDialog.tablero) return;
        
        isLoading.value = true;
        
        try {
          await axios.delete(`/api/tableros/${deleteDialog.tablero.id_tablero}`);
          
          // Filtrar el tablero eliminado de la lista
          tableros.value = tableros.value.filter(t => t.id_tablero !== deleteDialog.tablero.id_tablero);
          
          // Cerrar el diálogo
          deleteDialog.show = false;
          deleteDialog.tablero = null;
        } catch (error) {
          console.error('Error al eliminar el tablero:', error);
        } finally {
          isLoading.value = false;
        }
      };
      
      // Generar color basado en ID (para mantener consistencia)
      const getRandomColor = (id) => {
        return backgroundColors[id % backgroundColors.length];
      };
      
      // Formatear fecha
      const formatDate = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', { 
          year: 'numeric', 
          month: 'short', 
          day: 'numeric' 
        });
      };
      
      // Enfocar en el input cuando se muestra el diálogo
      watch(showCreateDialog, async (value) => {
        if (value) {
          await nextTick();
          boardTitleInput.value?.focus();
        }
      });
      
      return {
        tableros,
        isLoading,
        showCreateDialog,
        newBoard,
        deleteDialog,
        errorMessage,
        isDarkTheme,
        backgroundColors,
        boardTitleInput,
        fetchTableros,
        createBoard,
        confirmDelete,
        deleteBoard,
        getRandomColor,
        formatDate
      };
    }
  };
  </script>
  
  <style scoped>
  /* Estilos para la página de tableros */
  .board-page {
    min-height: 100vh;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    background-color: #fafbfc;
    color: #172b4d;
  }
  
  .board-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding-bottom: 16px;
    border-bottom: 1px solid #dfe1e6;
  }
  
  .board-header h1 {
    font-size: 24px;
    font-weight: 600;
    margin: 0;
  }
  
  .view-selector {
    display: flex;
  }
  
  .view-btn {
    background: #ebecf0;
    border: none;
    border-radius: 3px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    color: #172b4d;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.2s;
  }
  
  .view-btn:hover {
    background: #dfe1e6;
  }
  
  .view-btn.active {
    background: #e4f0f6;
    color: #0079bf;
  }
  
  .boards-section {
    margin-bottom: 48px;
  }
  
  .section-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 16px;
    color: #172b4d;
  }
  
  .boards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
  }
  
  /* Estilos de las tarjetas de tablero */
  .board-card {
    height: 120px;
    border-radius: 8px;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    color: white;
    background-color: #0079bf;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
  }
  
  .board-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
  
  .board-card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(0deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0) 40%);
    z-index: 1;
  }
  
  .board-card-content {
    position: relative;
    padding: 16px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    z-index: 2;
  }
  
  .board-title {
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
  }
  
  .board-meta {
    font-size: 12px;
    margin: 0;
    opacity: 0.8;
  }
  
  .board-actions {
    position: absolute;
    top: 8px;
    right: 8px;
    display: flex;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.2s;
    z-index: 3;
  }
  
  .board-card:hover .board-actions {
    opacity: 1;
  }
  
  .board-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    background-color: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
  }
  
  .board-action-btn:hover {
    background-color: rgba(255, 255, 255, 0.4);
  }
  
  .board-action-btn.danger:hover {
    background-color: #eb5a46;
  }
  
  /* Estilos para la tarjeta de crear tablero */
  .create-board {
    background-color: rgba(9, 30, 66, 0.04);
    color: #172b4d;
    border: 2px dashed #dfe1e6;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .create-board:hover {
    background-color: rgba(9, 30, 66, 0.08);
    border-color: #c1c7d0;
  }
  
  .create-board-content {
    text-align: center;
    z-index: 2;
  }
  
  .create-icon {
    font-size: 24px;
    margin-bottom: 8px;
    color: #42526e;
  }
  
  .create-board-content h3 {
    font-size: 16px;
    margin: 0;
    color: #42526e;
  }
  
  /* Estilos para los diálogos */
  .dialog-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 20px;
  }
  
  .dialog-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    width: 100%;
    max-width: 400px;
    overflow: hidden;
    animation: dialog-appear 0.2s ease-out;
  }
  
  @keyframes dialog-appear {
    from {
      transform: translateY(20px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }
  
  .dialog-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    border-bottom: 1px solid #dfe1e6;
  }
  
  .dialog-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
  }
  
  .dialog-body {
    padding: 16px;
  }
  
  .dialog-footer {
    padding: 16px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    border-top: 1px solid #dfe1e6;
  }
  
  .close-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #6b778c;
    width: 32px;
    height: 32px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .close-btn:hover {
    background-color: #ebecf0;
  }
  
  /* Formulario */
  .form-group {
    margin-bottom: 16px;
  }
  
  .form-group label {
    display: block;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 6px;
  }
  
  .form-control {
    width: 100%;
    padding: 8px 12px;
    font-size: 14px;
    border: 2px solid #dfe1e6;
    border-radius: 4px;
    transition: border-color 0.2s;
  }
  
  .form-control:focus {
    outline: none;
    border-color: #4c9aff;
  }
  
  .error-text {
    display: block;
    color: #eb5a46;
    font-size: 12px;
    margin-top: 4px;
  }
  
  /* Botones */
  .btn {
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .btn-primary {
    background-color: #0079bf;
    color: white;
  }
  
  .btn-primary:hover {
    background-color: #026aa7;
  }
  
  .btn-primary:disabled {
    background-color: #97b3cd;
    cursor: not-allowed;
  }
  
  .btn-cancel {
    background-color: #ebecf0;
    color: #172b4d;
  }
  
  .btn-cancel:hover {
    background-color: #dfe1e6;
  }
  
  .btn-danger {
    background-color: #eb5a46;
    color: white;
  }
  
  .btn-danger:hover {
    background-color: #cf513d;
  }
  
  .btn-danger:disabled {
    background-color: #f5afa6;
    cursor: not-allowed;
  }
  
  /* Selector de colores */
  .background-colors {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }
  
  .color-option {
    width: 40px;
    height: 40px;
    border-radius: 4px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform 0.1s, box-shadow 0.1s;
  }
  
  .color-option:hover {
    transform: scale(1.05);
  }
  
  .color-option.active {
    border-color: white;
    box-shadow: 0 0 0 2px #0079bf;
  }
  
  /* Estilos para confirmación de eliminación */
  .confirm-dialog {
    max-width: 450px;
  }
  
  .warning-text {
    color: #eb5a46;
    font-size: 14px;
  }
  
  /* Estilos para tema oscuro */
  .dark-theme {
    background-color: #1e1e2e;
    color: #e4e6eb;
  }
  
  .dark-theme .board-header {
    border-bottom-color: #383a46;
  }
  
  .dark-theme .view-btn {
    background: #24252d;
    color: #b5bac1;
  }
  
  .dark-theme .view-btn:hover {
    background: #383a46;
  }
  
  .dark-theme .view-btn.active {
    background: #1a00ff20;
    color: #1a00ff;
  }
  
  .dark-theme .section-title {
    color: #e4e6eb;
  }
  
  .dark-theme .create-board {
    background-color: #24252d;
    border-color: #383a46;
  }
  
  .dark-theme .create-board:hover {
    background-color: #383a46;
  }
  
  .dark-theme .create-board-content h3,
  .dark-theme .create-icon {
    color: #b5bac1;
  }
  
  .dark-theme .dialog-card {
    background-color: #292a36;
  }
  
  .dark-theme .dialog-header,
  .dark-theme .dialog-footer {
    border-color: #383a46;
  }
  
  .dark-theme .close-btn {
    color: #b5bac1;
  }
  
  .dark-theme .close-btn:hover {
    background-color: #383a46;
  }
  
  .dark-theme .form-control {
    background-color: #1e1e2e;
    border-color: #383a46;
    color: #e4e6eb;
  }
  
  .dark-theme .form-control:focus {
    border-color: #1a00ff;
  }
  
  .dark-theme .btn-cancel {
    background-color: #383a46;
    color: #e4e6eb;
  }
  
  .dark-theme .btn-cancel:hover {
    background-color: #4a4b59;
  }
  
  /* Estilos responsivos */
  @media (max-width: 768px) {
    .boards-grid {
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
    
    .board-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 16px;
    }
  }
  
  @media (max-width: 480px) {
    .boards-grid {
      grid-template-columns: 1fr;
    }
    
    .board-card {
      height: 100px;
    }
  }
  </style>