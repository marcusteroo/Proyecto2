<template>
  <div class="workflow-builder">
    <!-- Cabecera -->
    <header class="header">
      <!-- En lugar de Flow Builder, un input editable para el usuario -->
      <input
        class="title"
        v-model="flowName"
        placeholder="(Sin nombre)"
      />
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
            stroke="#000"
            stroke-width="2"
            marker-end="url(#arrowhead)"
          />
          <defs>
            <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="10" refY="3.5" orient="auto">
              <polygon points="0 0, 10 3.5, 0 7" fill="#000" />
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
        <p>¿Seguro que quieres eliminar la acción y las siguientes?</p>
        <button @click="confirmRemoveBlock">Sí</button>
        <button @click="hidePopup">No</button>
      </div>
    </div>
  </div>
  <div v-if="showTaskPopup" class="popup-overlay">
    <div class="popup">
      <h3>Configurar desencadenador</h3>
      
      <!-- Selector de tableros -->
      <div class="form-group">
        <label>Seleccione un tablero:</label>
        <select v-model="selectedBoard" @change="loadTasksForBoard" class="form-select">
          <option value="">Seleccionar tablero</option>
          <option v-for="board in boards" :key="board.id" :value="board">
            {{ board.title }}
          </option>
        </select>
      </div>
      
      <!-- Selector de tareas (aparece cuando se selecciona un tablero) -->
      <div class="form-group" v-if="selectedBoard">
        <label>Seleccione una tarea:</label>
        <select v-model="selectedTask" class="form-select">
          <option value="">Seleccionar tarea</option>
          <option v-for="task in tasks" :key="task.id" :value="task">
            {{ task.title }}
          </option>
        </select>
      </div>
      
      <!-- Selector de estados (aparece cuando se selecciona una tarea) -->
      <div class="form-group" v-if="selectedTask">
        <label>Seleccione un estado:</label>
        <select v-model="selectedStatus" class="form-select">
          <option value="">Seleccionar estado</option>
          <option value="Pendiente">Pendiente</option>
          <option value="En curso">En curso</option>
          <option value="Completado">Completado</option>
          <option value="Stoper">Stoper</option>
        </select>
      </div>
      
      <!-- Botones de acción -->
      <div class="popup-actions">
        <button 
          @click="confirmTaskSetup" 
          class="confirm-btn" 
          :disabled="!selectedBoard || !selectedTask || !selectedStatus">
          Confirmar
        </button>
        <button @click="closeTaskPopup" class="cancel-btn">Cancelar</button>
      </div>
    </div>
    <div v-if="showEmailPopup" class="popup-overlay">
    <div class="popup">
      <h3>Configurar Email</h3>
      
      <div class="form-group">
        <label>Destinatario:</label>
        <input 
          type="email" 
          v-model="emailConfig.to" 
          class="form-input" 
          placeholder="correo@ejemplo.com"
        />
      </div>
      
      <div class="form-group">
        <label>Asunto:</label>
        <input 
          type="text" 
          v-model="emailConfig.subject" 
          class="form-input" 
          placeholder="Asunto del correo"
        />
      </div>
      
      <div class="form-group">
        <label>Mensaje:</label>
        <textarea 
          v-model="emailConfig.message" 
          class="form-textarea" 
          rows="4"
          placeholder="Contenido del correo..."
        ></textarea>
      </div>
      
      <div class="popup-actions">
        <button 
          @click="confirmEmailSetup" 
          class="confirm-btn" 
          :disabled="!emailConfig.to || !emailConfig.subject || !emailConfig.message">
          Confirmar
        </button>
        <button @click="closeEmailPopup" class="cancel-btn">Cancelar</button>
      </div>
    </div>
  </div>
  </div>
</template>

<script setup>
import { ref, reactive, nextTick, watch, onMounted } from "vue";
import { useRoute } from "vue-router";
import draggable from "vuedraggable";
import axios from "axios";

