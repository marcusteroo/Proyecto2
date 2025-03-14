<template>
    <div class="row justify-content-center my-5">
      <div class="col-md-10">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <form @submit.prevent="submitForm">
              <!-- Campo: nombre -->
              <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input v-model="post.nombre" id="nombre" type="text" class="form-control" />
                <div class="text-danger mt-1">
                  {{ errors.nombre }}
                </div>
                <div class="text-danger mt-1">
                  <div v-for="message in validationErrors?.nombre" :key="message">
                    {{ message }}
                  </div>
                </div>
              </div>
  
              <!-- Campo: id_creador -->
              <div class="mb-3">
                <label for="id_creador" class="form-label">ID Creador</label>
                <input v-model="post.id_creador" id="id_creador" type="text" class="form-control" />
                <div class="text-danger mt-1">
                  {{ errors.id_creador }}
                </div>
                <div class="text-danger mt-1">
                  <div v-for="message in validationErrors?.id_creador" :key="message">
                    {{ message }}
                  </div>
                </div>
              </div>
  
              <!-- Campo: color_fondo -->
              <div class="mb-3">
                <label for="color_fondo" class="form-label">Color Fondo</label>
                <input v-model="post.color_fondo" id="color_fondo" type="text" class="form-control" />
                <div class="text-danger mt-1">
                  {{ errors.color_fondo }}
                </div>
                <div class="text-danger mt-1">
                  <div v-for="message in validationErrors?.color_fondo" :key="message">
                    {{ message }}
                  </div>
                </div>
              </div>
  
              <!-- Botón guardar -->
              <div class="mt-4">
                <button :disabled="isLoading" class="btn btn-primary">
                  <span v-if="isLoading">Processing...</span>
                  <span v-else>Guardar</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { reactive } from "vue";
  import { useForm, useField, defineRule } from "vee-validate";
  import { required } from "@/validation/rules";
  import useKanbans from "@/composables/kanbans";
  
  const { storeKanban, validationErrors, isLoading } = useKanbans();
  
  // Reglas de validación
  defineRule('required', required);
  
  const schema = {
    nombre: 'required',
    id_creador: 'required',
    color_fondo: 'required'
  };
  
  const { validate, errors } = useForm({ validationSchema: schema });
  
  const { value: nombre } = useField('nombre', null, { initialValue: '' });
  const { value: id_creador } = useField('id_creador', null, { initialValue: '' });
  const { value: color_fondo } = useField('color_fondo', null, { initialValue: '' });
  
  const post = reactive({
    nombre,
    id_creador,
    color_fondo
  });
  
  function submitForm() {
    validate().then(form => {
      if (form.valid) {
        storeKanban(post);
      }
    });
  }
  </script>  