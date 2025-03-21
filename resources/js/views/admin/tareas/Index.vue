<template>
  <div class="row justify-content-center my-2">
    <div class="col-md-12">
      <div class="card border-0">
        <!-- Encabezado de la tarjeta -->
        <div class="card-header bg-transparent">
          <h5 class="float-start">Tareas</h5>
          <router-link
            v-if="can('tarea-create')"
            :to="{ name: 'tareas.create' }"
            class="btn btn-primary btn-sm float-end"
          >
            Create Tarea
          </router-link>
        </div>
  
        <!-- Cuerpo de la tarjeta -->
        <div class="card-body shadow-sm">
          <!-- (Opcional) Barra de búsqueda o filtros -->
          <div class="mb-3">
            <input
              v-model="search_global"
              type="text"
              placeholder="Search..."
              class="form-control w-25"
            />
          </div>
          <!-- Tabla responsiva -->
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>ID Tarea</th>
                  <th>Titulo</th>
                  <th>Descripcion</th>
                  <th>Estado</th>
                  <th>ID Tablero</th>
                  <th>Created at</th>
                  <th>Updated at</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="tarea in kanbans.data" :key="tarea.id_tarea">
                  <td>{{ tarea.id_tarea }}</td>
                  <td>{{ tarea.titulo }}</td>
                  <td>{{ tarea.id_descripcion }}</td>
                  <td>{{ tarea.estado }}</td>
                  <td>{{ tarea.id_tablero }}</td>
                  <td>{{ tarea.created_at }}</td>
                  <td>{{ tarea.updated_at }}</td>
                  <td>
                    <!-- Se muestran siempre para probar -->
                    <router-link
                      v-if="tarea.id || tarea.id_tarea"
                      :to="{ name: 'tareas.edit', params: { id: tarea.id || tarea.id_tarea } }"
                      class="badge bg-primary"
                    >
                      Edit
                    </router-link>

                    <a
                      href="#"
                      @click.prevent="deleteTarea(tarea.id_tarea)"
                      class="ms-2 badge bg-danger"
                    >
                      Delete
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
  
        <!-- Pie de la tarjeta (puedes agregar paginación u otra info) -->
        <div class="card-footer">
          <!-- Aquí podrías colocar controles de paginación -->
        </div>
      </div>
    </div>
  </div>
</template>
  
<script setup>
import { ref, onMounted } from "vue";
import { useAbility } from "@casl/vue";
import useTareas from "@/composables/tareas";

// Importamos el composable que gestiona los Tareas
const { kanbans, getTareas, deleteTarea } = useTareas();

// Obtenemos la función can() de CASL
const { can } = useAbility();

// (Opcional) variable para búsqueda global; puedes usarla para filtrar la data
const search_global = ref("");

// Al montar el componente, cargamos los Tareas
onMounted(() => {
  getTareas();
});
</script>
