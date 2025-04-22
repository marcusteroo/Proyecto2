<template>
  <div class="kanban-container">
    <h1 class="kanban-title">{{ kanbanTitle }}</h1>
    
    <!-- Sección de listas de tareas -->
    <div class="columna">
      <div v-for="(lista, index) in listas" :key="index" class="seccion" :class="'seccion-' + index">
        <div class="seccion-header" :class="'header-' + index">
          <h2>{{ titulos[index] }}</h2>
          <span class="task-count">{{ lista.length }}</span>
        </div>

        <draggable v-model="listas[index]" :animation="300" group="tareas" class="listaTareas" :class="'column-' + index" @add="onTaskAdded($event, index)">
          <template #item="{ element: tarea }">
            <div
              class="tarea"
              :class="{ 'tarea-completada': tarea.completado }"
              @mouseover="tarea.hover = true"
              @mouseleave="tarea.hover = false"
            >
              <div class="tarea-contenido">
                <!-- CÍRCULO: Aparece y desaparece con fade en 0.5s -->
                <div
                  class="marcador"
                  :class="{ 'marcador-completado': tarea.completado }"
                  @click.stop="toggleCompletado(tarea)"
                >
                  <transition name="fade">
                    <span v-if="tarea.completado" class="check-icon">✔</span>
                  </transition>
                </div>

                <!-- TEXTO de la tarea: se desplaza con una transición de 0.5s -->
                <div
                  class="tarea-texto"
                  :class="{ desplazado: (tarea.hover || tarea.completado) }"
                  @click="editarTarea(tarea)"
                >
                  <p class="tarea-titulo" :class="{ completado: tarea.completado }">{{ tarea.titulo }}</p>
                  <p class="tarea-descripcion">{{ tarea.descripcion }}</p>
                  
                  <div class="tarea-meta" v-if="tarea.subtareas && tarea.subtareas.length > 0">
                    <span class="subtareas-count">
                      <i class="subtareas-icon pi pi-check-square"></i>
                      {{ getCompletedSubtasksCount(tarea) }}/{{ getSubtasksCount(tarea) }}
                    </span>
                  </div>
                </div>
                <!-- Removed the delete button from here -->
              </div>
            </div>
          </template>
        </draggable>

        <!-- Añadir nueva tarea a la lista -->
        <div class="add-task-container">
          <div class="input-field">
            <input
              v-model="nuevasTareas[index]"
              type="text"
              class="anadir-tarea"
              placeholder="+ Añadir tarea"
              maxlength="20"
              @keydown.enter="agregarTarea(index, nuevasTareas[index], titulos[index])"
            />
            <div class="char-counter mini" v-if="nuevasTareas[index].length > 0" 
                 :class="{ 'near-limit': nuevasTareas[index].length > 15 }">
              {{ nuevasTareas[index].length }}/20
            </div>
          </div>
          <button
            @click="agregarTarea(index, nuevasTareas[index], titulos[index])"
            class="anadir-tarea-btn"
          >
          <svg width="20" height="20" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <rect x="40" y="10" width="20" height="80" fill="white"/>
            <rect x="10" y="40" width="80" height="20" fill="white"/>
          </svg>
          </button>
        </div>
      </div>
    </div>

    <div v-if="popupAbierto" class="popup-overlay" @click.self="popupAbierto = false">
      <div class="popup">
        <div class="popup-header">
          <h2>Editar Tarea</h2>
          <button class="popup-close" @click="popupAbierto = false">✕</button>
        </div>
        <div class="popup-content">
          <!-- Título con contador de caracteres -->
          <div class="input-field">
            <input
              v-model="tareaEditada.titulo"
              type="text"
              placeholder="Título de la tarea"
              class="popup-input titulo-input"
              maxlength="20"
            />
            <div class="char-counter" :class="{ 'near-limit': tareaEditada.titulo.length > 15 }">
              {{ tareaEditada.titulo.length }}/20
            </div>
          </div>
          
          <!-- Descripción with counter - Now moved to popup-actions section -->
          <!-- <div class="input-field">
            <input
              v-model="tareaEditada.descripcion"
              type="text"
              placeholder="Descripción"
              class="popup-input descripcion-input"
              maxlength="30"
            />
            <div class="char-counter" :class="{ 'near-limit': tareaEditada.descripcion.length > 25 }">
              {{ tareaEditada.descripcion.length }}/30
            </div>
          </div> -->

          <div class="popup-subtareas">
            <h3>Subtareas</h3>
            <ul>
              <li
                v-for="(sub, i) in tareaEditada.subtareas"
                :key="i"
                class="popup-subtarea-item"
              >
                <label>
                  <input
                    type="checkbox"
                    :checked="sub.estado === 1"
                    @change="sub.estado = $event.target.checked ? 1 : 0"
                  />

                  <!-- Si no está en edición se muestra el texto; al hacer clic se activa la edición -->
                  <!-- Después (correcto) -->
                  <template v-if="!sub.editing">
                    <span :class="{ completado: sub.estado === 1 }" @click="sub.editing = true">
                      {{ sub.titulo }}
                    </span>
                  </template>
                  <template v-else>
                    <div class="input-field subtarea-edit-field">
                      <input
                        type="text"
                        v-model="sub.titulo"
                        class="subtarea-edit-input"
                        @blur="sub.editing = false"
                        @keydown.enter="sub.editing = false"
                        maxlength="15"
                      />
                      <div class="char-counter mini" :class="{ 'near-limit': sub.titulo.length > 12 }">
                        {{ sub.titulo.length }}/15
                      </div>
                    </div>
                  </template>

                </label>
                <button class="subtarea-delete" @click="eliminarSubtarea(i)">
                  <svg fill="none" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                    <path stroke="currentColor" stroke-width="1.5" d="M2.75 5.25h-1v-3.5h12.5v3.5h-1m-10.5 0V13c0 .69.56 1.25 1.25 1.25h8c.69 0 1.25-.56 1.25-1.25V5.25m-10.5 0h10.5m-7.75 3h5"/>
                  </svg>
                </button>
              </li>
            </ul>
            <!-- Añadir subtarea con limitación -->
            <div class="popup-add-subtarea">
              <div class="input-field subtarea-field">
                <input
                  v-model="nuevaSubtarea"
                  type="text"
                  placeholder="Añadir subtarea"
                  class="popup-input"
                  maxlength="15"
                  @keydown.enter="agregarSubtarea"
                  :disabled="isSubtaskInputLocked"
                  :class="{ 'input-locked': isSubtaskInputLocked }"
                />
                <div class="char-counter" :class="{ 'near-limit': nuevaSubtarea.length > 12 }">
                  {{ nuevaSubtarea.length }}/15
                </div>
              </div>
              <button 
                @click="agregarSubtarea" 
                class="popup-button"
                :disabled="isSubtaskInputLocked"
              >
                <span v-if="isSubtaskInputLocked">
                  <i class="pi pi-spin pi-spinner"></i>
                </span>
                <span v-else>Añadir</span>
              </button>
            </div>
          </div>
        </div>
        <div class="popup-actions">
          <!-- Added description field here -->
          <div class="popup-description-field">
            <input
              v-model="tareaEditada.descripcion"
              type="text"
              placeholder="Descripción"
              class="popup-input descripcion-input"
              maxlength="30"
            />
            <div class="char-counter" :class="{ 'near-limit': tareaEditada.descripcion.length > 25 }">
              {{ tareaEditada.descripcion.length }}/30
            </div>
          </div>
          <div class="popup-buttons">
            <!-- Added delete button here -->
            <button @click="confirmarEliminarTarea(tareaEditada)" class="popup-button delete-button">
              <i class="pi pi-trash"></i> Eliminar
            </button>
            <button @click="actualizarTareaBackend" class="popup-button guardar-button">
              Guardar cambios
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de confirmación para eliminar tarea -->
  <div v-if="deleteModalVisible" class="delete-modal-overlay" @click.self="cancelarEliminar">
    <div class="delete-modal">
      <div class="delete-modal-header">
        <h3>Confirmar eliminación</h3>
        <button class="modal-close-btn" @click="cancelarEliminar">✕</button>
      </div>
      <div class="delete-modal-content">
        <div class="delete-icon-container">
          <i class="pi pi-exclamation-triangle warning-icon"></i>
        </div>
        <p>¿Estás seguro de que deseas eliminar la tarea?</p>
        <p class="delete-task-title">{{ tareaAEliminar?.titulo }}</p>
      </div>
      <div class="delete-modal-actions">
        <button class="cancel-btn" @click="cancelarEliminar">Cancelar</button>
        <button class="confirm-delete-btn" @click="confirmarEliminar">
          <i class="pi pi-trash"></i> Eliminar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import draggable from 'vuedraggable';
