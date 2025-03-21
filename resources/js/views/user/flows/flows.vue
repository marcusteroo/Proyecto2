<template>
    <div class="flows-page">
      <div class="flows-header">
        <div class="title-section">
          <h1>Mis automatizaciones</h1>
          <p class="subtitle">Crea y gestiona flujos de trabajo automatizados</p>
        </div>
        <div class="actions-section">
          <button class="create-flow-btn" @click="addFlow">
            <i class="pi pi-plus"></i>
            <span>Crear automatización</span>
          </button>
        </div>
      </div>
  
      <div class="flows-filters">
        <div class="search-container">
          <span class="p-input-icon-left">
            <i class="pi pi-search"></i>
            <input type="text" v-model="searchText" placeholder="Buscar automatizaciones" class="search-input" />
          </span>
        </div>
        <div class="view-options">
          <button class="view-btn active">
            <i class="pi pi-th-large"></i>
          </button>
        </div>
      </div>
  
      <div v-if="filteredFlows.length === 0 && !loading" class="empty-state">
        <div class="empty-content">
          <i class="pi pi-cog empty-icon"></i>
          <h3>No se encontraron automatizaciones</h3>
          <p>Crea tu primera automatización para mejorar tu productividad</p>
          <button class="create-flow-btn" @click="addFlow">
            <i class="pi pi-plus"></i>
            <span>Crear automatización</span>
          </button>
        </div>
      </div>
  
      <div v-else-if="loading" class="loading-state">
        <i class="pi pi-spin pi-spinner loading-icon"></i>
        <p>Cargando automatizaciones...</p>
      </div>
  
      <div v-else class="flows-grid">
        <div v-for="flow in filteredFlows" :key="flow.id_workflow" class="flow-card">
          <div class="flow-card-header" :style="{ backgroundColor: getRandomColor(flow.id_workflow) }">
            <div class="flow-icon">
              <i class="pi pi-bolt"></i>
            </div>
          </div>
          <div class="flow-card-body">
            <h3 class="flow-title">{{ flow.nombre }}</h3>
            <p class="flow-description">{{ flow.descripcion }}</p>
            <div class="flow-meta">
              <span class="flow-status" :class="{ 'active': flow.status === 'active' }">
                <i class="pi pi-circle-fill status-dot"></i>
                {{ flow.status === 'active' ? 'Activo' : 'Inactivo' }}
              </span>
              <span class="flow-date">Creado: {{ formatDate(flow.created_at) }}</span>
            </div>
          </div>
          <div class="flow-card-footer">
            <button class="flow-action-btn danger" @click="confirmDelete(flow)">
              <i class="pi pi-trash"></i>
              <span>Eliminar</span>
            </button>
          </div>
        </div>
      </div>
  
      <!-- Modal de confirmación para eliminar flujo -->
      <div v-if="deleteDialog.show" class="modal-overlay" @click.self="deleteDialog.show = false">
        <div class="modal-card">
          <div class="modal-header">
            <h3>Eliminar automatización</h3>
            <button @click="deleteDialog.show = false" class="close-btn">
              <i class="pi pi-times"></i>
            </button>
          </div>
          <div class="modal-body">
            <p>¿Estás seguro de que quieres eliminar la automatización <strong>{{ deleteDialog.flow?.nombre }}</strong>?</p>
            <p class="warning-text">Esta acción no se puede deshacer y se perderán todas las configuraciones asociadas.</p>
          </div>
          <div class="modal-footer">
            <button @click="deleteDialog.show = false" class="cancel-btn">Cancelar</button>
            <button @click="deleteFlow(deleteDialog.flow.id_workflow)" class="confirm-btn" :disabled="deleting">
              <i v-if="deleting" class="pi pi-spinner pi-spin"></i>
              <span v-else>Eliminar</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import { ref, computed, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  
  export default {
    setup() {
      const flows = ref([]);
      const loading = ref(true);
      const deleting = ref(false);
      const searchText = ref('');
      const router = useRouter();
      
      const deleteDialog = ref({
        show: false,
        flow: null
      });
  
      const filteredFlows = computed(() => {
        if (!searchText.value) return flows.value;
        
        const searchLower = searchText.value.toLowerCase();
        return flows.value.filter(flow => 
          flow.nombre.toLowerCase().includes(searchLower) || 
          flow.descripcion.toLowerCase().includes(searchLower)
        );
      });
  
      const fetchFlows = async () => {
        loading.value = true;
        try {
          const response = await axios.get('/api/workflows');
          flows.value = response.data;
        } catch (error) {
          console.error('Error al obtener los workflows:', error);
        } finally {
          loading.value = false;
        }
      };
  
      const confirmDelete = (flow) => {
        deleteDialog.value.flow = flow;
        deleteDialog.value.show = true;
      };
  
      const deleteFlow = async (id) => {
        deleting.value = true;
        try {
          await axios.delete(`/api/workflows/${id}`);
          await fetchFlows();
          deleteDialog.value.show = false;
        } catch (error) {
          console.error('Error al eliminar el workflow:', error);
        } finally {
          deleting.value = false;
        }
      };
  
  
      const addFlow = () => {
        
        router.push('/app/flows/flow');
      };
  
      const formatDate = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('es-ES', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric'
        }).format(date);
      };
  
      const getRandomColor = (id) => {
        const colors = [
          '#0078d4', // Azul de Microsoft
          '#50e6ff', // Cyan claro
          '#6264a7', // Morado Teams
          '#0099bc', // Azul verdoso
          '#ffaa44', // Naranja
          '#f7630c', // Naranja Power Automate
          '#107c10', // Verde
          '#7719aa', // Morado fuerte
          '#5558af', // Azul morado
          '#847545'  // Marrón
        ];
        return colors[id % colors.length];
      };
  
      onMounted(() => {
        fetchFlows();
      });
  
      return {
        flows,
        loading,
        deleting,
        searchText,
        filteredFlows,
        deleteDialog,
        fetchFlows,
        confirmDelete,
        deleteFlow,
        addFlow,
        formatDate,
        getRandomColor
      };
    }
  };
  </script>
  
  <style scoped>
  .flows-page {
    padding: 24px;
    background-color: var(--surface-ground, #f5f5f5);
    min-height: calc(100vh - 70px);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  .flows-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
  }
  
  .title-section h1 {
    font-size: 28px;
    font-weight: 600;
    margin: 0;
    color: var(--text-color, #333);
  }
  
  .subtitle {
    margin: 8px 0 0;
    color: var(--text-secondary-color, #666);
    font-size: 16px;
  }
  
  .create-flow-btn {
    display: flex;
    align-items: center;
    background-color: #0078d4;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .create-flow-btn:hover {
    background-color: #106ebe;
  }
  
  .create-flow-btn i {
    margin-right: 8px;
    font-size: 16px;
  }
  
  .flows-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
  }
  
  .search-container {
    flex: 1;
    max-width: 400px;
  }
  
  .search-input {
    width: 100%;
    padding: 8px 8px 8px 36px;
    border: 1px solid var(--surface-border, #ddd);
    border-radius: 4px;
    background-color: var(--surface-card, white);
    font-size: 14px;
  }
  
  .p-input-icon-left {
    position: relative;
    display: block;
    width: 100%;
  }
  
  .p-input-icon-left i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary-color, #666);
  }
  
  .view-options {
    display: flex;
    gap: 8px;
  }
  
  .view-btn {
    background-color: var(--surface-card, white);
    border: 1px solid var(--surface-border, #ddd);
    border-radius: 4px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
  
  .view-btn.active {
    background-color: #f0f0f0;
    border-color: #0078d4;
    color: #0078d4;
  }
  
  .flows-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
  }
  
  .flow-card {
    background-color: var(--surface-card, white);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
  }
  
  .flow-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
  }
  
  .flow-card-header {
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    background-color: #0078D4!important;
  }
  
  .flow-icon {
    width: 48px;
    height: 48px;
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .flow-icon i {
    font-size: 24px;
  }
  
  .flow-card-body {
    padding: 16px;
    flex-grow: 1;
  }
  
  .flow-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 8px;
    color: var(--text-color, #333);
  }
  
  .flow-description {
    color: var(--text-secondary-color, #666);
    font-size: 14px;
    margin: 0 0 16px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  
  .flow-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: var(--text-secondary-color, #666);
  }
  
  .flow-status {
    display: flex;
    align-items: center;
  }
  
  .status-dot {
    font-size: 8px;
    margin-right: 4px;
    color: var(--text-secondary-color, #666);
  }
  
  .flow-status.active .status-dot {
    color: #10b981;
  }
  
  .flow-card-footer {
    padding: 12px 16px;
    display: flex;
    gap: 8px;
    border-top: 1px solid var(--surface-border, #ddd);
  }
  
  .flow-action-btn {
    flex: 1;
    padding: 8px;
    border-radius: 4px;
    border: none;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .flow-action-btn i {
    margin-right: 6px;
    font-size: 14px;
  }
  
  .flow-action-btn.primary {
    background-color: #f0f7ff;
    color: #0078d4;
  }
  
  .flow-action-btn.primary:hover {
    background-color: #deecf9;
  }
  
  .flow-action-btn.danger {
    background-color: #fff1f0;
    color: #d13438;
  }
  
  .flow-action-btn.danger:hover {
    background-color: #fde7e9;
  }
  
  .empty-state {
    text-align: center;
    padding: 60px 0;
  }
  
  .empty-content {
    max-width: 400px;
    margin: 0 auto;
  }
  
  .empty-icon {
    font-size: 48px;
    color: var(--text-secondary-color, #666);
    margin-bottom: 16px;
  }
  
  .empty-state h3 {
    font-size: 24px;
    font-weight: 600;
    margin: 0 0 8px;
    color: var(--text-color, #333);
  }
  
  .empty-state p {
    color: var(--text-secondary-color, #666);
    margin-bottom: 24px;
  }
  
  .loading-state {
    text-align: center;
    padding: 60px 0;
  }
  
  .loading-icon {
    font-size: 36px;
    color: #0078d4;
    margin-bottom: 16px;
  }
  
  .loading-state p {
    color: var(--text-secondary-color, #666);
  }
  
  /* Modal de confirmación */
  .modal-overlay {
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
  }
  
  .modal-card {
    background-color: var(--surface-card, white);
    border-radius: 8px;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    animation: modalFadeIn 0.2s ease;
  }
  
  @keyframes modalFadeIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--surface-border, #ddd);
  }
  
  .modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: var(--text-color, #333);
  }
  
  .close-btn {
    background: none;
    border: none;
    font-size: 16px;
    color: var(--text-secondary-color, #666);
    cursor: pointer;
  }
  
  .modal-body {
    padding: 20px;
  }
  
  .modal-body p {
    margin: 0 0 16px;
    color: var(--text-color, #333);
  }
  
  .warning-text {
    color: #d13438;
    font-size: 14px;
  }
  
  .modal-footer {
    padding: 16px 20px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    border-top: 1px solid var(--surface-border, #ddd);
  }
  
  .cancel-btn {
    background-color: var(--surface-ground, #f5f5f5);
    border: 1px solid var(--surface-border, #ddd);
    border-radius: 4px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
  }
  
  .confirm-btn {
    background-color: #d13438;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .confirm-btn:disabled {
    opacity: 0.7;
    cursor: default;
  }
  
  .confirm-btn i {
    margin-right: 8px;
  }
  
  /* Responsive styles */
  @media (max-width: 768px) {
    .flows-header {
      flex-direction: column;
      align-items: flex-start;
    }
    
    .actions-section {
      margin-top: 16px;
      width: 100%;
    }
    
    .create-flow-btn {
      width: 100%;
      justify-content: center;
    }
    
    .flows-filters {
      flex-direction: column;
      gap: 16px;
    }
    
    .search-container {
      width: 100%;
      max-width: none;
    }
    
    .view-options {
      align-self: flex-end;
    }
    
    .flows-grid {
      grid-template-columns: 1fr;
    }
    
    .modal-card {
      width: 90%;
      margin: 0 16px;
    }
  }
  /* Añadir estos estilos al final del archivo */

/* Tema oscuro para flows */
.dark-theme .flows-page,
body.dark-theme .flows-page {
  background-color: #121212;
  color: #e4e6eb;
}

.dark-theme .title-section h1,
body.dark-theme .title-section h1 {
  color: #e4e6eb;
}

.dark-theme .subtitle,
body.dark-theme .subtitle {
  color: #adb5bd;
}

.dark-theme .search-input,
body.dark-theme .search-input {
  background-color: #1e1e2e;
  border-color: #383a46;
  color: #e4e6eb;
}

.dark-theme .view-btn,
body.dark-theme .view-btn {
  background-color: #1e1e2e;
  border-color: #383a46;
  color: #adb5bd;
}

.dark-theme .view-btn.active,
body.dark-theme .view-btn.active {
  background-color: rgba(26, 0, 255, 0.2);
  border-color: #1A00FF;
  color: #1A00FF;
}

.dark-theme .flow-card,
body.dark-theme .flow-card {
  background-color: #1e1e2e;
}

.dark-theme .flow-title,
body.dark-theme .flow-title {
  color: #e4e6eb;
}

.dark-theme .flow-description,
body.dark-theme .flow-description {
  color: #adb5bd;
}

.dark-theme .flow-card-footer,
body.dark-theme .flow-card-footer {
  border-top-color: #383a46;
}

.dark-theme .flow-action-btn.primary,
body.dark-theme .flow-action-btn.primary {
  background-color: rgba(0, 120, 212, 0.15);
}

.dark-theme .flow-action-btn.danger,
body.dark-theme .flow-action-btn.danger {
  background-color: rgba(209, 52, 56, 0.15);
}

.dark-theme .empty-icon,
body.dark-theme .empty-icon {
  color: #adb5bd;
}

.dark-theme .empty-state h3,
body.dark-theme .empty-state h3 {
  color: #e4e6eb;
}

.dark-theme .empty-state p,
body.dark-theme .empty-state p {
  color: #adb5bd;
}

.dark-theme .modal-card,
body.dark-theme .modal-card {
  background-color: #1e1e2e;
}

.dark-theme .modal-header,
body.dark-theme .modal-header {
  border-bottom-color: #383a46;
}

.dark-theme .modal-body p,
body.dark-theme .modal-body p {
  color: #e4e6eb;
}

.dark-theme .modal-footer,
body.dark-theme .modal-footer {
  border-top-color: #383a46;
}

.dark-theme .cancel-btn,
body.dark-theme .cancel-btn {
  background-color: #24252d;
  border-color: #383a46;
  color: #e4e6eb;
}
  </style>