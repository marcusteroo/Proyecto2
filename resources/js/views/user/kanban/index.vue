<template>
  <div>
    <!-- Sección de listas de tareas -->
    <div class="columna">
      <div v-for="(lista, index) in listas" :key="index" class="seccion">
        <h2>{{ titulos[index] }}</h2>

        <draggable v-model="listas[index]" :animation="300" group="tareas" class="listaTareas column-{{index}}":class="'column-' + index"@add="onTaskAdded($event, index)"">
          <template #item="{ element: tarea }">
            <div
              class="tarea"
              :class="{ 'tarea-completada': tarea.completado }"
              @mouseover="tarea.hover = true"
              @mouseleave="tarea.hover = false"
            >
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
                <p :class="{ completado: tarea.completado }">{{ tarea.titulo }}</p>
                <p>{{ tarea.descripcion }}</p>
              </div>

              <!-- ÍCONO DE EDICIÓN: sin animación, aparece al hacer hover si la tarea no está completada -->
              <span
                v-if="tarea.hover && !tarea.completado"
                class="editar-tarea"
                @click.stop="editarTarea(tarea)"
              >
              <svg fill="none" viewBox="0 0 16 16" role="presentation" class="css-1t4wpzr">
                <path stroke="currentcolor" stroke-linejoin="round" stroke-width="1.5" d="M6 1.751H3c-.69 0-1.25.56-1.25 1.25v10c0 .69.56 1.25 1.25 1.25h10c.69 0 1.25-.56 1.25-1.25V10m-.75-5 1.116-1.116a1.25 1.25 0 0 0 0-1.768l-.732-.732a1.25 1.25 0 0 0-1.768 0L11 2.5M13.5 5 9.479 9.021c-.15.15-.336.26-.54.318l-3.189.911.911-3.189a1.25 1.25 0 0 1 .318-.54L11 2.5M13.5 5 11 2.5"></path>
              </svg>

              </span>
            </div>
          </template>
        </draggable>

        <!-- Añadir nueva tarea a la lista -->
        <div>
          <input
            v-model="nuevasTareas[index]"
            type="text"
            class="anadir-tarea"
            placeholder="Agregar nueva tarea"
          />
          <button
            @click="agregarTarea(index, nuevasTareas[index], titulos[index])"
            class="anadir-tarea"
          >
            Añadir
          </button>
        </div>
      </div>
    </div>

    <div v-if="popupAbierto" class="popup-overlay">
  <div class="popup">
    <div class="popup-header">
      <h2>Editar Tarea</h2>
      <button class="popup-close" @click="popupAbierto = false">✕</button>
    </div>
    <div class="popup-content">
      <input
        v-model="tareaEditada.titulo"
        type="text"
        placeholder="Título de la tarea"
        class="popup-input titulo-input"
      />
      <!-- Se reemplaza el textarea por un input -->
      <input
        v-model="tareaEditada.descripcion"
        type="text"
        placeholder="Descripción"
        class="popup-input descripcion-input"
      />

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
                v-model="sub.completado"
                class="popup-checkbox"
              />
              <!-- Si no está en edición se muestra el texto; al hacer clic se activa la edición -->
              <template v-if="!sub.editing">
                <span :class="{ completado: sub.completado }" @click="sub.editing = true">
                  {{ sub.texto }}
                </span>
              </template>
              <!-- Modo edición: se muestra un input para editar el texto -->
              <template v-else>
                <input
                  type="text"
                  v-model="sub.texto"
                  class="subtarea-edit-input"
                  @blur="sub.editing = false"
                  @keydown.enter="sub.editing = false"
                />
              </template>
            </label>
            <button class="subtarea-delete" @click="eliminarSubtarea(i)">
              <svg fill="none" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                <path stroke="currentColor" stroke-width="1.5" d="M2.75 5.25h-1v-3.5h12.5v3.5h-1m-10.5 0V13c0 .69.56 1.25 1.25 1.25h8c.69 0 1.25-.56 1.25-1.25V5.25m-10.5 0h10.5m-7.75 3h5"/>
              </svg>
            </button>
          </li>
        </ul>
        <div class="popup-add-subtarea">
          <input
            v-model="nuevaSubtarea"
            type="text"
            placeholder="Añadir subtarea"
            class="popup-input"
          />
          <button @click="agregarSubtarea" class="popup-button">
            Añadir
          </button>
        </div>
      </div>
    </div>
    <div class="popup-actions">
      <button @click="actualizarTareaBackend" class="popup-button guardar-button">
        Guardar cambios
      </button>
    </div>
  </div>