import { useRoute } from 'vue-router';
const route = useRoute();
const id_tablero = route.params.id;
const estadoAColumna = {
  'Pendiente': 0,       // Sin Empezar
  'En curso': 1,        // En Curso 
  'Completado': 2,      // Finalizadas
  'Stopper': 3          // Stopper
};

const columnaAEstado = {
  0: 'Pendiente',       // Sin Empezar → Pendiente
  1: 'En curso',        // En Curso → En curso
  2: 'Completado',      // Finalizadas → Completado
  3: 'Stopper'          // Stopper → Stopper
};
// Listas de tareas según estado
const sinEmpezar = ref([]);
const enCurso = ref([]);
const finalizadas = ref([]);
const stopper = ref([]);
const kanbanTitle = ref('Mi Tablero Kanban');
const isSubtaskInputLocked = ref(false);
// Lista de listas (columnas) y títulos
const listas = ref([]);
onMounted(() => {
  listas.value = [sinEmpezar.value, enCurso.value, finalizadas.value, stopper.value];
});
const titulos = ['Sin Empezar', 'En Curso', 'Finalizadas', 'Stopper'];

// Variables para nuevas tareas y edición
const nuevasTareas = ref(['', '', '', '']);
const popupAbierto = ref(false);
const deleteModalVisible = ref(false); // Adding this ref for the delete modal visibility
const tareaAEliminar = ref(null); // Adding the missing ref for the task to delete
const tareaEditada = ref({
  id_tarea: null,
  titulo: '',
  descripcion: '',
  estado: '',
  id_tablero: null,
  subtareas: [], 
  completado: false,
  hover: false
});
const nuevaSubtarea = ref('');

