<template>
  <div class="row justify-content-center my-2">
    <div class="col-md-12">
      <div class="card border-0">
        <!-- Card header for Kanbans (Boards) -->
        <div class="card-header bg-transparent">
          <h5 class="float-start">Tableros</h5>
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
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>ID Creador</th>
                  <th>Color Fondo</th>
                  <th>Created at</th>
                  <th>Updated at</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="kanban in kanbans.data" :key="kanban.id_tablero">
                  <td>{{ kanban.id_tablero }}</td>
                  <td>{{ kanban.nombre }}</td>
                  <td>{{ kanban.id_creador }}</td>
                  <td>{{ kanban.color_fondo }}</td>
                  <td>{{ kanban.created_at }}</td>
                  <td>{{ kanban.updated_at }}</td>
                  <td>
                    <!-- Se muestran siempre para probar -->
                    <router-link
                      :to="{ name: 'kanbans.edit', params: { id: kanban.id_tablero } }"
                      class="badge custom-primary-badge"
                    >
                      Editar
                    </router-link>
                    <a
                      href="#"
                      @click.prevent="deleteKanban(kanban.id_tablero)"
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
import useKanbans from "@/composables/kanbans";

// Importamos el composable que gestiona los Kanbans
const { kanbans, getKanbans, deleteKanban } = useKanbans();

// Obtenemos la función can() de CASL
const { can } = useAbility();

// (Opcional) variable para búsqueda global; puedes usarla para filtrar la data
const search_global = ref("");

// Al montar el componente, cargamos los Kanbans
onMounted(() => {
  getKanbans();
});
</script>
<style scoped>
.custom-primary-badge {
  background-color: #3f359b;
  color: white;
}
</style>
