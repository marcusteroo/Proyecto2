<template>
    <div class="row justify-content-center my-5">
      <div class="col-md-10">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <form @submit.prevent="submitForm">
  
              <!-- ID Tablero (solo lectura) -->
              <div class="mb-3">
                <label for="id_tablero" class="form-label">ID Tablero</label>
                <input
                  v-model="tablero.id_tablero"
                  id="id_tablero"
                  type="text"
                  class="form-control"
                  disabled
                />
                <div class="text-danger mt-1">
                  {{ errors.id_tablero }}
                </div>
              </div>
  
              <!-- Nombre -->
              <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input
                  v-model="tablero.nombre"
                  id="nombre"
                  type="text"
                  class="form-control"
                />
                <div class="text-danger mt-1">
                  {{ errors.nombre }}
                </div>
              </div>
  
              <!-- ID Creador -->
              <div class="mb-3">
                <label for="id_creador" class="form-label">ID Creador</label>
                <input
                  v-model="tablero.id_creador"
                  id="id_creador"
                  type="text"
                  class="form-control"
                />
                <div class="text-danger mt-1">
                  {{ errors.id_creador }}
                </div>
              </div>
  
              <!-- Color Fondo -->
              <div class="mb-3">
                <label for="color_fondo" class="form-label">Color Fondo</label>
                <input
                  v-model="tablero.color_fondo"
                  id="color_fondo"
                  type="text"
                  class="form-control"
                />
                <div class="text-danger mt-1">
                  {{ errors.color_fondo }}
                </div>
              </div>
  
              <!-- Botón para guardar cambios -->
              <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                  Actualizar
                </button>
              </div>
  
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { onMounted, reactive, watchEffect } from 'vue'
  import { useRoute } from 'vue-router'
  import { usePrimeVue } from 'primevue/config'
  import { useToast } from 'primevue/usetoast'
  import { useForm, useField, defineRule } from 'vee-validate'
  import { required } from '@/validation/rules'
  
  // Importamos el composable
  import useTableros from '@/composables/kanbans'
  
  // Destructuramos, renombrando "tablero" a "postData" (si queremos)
  const { getTablero, updateTablero, tablero: postData } = useTableros()
  
  // Reglas de validación
  defineRule('required', required)
  
  // Esquema de validación
  const schema = {
    id_tablero: 'required',
    nombre: 'required',
    id_creador: 'required',
    color_fondo: 'required'
  }
  
  // Inicializamos formulario con vee-validate
  const { validate, errors } = useForm({ validationSchema: schema })
  
  const { value: id_tablero } = useField('id_tablero', null, { initialValue: '' })
  const { value: nombre } = useField('nombre', null, { initialValue: '' })
  const { value: id_creador } = useField('id_creador', null, { initialValue: '' })
  const { value: color_fondo } = useField('color_fondo', null, { initialValue: '' })
  
  // Tablero local que usaremos para bindear inputs
  const tablero = reactive({
    id_tablero,
    nombre,
    id_creador,
    color_fondo
  })
  
  // Router y notificaciones
  const route = useRoute()
  const $primevue = usePrimeVue()
  const toast = useToast()
  
  // Cargamos los datos al montar
  onMounted(() => {
    getTablero(route.params.id)
  })
  
  // Cada vez que cambie postData, actualizamos tablero local
  watchEffect(() => {
    if (!postData.value) return  // <-- Evita el error si postData.value es undefined
  
    tablero.id_tablero   = postData.value.id_tablero
    tablero.nombre       = postData.value.nombre
    tablero.id_creador   = postData.value.id_creador
    tablero.color_fondo  = postData.value.color_fondo
  })
  
  // Al enviar el formulario
  function submitForm() {
    validate().then(form => {
      if (form.valid) {
        updateTablero(tablero)
          .then(() => {
            toast.add({
              severity: 'success',
              summary: 'Tablero actualizado',
              detail: 'El tablero se ha actualizado correctamente.',
              life: 3000
            })
          })
          .catch(() => {
            toast.add({
              severity: 'error',
              summary: 'Error',
              detail: 'No se pudo actualizar el tablero.',
              life: 3000
            })
          })
      }
    })
  }
  </script>
  