onMounted(async () => {
  try {
    const tableroResponse = await axios.get(`/api/tableros/${id_tablero}`);
    const tableroInfo = tableroResponse.data;
    
    kanbanTitle.value = tableroInfo.nombre || 'Mi Tablero Kanban';

    const response = await axios.get(`/api/kanban/${id_tablero}/tasks`);
    const kanbans = response.data;
    
    // Usar Promise.all para cargar tareas y subtareas de manera concurrente
    const tareasConSubtareas = await Promise.all(kanbans.map(async (kanban) => {
      try {
        const subtaskResponse = await axios.get(`/api/subtareas/tarea/${kanban.id_tarea}`);
        const subtareas = subtaskResponse.data || []; // Garantizar que siempre sea un array

        return {
          ...kanban,
          subtareas: Array.isArray(subtareas) ? subtareas : [], // Validación adicional
          completado: kanban.completado || false,
          hover: false
        };
      } catch (subtaskError) {
        console.error(`Error al cargar subtareas para tarea ${kanban.id_tarea}:`, subtaskError);
        return {
          ...kanban,
          subtareas: [],
          completado: kanban.completado || false,
          hover: false
        };
      }
    }));

    // Distribuir tareas en las columnas correspondientes
    tareasConSubtareas.forEach(tarea => {
      const columnaIndex = estadoAColumna[tarea.estado];
      if (columnaIndex !== undefined) {
        listas.value[columnaIndex].push(tarea);
      } else {
        listas.value[0].push(tarea);
      }
    });
  } catch (error) {
    console.error('Error al cargar las tareas:', error);
  }
});
const onTaskAdded = async (event, toColumnIndex) => {
  // Obtener el elemento arrastrado directamente desde el evento
  const itemEl = event.item;
  if (!itemEl || !itemEl.__draggable_context) {
    console.error("No hay información del elemento arrastrado");
    return;
  }

  // Obtener la tarea original desde el contexto
  const movedTask = itemEl.__draggable_context.element;
  if (!movedTask) {
    console.error("No se encontró información de la tarea arrastrada");
    return;
  }
  
  // Determinar el nuevo estado basado en la columna
  const newStatus = columnaAEstado[toColumnIndex];
  
  console.log(`Tarea "${movedTask.titulo}" movida a columna ${titulos[toColumnIndex]}, nuevo estado: ${newStatus}`);
  
  // Actualizar en backend solo si realmente cambió el estado
  if (movedTask.estado !== newStatus) {
    try {
      const oldStatus = movedTask.estado;
      movedTask.estado = newStatus;
      
      // Llamar a la API para actualizar la tarea
      await axios.put(`/api/kanban/${movedTask.id_tarea}`, {
        id_tarea: movedTask.id_tarea,
        titulo: movedTask.titulo,
        descripcion: movedTask.descripcion,
        estado: newStatus,
        id_tablero: movedTask.id_tablero
      });
      
      console.log(`Tarea #${movedTask.id_tarea} "${movedTask.titulo}" actualizada de "${oldStatus}" a "${newStatus}"`);
    } catch (error) {
      console.error('Error al actualizar el estado de la tarea:', error);
      // Revertir el cambio en caso de error
      movedTask.estado = oldStatus;
    }
  }
};
const getCompletedSubtasksCount = (tarea) => {
  if (!tarea.subtareas || !Array.isArray(tarea.subtareas)) return 0;
  return tarea.subtareas.filter(s => s.estado === 1).length;
};
const confirmarEliminarTarea = (tarea) => {
  tareaAEliminar.value = tarea;
  deleteModalVisible.value = true;
};
const cancelarEliminar = () => {
  deleteModalVisible.value = false;
  tareaAEliminar.value = null;
};
const confirmarEliminar = () => {
  if (tareaAEliminar.value) {
    eliminarTarea(tareaAEliminar.value);
    deleteModalVisible.value = false;
    popupAbierto.value = false; // Cerrar también el popup de edición
    tareaAEliminar.value = null;
  }
};
const eliminarTarea = async (tarea) => {
  try {
    // Llamar a la API para eliminar la tarea
    await axios.delete(`/api/kanban/${tarea.id_tarea}`);
    
    // Eliminar la tarea de la lista local
    const columnaIndex = estadoAColumna[tarea.estado];
    if (columnaIndex !== undefined) {
      const index = listas.value[columnaIndex].findIndex(t => t.id_tarea === tarea.id_tarea);
      if (index !== -1) {
        listas.value[columnaIndex].splice(index, 1);
      }
    }
    
    // Opcional: mostrar notificación de éxito
    if (window.$toast) {
      window.$toast.add({
        severity: 'success',
        summary: 'Tarea eliminada',
        detail: 'La tarea ha sido eliminada correctamente',
        life: 3000
      });
    }
  } catch (error) {
    console.error('Error al eliminar la tarea:', error);
    
    // Opcional: mostrar notificación de error
    if (window.$toast) {
      window.$toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'No se pudo eliminar la tarea',
        life: 3000
      });
    }
  }
};
// Obtener conteo total de subtareas de forma segura
const getSubtasksCount = (tarea) => {
  if (!tarea.subtareas || !Array.isArray(tarea.subtareas)) return 0;
  return tarea.subtareas.length;
};
// Función para agregar tarea (crea la tarea en el backend y la agrega a la lista local)
const agregarTarea = async (index, nuevaTarea, estado) => {
  if (nuevaTarea.trim()) {
    const estadoBaseDatos = columnaAEstado[index];
    
    const tarea = {
      titulo: nuevaTarea.trim(),
      descripcion: '',
      subtareas: [],
      estado: estadoBaseDatos,
      id_tablero: id_tablero, 
      completado: false,
      hover: false
    };
    try {
      const response = await axios.post('/api/kanban', {
        titulo: tarea.titulo,
        descripcion: tarea.descripcion,
        estado: tarea.estado,
        id_tablero: id_tablero 
      });
      tarea.id_tarea = response.data.id_tarea;
      listas.value[index].push(tarea);
      nuevasTareas.value[index] = '';
    } catch (error) {
      console.error('Error al agregar la tarea:', error);
    }
  }
};

