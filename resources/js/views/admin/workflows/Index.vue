<template>
  <div class="row justify-content-center my-2">
    <div class="col-md-12">
      <div class="card border-0">
        <!-- Card header for Workflows -->
        <div class="card-header bg-transparent">
          <h5 class="float-start">Administración de Workflows</h5>
        </div>
  
        <!-- Cuerpo de la tarjeta -->
        <div class="card-body shadow-sm">
          <!-- Barra de búsqueda -->
          <div class="mb-3">
            <input
              v-model="search_global"
              type="text"
              placeholder="Buscar workflows..."
              class="form-control w-25"
            />
          </div>
          
          <!-- Loading indicator -->
          <div v-if="isLoading" class="text-center my-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Cargando workflows...</span>
            </div>
            <p class="mt-2">Cargando workflows...</p>
          </div>
          
          <!-- Tabla responsiva -->
          <div v-else class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Usuario</th>
                  <th>Fecha Creación</th>
                  <th>Acciones</th>
                  <th>Operaciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="workflow in filteredWorkflows" :key="workflow.id_workflow">
                  <td>{{ workflow.id_workflow }}</td>
                  <td>{{ workflow.nombre }}</td>
                  <td>{{ workflow.user_name || 'No disponible' }}</td>
                  
                  <td>{{ formatDate(workflow.created_at) }}</td>
                  <td>
                    <button 
                      @click="showWorkflowActions(workflow)" 
                      class="badge custom-primary-badge border-0"
                    >
                      Ver Acciones
                    </button>
                  </td>
                  <td>
                    <button
                      @click="confirmDeleteWorkflow(workflow.id_workflow)"
                      class="badge bg-danger border-0"
                    >
                      Eliminar
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Mensaje si no hay workflows -->
          <div v-if="!isLoading && filteredWorkflows.length === 0" class="text-center my-4">
            <p class="text-muted">No se encontraron workflows</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal para ver acciones del workflow -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-container">
        <div class="modal-header">
          <h4>Acciones del Workflow: {{ selectedWorkflow.nombre }}</h4>
          <button @click="closeModal" class="close-button">&times;</button>
        </div>
        <div class="modal-body">
          <div v-if="loadingActions" class="text-center my-3">
            <div class="spinner-border spinner-border-sm" role="status">
              <span class="visually-hidden">Cargando acciones...</span>
            </div>
            <span class="ms-2">Cargando acciones...</span>
          </div>
          <div v-else-if="workflowActions.length === 0" class="text-center my-3">
            <p class="text-muted">Este workflow no tiene acciones configuradas</p>
          </div>
          <div v-else>
            <table class="table">
              <thead>
                <tr>
                  <th>Orden</th>
                  <th>Tipo</th>
                  <th>Nombre</th>
                  <th>Descripción</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="action in workflowActions" :key="action.id_action">
                  <td>{{ action.order_index + 1 }}</td>
                  <td>
                    <span class="badge" :class="getActionBadgeClass(action.action_type)">
                      {{ action.action_type }}
                    </span>
                  </td>
                  <td>{{ action.name }}</td>
                  <td>{{ action.description || 'Sin descripción' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="closeModal" class="btn btn-secondary">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

// Variables reactivas
const workflows = ref([]);
const isLoading = ref(true);
const search_global = ref("");
const selectedWorkflow = ref({});
const workflowActions = ref([]);
const showModal = ref(false);
const loadingActions = ref(false);

// Obtener todos los workflows al montar el componente
onMounted(async () => {
  await getAllWorkflows();
});

// Filtrar workflows según la búsqueda
const filteredWorkflows = computed(() => {
  if (!search_global.value) {
    return workflows.value;
  }
  
  const searchTerm = search_global.value.toLowerCase();
  return workflows.value.filter(workflow => 
    workflow.nombre.toLowerCase().includes(searchTerm) ||
    (workflow.user_name && workflow.user_name.toLowerCase().includes(searchTerm)) ||
    workflow.trigger_type?.toLowerCase().includes(searchTerm)
  );
});

// Función para obtener todos los workflows
const getAllWorkflows = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get('/api/admin/all-workflows');
    workflows.value = response.data.map(workflow => {
      // Intentar obtener el nombre del usuario creador
      const userName = workflow.user ? workflow.user.name : 'Usuario Desconocido';
      return {
        ...workflow,
        user_name: userName
      };
    });
  } catch (error) {
    console.error('Error al cargar workflows:', error);
    Swal.fire({
      title: 'Error',
      text: 'No se pudieron cargar los workflows',
      icon: 'error'
    });
  } finally {
    isLoading.value = false;
  }
};

