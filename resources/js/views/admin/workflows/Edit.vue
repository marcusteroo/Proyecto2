<template>
  <div class="row justify-content-center my-5">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h2 class="mb-4">Actualizar Workflow</h2>

          <form @submit.prevent="submitForm">
            <!-- Campo Nombre -->
            <div class="mb-3">
              <label for="workflow-nombre" class="form-label">Nombre</label>
              <input v-model="editedWorkflow.nombre" id="workflow-nombre" type="text" class="form-control">
              <div class="text-danger mt-1" v-if="validationErrors.nombre">
                <div v-for="(msg, index) in validationErrors.nombre" :key="index">
                  {{ msg }}
                </div>
              </div>
            </div>

            <!-- Campo Descripción -->
            <div class="mb-3">
              <label for="workflow-descripcion" class="form-label">Descripción</label>
              <textarea v-model="editedWorkflow.descripcion" id="workflow-descripcion" class="form-control" rows="3"></textarea>
              <div class="text-danger mt-1" v-if="validationErrors.descripcion">
                <div v-for="(msg, index) in validationErrors.descripcion" :key="index">
                  {{ msg }}
                </div>
              </div>
            </div>

            <!-- Campo Trigger Type (simplificado) -->
            <div class="mb-3">
              <label for="trigger-type" class="form-label">Tipo de Trigger</label>
              <select v-model="editedWorkflow.trigger.type" id="trigger-type" class="form-control">
                <option value="manual">Manual</option>
                <option value="scheduled">Programado</option>
                <option value="event">Evento</option>
              </select>
              <div class="text-danger mt-1" v-if="validationErrors['trigger.type']">
                <div v-for="(msg, index) in validationErrors['trigger.type']" :key="index">
                  {{ msg }}
                </div>
              </div>
            </div>

            <!-- Botón de actualización -->
            <div class="mt-4">
              <button :disabled="isLoading" class="btn btn-primary">
                <div v-show="isLoading" class="spinner-border spinner-border-sm me-1" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <span v-if="isLoading">Procesando...</span>
                <span v-else>Actualizar</span>
              </button>
              <button type="button" class="btn btn-secondary ms-2" @click="cancelar">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { defineRule, useForm } from 'vee-validate'
import { required, min } from '@/validation/rules'

// Definir reglas de validación
defineRule('required', required)
defineRule('min', min)

// Esquema de validación
const schema = {
  nombre: 'required|min:3',
  descripcion: 'required',
  'trigger.type': 'required'
}

// Configurar useForm
const { validate } = useForm()

// Estado reactivo para el workflow
const editedWorkflow = reactive({
  nombre: '',
  descripcion: '',
  trigger: {
    type: 'manual'
  }
})

const isLoading = ref(false)
const validationErrors = ref({})

const route = useRoute()
const router = useRouter()

// Cargar datos del workflow al montar
onMounted(async () => {
  try {
    isLoading.value = true
    const { data } = await axios.get(`/api/workflows/${route.params.id}`)
    
    // Asignar datos al editedWorkflow
    editedWorkflow.nombre = data.nombre
    editedWorkflow.descripcion = data.descripcion
    
    // Parsear los trigger_params si existen
    if (data.trigger_params) {
      try {
        const triggerParams = JSON.parse(data.trigger_params)
        editedWorkflow.trigger = triggerParams
      } catch (e) {
        // Si hay error al parsear, usar un trigger por defecto
        editedWorkflow.trigger = { type: data.trigger_type || 'manual' }
      }
    } else {
      editedWorkflow.trigger = { type: data.trigger_type || 'manual' }
    }
    
  } catch (err) {
    console.error("Error al obtener el Workflow:", err)
  } finally {
    isLoading.value = false
  }
})

// Función para actualizar el workflow
async function submitForm() {
  // Validar antes de enviar
  const { valid, errors: fieldErrors } = await validate({
    nombre: editedWorkflow.nombre,
    descripcion: editedWorkflow.descripcion,
    'trigger.type': editedWorkflow.trigger.type
  }, schema)
  
  if (!valid) {
    validationErrors.value = fieldErrors // Mostrar errores de validación en pantalla
    return
  }

  try {
    isLoading.value = true
    validationErrors.value = {} // Limpiar errores previos

    await axios.put(`/api/workflows/${route.params.id}`, {
      nombre: editedWorkflow.nombre,
      descripcion: editedWorkflow.descripcion,
      trigger: editedWorkflow.trigger
    })

    router.push('/admin/workflows') // Redirigir tras actualizar
  } catch (error) {
    if (error.response && error.response.status === 422) {
      validationErrors.value = error.response.data.errors // Mostrar errores del backend
    } else {
      console.error("Error al actualizar el Workflow:", error)
    }
  } finally {
    isLoading.value = false
  }
}

function cancelar() {
  router.push('/admin/workflows')
}
</script>