// Abre el popup de edición
const editarTarea = (tarea) => {
  tareaEditada.value = {
    ...tarea,
    titulo: tarea.titulo || '',
    descripcion: tarea.descripcion || '', 
    subtareas: Array.isArray(tarea.subtareas) ? [...tarea.subtareas] : [],
  };
  popupAbierto.value = true;
};

// Alterna la tarea como completada o no
const toggleCompletado = (tarea) => {
  tarea.completado = !tarea.completado;
};

// Actualiza la tarea en el backend y en la lista local
const actualizarTareaBackend = async () => {
  if (tareaEditada.value && tareaEditada.value.id_tarea) {
    try {
      // Primero actualizar la tarea principal
      await axios.put(`/api/kanban/${tareaEditada.value.id_tarea}`, tareaEditada.value);
      
      // Luego actualizar subtareas en bloque con el formato correcto
      if (tareaEditada.value.subtareas && tareaEditada.value.subtareas.length > 0) {
        await axios.put(`/api/subtareas/tarea/${tareaEditada.value.id_tarea}`, {
          subtareas: tareaEditada.value.subtareas.map(subtarea => ({
            id_subtarea: subtarea.id_subtarea,
            titulo: subtarea.titulo,
            estado: subtarea.estado
          }))
        });
      }

      // Actualizar en la lista local
      const lista = listas.value.find(list => list.some(t => t.id_tarea === tareaEditada.value.id_tarea));
      if (lista) {
        const idx = lista.findIndex(t => t.id_tarea === tareaEditada.value.id_tarea);
        if (idx !== -1) {
          lista[idx] = { ...tareaEditada.value };
        }
      }

      // Mostrar notificación de éxito usando toast (si tienes PrimeVue o un sistema similar)
      if (window.$toast) {
        window.$toast.add({
          severity: 'success',
          summary: 'Tarea actualizada',
          detail: 'Los cambios se guardaron correctamente',
          life: 3000
        });
      }
      
      popupAbierto.value = false;
    } catch (error) {
      console.error('Error al actualizar la tarea:', error);
      
      // Mostrar notificación de error
      if (window.$toast) {
        window.$toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'No se pudo actualizar la tarea. Inténtalo de nuevo.',
          life: 3000
        });
      }
    }
  }
};


