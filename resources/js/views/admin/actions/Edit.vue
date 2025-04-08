<template>
  <div class="row justify-content-center my-5">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h2 class="mb-4">Actualizar Acción</h2>

          <form @submit.prevent="submitForm">
            <!-- Campo Nombre -->
            <div class="mb-3">
              <label for="action-nombre" class="form-label">Nombre</label>
              <input v-model="editedAction.name" id="action-nombre" type="text" class="form-control">
              <div class="text-danger mt-1" v-if="validationErrors.name">
                <div v-for="(msg, index) in validationErrors.name" :key="index">
                  {{ msg }}
                </div>
              </div>
            </div>

            <!-- Campo Descripción -->
            <div class="mb-3">
              <label for="action-description" class="form-label">Descripción</label>
              <textarea v-model="editedAction.description" id="action-description" class="form-control"></textarea>
              <div class="text-danger mt-1" v-if="validationErrors.description">
                <div v-for="(msg, index) in validationErrors.description" :key="index">
                  {{ msg }}
                </div>
              </div>
            </div>

            <!-- Campo Configuración (como JSON) -->
            <div class="mb-3">
              <label for="action-config" class="form-label">Configuración (JSON)</label>
              <textarea v-model="configJson" id="action-config" class="form-control" rows="5"></textarea>
              <div class="text-danger mt-1" v-if="validationErrors.config">
                <div v-for="(msg, index) in validationErrors.config" :key="index">
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
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { defineRule, useForm } from 'vee-validate'
import { required, min } from '@/validation/rules'

// Definir reglas de validación
defineRule('required', required)
defineRule('min', min)

// Esquema de validación
const schema = {
  name: 'required|min:3',
  description: 'required'
}

// Configurar useForm (sin useField, solo validamos el objeto completo)
const { validate } = useForm()

// Estado reactivo para la acción
const editedAction = ref({
  name: '',
  description: '',
  x_position: 0,
  y_position: 0,
  config: {}
})

// Manejo especial para config como JSON
const configJson = computed({
  get: () => {
    try {
      return JSON.stringify(editedAction.value.config, null, 2)
    } catch (e) {
      return '{}'
    }
  },
  set: (val) => {
    try {
      editedAction.value.config = JSON.parse(val)
    } catch (e) {
      // Si hay error de parseo, mantener el valor como string
      console.error('Error parsing JSON:', e)
    }
  }
})

const isLoading = ref(false)
const validationErrors = ref({})

const route = useRoute()
const router = useRouter()

// Cargar datos de la acción al montar
onMounted(async () => {
  try {
    isLoading.value = true
    const { data } = await axios.get(`/api/workflow-actions/${route.params.id}`)
    editedAction.value = data
  } catch (err) {
    console.error("Error al obtener la Acción:", err)
  } finally {
    isLoading.value = false
  }
})

// Función para actualizar la acción
async function submitForm() {
  // Validar antes de enviar
  const { valid, errors: fieldErrors } = await validate({
    name: editedAction.value.name,
    description: editedAction.value.description
  }, schema)
  
  if (!valid) {
    validationErrors.value = fieldErrors // Mostrar errores de validación en pantalla
    return
  }

  try {
    isLoading.value = true
    validationErrors.value = {} // Limpiar errores previos

    // Parsear JSON antes de enviar
    let configData
    try {
      configData = typeof editedAction.value.config === 'string' 
        ? JSON.parse(editedAction.value.config)
        : editedAction.value.config
    } catch (e) {
      validationErrors.value.config = ['Formato JSON inválido']
      isLoading.value = false
      return
    }

    await axios.put(`/api/workflow-actions/${route.params.id}`, {
      name: editedAction.value.name,
      description: editedAction.value.description,
      x_position: editedAction.value.x_position,
      y_position: editedAction.value.y_position,
      config: configData
    })

    router.push({ name: 'workflows.detail', params: { id: editedAction.value.id_workflow } }) // Redirigir al workflow
  } catch (error) {
    if (error.response && error.response.status === 422) {
      validationErrors.value = error.response.data.errors // Mostrar errores del backend
    } else {
      console.error("Error al actualizar la Acción:", error)
    }
  } finally {
    isLoading.value = false
  }
}
</script>