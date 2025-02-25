<template>
  <div class="columna">
    <!-- Sección de listas de tareas -->
    <div v-for="(lista, index) in listas" :key="index" class="seccion">
      <h2>{{ titulos[index] }}</h2>
      <draggable v-model="listas[index]" :animation="300" group="tareas" class="listaTareas">
        <template #item="{ element: tarea }">
          <div class="tarea">
            <div @click="editarTarea(tarea)">
              <p>{{ tarea.titulo }}</p><br />
              <p>{{ tarea.descripcion }}</p>
            </div>
            <div>
              <span @click="editarTarea(tarea)" class="editar-tarea">✏️</span>
              <span @click="eliminarTarea(index, tarea)" class="borrar-tarea">🗑️</span>
            </div>
          </div>
        </template>
      </draggable>
      <div>
        <input v-model="nuevasTareas[index]" type="text" class="anadir-tarea" placeholder="Agregar nueva tarea" />
        <button @click="agregarTarea(index, nuevasTareas[index], titulos[index])" class="anadir-tarea">Añadir</button>
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
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import draggable from 'vuedraggable';

// Definición de listas según el estado
const sinEmpezar = ref([]);
const enCurso = ref([]);
const finalizadas = ref([]);
const stopper = ref([]);

// Lista de listas (columnas)
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
      // Aseguramos que la tarea tenga una propiedad "subtareas"
      const tarea = { ...kanban, subtareas: kanban.subtareas || [] };
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
    // Creamos el objeto tarea sin id_tarea (que se asignará tras crearla en el backend)
    const tarea = { 
      titulo: nuevaTarea.trim(), 
      descripcion: '', 
      subtareas: [], 
      estado,
      id_tablero: id_tablero.value 
    };
    try {
      // Enviamos solo los datos necesarios para la creación de la tarea
      const response = await axios.post('/api/kanban', {
        titulo: tarea.titulo,
        descripcion: tarea.descripcion,
        estado: tarea.estado,
        id_tablero: 1
      });
      // Se asume que el backend devuelve la propiedad "id_tarea" creada
      tarea.id_tarea = response.data.id_tarea;
      listas.value[index].push(tarea);
      nuevasTareas.value[index] = '';
    } catch (error) {
      console.error('Error al agregar la tarea:', error);
    }
  }
};


// Función para eliminar tarea (elimina en el backend y actualiza la lista local)
const eliminarTarea = async (index, tarea) => {
  if (!tarea.id_tarea) {
    console.error("La tarea no tiene un id_tarea válido.");
    return;
  }
  try {
    await axios.delete(`/api/kanban/${tarea.id_tarea}`);
    listas.value[index] = listas.value[index].filter(t => t.id_tarea !== tarea.id_tarea);
  } catch (error) {
    console.error('Error al eliminar la tarea:', error);
  }
};

// Función para preparar la edición de una tarea (se abre el popup con la tarea clonada)
const editarTarea = (tarea) => {
  tareaEditada.value = { ...tarea, subtareas: tarea.subtareas ? [...tarea.subtareas] : [] };
  popupAbierto.value = true;
};

// Función para actualizar la tarea en el backend y en la lista local
const actualizarTareaBackend = async () => {
  if (tareaEditada.value) {
    if (!tareaEditada.value.id_tarea) {
      console.error("La tarea no tiene un id_tarea válido.");
      return;
    }
    try {
      await axios.put(`/api/kanban/${tareaEditada.value.id_tarea}`, tareaEditada.value), {
      id_tarea: tareaEditada.value.id_tarea, // Cambio: de tablero_id a id_tarea
        titulo: tareaEditada.value.titulo,
        descripcion: tareaEditada.value.descripcion,
        estado: tareaEditada.value.estado,
        id_tablero: tareaEditada.value.id_tablero
      };
      // Actualizar la lista local: buscamos la lista que contiene la tarea editada
      const lista = listas.value.find(list => list.some(t => t.id_tarea === tareaEditada.value.id_tarea));
      if (lista) {
        const idx = lista.findIndex(t => t.id_tarea === tareaEditada.value.id_tarea);
        if (idx !== -1) {
          lista[idx] = { ...tareaEditada.value };
        }
      }
      popupAbierto.value = false; // Cerrar el popup
    } catch (error) {
      console.error('Error al actualizar la tarea:', error);
    }
  }
};




// Función para agregar una subtarea a la tarea en edición
const agregarSubtarea = () => {
  if (nuevaSubtarea.value.trim()) {
    if (!tareaEditada.value.subtareas) {
      tareaEditada.value.subtareas = [];
    }
    tareaEditada.value.subtareas.push({ texto: nuevaSubtarea.value.trim(), completado: false });
    nuevaSubtarea.value = '';
  }
};

// Función para eliminar una subtarea de la tarea en edición
const eliminarSubtarea = (i) => {
  if (tareaEditada.value && tareaEditada.value.subtareas) {
    tareaEditada.value.subtareas.splice(i, 1);
  }
};
</script>

<style>
/* Estilos para la columna de tareas */
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
.tarea {
  padding: 12px;
  border-radius: 8px;
  background-color: #f7f7f7;
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
}
.editar-tarea,
.borrar-tarea {
  cursor: pointer;
  margin-left: 8px;
}
.anadir-tarea {
  margin-top: 10px;
}

/* Estilos para el popup de edición de tarea */
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
  text-align: center;
  width: 300px;
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
.popup input[type="checkbox"] {
  margin-right: 10px;
}
textarea {
  width: 100%;
  height: 80px;
  margin: 10px 0;
}
.completado {
  text-decoration: line-through;
  color: gray;
}
</style>
