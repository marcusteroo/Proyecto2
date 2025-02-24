<template>
  <div class="columna">
    <!-- Sección de listas de tareas -->
    <div v-for="(lista, index) in listas" :key="index" class="seccion">
      <h2>{{ titulos[index] }}</h2>
      <draggable v-model="listas[index]" :animation="300" group="tareas" class="listaTareas">
        <template #item="{ element: tarea }">
          <div class="tarea">
            <div @click="editarTarea(tarea)">
              <strong>{{ tarea.nombre }}</strong><br />
              <small>{{ tarea.descripcion }}</small>
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

    <!-- Popup para editar tarea -->
    <div v-if="popupAbierto" class="popup-overlay">
      <div class="popup">
        <h2>Editar Tarea</h2>
        <input v-model="tareaEditada.nombre" type="text" placeholder="Nombre de la tarea" />
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

        <button @click="popupAbierto = false">Cerrar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import draggable from 'vuedraggable';

const sinEmpezar = ref([]);
const enCurso = ref([]);
const finalizadas = ref([]);
const stopper = ref([]);

const listas = ref([sinEmpezar.value, enCurso.value, finalizadas.value, stopper.value]);
const titulos = ['Sin Empezar', 'En Curso', 'Finalizadas', 'Stopper'];

const nuevasTareas = ref(['', '', '', '']);
const nuevaTarea = ref('');
const descripcionTarea = ref('');
const idCreador = ref(1); // Suponiendo que el ID del creador es 1, cámbialo según lo necesites

const popupAbierto = ref(false);
const tareaEditada = ref(null);
const nuevaSubtarea = ref('');

// Cargar las tareas del backend cuando el componente se monta
onMounted(async () => {
  try {
    const response = await axios.get('/api/kanbans');
    const kanbans = response.data;

    // Distribuir las tareas en sus respectivas listas
    kanbans.forEach(kanban => {
      if (kanban.estado === 'Sin Empezar') {
        sinEmpezar.value.push({ ...kanban, subtareas: [] });
      } else if (kanban.estado === 'En Curso') {
        enCurso.value.push({ ...kanban, subtareas: [] });
      } else if (kanban.estado === 'Finalizadas') {
        finalizadas.value.push({ ...kanban, subtareas: [] });
      } else {
        stopper.value.push({ ...kanban, subtareas: [] });
      }
    });
  } catch (error) {
    console.error('Error al cargar las tareas:', error);
  }
});

// Función para agregar tarea a la vista y al backend
const agregarTarea = (index, nuevaTarea, estado) => {
  if (nuevaTarea.trim()) {
    const tarea = { nombre: nuevaTarea.trim(), descripcion: '', subtareas: [], estado };
    listas.value[index].push(tarea);
    nuevasTareas.value[index] = '';

    // Agregar tarea al backend
    agregarTareaBackend(tarea);
  }
};

// Función para eliminar tarea
const eliminarTarea = async (index, tarea) => {
  try {
    const response = await axios.delete(`/api/kanban/${tarea.id}`);
    // Eliminar la tarea de la lista local
    const lista = listas.value[index];
    const i = lista.indexOf(tarea);
    if (i !== -1) {
      lista.splice(i, 1); // Eliminar tarea de la lista
    }
    console.log('Tarea eliminada:', response.data);
  } catch (error) {
    console.error('Error al eliminar la tarea:', error);
    }
};
const editarTarea = async (tarea) => {
  tareaEditada.value = { ...tarea }; // Copiar tarea para edición
  popupAbierto.value = true;
};

// Función para actualizar tarea en el backend
const actualizarTareaBackend = async () => {
  if (tareaEditada.value) {
    try {
      const response = await axios.put(`/api/kanbans/${tareaEditada.value.id}`, {
        nombre: tareaEditada.value.nombre,
        descripcion: tareaEditada.value.descripcion,
        estado: tareaEditada.value.estado, // Se mantendrá el estado actual
        subtareas: tareaEditada.value.subtareas
      });
      // Actualizar la lista local con los datos modificados
      const lista = listas.value.find(list => list.some(t => t.id === tareaEditada.value.id));
      const index = lista.findIndex(t => t.id === tareaEditada.value.id);
      if (index !== -1) {
        lista[index] = response.data; // Actualizar la tarea editada en la lista
      }
      popupAbierto.value = false; // Cerrar el popup
    } catch (error) {
      console.error('Error al actualizar la tarea:', error);
    }
  }
};

// Función para agregar subtarea
const agregarSubtarea = () => {
  if (nuevaSubtarea.value.trim()) {
    tareaEditada.value.subtareas.push({ texto: nuevaSubtarea.value.trim(), completado: false });
    nuevaSubtarea.value = '';
  }
};

// Función para eliminar subtarea
const eliminarSubtarea = (index) => {
  tareaEditada.value.subtareas.splice(index, 1);
};

// Función para agregar tarea al backend
const agregarTareaBackend = async (tarea) => {
  try {
    const response = await axios.post('/api/kanbans', {
      nombre: tarea.nombre,
      descripcion: tarea.descripcion,
      estado: tarea.estado,
      id_creador: idCreador.value,
    });

    console.log('Tarea creada:', response.data);
  } catch (error) {
    console.error('Error al crear la tarea:', error);
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
.editar-tarea, .borrar-tarea {
  cursor: pointer;
  margin-left: 8px;
}
.anadir-tarea {
  margin-top: 10px;
}

/* Estilos para el formulario */
.formulario {
  margin-top: 30px;
}
.formulario input, .formulario textarea {
  width: 100%;
  padding: 8px;
  margin: 5px 0;
  border: 1px solid #ccc;
  border-radius: 5px;
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
