<template>
  <div class="workflow-builder">
    <header class="header">
      <h1 class="title">Flow Builder</h1>
      <button class="run-button" @click="runFlow">Run Flow</button>
    </header>

    <div class="main-content">
      <!-- Lista de bloques disponibles -->
      <aside class="blocks-list">
        <h2 class="blocks-title">Actions</h2>
        <div
          v-for="(block, index) in availableBlocks"
          :key="index"
          class="block-item"
          draggable="true"
          @dragstart="startDrag(block)"
          @click="addBlock(block)"
        >
          {{ block.name }}
        </div>
      </aside>

      <!-- Área de trabajo -->
      <main class="workflow-canvas" @drop="onDrop" @dragover.prevent>
        <svg class="connections">
          <line
            v-for="(connection, index) in connections"
            :key="index"
            :x1="connection.x1"
            :y1="connection.y1"
            :x2="connection.x2"
            :y2="connection.y2"
            stroke="#000000"
            stroke-width="2"
            marker-end="url(#arrowhead)"
          />
          <defs>
            <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="10" refY="3.5" orient="auto">
              <polygon points="0 0, 10 3.5, 0 7" fill="#000000" />
            </marker>
          </defs>
        </svg>

        <draggable v-model="blocks" item-key="id" class="draggable-area" @end="updateConnections">
          <template #item="{ element, index }">
            <div
              class="workflow-block"
              :style="getBlockStyle(element, index)"
              @mousedown="startDragBlock(index)"
              @mouseup="endDragBlock"
            >
              <h3 class="block-name">{{ element.name }}</h3>
              <p class="block-description">{{ element.description }}</p>
              <button class="remove-button" @click="showPopup(element.id)">❌</button>
            </div>
          </template>
        </draggable>
      </main>
    </div>

    <!-- Popup de confirmación -->
    <div v-if="showConfirmPopup" class="popup-overlay">
      <div class="popup">
        <p>¿Seguro que quieres eliminar la acción?</p>
        <button @click="confirmRemoveBlock">Sí</button>
        <button @click="hidePopup">No</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, nextTick } from "vue";
import draggable from "vuedraggable";
// Bloques disponibles para arrastrar
const availableBlocks = ref([
  { id: 1, name: "Iniciar", description: "Inicio del flujo" },
  { id: 2, name: "Enviar Email", description: "Envía un correo electrónico" },
  { id: 3, name: "Esperar", description: "Espera un tiempo antes de continuar" },
  { id: 4, name: "Condición SI/NO", description: "Ejecuta según una condición" },
]);

// Bloques en el flujo
const blocks = ref([]);

// Conexiones entre bloques
const connections = ref([]);

// Variable temporal para el bloque arrastrado
let draggedBlock = null;

// Estado del popup de confirmación
const showConfirmPopup = ref(false);
let blockToRemove = null;

// Iniciar arrastre
const startDrag = (block) => {
  draggedBlock = block;
};

// Soltar bloque en el área de trabajo
const onDrop = async () => {
  if (draggedBlock) {
    blocks.value.push({ ...draggedBlock, id: Date.now() });
    await nextTick();
    updateConnections();
    draggedBlock = null;
  }
};

// Agregar bloque al hacer clic
const addBlock = async (block) => {
  blocks.value.push({ ...block, id: Date.now() });
  await nextTick();
  updateConnections();
};

// Mostrar popup de confirmación
const showPopup = (id) => {
  blockToRemove = id;
  showConfirmPopup.value = true;
};

// Ocultar popup de confirmación
const hidePopup = () => {
  blockToRemove = null;
  showConfirmPopup.value = false;
};

// Confirmar eliminación de bloque
const confirmRemoveBlock = () => {
  removeBlock(blockToRemove);
  hidePopup();
};

// Eliminar bloque del flujo
const removeBlock = async (id) => {
  blocks.value = blocks.value.filter((block) => block.id !== id);
  await nextTick();
  updateConnections();
};

// Simular la ejecución del flujo
const runFlow = () => {
  console.log("Ejecutando flujo con los siguientes bloques:");
  console.log(blocks.value);
};