// Bloques base
const availableBlocks = ref([
  { id: 1, name: "Tarea", description: "Inicio del flujo con Tarea (desencadenador)" },
  { id: 2, name: "Enviar Email", description: "Envía un correo electrónico" },
  { id: 3, name: "Esperar", description: "Espera un tiempo antes de continuar" },
]);
const showEmailPopup = ref(false);
const emailConfig = reactive({
  to: '',
  subject: '',
  message: ''
});
const showTaskPopup = ref(false);
const boards = ref([]);
const tasks = ref([]);
const selectedBoard = ref(null);
const selectedTask = ref(null);
const selectedStatus = ref("");
onMounted(async () => {
  try {
    const response = await axios.get('/api/kanban');
    boards.value = response.data;
  } catch (error) {
    console.error('Error al cargar tableros:', error);
  }
});
const loadTasksForBoard = async () => {
  if (!selectedBoard.value) return;
  
  try {
    const response = await axios.get(`/api/kanban/${selectedBoard.value.id}/tasks`);
    tasks.value = response.data;
    selectedTask.value = null; // Resetear la selección de tarea
    selectedStatus.value = ""; // Resetear la selección de estado
  } catch (error) {
    console.error('Error al cargar tareas:', error);
  }
};
// Bloques en el flujo
const blocks = ref([]);

// Conexiones entre bloques
const connections = ref([]);

// Para manejar arrastre
let draggedBlock = null;

// Popup de confirmación
const showConfirmPopup = ref(false);
let blockToRemove = null;

// Nombre y descripción del flujo, vacíos por defecto
const flowName = ref('');
const flowDescription = ref('');

// Cada vez que cambie el flowName, se actualiza automáticamente la descripción
watch(flowName, (newVal) => {
  const today = new Date().toISOString().split('T')[0];
  flowDescription.value = `${newVal} automatizacion ${today}`;
});

