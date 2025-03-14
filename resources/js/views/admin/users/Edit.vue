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
                <div class="text-danger mt-1">{{ errors.id_tablero }}</div>
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
                <div class="text-danger mt-1">{{ errors.nombre }}</div>
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
                <div class="text-danger mt-1">{{ errors.id_creador }}</div>
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
                <div class="text-danger mt-1">{{ errors.color_fondo }}</div>
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
  import { onMounted, reactive, watchEffect } from "vue";
  import { useRoute } from "vue-router";
  import { useToast } from "primevue/usetoast";
  import { useForm, useField, defineRule } from "vee-validate";
  import { required } from "@/validation/rules";
  import useTableros from "@/composables/tableros";
  
  // Definimos la regla de validación
  defineRule("required", required);
  
  // Esquema de validación para los campos
  const schema = {
    id_tablero: "required",
    nombre: "required",
    id_creador: "required",
    color_fondo: "required"
  };
  
  // Creamos el contexto del formulario con el esquema
  const { validate, errors } = useForm({ validationSchema: schema });
  
  // Definimos los campos a validar (similares a la versión de usuarios)
  const { value: id_tablero } = useField("id_tablero", null, { initialValue: "" });
  const { value: nombre } = useField("nombre", null, { initialValue: "" });
  const { value: id_creador } = useField("id_creador", null, { initialValue: "" });
  const { value: color_fondo } = useField("color_fondo", null, { initialValue: "" });
  
  // Creamos un objeto reactivo que agrupa los campos del tablero
  const tablero = reactive({
    id_tablero,
    nombre,
    id_creador,
    color_fondo
  });
  
  // Usamos el composable de tableros y renombramos la ref a postData para seguir el patrón
  const { getTablero, updateTablero, tablero: postData } = useTableros();
  
  const route = useRoute();
  const toast = useToast();
  
  // Al montar el componente, obtenemos los datos del tablero por ID
  onMounted(() => {
    getTablero(route.params.id);
  });
  
  // Actualizamos el objeto local "tablero" cuando se recibe la respuesta
  watchEffect(() => {
    // Con el composable definido correctamente, postData.value ya existe (aunque inicialmente con valores por defecto)
    if (postData.value) {
      tablero.id_tablero = postData.value.id_tablero;
      tablero.nombre = postData.value.nombre;
      tablero.id_creador = postData.value.id_creador;
      tablero.color_fondo = postData.value.color_fondo;
    }
  });
  
  // Función para enviar el formulario
  function submitForm() {
    validate().then(form => {
      if (form.valid) {
        updateTablero(tablero)
          .then(() => {
            toast.add({
              severity: "success",
              summary: "Tablero actualizado",
              detail: "El tablero se ha actualizado correctamente.",
              life: 3000
            });
          })
          .catch(() => {
            toast.add({
              severity: "error",
              summary: "Error",
              detail: "No se pudo actualizar el tablero.",
              life: 3000
            });
          });
      }
    });
  }
  </script>
  