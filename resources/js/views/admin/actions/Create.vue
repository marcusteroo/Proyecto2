<template>
    <div class="row justify-content-center my-5">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="mb-4">Crear Nueva Acción</h4>
                    <form @submit.prevent="submitForm">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Acción</label>
                            <input v-model="action.name" id="nombre" type="text" class="form-control">
                            <div class="text-danger mt-1">
                                {{ errors.name }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.name">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea v-model="action.description" id="description" class="form-control" rows="3"></textarea>
                            <div class="text-danger mt-1">
                                {{ errors.description }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.description">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="config" class="form-label">Configuración (JSON)</label>
                            <textarea v-model="configJson" id="config" class="form-control" rows="5"></textarea>
                            <div class="text-danger mt-1">
                                {{ errors.config }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.config">
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
    import { reactive, ref, computed } from "vue";
    import axios from "axios";
    import { useForm, defineRule } from "vee-validate";
    import { required } from "@/validation/rules";
    import { useRoute, useRouter } from 'vue-router';
    
    defineRule('required', required);
  
    const route = useRoute();
    const router = useRouter();
    const validationErrors = ref(null);
    const isLoading = ref(false);
  
    // Obtener el ID del workflow de los parámetros de la ruta
    const workflowId = route.params.workflowId;
  
    // Define a validation schema
    const schema = {
        name: 'required',
        description: 'required'
    }
    
    // Create a form context with the validation schema
    const { validate, errors } = useForm({ validationSchema: schema })
    
    // Estado reactivo para la acción
    const action = reactive({
        name: '',
        description: '',
        x_position: 0, // Por defecto o para ser rellenado después
        y_position: 0, // Por defecto o para ser rellenado después
        config: {} // Configuración en formato objeto
    })
  
    // Para manejar la configuración como texto JSON
    const configJson = computed({
      get: () => {
        try {
          return JSON.stringify(action.config, null, 2)
        } catch (e) {
          return '{}'
        }
      },
      set: (val) => {
        try {
          action.config = JSON.parse(val)
        } catch (e) {
          // Si hay error de parseo, mantener el valor pero mostrar error
          console.error('Error parsing JSON:', e)
        }
      }
    })
  
    const storeAction = async (data) => {
        isLoading.value = true;
        validationErrors.value = null;
        
        try {
            // Intentar parsear el JSON si viene como string
            if (typeof data.config === 'string') {
                try {
                    data.config = JSON.parse(data.config);
                } catch (e) {
                    validationErrors.value = { config: ['Formato JSON inválido'] };
                    isLoading.value = false;
                    return;
                }
            }
  
            const response = await axios.post(`/api/workflows/${workflowId}/actions`, data);
            alert('Acción creada correctamente');
            // Redirigir a lista de acciones o al workflow
            router.push({ name: 'workflows.detail', params: { id: workflowId } });
        } catch (error) {
            if (error.response && error.response.data && error.response.data.errors) {
                validationErrors.value = error.response.data.errors;
            } else {
                alert('Ha ocurrido un error al crear la acción');
                console.error('Error al guardar acción:', error);
            }
        } finally {
            isLoading.value = false;
        }
    }
  
    function resetForm() {
        action.name = '';
        action.description = '';
        action.config = {};
        // Volver al workflow
        router.push({ name: 'workflows.detail', params: { id: workflowId } });
    }
  
    function submitForm() {
        validate().then(result => { 
            if (result.valid) storeAction(action);
        });
    }
  </script>