// Iniciar arrastre
const startDrag = (event, block) => {
  draggedBlock = block;
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

// Crear/soltar bloque al centro
const placeBlockCentered = async (block) => {
  const canvas = document.querySelector(".workflow-canvas");
  if (!canvas) return;
  const rect = canvas.getBoundingClientRect();
  const x = rect.width / 2;
  const y = blocks.value.length * 100 + 20;
  blocks.value.push({ ...block, id: Date.now(), x, y });
  await nextTick();
  updateConnections();
};

const addBlock = (block) => {
  if (block.name === "Tarea") {
    showTaskPopup.value = true;
    return;
  } else if (block.name === "Enviar Email") {
    showEmailPopup.value = true;
    return;
  }
  
  // Si aún no hay bloques, solo se permite "Tarea"
  if (blocks.value.length === 0 && block.name !== "Tarea") {
      alert("La primera acción debe ser Tarea ya que es el desencadenador.");
      return;
  }

  // Limita a 3 acciones
  if (blocks.value.length >= 3) {
      alert("No se pueden agregar más de 3 acciones.");
      return;
  }

  // Evita duplicados
  if (blocks.value.some(b => b.name === block.name)) {
      alert("Esta acción ya fue agregada.");
      return;
  }

  placeBlockCentered(block);
};
const closeEmailPopup = () => {
  showEmailPopup.value = false;
  emailConfig.to = '';
  emailConfig.subject = '';
  emailConfig.message = '';
};
const closeTaskPopup = () => {
  showTaskPopup.value = false;
  selectedBoard.value = null;
  selectedTask.value = null;
  selectedStatus.value = "";
};
// Manejar el drop
const onDrop = async (event) => {
  if (draggedBlock) {
    await placeBlockCentered(draggedBlock);
    draggedBlock = null;
  }
};
const confirmEmailSetup = () => {
  if (!emailConfig.to || !emailConfig.subject || !emailConfig.message) {
    alert("Por favor, complete todos los campos del email.");
    return;
  }

  // Crear un bloque de Email con la configuración seleccionada
  const emailBlock = {
    id: Date.now(),
    name: "Enviar Email",
    description: "Enviar correo electrónico",
    config: {
      to: emailConfig.to,
      subject: emailConfig.subject,
      message: emailConfig.message
    }
  };

  // Colocar el bloque en el canvas
  placeBlockCentered(emailBlock);
  
  // Cerrar el popup
  closeEmailPopup();
};
// Mostrar popup
const showPopup = (id) => {
  blockToRemove = id;
  showConfirmPopup.value = true;
};

// Ocultar popup
const hidePopup = () => {
  blockToRemove = null;
  showConfirmPopup.value = false;
};

// Confirmación de borrar bloque
const confirmRemoveBlock = () => {
  removeBlockAndSubsequent(blockToRemove);
  hidePopup();
};

// Eliminar bloque y los siguientes
const removeBlockAndSubsequent = async (id) => {
  const index = blocks.value.findIndex((b) => b.id === id);
  if (index !== -1) {
    // Eliminar desde ese índice hasta el final
    blocks.value.splice(index, blocks.value.length - index);
  }
  await nextTick();
  updateConnections();
};
const confirmTaskSetup = () => {
  if (!selectedBoard.value || !selectedTask.value || !selectedStatus.value) {
    alert("Por favor, complete todas las selecciones.");
    return;
  }

  // Crear un bloque de Tarea con la configuración seleccionada
  const taskBlock = {
    id: 1,
    name: "Tarea",
    description: "Inicio del flujo con Tarea (desencadenador)",
    config: {
      boardId: selectedBoard.value.id,
      boardName: selectedBoard.value.title,
      taskId: selectedTask.value.id,
      taskName: selectedTask.value.title,
      status: selectedStatus.value
    }
  };

  // Colocar el bloque en el canvas
  placeBlockCentered(taskBlock);
  
  // Cerrar el popup
  closeTaskPopup();
};
// Actualizar conexiones
const updateConnections = () => {
  connections.value = [];
  const canvas = document.querySelector(".workflow-canvas");
  if (!canvas) return;

  blocks.value.forEach((block, index) => {
    if (index < blocks.value.length - 1) {
      const currentBlock = document.querySelectorAll(".workflow-block")[index];
      const nextBlock = document.querySelectorAll(".workflow-block")[index + 1];
      if (currentBlock && nextBlock) {
        const currentRect = currentBlock.getBoundingClientRect();
        const nextRect = nextBlock.getBoundingClientRect();
        const canvasRect = canvas.getBoundingClientRect();
        const x1 = currentRect.left + currentRect.width / 2 - canvasRect.left;
        const y1 = currentRect.bottom - canvasRect.top;
        const x2 = nextRect.left + nextRect.width / 2 - canvasRect.left;
        const y2 = nextRect.top - canvasRect.top;
        connections.value.push({ x1, y1, x2, y2 });
      }
    }
  });
};

// Posición de cada bloque
const getBlockStyle = (block, index) => {
  return {
    position: "absolute",
    zIndex: index,
    left: `calc(50% - 175px)`, // Ancho ~350px para centrar
    top: `${block.y}px`,
    transform: 'none'
  };
};

// Guardar flujo
const saveFlow = async () => {
  try {
    // Verificar que haya bloques
    if (blocks.value.length === 0) {
      alert('El flujo necesita al menos un desencadenador');
      return;
    }
    
    // Verificar que el primer bloque sea de tipo Tarea
    const trigger = blocks.value[0];
    if (trigger.name !== "Tarea" || !trigger.config) {
      alert('El primer bloque debe ser un desencadenador de tipo Tarea');
      return;
    }

    // Crear el workflow en backend
    const workflowResponse = await axios.post('/api/workflows', {
      nombre: flowName.value || 'Flujo sin nombre',
      descripcion: flowDescription.value || `Automatización creada el ${new Date().toISOString().split('T')[0]}`,
      trigger: {
        type: 'task_status',
        boardId: trigger.config.boardId,
        boardName: trigger.config.boardName,
        taskId: trigger.config.taskId,
        taskName: trigger.config.taskName,
        status: trigger.config.status
      }
    });
    
    const idWorkflow = workflowResponse.data.id_workflow;

    // Crear cada acción dentro del workflow
    for (let i = 0; i < blocks.value.length; i++) {
      const block = blocks.value[i];
      await axios.post(`/api/workflows/${idWorkflow}/actions`, {
        name: block.name,
        description: block.description || '',
        x_position: block.x || 0,
        y_position: block.y || 0,
        config: block.config || null,
      });
    }
    
    alert('Flujo de trabajo guardado exitosamente');
  } catch (error) {
    console.error(error);
    alert('Error al guardar el flujo de trabajo');
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
  color: white!important;
}
.title::placeholder {
    color: #fff; 
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
.header .title {
  font-size: 1.5rem;
  font-weight: bold;
  border: none;
  background: transparent;
  color: #fff;
  width: 60%;
  outline: none;
  margin-right: 20px;
}
.popup {
  width: 400px;
  max-width: 90%;
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.form-select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: white;
  font-size: 14px;
}

.popup-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
  gap: 10px;
}

.confirm-btn, .cancel-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.confirm-btn {
  background-color: #007bff;
  color: white;
}

.confirm-btn:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.cancel-btn {
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
}
.form-input, .form-textarea {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: white;
  font-size: 14px;
  margin-top: 5px;
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}



</style>