// Agrega una subtarea a la tarea en edición
const agregarSubtarea = async () => {
  // Si el campo está bloqueado o vacío, no hacer nada
  if (isSubtaskInputLocked.value || !nuevaSubtarea.value.trim()) return;
  
  // Bloquear el input inmediatamente
  isSubtaskInputLocked.value = true;
  
  try {
    // Al crear la subtarea, le damos un estado = 0 por defecto
    const response = await axios.post('/api/subtareas', {
      titulo: nuevaSubtarea.value.trim(),
      estado: 0,
      id_tarea: tareaEditada.value.id_tarea
    });

    const nuevaSubtareaConId = {
      id_subtarea: response.data.id_subtarea,
      titulo: nuevaSubtarea.value.trim(),
      estado: 0
    };

    // Añadir la subtarea a la lista
    tareaEditada.value.subtareas.push(nuevaSubtareaConId);
    
    // Limpiar el campo después de añadir
    nuevaSubtarea.value = '';
  } catch (error) {
    console.error('Error al agregar subtarea:', error);
  } finally {
    // Mantener el input bloqueado por 1 segundo
    setTimeout(() => {
      isSubtaskInputLocked.value = false;
    }, 1000);
  }
};


// Elimina una subtarea de la tarea en edición
const eliminarSubtarea = async (index) => {
  if (tareaEditada.value && tareaEditada.value.subtareas) {
    const subtareaAEliminar = tareaEditada.value.subtareas[index];
    
    try {
      // Eliminar subtarea del backend
      await axios.delete(`/api/subtareas/${subtareaAEliminar.id_subtarea}`);
      
      // Eliminar subtarea del frontend
      tareaEditada.value.subtareas.splice(index, 1);
    } catch (error) {
      console.error('Error al eliminar subtarea:', error);
    }
  }
};
</script>

<style>
/* Reset y variables globales */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

:root {
  --primary-color: #3498db;
  --primary-dark: #2980b9;
  --secondary-color: #2ecc71;
  --accent-color: #9b59b6;
  --background-color: #f0f2f5;
  --card-bg: #ffffff;
  --text-color: #333;
  --text-light: #666;
  --border-color: #e0e0e0;
  --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
  --column-width: 280px;
  --radius: 8px;
  --transition: 0.3s ease;

  /* Colores de columnas */
  --col-0-color: #4A6DD9;
  --col-1-color: #D99D23;
  --col-2-color: #37B569;
  --col-3-color: #D35F5F;
}

/* Contenedor principal */
.kanban-container {
  background-color: var(--background-color);
  min-height: 100vh;
  padding: 20px;
  font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

.kanban-title {
  font-size: 24px;
  margin-bottom: 20px;
  color: var(--text-color);
  padding: 0 20px;
}

/* Contenedor de las columnas con scroll horizontal */
.columna {
  display: flex;
  gap: 20px;
  padding: 10px;
  overflow-x: auto;
  min-height: calc(100vh - 120px);
  align-items: flex-start;
  padding-bottom: 20px;
}

/* Estilos de scroll horizontal más elegantes */
.columna::-webkit-scrollbar {
  height: 10px;
}

.columna::-webkit-scrollbar-track {
  background: var(--background-color);
  border-radius: 10px;
}

.columna::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 10px;
}

.columna::-webkit-scrollbar-thumb:hover {
  background: #aaa;
}

/* Columna */
.seccion {
  flex: 0 0 var(--column-width);
  display: flex;
  flex-direction: column;
  background-color: #ECF2F7;
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  max-height: calc(100vh - 120px);
  overflow: hidden;
}

/* Cabeceras de columna con colores diferentes */
.seccion-header {
  padding: 16px;
  border-radius: var(--radius) var(--radius) 0 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.seccion-0 .seccion-header {
  background-color: var(--col-0-color);
  color: white;
}

.seccion-1 .seccion-header {
  background-color: var(--col-1-color);
  color: white;
}

.seccion-2 .seccion-header {
  background-color: var(--col-2-color);
  color: white;
}

.seccion-3 .seccion-header {
  background-color: var(--col-3-color);
  color: white;
}

.seccion h2 {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
}

.task-count {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 12px;
  padding: 1px 8px;
  font-size: 14px;
  font-weight: 600;
}

/* Lista de tareas */
.listaTareas {
  flex: 1;
  padding: 12px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-height: 50px;
}

/* Estilos de scroll para las listas */
.listaTareas::-webkit-scrollbar {
  width: 6px;
}

.listaTareas::-webkit-scrollbar-track {
  background: transparent;
}

.listaTareas::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 6px;
}

/* Tarjeta de tarea */
.tarea {
  background-color: var(--card-bg);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  position: relative;
  cursor: pointer;
}

.tarea:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

