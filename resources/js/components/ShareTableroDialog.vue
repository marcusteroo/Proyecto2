<template>
    <Dialog v-model:visible="visible" header="Compartir tablero" 
            :modal="true" :closable="true" class="share-dialog">
      <div class="share-content">
        <p class="share-description">
          Comparte este tablero Kanban con otros usuarios de la plataforma
        </p>
  
        <div class="form-group">
          <label>Usuario</label>
          <!-- Mostrar spinner mientras carga -->
          <div v-if="loading" class="loading-container">
            <i class="pi pi-spinner pi-spin"></i>
            <span>Cargando usuarios...</span>
          </div>
          <Dropdown v-else v-model="selectedUser" :options="users" optionLabel="name" 
                  placeholder="Selecciona un usuario" class="w-full">
            <template #option="slotProps">
              <div class="user-option">
                <span>{{ slotProps.option.name }}</span>
                <span class="user-email">{{ slotProps.option.email }}</span>
              </div>
            </template>
          </Dropdown>
          <!-- Mostrar mensaje si no hay usuarios disponibles -->
          <small v-if="!loading && users.length === 0" class="empty-message">
            No hay usuarios disponibles para compartir.
          </small>
        </div>
      </div>
      <template #footer>
        <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="closeDialog" />
        <Button label="Compartir" icon="pi pi-share-alt" @click="shareTablero" 
                :disabled="!selectedUser || sharing || loading" :loading="sharing" />
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
  tableroId: Number
});

const emit = defineEmits(['close', 'shared']);

const visible = ref(props.show);
const users = ref([]);
const selectedUser = ref(null);
const sharing = ref(false);
const toast = useToast();
const loading = ref(false);

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
  loading.value = true;
  users.value = []; // Limpiar usuarios previos
  selectedUser.value = null; // Reset selección
  
  try {
    const response = await axios.get('/api/tableros/users/potential-share');
    
    // Verificar que la respuesta sea un array válido
    if (Array.isArray(response.data)) {
      users.value = response.data;
    } else {
      console.error('La respuesta no es un array:', response.data);
      toast.add({
        severity: 'error', 
        summary: 'Error', 
        detail: 'Formato de datos incorrecto',
        life: 3000
      });
    }
  } catch (error) {
    console.error('Error al cargar usuarios:', error);
    toast.add({
      severity: 'error', 
      summary: 'Error', 
      detail: 'No se pudieron cargar los usuarios',
      life: 3000
    });
  } finally {
    loading.value = false;
  }
};

const shareTablero = async () => {
  if (!selectedUser.value) return;
  
  sharing.value = true;
  try {
    await axios.post(`/api/tableros/${props.tableroId}/share`, {
      user_id: selectedUser.value.id
    });
    
    toast.add({
      severity: 'success',
      summary: 'Compartido',
      detail: `Tablero compartido con ${selectedUser.value.name}`,
      life: 3000
    });
    
    emit('shared');
    closeDialog();
  } catch (error) {
    let message = 'Error al compartir el tablero';
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
};

onMounted(() => {
  if (visible.value) {
    loadPotentialUsers();
  }
});
</script>
  
  <style scoped>
  .loading-container {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem;
  background-color: rgba(0,0,0,0.05);
  border-radius: 4px;
  font-size: 0.9rem;
  color: var(--text-color-secondary);
}

.empty-message {
  display: block;
  margin-top: 0.5rem;
  color: var(--text-color-secondary);
  font-style: italic;
}

/* Estilos existentes */
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
  </style>