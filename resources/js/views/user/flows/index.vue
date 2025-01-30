<template>
    <div class="workflow-builder">
      <h1>Flow Builder</h1>
      
      <!-- Lista de bloques disponibles -->
      <div class="blocks-list">
        <div v-for="(block, index) in availableBlocks" 
             :key="index" 
             class="block-item"
             draggable="true"
             @dragstart="startDrag(block)">
          {{ block.name }}
        </div>
      </div>
  
      <!-- Área de trabajo -->
      <div class="workflow-canvas" @drop="onDrop" @dragover.prevent>
        <draggable v-model="blocks" item-key="id" class="draggable-area">
          <template #item="{ element }">
            <div class="workflow-block">
              <h3>{{ element.name }}</h3>
              <p>{{ element.description }}</p>
              <button @click="removeBlock(element.id)">❌</button>
            </div>
          </template>
        </draggable>
      </div>
  
      <button @click="runFlow">Ejecutar Flujo</button>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import draggable from "vuedraggable";
  
  // Bloques disponibles para arrastrar
  const availableBlocks = ref([
    { id: 1, name: "Iniciar", description: "Inicio del flujo" },
    { id: 2, name: "Enviar Email", description: "Envía un correo electrónico" },
    { id: 3, name: "Esperar", description: "Espera un tiempo antes de continuar" },
    { id: 4, name: "Condición SI/NO", description: "Ejecuta según una condición" }
  ]);
  
  // Bloques en el flujo
  const blocks = ref([]);
  
  // Agregar bloques cuando el usuario los arrastra
  const startDrag = (block) => {
    blocks.value.push({ ...block, id: Date.now() });
  };
  
  // Eliminar bloque del flujo
  const removeBlock = (id) => {
    blocks.value = blocks.value.filter(block => block.id !== id);
  };
  
  // Simular la ejecución del flujo
  const runFlow = () => {
    console.log("Ejecutando flujo con los siguientes bloques:");
    console.log(blocks.value);
  };
  </script>
  
  <style scoped>
  .workflow-builder {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  
  .blocks-list {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
  }
  
  .block-item {
    padding: 10px;
    background: lightblue;
    cursor: grab;
    border-radius: 5px;
  }
  
  .workflow-canvas {
    width: 400px;
    min-height: 300px;
    border: 2px dashed gray;
    padding: 20px;
  }
  
  .workflow-block {
    padding: 10px;
    background: white;
    border: 1px solid gray;
    border-radius: 5px;
    margin-bottom: 10px;
    position: relative;
  }
  
  .workflow-block button {
    position: absolute;
    right: 5px;
    top: 5px;
  }
  </style>
  