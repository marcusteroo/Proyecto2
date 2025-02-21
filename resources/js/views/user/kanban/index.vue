<template>
  <div class="columna">
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
        <input v-model="nuevasTareas[index]" type="text" class="anadir-tarea" placeholder="Agregar nueva tarea"/>
        <button @click="agregarTarea(index, nuevasTareas[index])" class="anadir-tarea">Añadir</button>
      </div>
    </div>

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
import { ref } from 'vue';
import draggable from 'vuedraggable';

const sinEmpezar = ref([
  { nombre: 'Tarea 1', descripcion: 'Descripción 1', subtareas: [{ texto: 'Subtarea 1', completado: false }] },
  { nombre: 'Tarea 2', descripcion: 'Descripción 2', subtareas: [] }
]);
const enCurso = ref([{ nombre: 'Tarea 3', descripcion: 'Descripción 3', subtareas: [] }]);
const finalizadas = ref([]);
const stopper = ref([]);

const listas = ref([sinEmpezar.value, enCurso.value, finalizadas.value, stopper.value]);

const titulos = ['Sin Empezar', 'En Curso', 'Finalizadas', 'Stopper'];

const nuevasTareas = ref(['', '', '', '']);

const popupAbierto = ref(false);
const tareaEditada = ref(null);
const nuevaSubtarea = ref('');

const agregarTarea = (index, nuevaTarea) => {
  if (nuevaTarea.trim()) {
    listas.value[index].push({ nombre: nuevaTarea.trim(), descripcion: '', subtareas: [] });
    nuevasTareas.value[index] = '';
  }
};

const eliminarTarea = (index, tarea) => {
  const lista = listas.value[index];
  const i = lista.indexOf(tarea);
  if (i !== -1) {
    lista.splice(i, 1);
  }
};

const editarTarea = (tarea) => {
  tareaEditada.value = tarea;
  popupAbierto.value = true;
};

const agregarSubtarea = () => {
  if (nuevaSubtarea.value.trim()) {
    tareaEditada.value.subtareas.push({ texto: nuevaSubtarea.value.trim(), completado: false });
    nuevaSubtarea.value = '';
  }
};

const eliminarSubtarea = (index) => {
  tareaEditada.value.subtareas.splice(index, 1);
};
</script>

<style>
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