<template>
    <button 
    class="favorito-btn" 
    :class="[isFavorite ? 'is-favorite' : '', customClass]"
    @click.stop="toggleFavorito"
    :title="isFavorite ? 'Quitar de favoritos' : 'Añadir a favoritos'"
  >
    <i class="pi" :class="isFavorite ? 'pi-star-fill' : 'pi-star'"></i>
  </button>
  </template>
  
  <script setup>
  import { ref, onMounted, defineProps, defineEmits } from 'vue';
  import { useToast } from 'primevue/usetoast';
  import axios from 'axios';
  
  const props = defineProps({
  tipo: {
    type: String,
    required: true,
    validator: (value) => ['workflow', 'tablero'].includes(value)
  },
  itemId: {
    type: Number,
    required: true
  },
  customClass: {
    type: String,
    default: ''
  }
});
  
  const emit = defineEmits(['favoriteChanged']);
  const toast = useToast();
  const isFavorite = ref(false);
  
  // Verificar si el elemento es favorito al cargar
  onMounted(async () => {
    await checkFavorito();
  });
  
  // Verificar estado de favorito
  const checkFavorito = async () => {
    try {
      const response = await axios.post('/api/favoritos/check', {
        tipo: props.tipo,
        id: props.itemId
      });
      isFavorite.value = response.data.is_favorite;
    } catch (error) {
      console.error('Error al verificar favorito:', error);
    }
  };
  
  // Alternar estado de favorito
  const toggleFavorito = async () => {
    try {
      const response = await axios.post('/api/favoritos/toggle', {
        tipo: props.tipo,
        id: props.itemId
      });
      
      isFavorite.value = response.data.is_favorite;
      
      toast.add({
        severity: response.data.is_favorite ? 'success' : 'info',
        summary: response.data.is_favorite ? 'Añadido a favoritos' : 'Eliminado de favoritos',
        detail: response.data.message,
        life: 3000
      });
      
      emit('favoriteChanged', isFavorite.value);
    } catch (error) {
      console.error('Error al cambiar estado de favorito:', error);
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: error.response?.data?.message || 'Error al procesar la solicitud',
        life: 3000
      });
    }
  };
  </script>
  
  <style scoped>
  .favorito-btn {
    background: transparent;
    border: none;
    cursor: pointer;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    color: #aaa;
  }
  
  .favorito-btn:hover {
    background-color: rgba(0, 120, 215, 0.6); /* Actualizado al color azul deseado */
    color: white; /* Cambiamos el color del icono a blanco para mejor contraste */
  }
  
  .favorito-btn.is-favorite {
    color: #eeff01!important; /* Color del icono cuando es favorito */
  }
  
  .favorito-btn.is-favorite:hover {
    color: white; /* Cambiamos el color del icono a blanco para mejor contraste */
    background-color: rgba(0, 120, 215, 0.6);
  }
  
  /* Estilo para tema oscuro */
  :deep(.dark-theme) .favorito-btn,
  :deep(body.dark-theme) .favorito-btn {
    color: #777;
  }
  
  :deep(.dark-theme) .favorito-btn:hover,
  :deep(body.dark-theme) .favorito-btn:hover {
    background-color: rgba(0, 120, 215, 0.6);
    color: white;
  }
  </style>