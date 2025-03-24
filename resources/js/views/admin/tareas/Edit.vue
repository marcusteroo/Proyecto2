<template>
  <div class="row justify-content-center my-5">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h2 class="mb-4">Actualizar Tarea</h2>

          <form @submit.prevent="submitForm">
            <!-- Campo Título -->
            <div class="mb-3">
              <label for="tarea-titulo" class="form-label">Título</label>
              <input v-model="editedTarea.titulo" id="tarea-titulo" type="text" class="form-control">
              <div class="text-danger mt-1" v-if="validationErrors.titulo">
                <div v-for="(msg, index) in validationErrors.titulo" :key="index">
                  {{ msg }}
                </div>
              </div>
            </div>

            <!-- Campo Descripción -->
            <div class="mb-3">
              <label for="tarea-descripcion" class="form-label">Descripción</label>
              <textarea v-model="editedTarea.descripcion" id="tarea-descripcion" class="form-control"></textarea>
              <div class="text-danger mt-1" v-if="validationErrors.descripcion">
                <div v-for="(msg, index) in validationErrors.descripcion" :key="index">
                  {{ msg }}
                </div>
              </div>
            </div>

            <!-- Campo Estado -->
            <div class="mb-3">
              <label for="tarea-estado" class="form-label">Estado</label>
              <select v-model="editedTarea.estado" id="tarea-estado" class="form-control">
                <option value="pendiente">Pendiente</option>
                <option value="en_progreso">En Progreso</option>
                <option value="completada">Completada</option>
              </select>
              <div class="text-danger mt-1" v-if="validationErrors.estado">
                <div v-for="(msg, index) in validationErrors.estado" :key="index">
                  {{ msg }}
                </div>
              </div>
            </div>

            <!-- Botón de actualización -->
            <div class="mt-4">
              <button :disabled="isLoading" class="btn btn-primary">
                <span v-if="isLoading">Procesando...</span>
                <span v-else>Actualizar</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

// Estado reactivo para la tarea
const editedTarea = ref({
  titulo: '',
  descripcion: '',
  estado: ''
})

const isLoading = ref(false)
const validationErrors = ref({})

const route = useRoute()
const router = useRouter()

// Cargar datos de la tarea al montar
onMounted(async () => {
  try {
    isLoading.value = true
    const response = await axios.get(`/api/kanban/${route.params.id}`)
    
    const tarea = response.data.tarea || response.data
    
    editedTarea.value = {
      titulo: tarea.titulo || '',
      descripcion: tarea.descripcion || '',
      estado: tarea.estado || 'pendiente'
    }
  } catch (err) {
    console.error("Error al obtener la Tarea:", err)
  } finally {
    isLoading.value = false
  }
})

// Función para actualizar la tarea
async function submitForm() {
  try {
    isLoading.value = true
    validationErrors.value = {}

    await axios.put(`/api/kanban/${route.params.id}`, {
      titulo: editedTarea.value.titulo,
      descripcion: editedTarea.value.descripcion,
      estado: editedTarea.value.estado
    })

    router.push({ name: 'tareas.index' }) // Redirigir tras actualizar
  } catch (error) {
    if (error.response && error.response.status === 422) {
      validationErrors.value = error.response.data.errors
    } else {
      console.error("Error al actualizar la Tarea:", error)
    }
  } finally {
    isLoading.value = false
  }
}
</script>
