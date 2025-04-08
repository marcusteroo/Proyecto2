<template>
  <div class="row justify-content-center my-2">
    <div class="col-md-12">
      <div class="card border-0">
        <!-- Card header for Actions -->
        <div class="card-header bg-transparent">
          <h5 class="float-start">Acciones de Workflow</h5>
          <router-link
            v-if="workflowId" 
            :to="{ name: 'workflow-actions.create', params: { workflowId: workflowId } }"
            class="btn btn-primary btn-sm float-end"
          >
            Añadir Acción
          </router-link>
        </div>
  
        <!-- Cuerpo de la tarjeta -->
        <div class="card-body shadow-sm">
          <!-- (Opcional) Barra de búsqueda o filtros -->
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
            <table class="table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Tipo</th>
                  <th>Descripción</th>
                  <th>Orden</th>
                  <th>Creado</th>
                  <th>Actualizado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="action in actions" :key="action.id_action">
                  <td>{{ action.id_action }}</td>
                  <td>{{ action.name }}</td>
                  <td>{{ action.action_type }}</td>
                  <td>{{ action.description }}</td>
                  <td>{{ action.order_index }}</td>
                  <td>{{ action.created_at }}</td>
                  <td>{{ action.updated_at }}</td>
                  <td>
                    <router-link
                      :to="{ name: 'workflow-actions.edit', params: { id: action.id_action } }"
                      class="badge bg-primary"
                    >
                      Editar
                    </router-link>
                    <a
                      href="#"
                      @click.prevent="deleteAction(action.id_action)"
                      class="ms-2 badge bg-danger"
                    >
                      Eliminar
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
  
        <!-- Pie de la tarjeta -->
        <div class="card-footer">
          <button 
            v-if="workflowId" 
            @click="goBackToWorkflow" 
            class="btn btn-secondary btn-sm"
          >
            Volver al Workflow
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script setup>
import { ref, onMounted } from "vue";
import { useAbility } from "@casl/vue";
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

// Obtenemos la función can() de CASL
const { can } = useAbility();

// Referencias para mantener el estado
const actions = ref([]);
const search_global = ref("");
const isLoading = ref(false);
const route = useRoute();
const router = useRouter();
const workflowId = ref(null);

// Función para cargar las acciones de un workflow
async function getActions(id) {
  try {
    isLoading.value = true;
    const { data } = await axios.get(`/api/workflows-action/${id}`);
    actions.value = data;
  } catch (error) {
    console.error("Error al obtener las acciones:", error);
  } finally {
    isLoading.value = false;
  }
}

// Función para eliminar una acción
async function deleteAction(id) {
  if (!confirm('¿Estás seguro de que deseas eliminar esta acción?')) {
    return;
  }
  
  try {
    await axios.delete(`/api/workflow-actions/${id}`);
    // Recargar las acciones después de eliminar
    if (workflowId.value) {
      getActions(workflowId.value);
    }
  } catch (error) {
    console.error("Error al eliminar la acción:", error);
  }
}

// Función para volver al detalle del workflow
function goBackToWorkflow() {
  router.push({ name: 'workflows.detail', params: { id: workflowId.value } });
}

// Al montar el componente, obtener el ID del workflow de los parámetros y cargar las acciones
onMounted(() => {
  const id = route.params.workflowId;
  if (id) {
    workflowId.value = id;
    getActions(id);
  }
});
</script>