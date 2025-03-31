<template>
  <div class="rate-container">
    <h2>Deja tu valoración</h2>
    <form @submit.prevent="submitRating">
      <div class="form-group">
        <label>Puntuación (1-5)</label>
        <input type="number" v-model="score" min="1" max="5" required />
      </div>

      <div class="form-group">
        <label>Categorías (selecciona 1 o 2)</label>
        <div class="categories-container">
          <div v-for="category in availableCategories" :key="category">
            <input 
              type="checkbox" 
              :id="category" 
              :value="category" 
              v-model="selectedCategories"
              :disabled="selectedCategories.length >= 2 && !selectedCategories.includes(category)"
            >
            <label :for="category">{{ category }}</label>
          </div>
        </div>
        <small v-if="selectedCategories.length > 2" class="error-text">
          Máximo 2 categorías permitidas
        </small>
      </div>

      <div class="form-group">
        <label>Puesto de trabajo</label>
        <input type="text" v-model="jobPosition" required />
      </div>

      <div class="form-group">
        <label>Empresa</label>
        <input type="text" v-model="company" required />
      </div>

      <div class="form-group">
        <label>Comentario (opcional)</label>
        <textarea v-model="comment"></textarea>
      </div>

      <button :disabled="loading || selectedCategories.length > 2 || selectedCategories.length === 0">
        {{ loading ? 'Enviando...' : 'Enviar valoración' }}
      </button>
    </form>

    <!-- Mostrar errores -->
    <div v-if="errors.length" class="errors">
      <ul>
        <li v-for="(err, idx) in errors" :key="idx">{{ err }}</li>
      </ul>
    </div>

    <!-- Mensaje de éxito -->
    <div v-if="successMessage" class="success">
      {{ successMessage }}
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'RatePage',
  data() {
    return {
      score: 5,
      comment: '',
      selectedCategories: [],
      jobPosition: '',
      company: '',
      loading: false,
      errors: [],
      successMessage: '',
      availableCategories: [
        'Marketing', 
        'Comunicacion', 
        'Desarrollo', 
        'Customizacion', 
        'Startup', 
        'Escalabilidad'
      ]
    }
  },
  methods: {
    async submitRating() {
      if (this.selectedCategories.length < 1 || this.selectedCategories.length > 2) {
        this.errors = ['Por favor selecciona 1 o 2 categorías'];
        return;
      }
      
      this.loading = true;
      this.errors = [];
      this.successMessage = '';

      try {
        const response = await axios.post('/api/ratings', {
          score: this.score,
          comment: this.comment,
          categories: this.selectedCategories,
          job_position: this.jobPosition,
          company: this.company
        });
        
        this.successMessage = response.data.message;
        // Reset form
        this.score = 5;
        this.comment = '';
        this.selectedCategories = [];
        this.jobPosition = '';
        this.company = '';
      } catch (error) {
        console.error('Error completo:', error);
        
        if (error.response) {
          console.error('Respuesta del servidor:', error.response.data);
          console.error('Código de estado:', error.response.status);
          
          if (error.response.data.errors) {
            // Errores de validación
            this.errors = Object.values(error.response.data.errors).flat();
          } else if (error.response.data.error) {
            // Error específico devuelto por el API
            this.errors.push(error.response.data.error);
          } else if (error.response.data.message) {
            // Mensaje general de error
            this.errors.push(error.response.data.message);
          } else {
            this.errors.push('Error al enviar la valoración (Código: ' + error.response.status + ')');
          }
        } else if (error.request) {
          // La solicitud se realizó pero no se recibió respuesta
          this.errors.push('No se recibió respuesta del servidor');
        } else {
          // Error al configurar la solicitud
          this.errors.push('Error de configuración: ' + error.message);
        }
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.rate-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 1rem;
}
.form-group {
  margin-bottom: 1rem;
}
.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
}
.categories-container {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.5rem;
}
.error-text {
  color: red;
  display: block;
  margin-top: 0.5rem;
}
input[type="number"],
input[type="text"],
textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}
textarea {
  height: 100px;
}
button {
  background-color: #4CAF50;
  color: white;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}
.errors {
  margin-top: 1rem;
  color: red;
}
.success {
  margin-top: 1rem;
  color: green;
}
</style>