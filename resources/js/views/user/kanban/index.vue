<template>
  <div>
    <!-- Sección de listas de tareas -->
    <div class="columna">
      <div v-for="(lista, index) in listas" :key="index" class="seccion">
        <h2>{{ titulos[index] }}</h2>

        <draggable v-model="listas[index]" :animation="300" group="tareas" class="listaTareas">
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
                ✏️
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

    <!-- Popup para editar tarea -->
    <div v-if="popupAbierto" class="popup-overlay">
      <div class="popup">
        <h2>Editar Tarea</h2>
        <input v-model="tareaEditada.titulo" type="text" placeholder="Título de la tarea" />
        <textarea v-model="tareaEditada.descripcion" placeholder="Descripción"></textarea>

        <h3>Subtareas</h3>
        <ul>
          <li v-for="(sub, i) in tareaEditada.subtareas" :key="i">
            <input type="checkbox" v-model="sub.completado" />
            <span :class="{ completado: sub.completado }">{{ sub.texto }}</span>
            <span @click="eliminarSubtarea(i)">❌</span>
          </li>
        </ul>

        <input v-model="nuevaSubtarea" type="text" placeholder="Añadir subtarea" />
        <button @click="agregarSubtarea">Añadir</button>

        <button @click="actualizarTareaBackend">Guardar cambios</button>
        <button @click="popupAbierto = false">Cerrar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import draggable from 'vuedraggable';

// Listas de tareas según estado
const sinEmpezar = ref([]);
const enCurso = ref([]);
const finalizadas = ref([]);
const stopper = ref([]);

// Lista de listas (columnas) y títulos
const listas = ref([sinEmpezar.value, enCurso.value, finalizadas.value, stopper.value]);
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
      if (tarea.estado === 'Sin Empezar') {
        sinEmpezar.value.push(tarea);
      } else if (tarea.estado === 'En Curso') {
        enCurso.value.push(tarea);
      } else if (tarea.estado === 'Finalizadas') {
        finalizadas.value.push(tarea);
      } else {
        stopper.value.push(tarea);
      }
    });
  } catch (error) {
    console.error('Error al cargar las tareas:', error);
  }
});

// Función para agregar tarea (crea la tarea en el backend y la agrega a la lista local)
const agregarTarea = async (index, nuevaTarea, estado) => {
  if (nuevaTarea.trim()) {
    const tarea = {
      titulo: nuevaTarea.trim(),
      descripcion: '',
      subtareas: [],
      estado,
      id_tablero: id_tablero,
      completado: false,
      hover: false
    };
    try {
      const response = await axios.post('/api/kanban', {
        titulo: tarea.titulo,
        descripcion: tarea.descripcion,
        estado: tarea.estado,
        id_tablero: 1
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

/* Popup de edición */
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
}
.popup {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 300px;
}
.popup h2 {
  margin-top: 0;
}
.popup ul {
  list-style: none;
  padding: 0;
}
.popup li {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 5px 0;
  padding: 5px;
  background: #f0f0f0;
  border-radius: 5px;
}
.popup input[type='checkbox'] {
  margin-right: 10px;
}
textarea {
  width: 100%;
  height: 80px;
  margin: 10px 0;
}
</style>