<template>
  <div class="row justify-content-center my-5">
      <div class="col-md-10">
          <div class="card border-0 shadow-sm">
              <div class="card-body">
                  <h4 class="mb-4">Crear Nuevo Tablero</h4>
                  <form @submit.prevent="submitForm">
                      <div class="mb-3">
                          <label for="nombre" class="form-label">Nombre del Tablero</label>
                          <input v-model="tablero.nombre" id="nombre" type="text" class="form-control">
                          <div class="text-danger mt-1">
                              {{ errors.nombre }}
                          </div>
                          <div class="text-danger mt-1">
                              <div v-for="message in validationErrors?.nombre">
                                  {{ message }}
                              </div>
                          </div>
                      </div>
                      <div class="mb-3">
                          <label for="color_fondo" class="form-label">Color de Fondo (opcional)</label>
                          <input v-model="tablero.color_fondo" id="color_fondo" type="color" class="form-control form-control-color">
                          <div class="text-danger mt-1">
                              {{ errors.color_fondo }}
                          </div>
                          <div class="text-danger mt-1">
                              <div v-for="message in validationErrors?.color_fondo">
                                  {{ message }}
                              </div>
                          </div>
                      </div>
                      <!-- Buttons -->
                      <div class="mt-4">
                          <button :disabled="isLoading" type="submit" class="btn btn-primary">
                              <div v-show="isLoading" class="spinner-border spinner-border-sm me-1" role="status">
                                  <span class="visually-hidden">Loading...</span>
                              </div>
                              <span v-if="isLoading">Procesando...</span>
                              <span v-else>Guardar</span>
                          </button>
                          <button type="button" class="btn btn-secondary ms-2" @click="resetForm">Cancelar</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
</template>

<script setup>
  import { onMounted, reactive, ref } from "vue";
  import axios from "axios";
  import { useForm, useField, defineRule } from "vee-validate";
  import { required } from "@/validation/rules";
  import { useRouter } from 'vue-router';
  
  defineRule('required', required);

  const router = useRouter();
  const validationErrors = ref(null);
  const isLoading = ref(false);

  // Define a validation schema
  const schema = {
      nombre: 'required'
  }
  
  // Create a form context with the validation schema
  const { validate, errors } = useForm({ validationSchema: schema })
  
  // Define actual fields for validation
  const { value: nombre } = useField('nombre', null, { initialValue: '' });
  const { value: color_fondo } = useField('color_fondo', null, { initialValue: '#ffffff' });

  const tablero = reactive({
      nombre,
      color_fondo,
      id_creador: ''  // Will be set automatically by the backend with Auth::id()
  })

  const getCurrentUser = async () => {
      try {
          const response = await axios.get('/api/user');
          tablero.id_creador = response.data.id;
      } catch (error) {
          console.error('Error al obtener el usuario actual:', error);
      }
  }

  const storeTablero = async (data) => {
      isLoading.value = true;
      validationErrors.value = null;
      
      try {
          const response = await axios.post('/api/tableros', data);
          alert('Tablero creado correctamente');
          // Redirect to the list of boards or to the new board
          router.push('/tableros');
      } catch (error) {
          if (error.response && error.response.data && error.response.data.errors) {
              validationErrors.value = error.response.data.errors;
          } else {
              alert('Ha ocurrido un error al crear el tablero');
              console.error('Error al guardar tablero:', error);
          }
      } finally {
          isLoading.value = false;
      }
  }

  function resetForm() {
      tablero.nombre = '';
      tablero.color_fondo = '#ffffff';
      // Option: go back to the list
      router.push('/tableros');
  }

  function submitForm() {
      validate().then(result => { 
          if (result.valid) storeTablero(tablero);
      });
  }

  onMounted(() => {
      getCurrentUser();
  })
</script>