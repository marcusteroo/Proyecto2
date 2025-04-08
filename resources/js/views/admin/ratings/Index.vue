<template>
  <div class="ratings-admin-container">
    <div class="card">
      <div class="card-header">
        <h1 class="card-title">Gestión de Reseñas Destacadas</h1>
        <div class="card-subtitle">
          Selecciona hasta 6 reseñas para mostrar en la página principal
        </div>
      </div>
      
      <div class="card-content">
        <div class="featured-section">
          <h2>Reseñas Destacadas ({{ featuredRatings.length }}/6)</h2>
          <p class="help-text">Arrastra y suelta para reordenar las reseñas destacadas</p>
          
          <div v-if="loading" class="loading">
            <ProgressSpinner />
          </div>
          
          <draggable 
            v-else
            v-model="featuredRatings"
            item-key="id"
            group="ratings"
            @end="handleOrderChange"
            :disabled="saving"
            class="featured-ratings-grid"
          >
            <template #item="{ element }">
              <div class="rating-card featured">
                <div class="rating-order">{{ element.featured_order }}</div>
                <div class="rating-header">
                  <img :src="element.photo_path || '/path/to/default.png'" alt="Profile picture" class="avatar" />
                  <div class="user-info">
                    <h3>{{ element.user?.name }}</h3>
                    <div class="position">{{ element.job_position }}</div>
                    <div v-if="element.company" class="company">{{ element.company }}</div>
                  </div>
                </div>
                
                <div class="rating-score">
                  <i class="pi pi-star-fill" v-for="n in element.score" :key="`star-${element.id}-${n}`"></i>
                </div>
                
                <div class="rating-comment">
                  {{ element.comment }}
                </div>
                
                <div class="rating-tags">
                  <span v-for="(tag, index) in getTagsArray(element.tags)" :key="`tag-${element.id}-${index}`" class="tag">
                    {{ tag }}
                  </span>
                </div>
                
                <div class="rating-actions">
                  <Button 
                    icon="pi pi-trash" 
                    class="p-button-danger p-button-sm"
                    @click="unfeatureRating(element)" 
                    :disabled="saving"
                    :loading="element.processing"
                  />
                </div>
              </div>
            </template>
          </draggable>
        </div>
        
        <div class="all-ratings-section">
          <h2>Todas las Reseñas</h2>
          <div class="filters">
            <InputText v-model="filters.search" placeholder="Buscar reseñas..." class="search-input" />
          </div>
          
          <div v-if="loading" class="loading">
            <ProgressSpinner />
          </div>
          
          <div v-else-if="availableRatings.length === 0" class="empty-state">
            No se encontraron reseñas adicionales
          </div>
          
          <div v-else class="ratings-grid">
            <div 
              v-for="rating in availableRatings" 
              :key="rating.id"
              class="rating-card"
            >
              <div class="rating-header">
                <img :src="rating.photo_path || '/path/to/default.png'" alt="Profile picture" class="avatar" />
                <div class="user-info">
                  <h3>{{ rating.user?.name }}</h3>
                  <div class="position">{{ rating.job_position }}</div>
                  <div v-if="rating.company" class="company">{{ rating.company }}</div>
                </div>
              </div>
              
              <div class="rating-score">
                <i class="pi pi-star-fill" v-for="n in rating.score" :key="`star-${rating.id}-${n}`"></i>
              </div>
              
              <div class="rating-comment">
                {{ rating.comment }}
              </div>
              
              <div class="rating-tags">
                <span v-for="(tag, index) in getTagsArray(rating.tags)" :key="`tag-${rating.id}-${index}`" class="tag">
                  {{ tag }}
                </span>
              </div>
              
              <div class="rating-actions">
                <Button 
                  icon="pi pi-plus" 
                  label="Destacar" 
                  class="p-button-success p-button-sm"
                  @click="featureRating(rating)"
                  :disabled="featuredRatings.length >= 6 || saving || rating.processing"
                  :loading="rating.processing"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <Toast />
  </div>
