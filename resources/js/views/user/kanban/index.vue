<script setup>
import { ref } from 'vue';
import draggable from 'vuedraggable';

const sinEmpezar = ref(['Tarea 1', 'Tarea 2', 'Tarea 3']);
const enCurso = ref(['Tarea 4', 'Tarea 5']);
const finalizadas = ref(['Tarea 6']);
const stopper = ref(['Tarea 7']);

const nuevaTareaSinEmpezar = ref('');
const nuevaTareaEnCurso = ref('');
const nuevaTareaFinalizadas = ref('');
const nuevaTareaStopper = ref('');

const agregarTarea = (lista, tarea) => {
  if (tarea.trim()) {
    lista.push(tarea.trim());
  }
};

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

<template>
  <div class="columna">
    <div class="seccion">
      <h2>Sin Empezar</h2>
      <draggable v-model="sinEmpezar" :animation="300" group="tasks" class="listaTareas">
        <template #item="{ element: task }">
          <div class="tarea">
            {{ task }}
            <span @click="eliminarTarea(sinEmpezar, task)" class="delete-button">🗑️</span>
          </div>
        </template>
      </draggable>
      <div>
        <input v-model="nuevaTareaSinEmpezar" type="text" class="input-tarea" placeholder="Agregar nueva tarea"
        />
        <button @click="agregarTarea(sinEmpezar, nuevaTareaSinEmpezar)" class="add-button">Añadir</button>
      </div>
    </div>

    <div class="seccion">
      <h2>En Curso</h2>
      <draggable v-model="enCurso" :animation="300" group="tasks" class="listaTareas">
        <template #item="{ element: task }">
          <div class="tarea">
            {{ task }}
            <span @click="eliminarTarea(enCurso, task)" class="delete-button">🗑️</span>
          </div>
        </template>
      </draggable>
      <div>
        <input v-model="nuevaTareaEnCurso" type="text" class="input-tarea" placeholder="Agregar nueva tarea"/>
        <button @click="agregarTarea(enCurso, nuevaTareaEnCurso)" class="add-button">Añadir</button>
      </div>
    </div>

    <div class="seccion">
      <h2>Finalizadas</h2>
      <draggable v-model="finalizadas" :animation="300" group="tasks" class="listaTareas">
        <template #item="{ element: task }">
          <div class="tarea">
            {{ task }}
            <span @click="eliminarTarea(finalizadas, task)" class="delete-button">🗑️</span>
          </div>
        </template>
      </draggable>
      <div>
        <input
          v-model="nuevaTareaFinalizadas"
          type="text"
          class="input-tarea"
          placeholder="Agregar nueva tarea"
        />
        <button @click="agregarTarea(finalizadas, nuevaTareaFinalizadas)" class="add-button">Añadir</button>
      </div>
    </div>

    <div class="seccion">
      <h2>Stopper</h2>
      <draggable v-model="stopper" :animation="300" group="tasks" class="listaTareas">
        <template #item="{ element: task }">
          <div class="tarea">
            {{ task }}
            <span @click="eliminarTarea(stopper, task)" class="delete-button">🗑️</span>
          </div>
        </template>
      </draggable>
      <div>
        <input
          v-model="nuevaTareaStopper"
          type="text"
          class="input-tarea"
          placeholder="Agregar nueva tarea"
        />
        <button @click="agregarTarea(stopper, nuevaTareaStopper)" class="add-button">Añadir</button>
      </div>
    </div>
  </div>
</template>
