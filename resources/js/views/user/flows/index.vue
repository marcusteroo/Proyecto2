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
          @dragstart="(event) => startDrag(event, block)"
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
import { ref, reactive, nextTick, watch } from "vue";
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
const startDrag = (event, block) => {
  draggedBlock = block;

  // Usar el tamaño original
  const rect = event.target.getBoundingClientRect();
  const dragImage = event.target.cloneNode(true);
  dragImage.style.position = "absolute";
  dragImage.style.width = rect.width + "px";
  dragImage.style.height = rect.height + "px";
  dragImage.style.top = "-9999px";
  dragImage.style.left = "-9999px";
  dragImage.style.opacity = "1";
  dragImage.setAttribute("tabindex", "-1");

  document.body.appendChild(dragImage);
  event.dataTransfer.setDragImage(dragImage, rect.width / 2, rect.height / 2);
  event.dataTransfer.effectAllowed = "move";
  event.dataTransfer.setData("text/plain", JSON.stringify(block));
};

// Crear/soltar bloque en el centro
const placeBlockCentered = async (block) => {
  const canvas = document.querySelector(".workflow-canvas");
  if (!canvas) return;

  const rect = canvas.getBoundingClientRect();
  const x = rect.width / 2; // Centro horizontal
  const y = blocks.value.length * 100 + 20; // Posición vertical secuencial con margen

  blocks.value.push({ ...block, id: Date.now(), x, y });
  await nextTick();
  updateConnections();
};

// Añadir bloque al hacer clic
const addBlock = (block) => {
  placeBlockCentered(block);
};

// Manejar soltado del drag
const onDrop = async (event) => {
  if (draggedBlock) {
    await placeBlockCentered(draggedBlock);
    draggedBlock = null;
  }
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

// Ajustar estilos para posicionar en x, y
const getBlockStyle = (block, index) => {
  return {
    position: "absolute",
    zIndex: index,
    left: `calc(50% - 175px)`, // Centra asumiendo ancho ~350px
    top: `${block.y}px`,
    transform: 'none'
  };
};
watch(showConfirmPopup, (val) => {
  if (val) {
    document.body.classList.add('no-scroll');
  } else {
    document.body.classList.remove('no-scroll');
  }
});

</script>

<style scoped>
.workflow-builder {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: #f4f4f4;
  border-radius: 12px;
  padding: 20px;
  gap: 20px;
}
.workflow-canvas {
  position: relative;
  flex-grow: 1;
  height: 100%;
  background: #ffffff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  overflow: auto;
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
  transition: background 0.3s, transform 0.2s ease-in-out;
}

.block-item:hover {
  background: #005a9e;
}

.block-item:active {
  transform: none; /* Desactivar el efecto de zoom cuando se arrastra */
  opacity: 0.7;
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
  display: flex;
  justify-content: center; /* Centrar el contenido horizontalmente */
  align-items: center; /* Centrar el contenido verticalmente */
  padding: 15px;
  background: #f0f4f8;
  border: 2px solid #0078d4; /* Borde azul más profesional */
  border-radius: 8px;
  position: absolute; /* Asegura que los bloques se posicionen correctamente */
  transition: background 0.3s, transform 0.2s;
  cursor: grab;
  margin-bottom: 40px; /* Aumenta el espacio entre bloques */
  width: 350px; /* Hacer los bloques más anchos */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.workflow-block:hover {
  background: #e1e1e1;
}

.block-name {
  font-size: 1rem;
  font-weight: bold;
  margin: 0;
  text-align: center; /* Centrar el texto */
}

.remove-button {
  position: absolute;
  top: 10px;
  right: 10px;
  background: transparent;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  color: #ff4d4f;
  transition: background 0.3s, color 0.3s;
  padding: 5px 10px;
}

.remove-button:hover {
  color: #fff;
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
[draggable]:focus {
    outline: none !important;
}
.no-scroll {
  overflow: hidden;
  position: fixed;
  width: 100%;
}



</style>