</div>


</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import draggable from 'vuedraggable';
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

// Lista de listas (columnas) y títulos
const listas = ref([]);
onMounted(() => {
  listas.value = [sinEmpezar.value, enCurso.value, finalizadas.value, stopper.value];
});
const titulos = ['Sin Empezar', 'En Curso', 'Finalizadas', 'Stopper'];

// Variables para nuevas tareas y edición
const nuevasTareas = ref(['', '', '', '']);
const popupAbierto = ref(false);
const tareaEditada = ref(null);
const nuevaSubtarea = ref('');
const id_tablero = 1;

// Cargar tareas desde el backend al montar el componente
onMounted(async () => {
  try {
    const response = await axios.get('/api/kanbans');
    const kanbans = response.data;
    kanbans.forEach(kanban => {
      const tarea = {
        ...kanban,
        subtareas: kanban.subtareas || [],
        completado: kanban.completado || false,
        hover: false
      };

      // Usar el mapeo para determinar en qué columna va
      const columnaIndex = estadoAColumna[tarea.estado];
      if (columnaIndex !== undefined) {
        listas.value[columnaIndex].push(tarea);
      } else {
        // Si el estado no coincide con ninguno de los mapeos, ponerlo en Sin Empezar
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
// Función para agregar tarea (crea la tarea en el backend y la agrega a la lista local)
const agregarTarea = async (index, nuevaTarea, estado) => {
  if (nuevaTarea.trim()) {
    // Convertir el título de columna al estado de la base de datos
    const estadoBaseDatos = columnaAEstado[index];
    
    const tarea = {
      titulo: nuevaTarea.trim(),
      descripcion: '',
      subtareas: [],
      estado: estadoBaseDatos, // Usar el estado correcto para la BD
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
    subtareas: tarea.subtareas ? [...tarea.subtareas] : []
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
      await axios.put(`/api/kanban/${tareaEditada.value.id_tarea}`, tareaEditada.value);
      const lista = listas.value.find(list => list.some(t => t.id_tarea === tareaEditada.value.id_tarea));
      if (lista) {
        const idx = lista.findIndex(t => t.id_tarea === tareaEditada.value.id_tarea);
        if (idx !== -1) {
          lista[idx] = { ...tareaEditada.value };
        }
      }
      popupAbierto.value = false;
    } catch (error) {
      console.error('Error al actualizar la tarea:', error);
    }
  }
};

// Agrega una subtarea a la tarea en edición
const agregarSubtarea = () => {
  if (nuevaSubtarea.value.trim()) {
    if (!tareaEditada.value.subtareas) {
      tareaEditada.value.subtareas = [];
    }
    tareaEditada.value.subtareas.push({ texto: nuevaSubtarea.value.trim(), completado: false });
    nuevaSubtarea.value = '';
  }
};

// Elimina una subtarea de la tarea en edición
const eliminarSubtarea = (i) => {
  if (tareaEditada.value && tareaEditada.value.subtareas) {
    tareaEditada.value.subtareas.splice(i, 1);
  }
};
</script>

<style>
/* Contenedor de las columnas */
.columna {
  display: flex;
  gap: 20px;
  padding: 20px;
}
.seccion {
  flex: 1;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  border: 1px solid #ccc;
  min-width: 300px;
  max-width: 350px;
  display: flex;
  flex-direction: column;
}
.listaTareas {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Tarjeta de tarea */
.tarea {
  display: flex;
  align-items: center;
  background-color: #f7f7f7;
  padding: 12px;
  border-radius: 8px;
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: opacity 0.3s ease;
}
.tarea-completada {
  opacity: 0.7;
}

/* Círculo de completado con transición de opacidad */
.marcador {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid gray;
  margin-right: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  opacity: 0; /* Inicialmente oculto */
  transition: opacity 0.5s ease-in-out;
}

/* Cuando la tarea está en hover o completada, el círculo aparece */
.tarea:hover .marcador,
.tarea-completada .marcador {
  opacity: 1;
}

.marcador-visible {
  opacity: 1; /* Se hace visible */
}
.marcador-completado {
  background-color: #28a745;
  border-color: #28a745;
}
.check-icon {
  font-size: 14px;
  line-height: 20px;
  font-weight: bold;
}

/* Texto de la tarea: transición para mover el texto en 0.5s */
.tarea-texto {
  flex: 1;
  text-align: left;
  transition: transform 0.5s ease;
}
.tarea-texto.desplazado {
  transform: translateX(20px);
}
.completado {
  text-decoration: line-through;
  color: gray;
}

/* Ícono de edición sin animación */
.editar-tarea {
  margin-left: 10px;
  cursor: pointer;
}

/* Input y botón de añadir tarea */
.anadir-tarea {
  margin-top: 10px;
}
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
}

/* Contenedor del popup */
.popup {
  background: #fff;
  border-radius: 8px;
  width: 340px;
  max-width: 95%;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* Header del popup */
.popup-header {
  background: linear-gradient(135deg, #0079bf, #005a8e);
  color: #fff;
  padding: 12px 16px;
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
}

/* Contenido del popup */
.popup-content {
  padding: 16px;
  flex: 1;
}
.popup-input {
  width: 100%;
  padding: 10px;
  margin-bottom: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 15px;
}
.titulo-input {
  font-weight: bold;
  font-size: 16px;
}
.descripcion-input {
  font-style: italic;
}

/* Sección de subtareas */
.popup-subtareas h3 {
  margin-bottom: 8px;
  font-size: 16px;
}
.popup-subtareas ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.popup-subtarea-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px;
  background: #f9f9f9;
  border-radius: 4px;
  margin-bottom: 8px;
}
/* Alinear checkbox y texto o input */
.popup-subtarea-item label {
  display: flex;
  align-items: center;
  gap: 8px;
}
.popup-checkbox {
  margin: 0;
}
.subtarea-delete {
  background: transparent;
  border: none;
  color: #d9534f;
  font-size: 18px;
  cursor: pointer;
}

/* Estilo para el input en modo edición de subtarea */
.subtarea-edit-input {
  padding: 4px 6px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

/* Input y botón de añadir subtarea con la misma altura */
.popup-add-subtarea {
  display: flex;
  gap: 8px;
  margin-top: 8px;
}
.popup-add-subtarea input,
.popup-add-subtarea button {
  height: 40px;
  padding: 0 10px;
  border-radius: 4px;
  font-size: 15px;
  border: 1px solid #ccc;
}
.popup-add-subtarea button {
  background-color: #5aac44;
  color: #fff;
  border: none;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}
.popup-add-subtarea button:hover {
  background-color: #519839;
  transform: translateY(-2px);
}
.popup-add-subtarea button:active {
  transform: translateY(0);
  background-color: #498d32;
}

/* Botones generales */
.popup-button {
  padding: 10px 18px;
  border: none;
  border-radius: 4px;
  background-color: #5aac44;
  color: #fff;
  cursor: pointer;
  font-size: 15px;
  font-weight: bold;
  transition: background-color 0.3s ease, transform 0.2s ease;
}
.popup-button:hover {
  background-color: #519839;
  transform: translateY(-2px);
}
.popup-button:active {
  transform: translateY(0);
  background-color: #498d32;
}
.guardar-button {
  width: 100%;
}

/* Sección de acciones del popup */
.popup-actions {
  padding: 12px 16px;
  border-top: 1px solid #eee;
  display: flex;
  justify-content: flex-end;
  background: #f7f7f7;
}


textarea {
  width: 100%;
  height: 80px;
  margin: 10px 0;
}
</style>