.tarea-contenido {
  display: flex;
  align-items: flex-start;
  padding: 12px;
  position: relative; /* Importante para posicionar absolutamente el icono */
}

.tarea-completada {
  opacity: 0.8;
}

/* Círculo de completado */
.marcador {
  min-width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid #ccc;
  margin-right: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  transition: all 0.3s ease;
  cursor: pointer;
}

.tarea:hover .marcador {
  border-color: var(--secondary-color);
}

.marcador-completado {
  background-color: var(--secondary-color);
  border-color: var(--secondary-color);
}

.check-icon {
  font-size: 12px;
  font-weight: bold;
}

/* Texto de la tarea */
.tarea-texto {
  flex: 1;
  text-align: left;
  transition: transform 0.3s ease;
}

.tarea-titulo {
  font-weight: 500;
  margin-bottom: 4px;
  color: var(--text-color);
  font-size: 15px;
  line-height: 1.2;
  word-break: break-word;
}

.tarea-descripcion {
  font-size: 13px;
  color: #666;
  margin-bottom: 10px;
  line-height: 1.3;
}

.tarea-meta {
  display: flex;
  align-items: center;
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px dashed rgba(0, 0, 0, 0.1);
}

.subtareas-count {
  display: flex;
  align-items: center;
  gap: 6px;
  background-color: rgba(52, 152, 219, 0.1);
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  color: var(--primary-dark);
  font-weight: 500;
  transition: background-color 0.2s ease;
}

.subtareas-count:hover {
  background-color: rgba(52, 152, 219, 0.2);
}

.subtareas-icon {
  font-style: normal;
  color: var(--primary-dark);
  font-size: 14px;
}

.tarea-texto.desplazado {
  transform: translateX(10px);
}

.completado {
  text-decoration: line-through;
  color: #666;
}

/* Ícono de edición */
.editar-tarea {
  position: absolute;
  top: 8px;
  right: 8px;
  color: var(--text-light);
  background-color: rgba(255, 255, 255, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s ease, transform 0.1s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  z-index: 2;
}

.editar-tarea:hover {
  background-color: var(--primary-color);
  color: white;
  transform: scale(1.1);
}

.edit-icon {
  width: 16px;
  height: 16px;
}

/* Input y botón de añadir tarea */
.add-task-container {
  display: flex;
  padding: 12px;
  border-top: 1px solid var(--border-color);
  background-color: rgba(255, 255, 255, 0.5);
  align-items: flex-start; /* Cambiado a flex-start para alinear con el top del input */
  gap: 8px; /* Espaciado consistente */
}

.input-field {
  position: relative;
  margin-bottom: 16px;
  flex: 1; /* Asegura que ocupe todo el espacio disponible */
  display: flex; /* Nuevo: convertir en flex para mejor control */
  flex-direction: column; /* Nuevo: organizar verticalmente */
}

.anadir-tarea {
  width: 100%; /* Usa todo el ancho disponible */
  height: 40px; /* Altura fija para consistencia */
  padding: 8px 12px;
  border: 1px solid var(--border-color);
  border-radius: var(--radius);
  font-size: 14px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  margin-bottom: 0; /* Quitar margen inferior */
}

.anadir-tarea:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.anadir-tarea-btn {
  height: 40px; /* Misma altura que el input */
  width: 40px; /* Ancho igual a la altura para forma cuadrada */
  border: none;
  background-color: #3498db!important;
  color: white;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 20px;
  transition: background-color 0.2s ease, transform 0.2s ease;
  flex-shrink: 0; /* Evita que el botón se encoja */
  margin-top: 0; /* Asegura que no haya margen superior */
  align-self: flex-start; /* Alinea con la parte superior */
}

/* Ajuste específico para el contador en la adición de tarea */
.add-task-container .char-counter.mini {
  bottom: -12px; /* Reducido de -16px a -12px */
}

/* Popup overlay */
.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(3px);
}

/* Contenedor del popup */
.popup {
  background: #fff;
  border-radius: 12px;
  width: 450px;
  max-width: 95%;
  max-height: 90vh;
  box-shadow: var(--shadow-lg);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  animation: popup-appear 0.3s ease-out;
}