</template>

<script setup>
import { ref, computed, onMounted} from 'vue';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import ProgressSpinner from 'primevue/progressspinner';
import InputText from 'primevue/inputtext';
import draggable from 'vuedraggable';
import Toast from 'primevue/toast';
import axios from 'axios';

const toast = useToast();
const ratings = ref([]);
const loading = ref(true);
const saving = ref(false);

const filters = ref({
  search: ''
});

// Obtener todas las reseñas
const loadRatings = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/ratings');
    if (response.data.success) {
      ratings.value = response.data.data.map(rating => ({
        ...rating,
        processing: false
      }));
    }
  } catch (error) {
    console.error('Error al cargar reseñas:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudieron cargar las reseñas',
      life: 3000
    });
  } finally {
    loading.value = false;
  }
};

// Reseñas filtradas y separadas
const featuredRatings = computed(() => {
  return ratings.value
    .filter(rating => rating.featured)
    .sort((a, b) => a.featured_order - b.featured_order);
});

const availableRatings = computed(() => {
  return ratings.value
    .filter(rating => !rating.featured)
    .filter(rating => {
      if (!filters.value.search) return true;
      const search = filters.value.search.toLowerCase();
      return (
        (rating.user?.name?.toLowerCase().includes(search)) ||
        (rating.comment?.toLowerCase().includes(search)) ||
        (rating.company?.toLowerCase().includes(search)) ||
        (rating.job_position?.toLowerCase().includes(search))
      );
    });
});

// Función para convertir tags (pueden venir como string o array)
const getTagsArray = (tags) => {
  if (!tags) return [];
  if (Array.isArray(tags)) return tags;
  try {
    // Intentar parsear como JSON
    return JSON.parse(tags);
  } catch (e) {
    // Si falla, podría ser una cadena separada por comas
    if (typeof tags === 'string') {
      return tags.split(',').map(tag => tag.trim());
    }
    return [];
  }
};

// Marcar una reseña como destacada
const featureRating = async (rating) => {
  if (featuredRatings.value.length >= 6) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Ya hay 6 reseñas destacadas. Elimina alguna antes de añadir más.',
      life: 3000
    });
    return;
  }
  
  rating.processing = true;
  
  try {
    const response = await axios.patch(`/api/admin/ratings/${rating.id}/featured`, {
      featured: true
    });
    
    if (response.data.success) {
      // Actualizar la reseña en el array local
      const index = ratings.value.findIndex(r => r.id === rating.id);
      if (index !== -1) {
        ratings.value[index] = { ...response.data.data, processing: false };
      }
      
      toast.add({
        severity: 'success',
        summary: 'Éxito',
        detail: 'La reseña ahora aparecerá en la página principal',
        life: 2000
      });
    }
  } catch (error) {
    console.error('Error al destacar reseña:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: error.response?.data?.message || 'No se pudo destacar la reseña',
      life: 3000
    });
  } finally {
    rating.processing = false;
  }
};

// Quitar reseña de destacados
const unfeatureRating = async (rating) => {
  rating.processing = true;
  
  try {
    const response = await axios.patch(`/api/admin/ratings/${rating.id}/featured`, {
      featured: false
    });
    
    if (response.data.success) {
      // Actualizar la reseña en el array local
      const index = ratings.value.findIndex(r => r.id === rating.id);
      if (index !== -1) {
        ratings.value[index] = { ...response.data.data, processing: false };
      }
      
      toast.add({
        severity: 'success',
        summary: 'Éxito',
        detail: 'La reseña ya no aparecerá en la página principal',
        life: 2000
      });
    }
  } catch (error) {
    console.error('Error al quitar reseña de destacados:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: error.response?.data?.message || 'No se pudo modificar la reseña',
      life: 3000
    });
  } finally {
    rating.processing = false;
  }
};

