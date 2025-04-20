<template>
  <div class="row justify-content-center my-2">
    <div class="col-md-12">
      <div class="card border-0">
        <div class="card-header bg-transparent">
          <h5 class="float-start">Tareas</h5>
        </div>

        <div class="card-body shadow-sm">
          <!-- Barra de búsqueda -->
          <div class="mb-3">
            <input
              v-model="search_global"
              type="text"
              placeholder="Buscar..."
              class="form-control w-25"
            />
          </div>
          <!-- Tabla responsiva -->
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>ID Tarea</th>
                  <th>Título</th>
                  <th>Descripción</th>
                  <th>Estado</th>
                  <th>ID Tablero</th>
                  <th>Creado en</th>
                  <th>Actualizado en</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="tarea in tareas.data" :key="tarea.id_tarea">
                  <td>{{ tarea.id_tarea }}</td>
                  <td>{{ tarea.titulo }}</td>
                  <td>{{ tarea.descripcion }}</td>
                  <td>{{ tarea.estado }}</td>
                  <td>{{ tarea.id_tablero }}</td>
                  <td>{{ tarea.created_at }}</td>
                  <td>{{ tarea.updated_at }}</td>
                  <td>
                    <!-- Enlace a la edición -->
                    <router-link
                      :to="{ name: 'tareas.edit', params: { id: tarea.id_tarea } }"
                      class="badge custom-primary-badge"
                    >
                      Editar
                    </router-link>
                    <!-- Enlace para eliminar -->
                    <a
                      href="#"
                      @click.prevent="deleteTarea(tarea.id_tarea)"
                      class="ms-2 badge bg-danger"
                    >
                      Eliminar
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card-footer">
          <!-- Paginación u otra info -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import useTareas from "@/composables/tareas"; // Ejemplo de tu composable o servicio

// Obtenemos el composable que gestiona las tareas
const { tareas, getTareas, deleteTarea } = useTareas();

// Variable para búsqueda global
const search_global = ref("");

// Al montar, cargamos las tareas
onMounted(async () => {
  await getTareas();
  console.log("Datos de la tabla:", tareas.value);
});
</script>
<style scoped>
.custom-primary-badge {
  background-color: #3f359b;
  color: white;
}
</style>
