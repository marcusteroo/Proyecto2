<template>
  <div class="columna">
    <div class="seccion">
      <h2>Sin Empezar</h2>
      <draggable v-model="sinEmpezar" :animation="300" group="tareas" class="listaTareas">
        <template #item="{ element: tarea }">
          <div class="tarea">
            {{ tarea.nombre }}
            {{ tarea.descripcion }}
            <span @click="eliminarTarea(sinEmpezar, tarea)" class="borrar-tarea">🗑️</span>
          </div>
        </template>
      </draggable>
      <div>
        <input v-model="nuevaTareaSinEmpezar" type="text" class="anadir-tarea" placeholder="Agregar nueva tarea"/>
        <button @click="agregarTarea(sinEmpezar, nuevaTareaSinEmpezar)" class="anadir-tarea">Añadir</button>
      </div>
    </div>

    <div class="seccion">
      <h2>En Curso</h2>
      <draggable v-model="enCurso" :animation="300" group="tareas" class="listaTareas">
        <template #item="{ element: tarea }">
          <div class="tarea">
            {{ tarea.nombre }}
            {{ tarea.descripcion }}
            <span @click="eliminarTarea(enCurso, tarea)" class="borrar-tarea">🗑️</span>
          </div>
        </template>
      </draggable>
      <div>
        <input v-model="nuevaTareaEnCurso" type="text" class="anadir-tarea" placeholder="Agregar nueva tarea"/>
        <button @click="agregarTarea(enCurso, nuevaTareaEnCurso)" class="anadir-tarea">Añadir</button>
      </div>
    </div>

    <div class="seccion">
      <h2>Finalizadas</h2>
      <draggable v-model="finalizadas" :animation="300" group="tareas" class="listaTareas">
        <template #item="{ element: tarea }">
          <div class="tarea">
            {{ tarea.nombre }}
            {{ tarea.descripcion }}
            <span @click="eliminarTarea(finalizadas, tarea)" class="borrar-tarea">🗑️</span>
          </div>
        </template>
      </draggable>
      <div>
        <input v-model="nuevaTareaFinalizadas" type="text" class="anadir-tarea" placeholder="Agregar nueva tarea"/>
        <button @click="agregarTarea(finalizadas, nuevaTareaFinalizadas)" class="anadir-tarea">Añadir</button>
      </div>
    </div>

    <div class="seccion">
      <h2>Stopper</h2>
      <draggable v-model="stopper" :animation="300" group="tareas" class="listaTareas">
        <template #item="{ element: tarea }">
          <div class="tarea">
            {{ tarea.nombre }}
            {{ tarea.descripcion }}
            <span @click="eliminarTarea(stopper, tarea)" class="borrar-tarea">🗑️</span>
          </div>
        </template>
      </draggable>
      <div>
        <input v-model="nuevaTareaStopper" type="text" class="anadir-tarea" placeholder="Agregar nueva tarea"/>
        <button @click="agregarTarea(stopper, nuevaTareaStopper)" class="anadir-tarea">Añadir</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import draggable from 'vuedraggable';

const sinEmpezar = ref([
  { nombre: 'Tarea 1', descripcion: 'Descripción 1', img:'', puntos: ['punto 1', 'punto 2']},
  { nombre: 'Tarea 2', descripcion: 'Descripción 2', img:'', puntos: ['punto 1', 'punto 2']},
  { nombre: 'Tarea 3', descripcion: 'Descripción 3', img:'', puntos: ['punto 1', 'punto 2']}
]);

const enCurso = ref([
  { nombre: 'Tarea 4', descripcion: 'Descripción 4', img:'', puntos: ['punto 1', 'punto 2']},
  { nombre: 'Tarea 5', descripcion: 'Descripción 5', img:'', puntos: ['punto 1', 'punto 2']}
]);

const finalizadas = ref([]);
const stopper = ref([]);

const nuevaTarea = ref('');
const nuevaTareaSinEmpezar = ref('');
const nuevaTareaEnCurso = ref('');
const nuevaTareaFinalizadas = ref('');
const nuevaTareaStopper = ref('');

// Estado para el pop-up
const showModal = ref(false);
const tareaSeleccionada = ref(null);

// Función para mostrar el pop-up
const mostrarModal = (tarea) => {
  tareaSeleccionada.value = { ...tarea }; // Crear una copia de la tarea
  showModal.value = true;
};

// Función para cerrar el pop-up
const cerrarModal = () => {
  showModal.value = false;
  tareaSeleccionada.value = null;
};

// Función para actualizar la descripción de la tarea
const actualizarDescripcion = () => {
  if (tareaSeleccionada.value) {
    // Buscar la tarea en la lista y actualizar la descripción
    const lista = [...sinEmpezar.value, ...enCurso.value, ...finalizadas.value, ...stopper.value];
    const tareaIndex = lista.findIndex(t => t.nombre === tareaSeleccionada.value.nombre);
    if (tareaIndex !== -1) {
      lista[tareaIndex].descripcion = tareaSeleccionada.value.descripcion;
    }
  }
};

// Función para agregar tarea
const agregarTarea = (lista, nuevaTarea) => {
  if (nuevaTarea.trim()) {
    lista.push({ nombre: nuevaTarea.trim(), descripcion: '' });
    nuevaTarea.value = '';
  }
};

// Función para eliminar tarea
const eliminarTarea = (lista, tarea) => {
  const index = lista.indexOf(tarea);
  if (index !== -1) {
    lista.splice(index, 1);
  }
};
</script>

<style>
h2 {
  font-size: 1.2rem;
  margin-bottom: 10px;
  text-align: center;
  font-weight: bold;
}
.columna {
  min-height: 90vh;
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
.tarea {
  padding: 12px;
  border-radius: 8px;
  text-align: center;
  font-weight: bold;
  background-color: #f7f7f7;
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.listaTareas {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
</style>