@keyframes popup-appear {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Header del popup */
.popup-header {
  background-color: #106ebe;
  color: #fff;
  padding: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.popup-header h2 {
  margin: 0;
  font-size: 20px;
}

.popup-close {
  background: transparent;
  border: none;
  color: #fff;
  font-size: 22px;
  cursor: pointer;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.2s ease;
}

.popup-close:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

/* Contenido del popup */
.popup-content {
  padding: 20px;
  flex: 1;
  overflow-y: auto;
}

.popup-input {
  width: 100%;
  padding: 12px;
  margin-bottom: 18px; 
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-size: 15px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.popup-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.titulo-input {
  font-weight: 600;
  font-size: 16px;
}

.descripcion-input {
  font-style: normal;
}

/* Sección de subtareas */
.popup-subtareas {
  margin-top: 20px;
}

.popup-subtareas h3 {
  margin-bottom: 12px;
  font-size: 16px;
  color: var (--text-color);
}

.popup-subtareas ul {
  list-style: none;
  padding: 0;
  margin: 0 0 16px 0;
}

.popup-subtarea-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px;
  background: #f9f9f9;
  border-radius: 6px;
  margin-bottom: 8px;
  transition: background-color 0.2s ease;
}

.popup-subtarea-item:hover {
  background: #f0f0f0;
}

/* Alinear checkbox y texto o input */
.popup-subtarea-item label {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
  cursor: pointer;
}

.popup-checkbox {
  margin: 0;
  width: 18px;
  height: 18px;
}

.subtarea-delete {
  background: transparent;
  border: none;
  color: #d9534f;
  width: 30px;
  height: 30px;
  cursor: pointer;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s ease;
}

.subtarea-delete:hover {
  background-color: rgba(217, 83, 79, 0.1);
}

.subtarea-delete svg {
  width: 16px;
  height: 16px;
}

/* Estilo para el input en modo edición de subtarea */
.subtarea-edit-input {
  padding: 6px 8px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
  flex: 1;
}

/* Input y botón de añadir subtarea */
.popup-add-subtarea {
  display: flex;
  gap: 8px;
  align-items: center; 
}

.popup-add-subtarea .popup-input {
  height: 40px; 
  padding: 8px 12px;
  margin-bottom: 18px; 
}

.popup-add-subtarea button {
  height: 40px; 
  padding: 0 16px;
  white-space: nowrap;
  margin-bottom: 18px; 
  flex-shrink: 0; 
}

/* Botones generales */
.popup-button {
  padding: 10px 18px;
  border: none;
  border-radius: 6px;
  background-color: var(--primary-color);
  color: #fff;
  cursor: pointer;
  font-size: 15px;
  font-weight: 500;
  transition: background-color 0.2s ease, transform 0.2s ease;
}

.popup-button:hover {
  background-color: var(--primary-dark);
  transform: translateY(-2px);
}

.popup-button:active {
  transform: translateY(0);
}

/* Sección de acciones del popup */
.popup-actions {
  padding: 16px;
  border-top: 1px solid #eee;
  display: flex;
  flex-direction: column;
  gap: 16px;
  background: #f7f7f7;
}

.popup-description-field {
  position: relative;
  width: 100%;
}

.popup-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.guardar-button {
  background-color: var(--secondary-color);
}

.guardar-button:hover {
  background-color: #27ae60;
}

.delete-button {
  background-color: #d9534f;
}

.delete-button:hover {
  background-color: #c9302c;
}

/* Animaciones */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}

/* Media Queries para Responsive */
@media (max-width: 768px) {
  .columna {
    padding: 10px 0;
  }
  
  .seccion {
    flex: 0 0 260px;
  }
  
  .kanban-title {
    font-size: 20px;
    text-align: center;
  }
}

@media (max-width: 576px) {
  .kanban-container {
    padding: 10px;
  }
  
  .seccion {
    flex: 0 0 230px;
  }
  
  .popup {
    width: 95%;
    max-height: 80vh;
  }
  
  .popup-header {
    padding: 12px;
  }
  
  .popup-content {
    padding: 15px;
  }
  
  .tarea-titulo {
    font-size: 14px;
  }
  
  .tarea-descripcion {
    font-size: 12px;
  }

  .editar-tarea {
    top: 6px;
    right: 6px;
  }
  
  .subtareas-count {
    padding: 3px 6px;
    font-size: 11px;
  }

  .anadir-tarea, .anadir-tarea-btn,
  .popup-add-subtarea .popup-input, .popup-add-subtarea button {
    height: 36px; /* Ligeramente más pequeño en móviles */
  }
  
  .anadir-tarea-btn {
    width: 36px;
  }
}

/* Estilos para dispositivos muy pequeños */
@media (max-width: 350px) {
  .seccion {
    flex: 0 0 200px;
  }
  
  .add-task-container {
    flex-direction: column;
    gap: 12px;
  }
  
  .anadir-tarea-btn {
    margin-left: auto;
    width: 36px;
    height: 36px;
    margin-top: -16px; /* Ajuste para compensar el espacio del contador */
  }
  
  /* En dispositivos muy pequeños, mantenemos los botones de subtareas alineados */
  .popup-add-subtarea {
    flex-direction: row;
  }
}

/* Contenedor para input con contador */
.input-field {
  position: relative;
  margin-bottom: 16px;
  flex: 1; /* Asegura que ocupe todo el espacio disponible */
  display: flex; /* Nuevo: convertir en flex para mejor control */
  flex-direction: column; /* Nuevo: organizar verticalmente */
}

.subtarea-field {
  flex: 1;
  margin-bottom: 0;
}

.subtarea-edit-field {
  position: relative;
  flex: 1;
}

/* Contador de caracteres */
.char-counter {
  position: absolute;
  right: 10px;
  bottom: -16px; /* Reducido de -18px a -16px */
  font-size: 12px;
  color: #888;
  transition: color 0.2s ease;
}

.char-counter.mini {
  bottom: -12px; /* Reducido de -16px a -12px */
  font-size: 10px;
  right: 5px;
}

/* Ajustar márgenes para dar menos espacio al contador */
.popup-input {
  margin-bottom: 18px; /* Reducido de 22px a 18px */
}

.popup-add-subtarea .popup-input {
  margin-bottom: 18px; /* Reducido de 22px a 18px */
}

/* Input y botón de añadir subtarea - ajustamos espaciado */
.popup-add-subtarea button {
  height: 40px;
  padding: 0 16px;
  white-space: nowrap;
  margin-bottom: 18px; /* Reducido de 22px a 18px para coincidir con el input */
  flex-shrink: 0;
}

/* Ajuste para el contador en la adición de tarea */
.add-task-container .char-counter.mini {
  bottom: -12px; /* Reducido de -16px a -12px */
  right: 5px;
}

.subtarea-edit-field .char-counter.mini {
  bottom: -12px; /* Asegurar consistencia en inputs de subtareas */
}

/* Ajustes para responsividad */
@media (max-width: 576px) {
  .char-counter {
    font-size: 10px;
    bottom: -12px; /* Reducido de -16px a -12px */
  }
  
  .char-counter.mini {
    bottom: -10px; /* Reducido de -14px a -10px */
    font-size: 9px;
  }
}
.input-locked {
  opacity: 0.7;
  cursor: not-allowed;
  background-color: #f9f9f9;
}

.popup-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}
.tarea-delete-btn {
  position: absolute;
  top: 5px;
  right: 5px;
  background-color: rgba(255, 255, 255, 0.9);
  color: #d9534f;
  border: none;
  border-radius: 4px;
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  opacity: 0;
  z-index: 5;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.tarea:hover .tarea-delete-btn {
  opacity: 1;
}

.tarea-delete-btn:hover {
  background-color: #d9534f;
  color: white;
  transform: scale(1.1);
}

/* Estilos para el modal de confirmación de eliminación */
.delete-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(3px);
  animation: fade-in 0.2s ease;
}