// Mostrar acciones de un workflow específico
const showWorkflowActions = async (workflow) => {
  selectedWorkflow.value = workflow;
  showModal.value = true;
  loadingActions.value = true;
  
  try {
    const response = await axios.get(`/api/workflows-action/${workflow.id_workflow}`);
    workflowActions.value = response.data;
  } catch (error) {
    console.error('Error al cargar acciones del workflow:', error);
    Swal.fire({
      title: 'Error',
      text: 'No se pudieron cargar las acciones del workflow',
      icon: 'error'
    });
  } finally {
    loadingActions.value = false;
  }
};

// Cerrar el modal
const closeModal = () => {
  showModal.value = false;
  selectedWorkflow.value = {};
  workflowActions.value = [];
};

// Confirmar eliminación de un workflow
const confirmDeleteWorkflow = (id) => {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "Esta acción no se puede deshacer",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      deleteWorkflow(id);
    }
  });
};

// Eliminar un workflow
const deleteWorkflow = async (id) => {
  try {
    await axios.delete(`/api/workflows/${id}`);
    await getAllWorkflows();
    Swal.fire(
      '¡Eliminado!',
      'El workflow ha sido eliminado correctamente',
      'success'
    );
  } catch (error) {
    console.error('Error al eliminar workflow:', error);
    Swal.fire({
      title: 'Error',
      text: 'No se pudo eliminar el workflow',
      icon: 'error'
    });
  }
};

// Formatear fecha para mejor legibilidad
const formatDate = (dateString) => {
  if (!dateString) return '';
  
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

// Formatear tipo de trigger para mejor legibilidad
const formatTriggerType = (triggerType) => {
  if (!triggerType) return 'Desconocido';
  
  switch (triggerType) {
    case 'task_status':
      return 'Estado de Tarea';
    case 'manual':
      return 'Manual';
    default:
      return triggerType;
  }
};

// Obtener clase de badge para el trigger
const getTriggerBadgeClass = (triggerType) => {
  switch (triggerType) {
    case 'task_status':
      return 'bg-info';
    case 'manual':
      return 'bg-warning';
    default:
      return 'bg-secondary';
  }
};

// Obtener clase de badge para el estado
const getStatusBadgeClass = (status) => {
  return status === 'active' ? 'bg-success' : 'bg-secondary';
};

// Obtener clase de badge para tipo de acción
const getActionBadgeClass = (actionType) => {
  switch (actionType) {
    case 'Tarea':
      return 'bg-primary';
    case 'Enviar Email':
      return 'bg-info';
    default:
      return 'bg-secondary';
  }
};
</script>

<style scoped>
.custom-primary-badge {
  background-color: #3f359b;
  color: white;
  cursor: pointer;
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-container {
  background-color: white;
  width: 80%;
  max-width: 800px;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid #e0e0e0;
}

.modal-body {
  padding: 20px;
  max-height: 60vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 16px 20px;
  border-top: 1px solid #e0e0e0;
  display: flex;
  justify-content: flex-end;
}

.close-button {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #666;
}

/* Responsive styles */
@media (max-width: 768px) {
  .modal-container {
    width: 95%;
    max-height: 90vh;
  }
  
  .modal-body {
    max-height: 70vh;
  }
}

/* Estilo para tema oscuro */
:deep(.dark-theme .card),
:deep(body.dark-theme .card) {
  background-color: #1e1e2e !important;
  color: #e4e6eb !important;
}

:deep(.dark-theme .table),
:deep(body.dark-theme .table) {
  color: #e4e6eb !important;
}

:deep(.dark-theme .modal-container),
:deep(body.dark-theme .modal-container) {
  background-color: #1e1e2e !important;
  color: #e4e6eb !important;
}

:deep(.dark-theme .modal-header),
:deep(.dark-theme .modal-footer),
:deep(body.dark-theme .modal-header),
:deep(body.dark-theme .modal-footer) {
  border-color: #383a46 !important;
}
</style>