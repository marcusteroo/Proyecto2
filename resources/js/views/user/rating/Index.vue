<template>
    <div class="rate-container">
      <h2>Deja tu valoración</h2>
      <form @submit.prevent="submitRating">
        <div>
          <label>Puntuación (1-5)</label>
          <input type="number" v-model="score" min="1" max="5" required />
        </div>
  
        <div>
          <label>Comentario (opcional)</label>
          <textarea v-model="comment"></textarea>
        </div>
  
        <button :disabled="loading">
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
        loading: false,
        errors: [],
        successMessage: ''
      }
    },
    methods: {
      async submitRating() {
        this.loading = true;
        this.errors = [];
        this.successMessage = '';
  
        try {
          const response = await axios.post('/api/ratings', {
            score: this.score,
            comment: this.comment
          });
          this.successMessage = response.data.message;
          // Reset form
          this.score = 5;
          this.comment = '';
        } catch (error) {
          if (error.response && error.response.data.errors) {
            // Errores de validación
            const validationErrors = error.response.data.errors;
            // Convertir { campo: [mensajes] } en array de strings
            this.errors = Object.values(validationErrors).flat();
          } else {
            this.errors.push('Error al enviar la valoración');
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
  .errors {
    margin-top: 1rem;
    color: red;
  }
  .success {
    margin-top: 1rem;
    color: green;
  }
  </style>
    