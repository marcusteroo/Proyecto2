<template>
  <div class="row justify-content-center my-5">
      <div class="col-md-10">
          <div class="card border-0 shadow-sm">
              <div class="card-body">
                  <form @submit.prevent="submitForm">
                      <div class="mb-3">
                          <label for="titulo" class="form-label">Título</label>
                          <input v-model="post.titulo" id="titulo" type="text" class="form-control">
                          <div class="text-danger mt-1">
                              {{ errors.titulo }}
                          </div>
                          <div class="text-danger mt-1">
                              <div v-for="message in validationErrors?.titulo">
                                  {{ message }}
                              </div>
                          </div>
                      </div>
                      <div class="mb-3">
                          <label for="descripcion" class="form-label">Descripción</label>
                          <textarea v-model="post.descripcion" id="descripcion" class="form-control" rows="3"></textarea>
                          <div class="text-danger mt-1">
                              {{ errors.descripcion }}
                          </div>
                          <div class="text-danger mt-1">
                              <div v-for="message in validationErrors?.descripcion">
                                  {{ message }}
                              </div>
                          </div>
                      </div>
                      <div class="mb-3">
                          <label for="estado" class="form-label">Estado</label>
                          <select v-model="post.estado" id="estado" class="form-control">
                              <option value="Pendiente">Pendiente</option>
                              <option value="En Progreso">En Progreso</option>
                              <option value="Completado">Completado</option>
                          </select>
                          <div class="text-danger mt-1">
                              {{ errors.estado }}
                          </div>
                          <div class="text-danger mt-1">
                              <div v-for="message in validationErrors?.estado">
                                  {{ message }}
                              </div>
                          </div>
                      </div>
                      <div class="mb-3">
                          <label for="id_tablero" class="form-label">Tablero</label>
                          <select v-model="post.id_tablero" id="id_tablero" class="form-control">
                              <option v-for="board in boards" :key="board.id" :value="board.id">{{ board.title }}</option>
                          </select>
                          <div class="text-danger mt-1">
                              {{ errors.id_tablero }}
                          </div>
                          <div class="text-danger mt-1">
                              <div v-for="message in validationErrors?.id_tablero">
                                  {{ message }}
                              </div>
                          </div>
                      </div>
                      <!-- Buttons -->
                      <div class="mt-4">
                          <button :disabled="isLoading" class="btn btn-primary">
                              <div v-show="isLoading" class=""></div>
                              <span v-if="isLoading">Processing...</span>
                              <span v-else>Save</span>
                          </button>
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
  
  defineRule('required', required);

  const boards = ref([]);
  const validationErrors = ref(null);
  const isLoading = ref(false);

  // Define a validation schema
  const schema = {
      titulo: 'required',
      descripcion: 'required',
      estado: 'required',
      id_tablero: 'required'
  }
  
  // Create a form context with the validation schema
  const { validate, errors } = useForm({ validationSchema: schema })
  
  // Define actual fields for validation
  const { value: titulo } = useField('titulo', null, { initialValue: '' });
  const { value: descripcion } = useField('descripcion', null, { initialValue: '' });
  const { value: estado } = useField('estado', null, { initialValue: 'Pendiente' });
  const { value: id_tablero } = useField('id_tablero', null, { initialValue: '' });

  const post = reactive({
      titulo,
      descripcion,
      estado,
      id_tablero,
  })

  const getBoards = async () => {
      try {
          const response = await axios.get('/api/kanban');
          boards.value = response.data;
      } catch (error) {
          console.error('Error al cargar los tableros:', error);
      }
  }

  const storeTarea = async (data) => {
      isLoading.value = true;
      validationErrors.value = null;
      
      try {
          await axios.post('/api/kanban', data);
          // Redireccionar o mostrar mensaje de éxito
          alert('Tarea creada correctamente');
          // Opcional: resetear formulario
          resetForm();
      } catch (error) {
          if (error.response && error.response.data && error.response.data.errors) {
              validationErrors.value = error.response.data.errors;
          } else {
              alert('Ha ocurrido un error');
              console.error('Error al guardar tarea:', error);
          }
      } finally {
          isLoading.value = false;
      }
  }

  function resetForm() {
      post.titulo = '';
      post.descripcion = '';
      post.estado = 'Pendiente';
      post.id_tablero = '';
  }

  function submitForm() {
      validate().then(form => { 
          if (form.valid) storeTarea(post) 
      });
  }

  onMounted(() => {
      getBoards();
  })
</script>