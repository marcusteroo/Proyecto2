<template>
    <div class="row justify-content-center my-5">
      <div class="col-md-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="mb-4">Actualizar Tablero</h2>
  
            <form @submit.prevent="submitForm">
              <!-- Campo Nombre -->
              <div class="mb-3">
                <label for="tablero-nombre" class="form-label">Nombre</label>
                <input v-model="editedTablero.nombre" id="tablero-nombre" type="text" class="form-control">
                <div class="text-danger mt-1" v-if="validationErrors.nombre">
                  <div v-for="(msg, index) in validationErrors.nombre" :key="index">
                    {{ msg }}
                  </div>
                </div>
              </div>
  
              <!-- Campo Color de Fondo -->
              <div class="mb-3">
                <label for="tablero-color" class="form-label">Color de Fondo</label>
                <input v-model="editedTablero.color_fondo" id="tablero-color" type="text" class="form-control">
                <div class="text-danger mt-1" v-if="validationErrors.color_fondo">
                  <div v-for="(msg, index) in validationErrors.color_fondo" :key="index">
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
  import { defineRule, useForm } from 'vee-validate'
  import { required, min } from '@/validation/rules'
  
  // Definir reglas de validación
  defineRule('required', required)
  defineRule('min', min)
  
  // Esquema de validación
  const schema = {
    nombre: 'required|min:3',
    color_fondo: 'required'
  }
  
  // Configurar useForm (sin useField, solo validamos el objeto completo)
  const { validate } = useForm()
  
  // Estado reactivo para el tablero
  const editedTablero = ref({
    nombre: '',
    color_fondo: ''
  })
  
  const isLoading = ref(false)
  const validationErrors = ref({})
  
  const route = useRoute()
  const router = useRouter()
  
  // Cargar datos del tablero al montar
  onMounted(async () => {
    try {
      isLoading.value = true
      const { data } = await axios.get(`/api/tableros/${route.params.id}`)
      editedTablero.value = data
    } catch (err) {
      console.error("Error al obtener el Tablero:", err)
    } finally {
      isLoading.value = false
    }
  })
  
  // Función para actualizar el tablero
  async function submitForm() {
    // Validar antes de enviar
    const { valid, errors: fieldErrors } = await validate(editedTablero.value, schema)
    
    if (!valid) {
      validationErrors.value = fieldErrors // Mostrar errores de validación en pantalla
      return
    }
  
    try {
      isLoading.value = true
      validationErrors.value = {} // Limpiar errores previos
  
      await axios.put(`/api/tableros/${route.params.id}`, {
        nombre: editedTablero.value.nombre,
        color_fondo: editedTablero.value.color_fondo
      })
  
      router.push({ name: 'kanbans.index' }) // Redirigir tras actualizar
    } catch (error) {
      if (error.response && error.response.status === 422) {
        validationErrors.value = error.response.data.errors // Mostrar errores del backend
      } else {
        console.error("Error al actualizar el Tablero:", error)
      }
    } finally {
      isLoading.value = false
    }
  }
  </script>
  