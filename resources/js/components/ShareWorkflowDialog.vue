<template>
    <Dialog v-model:visible="visible" header="Compartir automatización" 
            :modal="true" :closable="true" class="share-dialog">
      <div class="share-content">
        <p class="share-description">
          Comparte esta automatización con otros usuarios de la plataforma
        </p>
  
        <div class="form-group">
          <label>Usuario</label>
          <Dropdown v-model="selectedUser" :options="users" optionLabel="name" 
                    placeholder="Selecciona un usuario" class="w-full">
            <template #option="slotProps">
              <div class="user-option">
                <span>{{ slotProps.option.name }}</span>
                <span class="user-email">{{ slotProps.option.email }}</span>
              </div>
            </template>
          </Dropdown>
        </div>
  
        <div class="form-group">
          <label>Permisos</label>
          <div class="permission-options">
            <div class="permission-option" :class="{ active: selectedRole === 'espectador' }"
                 @click="selectedRole = 'espectador'">
              <i class="pi pi-eye"></i>
              <div>
                <strong>Espectador</strong>
                <p>Solo puede ver el flujo de trabajo</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <template #footer>
        <Button label="Cancelar" icon="pi pi-times" class="boton-cancelar" @click="closeDialog" />
        <Button label="Compartir" icon="pi pi-share-alt" class="boton-compartir" @click="shareWorkflow" 
                :disabled="!selectedUser || sharing" :loading="sharing" />
      </template>
    </Dialog>
  </template>
  
  <script setup>
  import { ref, onMounted, watch } from 'vue';
  import Dialog from 'primevue/dialog';
  import Button from 'primevue/button';
  import Dropdown from 'primevue/dropdown';
  import { useToast } from 'primevue/usetoast';
  import axios from 'axios';
  
  const props = defineProps({
    show: Boolean,
    workflowId: Number
  });
  
  const emit = defineEmits(['close', 'shared']);
  
  const visible = ref(props.show);
  const users = ref([]);
  const selectedUser = ref(null);
  const selectedRole = ref('espectador');
  const sharing = ref(false);
  const toast = useToast();
  
  watch(() => props.show, (newValue) => {
    visible.value = newValue;
    if (newValue) {
      loadPotentialUsers();
    }
  });
  
  watch(visible, (newValue) => {
    if (!newValue) {
      emit('close');
    }
  });
  
  const loadPotentialUsers = async () => {
    try {
      const response = await axios.get('/api/workflows/users/potential');
      users.value = response.data;
    } catch (error) {
      toast.add({
        severity: 'error', 
        summary: 'Error', 
        detail: 'No se pudieron cargar los usuarios',
        life: 3000
      });
    }
  };
  
  const shareWorkflow = async () => {
    if (!selectedUser.value) return;
    
    sharing.value = true;
    try {
      await axios.post(`/api/workflows/${props.workflowId}/share`, {
        user_id: selectedUser.value.id,
        rol: selectedRole.value
      });
      
      toast.add({
        severity: 'success',
        summary: 'Compartido',
        detail: `Automatización compartida con ${selectedUser.value.name}`,
        life: 3000
      });
      
      emit('shared');
      closeDialog();
    } catch (error) {
      let message = 'Error al compartir la automatización';
      if (error.response?.data?.message) {
        message = error.response.data.message;
      }
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: message,
        life: 3000
      });
    } finally {
      sharing.value = false;
    }
  };
  
  const closeDialog = () => {
    visible.value = false;
    selectedUser.value = null;
    selectedRole.value = 'espectador';
  };
  
  onMounted(() => {
    if (visible.value) {
      loadPotentialUsers();
    }
  });
  </script>
  
  <style scoped>
  .share-content {
    padding: 1rem 0;
  }
  
  .share-description {
    margin-bottom: 1.5rem;
    color: var(--text-color-secondary);
  }
  
  .form-group {
    margin-bottom: 1.5rem;
  }
  
  .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
  }
  
  .permission-options {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
  }
  
  .permission-option {
    flex: 1;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .permission-option.active {
    border-color: var(--primary-color);
    background-color: var(--primary-color-lightest);
  }
  
  .permission-option i {
    font-size: 1.5rem;
    color: var(--primary-color);
  }
  
  .permission-option p {
    margin: 0.25rem 0 0 0;
    font-size: 0.875rem;
    color: var(--text-color-secondary);
  }
  
  .user-option {
    display: flex;
    flex-direction: column;
  }
  
  .user-email {
    font-size: 0.8rem;
    color: var(--text-color-secondary);
  }
  
  /* Dark theme support */
  :deep(.dark-theme) .permission-option, 
  :deep(body.dark-theme) .permission-option {
    border-color: #383a46;
    background-color: #1e1e2e;
  }
  
  :deep(.dark-theme) .permission-option.active, 
  :deep(body.dark-theme) .permission-option.active {
    border-color: #1A00FF;
    background-color: rgba(26, 0, 255, 0.15);
  }
  .boton-cancelar {
  background-color: #FF4D4F;
  border: none!important;
}
.boton-cancelar:hover {
  background-color: #FF1A1A!important;
}
.boton-compartir {
  background-color: #106EBE;
  border: none!important;
}
.boton-compartir:hover {
  background-color: #1067b3!important;
  border: none!important;
}
  </style>