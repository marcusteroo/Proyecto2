<template>
    <div class="workflow-builder">
      <header class="header">
        <h1 class="title">Flow Builder</h1>
        <button class="run-button" @click="saveFlow">Guardar</button>
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
          <p>¿Seguro que quieres eliminar la acciónk?</p>
          <button @click="confirmRemoveBlock">Sí</button>
          <button @click="hidePopup">No</button>
        </div>
      </div>
    </div>
    <div class="flow-info">
      <input v-model="flowName" type="text" placeholder="Nombre del flujo" />
      <input v-model="flowDescription" type="text" placeholder="Descripción del flujo" />
    </div>
  </template>
  
    <script setup>
        import { ref, onMounted, watch } from "vue";
        import { useRoute } from "vue-router";
        import axios from "axios";

        const route = useRoute();
        const flowId = ref(null);
        const flowName = ref("");
        const flowDescription = ref("");
        const blocks = ref([]);
        const connections = ref([]);

        onMounted(async () => {
            if (route.params.id) {
                flowId.value = route.params.id;
                await loadFlowData(flowId.value);
            }
        });

        const loadFlowData = async (id) => {
            try {
                const response = await axios.get(`/api/workflows/${id}`);
                const workflow = response.data;

                flowName.value = workflow.nombre;
                flowDescription.value = workflow.descripcion;

                const actionsResponse = await axios.get(`/api/workflows/${id}/actions`);
                blocks.value = actionsResponse.data.map(action => ({
                    id: action.id_action,
                    name: action.name,
                    description: action.description,
                    x: action.x_position,
                    y: action.y_position
                }));

                updateConnections();
            } catch (error) {
                console.error("Error al cargar el workflow:", error);
            }
        };
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