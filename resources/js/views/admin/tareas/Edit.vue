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

            <!-- Nota: Los campos 'estado' e 'id_tablero' no se muestran en el formulario, pero se envían al servidor -->
            <!-- Puedes incluirlos como inputs ocultos si es necesario
            <input type="hidden" v-model="editedTarea.estado" />
            <input type="hidden" v-model="editedTarea.id_tablero" />
            -->

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

// Estado reactivo para la tarea que editaremos
const editedTarea = ref({
  id_tarea: null,
  titulo: '',
  descripcion: '',
  estado: '',
  id_tablero: null,
})

const isLoading = ref(false)
const validationErrors = ref({})

const route = useRoute()
const router = useRouter()

// Al montar el componente, buscamos los datos de la tarea para precargar el formulario
onMounted(async () => {
  try {
    isLoading.value = true
    const response = await axios.get(`/api/kanban/${route.params.id}`)

    // Verificamos si la respuesta viene encapsulada en "tarea" o directamente en data
    const tarea = response.data.tarea || response.data

    // Asignamos todos los campos necesarios que luego enviamos en el PUT
    editedTarea.value = {
      id_tarea: tarea.id_tarea,
      titulo: tarea.titulo || '',
      descripcion: tarea.descripcion || '',
      estado: tarea.estado || 'pendiente',
      id_tablero: tarea.id_tablero || null
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

    // Enviamos todos los campos que el backend espera
    await axios.put(`/api/kanban/${route.params.id}`, {
      id_tarea: editedTarea.value.id_tarea,
      titulo: editedTarea.value.titulo,
      descripcion: editedTarea.value.descripcion,
      estado: editedTarea.value.estado,
      id_tablero: editedTarea.value.id_tablero,
    })

    router.push({ name: 'tareas.index' })
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
