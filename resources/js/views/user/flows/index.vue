<template>
  <div class="workflow-builder" :class="{'dark-theme': isDarkTheme}">
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
          <template v-for="(connection, index) in connections" :key="index">
            <!-- Línea recta para desktop -->
            <line v-if="!connection.useCurve"
              :x1="connection.x1"
              :y1="connection.y1"
              :x2="connection.x2"
              :y2="connection.y2"
              stroke="#000"
              stroke-width="2"
              marker-end="url(#arrowhead)"
            />
            <!-- Curva para móvil -->
            <path v-else
              :d="`M${connection.x1},${connection.y1} 
                  C${connection.x1},${connection.y1 + 20} 
                    ${connection.x2},${connection.y2 - 20} 
                    ${connection.x2},${connection.y2}`"
              fill="none"
              stroke="#000"
              stroke-width="2"
              marker-end="url(#arrowhead)"
            />
          </template>
          
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

    <!-- Popup de tarea -->
    <div v-if="showTaskPopup" class="popup-overlay">
      <div class="popup popup-modern">
        <!-- Header del popup -->
        <div class="popup-header">
          <h3>Configurar Desencadenador</h3>
          <button class="popup-close" @click="closeTaskPopup"><span class="close-symbol">×</span></button>
        </div>
        
        <!-- Body del popup -->
        <div class="popup-body">
          <!-- Selector de tableros con UI mejorada -->
          <div class="form-group">
            <label class="form-label">Seleccione un tablero</label>
            <div class="select-wrapper">
              <select v-model="selectedBoard" @change="loadTasksForBoard" class="form-select">
                <option value="">Seleccionar tablero</option>
                <option v-for="board in boards" :key="board.id_tablero" :value="board">
                  {{ board.nombre }}
                </option>
              </select>
              <span class="select-icon">
                <i class="pi pi-chevron-down"></i>
              </span>
            </div>
          </div>
          
          <!-- Selector de tareas con animación de carga -->
          <div class="form-group" v-if="selectedBoard">
            <label class="form-label">Seleccione una tarea</label>
            <div v-if="isLoadingTasks" class="loading-indicator">
              <div class="spinner"></div>
              <span>Cargando tareas...</span>
            </div>
            <div v-else class="select-wrapper">
              <select v-model="selectedTask" class="form-select">
                <option value="">Seleccionar tarea</option>
                <option v-for="task in tasks" :key="task.id_tarea" :value="task">
                  {{ task.titulo }}
                </option>
              </select>
              <span class="select-icon">
                <i class="pi pi-chevron-down"></i>
              </span>
            </div>
            <div v-if="tasks.length === 0 && !isLoadingTasks" class="empty-message">
              No hay tareas disponibles en este tablero
            </div>
          </div>
          
          <!-- Selector de estados con diseño visual -->
          <div class="form-group" v-if="selectedTask">
            <label class="form-label">¿Cuándo se activará este flujo?</label>
            <div class="status-options">
              <div 
                v-for="status in statusOptions" 
                :key="status.value"
                class="status-option" 
                :class="{ active: selectedStatus === status.value }"
                @click="selectedStatus = status.value"
              >
                <div class="status-icon" :style="{ backgroundColor: status.color }">
                  <i :class="status.icon"></i>
                </div>
                <div class="status-details">
                  <div class="status-label">{{ status.label }}</div>
                  <div class="status-desc">{{ status.description }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer del popup -->
        <div class="popup-footer">
          <button @click="closeTaskPopup" class="btn-cancel">
            <i class="pi pi-times"></i>
            Cancelar
          </button>
          <button 
            @click="confirmTaskSetup" 
            class="btn-confirm" 
            :disabled="!selectedBoard || !selectedTask || !selectedStatus"
          >
            <i class="pi pi-check"></i>
            Confirmar
          </button>
        </div>
      </div>
    </div>

    <!-- Popup de email modernizado -->
    <div v-if="showEmailPopup" class="popup-overlay">
      <div class="popup popup-modern">
        <!-- Header del popup -->
        <div class="popup-header">
          <h3>Configurar Email</h3>
          <button class="popup-close" @click="closeEmailPopup"><span class="close-symbol">×</span></button>
        </div>
        
        <!-- Body del popup -->
        <div class="popup-body">
          <div class="form-group">
            <label class="form-label">
              <i class="pi pi-envelope"></i>
              Destinatario
            </label>
            <div class="input-wrapper">
              <input 
                type="email" 
                v-model="emailConfig.to" 
                class="form-input" 
                placeholder="correo@ejemplo.com"
              />
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label">
              <i class="pi pi-tag"></i>
              Asunto
            </label>
            <div class="input-wrapper">
              <input 
                type="text" 
                v-model="emailConfig.subject" 
                class="form-input" 
                placeholder="Asunto del correo"
              />
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label">
              <i class="pi pi-file-edit"></i>
              Mensaje
            </label>
            <div class="textarea-wrapper">
              <textarea 
                v-model="emailConfig.message" 
                class="form-textarea" 
                rows="4"
                placeholder="Contenido del correo..."
              ></textarea>
            </div>
          </div>
        </div>
        
        <!-- Footer del popup -->
        <div class="popup-footer">
          <button @click="closeEmailPopup" class="btn-cancel">
            <i class="pi pi-times"></i>
            Cancelar
          </button>
          <button 
            @click="confirmEmailSetup" 
            class="btn-confirm" 
            :disabled="!emailConfig.to || !emailConfig.subject || !emailConfig.message"
          >
            <i class="pi pi-check"></i>
            Confirmar
          </button>
        </div>
      </div>
    </div>
    <Toast position="bottom-right" />
  </div>
</template>

<script setup>
import { ref, reactive, nextTick, watch, onMounted, onUnmounted } from "vue";
import { useRoute } from "vue-router";
import draggable from "vuedraggable";
import axios from "axios";
import { useToast } from 'primevue/usetoast';
import { authStore } from "../../../store/auth";

// Bloques base
const availableBlocks = ref([
  { id: 1, name: "Tarea", description: "Inicio del flujo con Tarea (desencadenador)" },
  { id: 2, name: "Enviar Email", description: "Envía un correo electrónico" },
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
const isLoadingTasks = ref(false);
const toast = useToast();
const isDarkTheme = ref(localStorage.getItem('theme') === 'dark');
const isMobile = ref(window.innerWidth < 768);
const updateMobileState = () => {
  isMobile.value = window.innerWidth < 768;
};
const statusOptions = [
  { 
    value: "Pendiente", 
    label: "Cuando la tarea esté pendiente", 
    description: "Se activa al poner una tarea en estado 'Sin empezar'",
    color: "#ffa500",
    icon: "pi pi-clock"
  },
  { 
    value: "En curso", 
    label: "Cuando la tarea esté en progreso", 
    description: "Se activa al cambiar una tarea a 'En curso'",
    color: "#1e90ff",
    icon: "pi pi-spin pi-sync"
  },
  { 
    value: "Completado", 
    label: "Cuando la tarea se complete", 
    description: "Se activa al marcar una tarea como completada",
    color: "#32cd32",
    icon: "pi pi-check-circle"
  },
  { 
    value: "Stopper", 
    label: "Cuando la tarea se bloquee", 
    description: "Se activa cuando una tarea se marca como bloqueada",
    color: "#ff4500",
    icon: "pi pi-exclamation-triangle"
  }
];
onMounted(async () => {
  // Cargar tableros del usuario
  await loadBoards();
  
  // Aplicar tema actual al cargar y configurar eventos
  applyGlobalTheme();
  window.addEventListener('themeChanged', applyGlobalTheme);
  window.addEventListener('storage', handleStorageChange);
  window.addEventListener('resize', updateMobileState);
});
//Esto es para cargar solo los tableros del usuario
const loadBoards = async () => {
  try {
    
    const auth = authStore();
    const userId = auth.user?.id;
    
    if (!userId) {
      console.error("Usuario no autenticado");
      return;
    }
    
    // Usar la API que filtra tableros por usuario
    const response = await axios.get(`/api/tableros/user/${userId}`);
    boards.value = response.data;
  } catch (error) {
    console.error('Error al cargar tableros:', error);
  }
};
onUnmounted(() => {
  window.removeEventListener('resize', updateMobileState);
  window.removeEventListener('themeChanged', applyGlobalTheme);
  window.removeEventListener('storage', handleStorageChange);
});
// Función para aplicar el tema global
const applyGlobalTheme = () => {
  // Obtener el tema actual del localStorage
  const currentTheme = localStorage.getItem('theme') || 'light';
  // Actualizar el estado local
  isDarkTheme.value = currentTheme === 'dark';
};

// Función para manejar cambios en el almacenamiento
const handleStorageChange = (e) => {
  if (e.key === 'theme') {
    applyGlobalTheme();
  }
};
const adjustBlocksPosition = () => {
  blocks.value.forEach((block, index) => {
    // Espaciar más los bloques en móvil
    const spacing = isMobile.value ? 150 : 100;
    block.y = index * spacing + 20;
  });
  updateConnections();
};
const loadTasksForBoard = async () => {
  if (!selectedBoard.value) return;
  
  try {
    isLoadingTasks.value = true;
    const response = await axios.get(`/api/kanban/${selectedBoard.value.id_tablero}/tasks`);
    tasks.value = response.data;
    selectedTask.value = null;
    selectedStatus.value = "";
  } catch (error) {
    console.error('Error al cargar tareas:', error);
    tasks.value = [];
  } finally {
    isLoadingTasks.value = false;
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

  // Muestra el popup correspondiente
  if (block.name === "Tarea") {
    showTaskPopup.value = true;
    return;
  } else if (block.name === "Enviar Email") {
    showEmailPopup.value = true;
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
    // Verificar si es el primer bloque y no es de tipo Tarea
    if (blocks.value.length === 0 && draggedBlock.name !== "Tarea") {
      alert("La primera acción debe ser Tarea ya que es el desencadenador.");
      draggedBlock = null;
      return;
    }
    
    // Limitar a 3 acciones
    if (blocks.value.length >= 3) {
      alert("No se pueden agregar más de 3 acciones.");
      draggedBlock = null;
      return;
    }
    
    // Evitar duplicados
    if (blocks.value.some(b => b.name === draggedBlock.name)) {
      alert("Esta acción ya fue agregada.");
      draggedBlock = null;
      return;
    }
    
    // Si es tipo Tarea o Email, mostrar el popup correspondiente
    if (draggedBlock.name === "Tarea") {
      showTaskPopup.value = true;
    } else if (draggedBlock.name === "Enviar Email") {
      showEmailPopup.value = true;
    } else {
      // Para otros tipos de bloques
      await placeBlockCentered(draggedBlock);
    }
    
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
    // Notificación elegante en lugar de alert
    toast.add({ 
      severity: 'warn',
      summary: 'Configuración incompleta', 
      detail: 'Por favor, complete todas las selecciones', 
      life: 3000 
    });
    return;
  }

  // Crear un bloque de Tarea con la configuración seleccionada
  const taskBlock = {
    id: Date.now(),
    name: "Tarea",
    description: "Desencadenador: " + selectedTask.value.titulo,
    config: {
      boardId: selectedBoard.value.id_tablero,
      boardName: selectedBoard.value.nombre,
      taskId: selectedTask.value.id_tarea,
      taskName: selectedTask.value.titulo,
      status: selectedStatus.value
    }
  };

  // Colocar el bloque en el canvas
  placeBlockCentered(taskBlock);
  
  // Cerrar el popup con animación
  closeTaskPopup();
  
  // Notificación de éxito
  toast.add({ 
    severity: 'success',
    summary: 'Desencadenador configurado', 
    detail: `La automatización se iniciará cuando "${selectedTask.value.titulo}" pase a estado "${selectedStatus.value}"`, 
    life: 3000 
  });
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
  // Ajustar dinámicamente la anchura según el tamaño de pantalla
  const canvas = document.querySelector(".workflow-canvas");
  if (!canvas) return {};
  
  const canvasWidth = canvas.clientWidth;
  let blockWidth = 350; // Anchura por defecto
  
  if (canvasWidth < 576) {
    blockWidth = 220;
  } else if (canvasWidth < 768) {
    blockWidth = 250;
  } else if (canvasWidth < 1200) {
    blockWidth = 300;
  }
  
  return {
    position: "absolute",
    zIndex: index,
    left: `calc(50% - ${blockWidth/2}px)`,
    top: `${block.y}px`,
    transform: 'none',
    width: `${blockWidth}px`
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
watch([isMobile, () => blocks.value.length], adjustBlocksPosition);

</script>

<style scoped>
/* Base Styles */
.workflow-builder {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: #f4f4f4;
  border-radius: 12px;
  padding: 20px;
  gap: 20px;
  transition: background-color 0.3s ease;
}

/* Header Styles */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #0078d4;
  color: #fff;
  padding: 15px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: background 0.3s ease;
}

.title {
  font-size: 1.5rem;
  font-weight: bold;
  border: none;
  background: transparent;
  color: white !important;
  width: 60%;
  outline: none;
  margin-right: 20px;
  transition: opacity 0.2s ease;
}

.title::placeholder {
  color: rgba(255, 255, 255, 0.8);
}

.title:focus {
  opacity: 0.9;
}

.run-button {
  padding: 10px 24px;
  background: #005a9e;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.run-button:hover {
  background: #003f73;
  transform: translateY(-1px);
}

.run-button:active {
  transform: translateY(0);
}

/* Layout Styles */
.main-content {
  display: flex;
  flex-grow: 1;
  gap: 20px;
  overflow: hidden;
}

/* Sidebar Styles */
.blocks-list {
  width: 250px;
  background: #ffffff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  flex-shrink: 0;
  transition: background 0.3s ease, box-shadow 0.3s ease;
}

.blocks-title {
  font-size: 1.25rem;
  font-weight: bold;
  margin-bottom: 15px;
  padding-bottom: 8px;
  border-bottom: 1px solid #eee;
}

.block-item {
  padding: 15px;
  background: #0078d4;
  color: #fff;
  border-radius: 8px;
  cursor: grab;
  margin-bottom: 12px;
  transition: all 0.25s ease;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
}

.block-item:hover {
  background: #005a9e;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.block-item:active {
  transform: none;
  opacity: 0.7;
}

/* Canvas Styles */
.workflow-canvas {
  flex-grow: 1;
  background: #ffffff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  position: relative;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><circle cx="1" cy="1" r="1" fill="%23ccc" /></svg>');
  transition: background 0.3s ease, box-shadow 0.3s ease;
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

/* Block Styles */
.workflow-block {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 15px;
  background: #f0f4f8;
  border: 2px solid #0078d4;
  border-radius: 8px;
  position: absolute;
  transition: all 0.3s ease;
  cursor: grab;
  margin-bottom: 40px;
  width: 350px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.workflow-block:hover {
  background: #e1e1e1;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
}

.block-name {
  font-size: 1rem;
  font-weight: bold;
  margin: 0;
  text-align: center;
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
  transition: all 0.2s ease;
  padding: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
}

.remove-button:hover {
  color: #fff;
  background-color: rgba(255, 77, 79, 0.8);
  transform: scale(1.15);
}

/* Popup Styles - Optimized and Enhanced */
.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(3px);
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.popup {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
  text-align: center;
  max-width: 90%;
  animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes popIn {
  from { 
    transform: scale(0.95);
    opacity: 0; 
  }
  to { 
    transform: scale(1);
    opacity: 1; 
  }
}

.popup button {
  margin: 10px;
  padding: 10px 24px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-weight: 500;
}

.popup button:first-of-type {
  background: #ff4d4f;
  color: #fff;
  box-shadow: 0 2px 5px rgba(255, 77, 79, 0.25);
}

.popup button:first-of-type:hover {
  background: #ff1a1a;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(255, 77, 79, 0.3);
}

.popup button:last-of-type {
  background: #f0f0f0;
  color: #333;
}

.popup button:last-of-type:hover {
  background: #e0e0e0;
  transform: translateY(-2px);
}

.popup button:active {
  transform: translateY(0);
}

/* Enhanced Popup Styles */
.popup-modern {
  width: 500px;
  max-width: 95vw;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
  display: flex;
  flex-direction: column;
  animation: slideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideIn {
  from { 
    transform: translateY(20px);
    opacity: 0; 
  }
  to { 
    transform: translateY(0);
    opacity: 1; 
  }
}

.popup-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 24px;
  background: linear-gradient(135deg, #3f359b, #1A00FF);
  color: white;
  position: relative;
}

.popup-header::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(to right, #23a6d5, #6f42c1);
}

.popup-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
}

.popup-close {
  background-color: #ff3b30!important;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  transition: all 0.2s ease;
  cursor: pointer;
}
.close-symbol {
  color: white;
  font-size: 24px;
  line-height: 1;
  position: relative;
  font-family: Arial, sans-serif;
}
.popup-close:hover {
  background-color: rgba(255, 255, 255, 0.2);
  transform: rotate(90deg);
}

.popup-body {
  padding: 24px 28px;
  flex-grow: 1;
  overflow-y: auto;
  max-height: 65vh;
}

.popup-footer {
  display: flex;
  justify-content: center; 
  align-items: center;
  gap: 16px; 
  padding: 16px 24px;
  border-top: 1px solid #eee;
  background: #f9f9f9;
}

/* Enhanced Form Styles */
.form-group {
  margin-bottom: 22px;
}

.form-label {
  display: flex;
  align-items: center;
  font-weight: 500;
  margin-bottom: 10px;
  color: #333;
}

.form-label i {
  margin-right: 8px;
  color: #3f359b;
  font-size: 1.1em;
}

.select-wrapper {
  position: relative;
}

.select-icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  color: #666;
  transition: transform 0.2s;
}

.form-select:focus + .select-icon {
  transform: translateY(-50%) rotate(180deg);
}

.form-select,
.form-input, 
.form-textarea {
  width: 100%;
  padding: 12px 16px;
  font-size: 15px;
  border: 1px solid #ddd;
  border-radius: 10px;
  background-color: white;
  transition: all 0.25s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) inset;
}

.form-textarea {
  min-height: 120px;
  resize: vertical;
}

.form-select:focus,
.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #3f359b;
  box-shadow: 0 0 0 3px rgba(63, 53, 155, 0.15);
}

.input-wrapper,
.textarea-wrapper {
  position: relative;
}

.input-wrapper i,
.textarea-wrapper i {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #aaa;
  pointer-events: none;
}

/* Enhanced Button Styles */
.btn-cancel,
.btn-confirm {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 24px;
  font-size: 15px;
  font-weight: 500;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-cancel {
  background-color: #f0f0f0;
  color: #666;
}

.btn-cancel:hover {
  background-color: #e0e0e0;
  transform: translateY(-1px);
}

.btn-confirm {
  background-color:#5cb85c	!important;
  color: white !important;
  box-shadow: 0 4px 10px rgba(26, 0, 255, 0.2);
}

.btn-confirm:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(26, 0, 255, 0.3);
  background: linear-gradient(135deg, #4f45ab, #2a10ff);
}

.btn-confirm:active {
  transform: translateY(0);
}

.btn-confirm:disabled {
  background: linear-gradient(135deg, #a6a6a6, #7a7a7a);
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
  opacity: 0.7;
}

/* Loading Indicator */
.loading-indicator {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 0;
  color: #666;
}

.spinner {
  width: 24px;
  height: 24px;
  border: 3px solid rgba(63, 53, 155, 0.2);
  border-top-color: #3f359b;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.empty-message {
  color: #888;
  font-size: 14px;
  padding: 12px;
  font-style: italic;
  text-align: center;
  border: 1px dashed #ddd;
  border-radius: 8px;
  margin-top: 10px;
}

/* Status Options */
.status-options {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 12px;
}

.status-option {
  display: flex;
  align-items: center;
  padding: 14px;
  border: 1px solid #ddd;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
}

.status-option:hover {
  background-color: #f9f9f9;
  transform: translateY(-2px);
}

.status-option.active {
  border-color: #1A00FF;
  background-color: rgba(26, 0, 255, 0.05);
  box-shadow: 0 0 0 2px rgba(26, 0, 255, 0.2);
}

.status-option.active::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: #1A00FF;
}

.status-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 16px;
  color: white;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  transition: transform 0.2s ease;
}

.status-option:hover .status-icon {
  transform: scale(1.1);
}

.status-details {
  flex: 1;
}

.status-label {
  font-weight: 600;
  margin-bottom: 6px;
  font-size: 15px;
}

.status-desc {
  font-size: 13px;
  color: #666;
  line-height: 1.4;
}

/* Utility Classes */
[draggable]:focus {
    outline: none !important;
}

.no-scroll {
  overflow: hidden;
  position: fixed;
  width: 100%;
}

/* Dark Theme */
.dark-theme {
  background-color: #121212;
  color: #e4e6eb;
}

.dark-theme .header {
  background: #1A00FF;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.dark-theme .blocks-list {
  background: #1e1e2e;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.dark-theme .blocks-title {
  color: #e4e6eb;
  border-bottom-color: #383a46;
}

.dark-theme .workflow-canvas {
  background: #1e1e2e;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><circle cx="1" cy="1" r="1" fill="%23444" /></svg>');
}

.dark-theme .workflow-block {
  background: #292a36;
  border-color: #1A00FF;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
}

.dark-theme .workflow-block:hover {
  background: #333446;
}

.dark-theme .popup {
  background: #1e1e2e;
  color: #e4e6eb;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
}

.dark-theme .popup-modern {
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
}

.dark-theme .popup-footer {
  background-color: #292a36;
  border-top-color: #383a46;
}

.dark-theme .form-label {
  color: #e4e6eb;
}

.dark-theme .form-select,
.dark-theme .form-input,
.dark-theme .form-textarea {
  background-color: #292a36;
  border-color: #383a46;
  color: #e4e6eb;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) inset;
}

.dark-theme .form-select:focus,
.dark-theme .form-input:focus,
.dark-theme .form-textarea:focus {
  box-shadow: 0 0 0 3px rgba(26, 0, 255, 0.3);
  border-color: #1A00FF;
}

.dark-theme .btn-cancel {
  background-color: #383a46;
  color: #e4e6eb;
}

.dark-theme .btn-cancel:hover {
  background-color: #44465a;
}

.dark-theme .status-option {
  border-color: #383a46;
  background-color: #292a36;
}

.dark-theme .status-option:hover {
  background-color: #333446;
}

.dark-theme .status-option.active {
  background-color: rgba(26, 0, 255, 0.2);
}

.dark-theme .status-desc {
  color: #adb5bd;
}

.dark-theme .empty-message {
  color: #adb5bd;
  border-color: #383a46;
}

.dark-theme svg line,
.dark-theme svg path {
  stroke: #adb5bd;
}

.dark-theme svg polygon {
  fill: #adb5bd;
}

/* Sidebar Theme Override */
:deep(.dark-theme .layout-sidebar),
:deep(body.dark-theme .layout-sidebar) {
  background-color: #3f359b !important;
}

:deep(.dark-theme .layout-sidebar .nav-link-text),
:deep(.dark-theme .layout-sidebar .user-name),
:deep(.dark-theme .layout-sidebar .sidebar-title),
:deep(.dark-theme .layout-sidebar .greeting-text),
:deep(.dark-theme .layout-sidebar .app-version) {
  color: white !important;
}

/* Responsive Styles */
@media (max-width: 1200px) {
  .workflow-block {
    width: 300px;
  }
}

@media (max-width: 992px) {
  .main-content {
    flex-direction: column;
  }
  
  .blocks-list {
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }
  
  .blocks-title {
    width: 100%;
  }
  
  .block-item {
    flex: 0 0 calc(33.33% - 10px);
    margin-bottom: 0;
  }
  
  .workflow-canvas {
    height: calc(100vh - 350px);
    min-height: 400px;
  }
}

@media (max-width: 768px) {
  .workflow-builder {
    padding: 10px;
    gap: 15px;
    height: auto;
    min-height: 100vh;
  }
  
  .header {
    flex-direction: column;
    padding: 12px;
    gap: 10px;
  }
  
  .title {
    width: 100%;
    text-align: center;
    margin-right: 0;
    font-size: 1.3rem;
  }
  
  .run-button {
    width: 100%;
  }
  
  .workflow-block {
    width: 250px;
  }
  
  .block-item {
    flex: 0 0 calc(50% - 10px);
  }
  
  .popup-header {
    padding: 16px 20px;
  }
  
  .popup-body {
    padding: 20px;
  }
}

@media (max-width: 576px) {
  .workflow-builder {
    padding: 8px;
  }
  
  .header {
    padding: 10px;
  }
  
  .popup {
    width: 95%;
    padding: 15px;
  }
  
  .workflow-block {
    width: 220px;
  }
  
  .block-item {
    flex: 0 0 100%;
  }
  
  .remove-button {
    padding: 3px;
    font-size: 0.9rem;
  }
  
  .form-group label {
    font-size: 14px;
  }
  
  .form-select, .form-input, .form-textarea {
    padding: 10px;
    font-size: 14px;
  }
  
  .popup-overlay {
    padding: 0 10px;
    align-items: flex-start;
    padding-top: 20%;
  }
  
  .popup-modern {
    max-height: 80vh;
  }
  
  .popup-body {
    max-height: 60vh;
  }
  
  .popup-footer {
    flex-direction: column-reverse;
    gap: 10px;
    padding: 15px;
  }
  
  .btn-cancel, .btn-confirm {
    width: 100%;
    padding: 12px;
  }
  
  .status-option {
    padding: 12px 10px;
  }
  
  .status-icon {
    width: 32px;
    height: 32px;
    margin-right: 12px;
  }
  
  .status-label {
    font-size: 14px;
  }
  
  .status-desc {
    font-size: 12px;
  }
}
</style>