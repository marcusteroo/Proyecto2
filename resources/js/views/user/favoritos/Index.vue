<template>
    <div class="favoritos-page" :class="{'dark-theme': isDarkTheme}">
      <div class="title-section">
        <h1>Mis Favoritos</h1>
        <p class="subtitle">Accede rápidamente a tus elementos favoritos</p>
      </div>
      
      <!-- Sección de workflows favoritos -->
      <div class="section-container" v-if="favoritos.workflows && favoritos.workflows.length > 0">
        <h2 class="section-title">
          <i class="pi pi-bolt section-icon"></i>
          Automatizaciones
        </h2>
        
        <div class="items-grid">
          <div 
            v-for="flow in favoritos.workflows" 
            :key="flow.id_workflow" 
            class="fav-item flow-card"
          >
            <div class="fav-item-header" :style="{ backgroundColor: getRandomColor(flow.id_workflow) }">
              <div class="fav-icon">
                <i class="pi pi-bolt"></i>
              </div>
              <div class="favorite-icon" @click.stop>
                <FavoritoButton 
                  tipo="workflow" 
                  :itemId="flow.id_workflow"
                  @favoriteChanged="fetchFavoritos"
                />
              </div>
            </div>
            <div class="fav-item-content">
              <h3 class="fav-item-title">{{ flow.nombre }}</h3>
              <p class="fav-item-description">{{ flow.descripcion || 'Sin descripción' }}</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Sección de tableros favoritos -->
      <div class="section-container" v-if="favoritos.tableros && favoritos.tableros.length > 0">
        <h2 class="section-title">
          <i class="pi pi-th-large section-icon"></i>
          Tableros Kanban
        </h2>
        
        <div class="items-grid">
          <div 
            v-for="tablero in favoritos.tableros" 
            :key="tablero.id_tablero" 
            class="fav-item board-card"
            @click="$router.push(`/app/kanban/${tablero.id_tablero}`)"
            :style="{ backgroundColor: tablero.color_fondo || getRandomColor(tablero.id_tablero) }"
          >
            <div class="board-card-overlay"></div>
            <div class="favorite-icon" @click.stop>
              <FavoritoButton 
                tipo="tablero" 
                :itemId="tablero.id_tablero"
                @favoriteChanged="fetchFavoritos"
              />
            </div>
            <div class="fav-item-content">
              <h3 class="fav-item-title">{{ tablero.nombre }}</h3>
              
              <div class="shared-info" v-if="tablero.is_shared">
                <i class="pi pi-share-alt"></i> 
                <span>Compartido por {{ tablero.shared_by }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Mensaje de sin favoritos -->
      <div v-if="!isLoading && favoritos.total === 0" class="empty-state">
        <div class="empty-icon">
          <i class="pi pi-star"></i>
        </div>
        <h3>No tienes favoritos</h3>
        <p>Marca workflows o tableros como favoritos para acceder rápidamente a ellos desde aquí</p>
      </div>
      
      <!-- Loader -->
      <div v-if="isLoading" class="loader-container">
        <i class="pi pi-spin pi-spinner"></i>
        <span>Cargando favoritos...</span>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, onMounted, computed } from 'vue';
  import axios from 'axios';
  import FavoritoButton from '../../../components/FavoritoButton.vue';
  
  export default {
    components: {
      FavoritoButton
    },
    setup() {
      const favoritos = ref({ workflows: [], tableros: [], total: 0 });
      const isLoading = ref(true);
      
      // Detectar tema oscuro
      const isDarkTheme = computed(() => {
        return localStorage.getItem('theme') === 'dark';
      });
      
      // Obtener favoritos
      const fetchFavoritos = async () => {
        isLoading.value = true;
        try {
          const response = await axios.get('/api/favoritos');
          favoritos.value = response.data;
        } catch (error) {
          console.error('Error al obtener favoritos:', error);
        } finally {
          isLoading.value = false;
        }
      };
      
      // Generar colores aleatorios pero consistentes
      const getRandomColor = (id) => {
        const colors = [
          '#0078d4', // Azul de Microsoft
          '#50e6ff', // Cyan claro
          '#6264a7', // Morado Teams
          '#0099bc', // Azul verdoso
          '#ffaa44', // Naranja
          '#f7630c', // Naranja Power Automate
          '#107c10', // Verde
          '#7719aa', // Morado fuerte
          '#5558af', // Azul morado
          '#847545'  // Marrón
        ];
        return colors[id % colors.length];
      };
      
      onMounted(() => {
        fetchFavoritos();
      });
      
      return {
        favoritos,
        isLoading,
        isDarkTheme,
        fetchFavoritos,
        getRandomColor
      };
    }
  };
  </script>
  
  <style scoped>
  .favoritos-page {
    min-height: 100vh;
    padding: 20px;
    background-color: #f8f9fc;
    color: #333;
  }
  
  .title-section {
    margin-bottom: 30px;
  }
  
  .title-section h1 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 6px;
  }
  
  .subtitle {
    font-size: 16px;
    color: #64748b;
  }
  
  .section-container {
    margin-bottom: 40px;
  }
  
  .section-title {
    display: flex;
    align-items: center;
    font-size: 20px;
    margin-bottom: 20px;
    font-weight: 600;
    color: #333;
  }
  
  .section-icon {
    margin-right: 10px;
    color: #1A00FF;
  }
  
  .items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
  }
  
  .fav-item {
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    height: 180px;
    display: flex;
    flex-direction: column;
  }
  
  .fav-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  }
  
  .fav-item-header {
    height: 80px;
    display: flex;
    align-items: center;
    padding: 16px;
    position: relative;
  }
  
  .fav-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .fav-icon i {
    color: white;
    font-size: 18px;
  }
  
  .favorite-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 5;
  }
  
  .fav-item-content {
    flex: 1;
    padding: 16px;
    background: white;
    display: flex;
    flex-direction: column;
  }
  
  .board-card .fav-item-content {
    background: transparent;
    color: white;
    z-index: 2;
  }
  
  .fav-item-title {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 8px 0;
  }
  
  .board-card .fav-item-title {
    color: white;
  }
  
  .fav-item-description {
    font-size: 14px;
    color: #64748b;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }
  
  .board-card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.4));
    pointer-events: none;
    z-index: 1;
  }
  
  .shared-info {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.9);
    margin-top: 0.25rem;
    font-weight: 500;
    background-color: rgba(0, 0, 0, 0.2);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    width: fit-content;
    backdrop-filter: blur(2px);
    z-index: 2;
  }
  
  .shared-info i {
    font-size: 0.8rem;
  }
  
  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    text-align: center;
  }
  
  .empty-icon {
    width: 80px;
    height: 80px;
    font-size: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    margin-bottom: 20px;
    border-radius: 50%;
    background: #edf2f7;
  }
  
  .empty-state h3 {
    font-size: 20px;
    margin-bottom: 10px;
    color: #333;
  }
  
  .empty-state p {
    font-size: 16px;
    color: #64748b;
    max-width: 400px;
  }
  
  .loader-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
  }
  
  .loader-container i {
    font-size: 40px;
    color: #1A00FF;
    margin-bottom: 16px;
  }
  
  /* Dark theme */
  .dark-theme.favoritos-page {
    background-color: #121212;
    color: #e4e6eb;
  }
  
  .dark-theme .title-section h1 {
    color: #e4e6eb;
  }
  
  .dark-theme .subtitle {
    color: #adb5bd;
  }
  
  .dark-theme .section-title {
    color: #e4e6eb;
  }
  
  .dark-theme .fav-item {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  }
  
  .dark-theme .fav-item-content {
    background: #1e1e2e;
  }
  
  .dark-theme .fav-item-title {
    color: #e4e6eb;
  }
  
  .dark-theme .fav-item-description {
    color: #adb5bd;
  }
  
  .dark-theme .empty-icon {
    background: #2c2c2c;
    color: #adb5bd;
  }
  
  .dark-theme .empty-state h3 {
    color: #e4e6eb;
  }
  
  .dark-theme .empty-state p {
    color: #adb5bd;
  }
  
  @media (max-width: 768px) {
    .items-grid {
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    }
  }
  
  @media (max-width: 480px) {
    .items-grid {
      grid-template-columns: 1fr;
    }
  }
  .fav-item-header {
    background: #0078D4 !important;
  }
  </style>