<template>
  <div class="row justify-content-center my-2">
    <div class="col-md-12">
      <div class="card border-0">
        <!-- Card header for Workflows -->
        <div class="card-header bg-transparent">
          <h5 class="float-start">Workflows</h5>
        </div>
  
        <!-- Cuerpo de la tarjeta -->
        <div class="card-body shadow-sm">
          <!-- Barra de búsqueda -->
          <div class="mb-3">
            <input
              v-model="search_global"
              type="text"
              placeholder="Buscar..."
              class="form-control w-25"
            />
          </div>
          
          <!-- Tabla responsiva -->
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Descripción</th>
                  <th>Tipo de Trigger</th>
                  <th>Estado</th>
                  <th>Creado por</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="workflow in filteredWorkflows" :key="workflow.id_workflow">
                  <td>{{ workflow.id_workflow }}</td>
                  <td>{{ workflow.nombre }}</td>
                  <td>{{ workflow.descripcion ? workflow.descripcion.slice(0, 50) + (workflow.descripcion.length > 50 ? '...' : '') : '' }}</td>
                  <td>{{ workflow.trigger_type }}</td>
                  <td>
                    <span :class="getStatusBadgeClass(workflow.status)">
                      {{ workflow.status }}
                    </span>
                  </td>
                  <td>
                    <span v-if="workflow.is_shared">
                      {{ workflow.shared_by }}
                      <span class="badge bg-info ms-1">{{ workflow.shared_role }}</span>
                    </span>
                    <span v-else>Tú</span>
                  </td>
                  <td class="d-flex gap-1">
                    <router-link
                      :to="{ name: 'workflows.edit', params: { id: workflow.id_workflow } }"
                      class="badge bg-primary"
                    >
                      Editar
                    </router-link>
                    
                    <a
                      href="#"
                      @click.prevent="executeWorkflow(workflow.id_workflow)"
                      class="badge bg-success"
                    >
                      Ejecutar
                    </a>
                    
                    <a
                      v-if="workflow.is_owner"
                      href="#"
                      @click.prevent="showShareDialog(workflow.id_workflow)"
                      class="badge bg-info"
                    >
                      Compartir
                    </a>
                    
                    <a
                      v-if="workflow.is_owner"
                      href="#"
                      @click.prevent="deleteWorkflow(workflow.id_workflow)"
                      class="badge bg-danger"
                    >
                      Eliminar
                    </a>
                  </td>
                </tr>
                <tr v-if="workflows.length === 0">
                  <td colspan="7" class="text-center">No hay workflows disponibles</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal de compartir workflow -->
    <div v-if="showShare" class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Compartir Workflow</h5>
            <button type="button" class="btn-close" @click="closeShareDialog"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Usuario</label>
              <select v-model="shareData.user_id" class="form-select">
                <option value="" disabled>Selecciona un usuario</option>
                <option v-for="user in potentialUsers" :key="user.id" :value="user.id">
                  {{ user.name }} ({{ user.email }})
                </option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Rol</label>
              <select v-model="shareData.rol" class="form-select">
                <option value="editor">Editor</option>
                <option value="espectador">Espectador</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeShareDialog">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="shareWorkflow" :disabled="isSharing">
              <span v-if="isSharing">Compartiendo...</span>
              <span v-else>Compartir</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script setup>
import { ref, computed, onMounted, reactive } from "vue";
import axios from "axios";
import { useAbility } from "@casl/vue";

// Obtenemos la función can() de CASL
const { can } = useAbility();

// Estado de los workflows
const workflows = ref([]);
const isLoading = ref(false);
const search_global = ref("");

// Estado para compartir workflows
const showShare = ref(false);
const selectedWorkflowId = ref(null);
const potentialUsers = ref([]);
const isSharing = ref(false);
const shareData = reactive({
  user_id: "",
  rol: "espectador"
});

// Filtra workflows según la búsqueda
const filteredWorkflows = computed(() => {
  if (!search_global.value) return workflows.value;
  
  const searchTerm = search_global.value.toLowerCase();
  return workflows.value.filter(workflow => 
    workflow.nombre.toLowerCase().includes(searchTerm) ||
    (workflow.descripcion && workflow.descripcion.toLowerCase().includes(searchTerm)) ||
    workflow.trigger_type.toLowerCase().includes(searchTerm)
  );
});

// Obtener todos los workflows
async function getWorkflows() {
  try {
    isLoading.value = true;
    const { data } = await axios.get('/api/workflows');
    workflows.value = data;
  } catch (error) {
    console.error("Error al obtener workflows:", error);
  } finally {
    isLoading.value = false;
  }
}

// Eliminar workflow
async function deleteWorkflow(id) {
  if (!confirm('¿Estás seguro que deseas eliminar este workflow?')) return;
  
  try {
    await axios.delete(`/api/workflows/${id}`);
    getWorkflows(); // Recargar la lista
  } catch (error) {
    console.error("Error al eliminar workflow:", error);
    alert('No se pudo eliminar el workflow');
  }
}

// Ejecutar workflow
async function executeWorkflow(id) {
  try {
    const { data } = await axios.post(`/api/workflows/${id}/execute`);
    alert(data.message);
  } catch (error) {
    console.error("Error al ejecutar workflow:", error);
    alert('Error al ejecutar el workflow');
  }
}

// Obtener usuarios para compartir
async function getPotentialUsers() {
  try {
    const { data } = await axios.get('/api/workflows/potential-users');
    potentialUsers.value = data;
  } catch (error) {
    console.error("Error al obtener usuarios:", error);
  }
}

// Mostrar diálogo para compartir
function showShareDialog(workflowId) {
  selectedWorkflowId.value = workflowId;
  getPotentialUsers();
  showShare.value = true;
}

// Cerrar diálogo de compartir
function closeShareDialog() {
  showShare.value = false;
  shareData.user_id = "";
  shareData.rol = "espectador";
}

// Compartir workflow
async function shareWorkflow() {
  if (!shareData.user_id) {
    alert("Por favor selecciona un usuario");
    return;
  }
  
  try {
    isSharing.value = true;
    await axios.post(`/api/workflows/${selectedWorkflowId.value}/share`, shareData);
    alert("Workflow compartido exitosamente");
    closeShareDialog();
  } catch (error) {
    console.error("Error al compartir workflow:", error);
    if (error.response && error.response.data && error.response.data.message) {
      alert(error.response.data.message);
    } else {
      alert("Error al compartir el workflow");
    }
  } finally {
    isSharing.value = false;
  }
}

// Obtener clase CSS para el badge de estado
function getStatusBadgeClass(status) {
  switch(status) {
    case 'active':
      return 'badge bg-success';
    case 'inactive':
      return 'badge bg-secondary';
    case 'error':
      return 'badge bg-danger';
    default:
      return 'badge bg-info';
  }
}

// Al montar el componente, cargamos los workflows
onMounted(() => {
  getWorkflows();
});
</script>