const updateConnections = () => {
  connections.value = []; // Reinicia las conexiones

  // Encuentra el contenedor del área de trabajo
  const canvas = document.querySelector(".workflow-canvas");

  if (!canvas) return;

  blocks.value.forEach((block, index) => {
    if (index < blocks.value.length - 1) {
      const currentBlock = document.querySelectorAll(".workflow-block")[index];
      const nextBlock = document.querySelectorAll(".workflow-block")[index + 1];

      if (currentBlock && nextBlock) {
        // Obtén las coordenadas relativas al contenedor
        const currentRect = currentBlock.getBoundingClientRect();
        const nextRect = nextBlock.getBoundingClientRect();
        const canvasRect = canvas.getBoundingClientRect();

        // Calcula posiciones relativas al contenedor del área de trabajo
        const x1 = currentRect.left + currentRect.width / 2 - canvasRect.left;
        const y1 = currentRect.bottom - canvasRect.top;
        const x2 = nextRect.left + nextRect.width / 2 - canvasRect.left;
        const y2 = nextRect.top - canvasRect.top;

        // Agrega la conexión
        connections.value.push({ x1, y1, x2, y2 });
      }
    }
  });
};

// Obtener estilo dinámico de los bloques
const getBlockStyle = (block, index) => {
  return {
    position: "relative",
    zIndex: index,
    left: '50%',
    transform: 'translateX(-50%)'
  };
};
</script>

<style scoped>
.workflow-builder {
  display: flex;
  flex-direction: column;
  height: 100vh; /* El contenedor ocupa toda la altura de la pantalla */
  background-color: #f4f4f4;
  border-radius: 12px;
  padding: 20px;
  gap: 20px;
}
.workflow-canvas {
  flex-grow: 1; /* Ajusta el tamaño automáticamente */
  height: 100%; /* Ocupa el alto completo del contenedor */
  background: #ffffff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: auto; /* Agrega scroll si los bloques superan el tamaño */
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><circle cx="1" cy="1" r="1" fill="%23ccc" /></svg>');
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #0078d4;
  color: #fff;
  padding: 15px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.title {
  font-size: 1.5rem;
  font-weight: bold;
}

.run-button {
  padding: 10px 20px;
  background: #005a9e;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s;
}

.run-button:hover {
  background: #003f73;
}

.main-content {
  display: flex;
  flex-grow: 1; /* Ocupa todo el espacio restante */
  gap: 20px;
  overflow: hidden; /* Evita que los bloques sobresalgan */
}

.blocks-list {
  width: 250px;
  background: #ffffff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  flex-shrink: 0;
}

.blocks-title {
  font-size: 1.25rem;
  font-weight: bold;
  margin-bottom: 10px;
}

.block-item {
  padding: 15px;
  background: #0078d4;
  color: #fff;
  border-radius: 8px;
  cursor: grab;
  margin-bottom: 10px;
  transition: background 0.3s;
}

.block-item:hover {
  background: #005a9e;
}

.workflow-canvas {
  flex-grow: 1;
  background: #ffffff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  position: relative;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><circle cx="1" cy="1" r="1" fill="%23ccc" /></svg>');
}

.connections {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 0;
}

.workflow-block {
  padding: 15px;
  background: #e1e1e1;
  border: 1px solid #ccc;
  border-radius: 8px;
  position: absolute; /* Asegura que los bloques se posicionen correctamente */
  transition: background 0.3s, transform 0.2s;
  cursor: grab;
  margin-bottom: 40px; /* Aumenta el espacio entre bloques */
  width: 200px; /* Hacer los bloques más estrechos */
}

.workflow-block:hover {
  background: #d1d1d1;
}

.block-name {
  font-size: 1rem;
  font-weight: bold;
  margin: 0;
}

.block-description {
  font-size: 0.875rem;
  color: #666;
}

.remove-button {
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 1.25rem;
  color: #ff4d4f;
  transition: color 0.3s;
}

.remove-button:hover {
  color: #ff1a1a;
}

/* Estilos para el popup de confirmación */
.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.popup {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.popup button {
  margin: 10px;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s;
}

.popup button:first-of-type {
  background: #ff4d4f;
  color: #fff;
}

.popup button:first-of-type:hover {
  background: #ff1a1a;
}

.popup button:last-of-type {
  background: #ccc;
}

.popup button:last-of-type:hover {
  background: #999;
}
</style>