// Manejar cambio de orden con drag and drop
const handleOrderChange = async () => {
  saving.value = true;
  
  // Actualizar los featured_order
  const orderedRatings = featuredRatings.value.map((rating, index) => ({
    id: rating.id,
    featured_order: index + 1
  }));
  
  try {
    const response = await axios.post('/api/admin/ratings/order', {
      ratings: orderedRatings
    });
    
    if (response.data.success) {
      // Actualizar todas las reseñas con sus nuevos órdenes
      orderedRatings.forEach(({id, featured_order}) => {
        const index = ratings.value.findIndex(r => r.id === id);
        if (index !== -1) {
          ratings.value[index].featured_order = featured_order;
        }
      });
      
      toast.add({
        severity: 'success',
        summary: 'Éxito',
        detail: 'Orden actualizado correctamente',
        life: 2000
      });
    }
  } catch (error) {
    console.error('Error al actualizar orden:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudo actualizar el orden',
      life: 3000
    });
    
    // Recargar datos para asegurar consistencia
    await loadRatings();
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  loadRatings();
});
</script>

<style scoped>
.ratings-admin-container {
  padding: 20px;
}

.card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
  padding: 20px;
  border-bottom: 1px solid #eee;
}

.card-title {
  font-size: 24px;
  margin: 0;
  margin-bottom: 5px;
}

.card-subtitle {
  color: #666;
}

.card-content {
  padding: 20px;
}

.featured-section,
.all-ratings-section {
  margin-bottom: 40px;
}

h2 {
  font-size: 18px;
  margin-bottom: 15px;
}

.help-text {
  color: #666;
  font-size: 14px;
  margin-bottom: 15px;
}

.featured-ratings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.ratings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.rating-card {
  background: #f9f9f9;
  border-radius: 8px;
  padding: 15px;
  position: relative;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  transition: all 0.2s ease;
  display: flex;
  flex-direction: column;
}

.rating-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.rating-card.featured {
  background: #f0f7ff;
  border: 1px solid #d0e3ff;
}

.rating-order {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 24px;
  height: 24px;
  background: #3f51b5;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 14px;
}

.rating-header {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}

.avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  margin-right: 15px;
  background-color: #e9ecef; /* Color de fondo por si la imagen falla */
}

.user-info h3 {
  font-size: 16px;
  margin: 0;
  margin-bottom: 4px;
}

.position {
  font-size: 14px;
  color: #666;
}

.company {
  font-size: 12px;
  color: #888;
}

.rating-score {
  margin-bottom: 10px;
  color: #ffc107;
}

.rating-comment {
  margin-bottom: 15px;
  font-size: 14px;
  line-height: 1.5;
  flex-grow: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 3; /* Limitar a 3 líneas */
  -webkit-box-orient: vertical;
}

.rating-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 15px;
}

.tag {
  background: #e0e0e0;
  padding: 4px 10px;
  border-radius: 15px;
  font-size: 12px;
}

.rating-actions {
  display: flex;
  justify-content: flex-end;
}

.filters {
  margin-bottom: 20px;
}

.search-input {
  width: 100%;
  max-width: 400px;
}

.loading,
.empty-state {
  display: flex;
  justify-content: center;
  padding: 40px;
}

.empty-state {
  color: #888;
  font-style: italic;
}

/* Media queries para responsividad */
@media (max-width: 991px) {
  .featured-ratings-grid,
  .ratings-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }
}

@media (max-width: 767px) {
  .rating-comment {
    -webkit-line-clamp: 2; /* Menos líneas en móvil */
  }
  
  .card-content {
    padding: 15px;
  }
  
  .ratings-admin-container {
    padding: 10px;
  }
}

@media (max-width: 576px) {
  .featured-ratings-grid,
  .ratings-grid {
    grid-template-columns: 1fr;
  }
  
  .search-input {
    max-width: 100%;
  }
  
  .card-header {
    padding: 15px;
  }
  
  .card-title {
    font-size: 20px;
  }
  
  .rating-header {
    flex-wrap: wrap;
  }
  
  .avatar {
    margin-bottom: 10px;
  }
}
</style>