.delete-modal {
  background: white;
  border-radius: 10px;
  width: 380px;
  max-width: 90%;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  animation: scale-in 0.3s ease;
}

.delete-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid #eee;
}

.delete-modal-header h3 {
  margin: 0;
  color: #d9534f;
  font-size: 18px;
  font-weight: 600;
}

.modal-close-btn {
  background: transparent;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #666;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  transition: background-color 0.2s;
}

.modal-close-btn:hover {
  background-color: #f0f0f0;
}

.delete-modal-content {
  padding: 20px;
  text-align: center;
}

.delete-icon-container {
  margin-bottom: 15px;
}

.warning-icon {
  font-size: 36px;
  color: #f39c12;
  background: rgba(243, 156, 18, 0.1);
  width: 70px;
  height: 70px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 15px;
}

.delete-modal-content p {
  margin: 0 0 10px;
  color: #555;
  font-size: 16px;
}

.delete-task-title {
  font-weight: 600;
  color: #333;
  background: #f8f8f8;
  border-radius: 4px;
  padding: 8px 12px;
  margin-top: 10px;
  word-break: break-word;
}

.delete-modal-actions {
  display: flex;
  padding: 15px 20px;
  border-top: 1px solid #eee;
  background: #f9f9f9;
  justify-content: flex-end;
  gap: 10px;
}

.cancel-btn {
  padding: 8px 16px;
  background: #f8f9fa;
  color: #666;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}

.cancel-btn:hover {
  background: #e9ecef;
}

.confirm-delete-btn {
  padding: 8px 16px;
  background: #d9534f;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}

.confirm-delete-btn:hover {
  background: #c9302c;
  transform: translateY(-1px);
}

/* Animaciones para el modal */
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes scale-in {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Responsividad para el modal */
@media (max-width: 576px) {
  .delete-modal {
    width: 95%;
  }
  
  .delete-modal-content {
    padding: 15px;
  }
  
  .warning-icon {
    width: 60px;
    height: 60px;
    font-size: 30px;
  }
  
  .delete-modal-actions {
    padding: 12px 15px;
  }
}
</style>
