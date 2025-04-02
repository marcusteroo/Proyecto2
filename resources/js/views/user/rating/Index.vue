<template>
  <div class="rating-container">
    <div class="rating-card">
      <!-- Encabezado con ilustración -->
      <div class="rating-header">
        <div class="rating-header-content">
          <div class="rating-title">
            <i class="pi pi-star-fill"></i>
            <h2>Tu opinión es importante</h2>
          </div>
          <p class="rating-subtitle">Ayúdanos a mejorar con tu valoración y comentarios</p>
        </div>
      </div>

      <form @submit.prevent="submitRating" class="rating-form">
        <!-- Escala de puntuación mejorada -->
        <div class="form-section">
          <div class="section-header">
            <i class="pi pi-star section-icon"></i>
            <label class="form-label">¿Cómo valorarías tu experiencia? <span class="required-mark">*</span></label>
          </div>
          <div class="star-rating-container">
            <div class="star-rating">
              <button 
                type="button" 
                v-for="star in 5" 
                :key="star"
                @click="score = star"
                class="star-btn"
                :class="{ 'active': star <= score }">
                <i class="pi pi-star-fill"></i>
              </button>
            </div>
            <div class="rating-details">
              <div class="rating-badge" :class="getRatingClass()">
                <span class="rating-value">{{ score }}</span>
                <span class="rating-text">{{ getRatingText() }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Categorías con mejor diseño -->
        <div class="form-section">
          <div class="section-header">
            <i class="pi pi-tags section-icon"></i>
            <label class="form-label">
              ¿Qué aspectos destacarías? <span class="required-mark">*</span>
              <span class="selection-count" :class="{ 'warning': selectedCategories.length === 0, 'success': selectedCategories.length > 0 }">
                ({{ selectedCategories.length }}/2)
              </span>
            </label>
          </div>
          <p class="section-description">Selecciona hasta 2 aspectos que más valoras de nuestra plataforma</p>
          
          <div class="categories-grid">
            <div 
              v-for="category in availableCategories" 
              :key="category"
              class="category-card" 
              :class="{ 
                'selected': selectedCategories.includes(category),
                'disabled': selectedCategories.length >= 2 && !selectedCategories.includes(category)
              }"
              @click="toggleCategory(category)"
            >
              <div class="category-icon">
                <i class="pi" :class="getCategoryIcon(category)"></i>
              </div>
              <div class="category-info">
                <span class="category-name">{{ category }}</span>
                <span class="category-description">{{ getCategoryDescription(category) }}</span>
              </div>
              <div class="selection-indicator">
                <i class="pi pi-check-circle"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Sección profesional mejorada -->
        <div class="form-section">
          <div class="section-header">
            <i class="pi pi-user section-icon"></i>
            <label class="form-label">Información profesional <span class="required-mark">*</span></label>
          </div>
          <p class="section-description">Estos datos nos ayudan a entender mejor el perfil de nuestros usuarios</p>
          
          <div class="professional-info">
            <div class="info-field">
              <label for="job-position">Puesto de trabajo <span class="required-mark">*</span></label>
              <div class="input-with-icon" :class="{'error': submitted && !jobPosition}">
                <i class="pi pi-briefcase"></i>
                <input 
                  type="text" 
                  id="job-position"
                  v-model="jobPosition" 
                  placeholder="Ej: Desarrollador Senior, Project Manager..." 
                  required
                />
              </div>
              <small class="error-message" v-if="submitted && !jobPosition">
                Por favor, indica tu puesto de trabajo
              </small>
            </div>

            <div class="info-field">
              <label for="company">Empresa <span class="required-mark">*</span></label>
              <div class="input-with-icon" :class="{'error': submitted && !company}">
                <i class="pi pi-building"></i>
                <input 
                  type="text" 
                  id="company"
                  v-model="company" 
                  placeholder="Ej: Tech Solutions, Freelance..." 
                  required
                />
              </div>
              <small class="error-message" v-if="submitted && !company">
                Por favor, indica tu empresa
              </small>
            </div>
          </div>
        </div>

        <!-- Comentario - ahora obligatorio -->
        <div class="form-section">
          <div class="section-header">
            <i class="pi pi-comments section-icon"></i>
            <label class="form-label" for="comment">
              Detalla tu experiencia <span class="required-mark">*</span>
            </label>
          </div>
          <p class="section-description">Tu opinión nos ayuda a mejorar nuestros servicios</p>
          
          <div class="input-with-icon textarea-container" :class="{'error': submitted && !comment}">
            <i class="pi pi-pencil"></i>
            <textarea 
              id="comment"
              v-model="comment" 
              placeholder="¿Qué te ha gustado más de nuestra plataforma? ¿Qué aspectos crees que podríamos mejorar?"
              maxlength="500"
              required
            ></textarea>
          </div>
          <div class="input-footer">
            <small class="error-message" v-if="submitted && !comment">
              Por favor, comparte tu opinión con nosotros
            </small>
            <div class="char-counter" :class="{'near-limit': comment.length > 400, 'limit': comment.length === 500}">
              {{ comment.length }}/500 caracteres
            </div>
          </div>
        </div>

        <!-- Panel de términos y condiciones -->
        <div class="terms-panel">
          <div class="checkbox-container">
            <input type="checkbox" id="terms" v-model="termsAccepted" required />
            <label for="terms">
              He leído y acepto los <a href="#" @click.prevent="showTerms = true">términos y condiciones</a> 
              <span class="required-mark">*</span>
            </label>
          </div>
          <small class="error-message" v-if="submitted && !termsAccepted">
            Debes aceptar los términos y condiciones para continuar
          </small>
        </div>

        <!-- Botón de envío mejorado -->
        <div class="form-actions">
          <button 
            type="submit" 
            class="submit-btn" 
            :disabled="loading"
            :class="{'loading': loading}"
          >
            <span v-if="!loading">
              <i class="pi pi-send"></i>
              Enviar valoración
            </span>
            <span v-else>
              <i class="pi pi-spin pi-spinner"></i>
              Procesando...
            </span>
          </button>
        </div>
      </form>

      <!-- Mensajes de resultado mejorados -->
      <transition name="fade">
        <div v-if="errors.length" class="message-container error">
          <div class="message-header">
            <div class="message-icon">
              <i class="pi pi-exclamation-triangle"></i>
            </div>
            <h3>No se pudo enviar tu valoración</h3>
          </div>
          <div class="message-content">
            <ul>
              <li v-for="(err, idx) in errors" :key="idx">{{ err }}</li>
            </ul>
            <button class="retry-button" @click="errors = []">
              <i class="pi pi-refresh"></i> Intentar de nuevo
            </button>
          </div>
        </div>
      </transition>

      <transition name="fade">
        <div v-if="successMessage" class="message-container success">
          <div class="message-header">
            <div class="message-icon">
              <i class="pi pi-check-circle"></i>
            </div>
            <h3>¡Valoración enviada con éxito!</h3>
          </div>
          <div class="message-content">
            <p>{{ successMessage }}</p>
            <div class="success-animation">
              <i class="pi pi-thumbs-up"></i>
            </div>
          </div>
        </div>
      </transition>
    </div>
    
    <!-- Modal de términos y condiciones -->
    <div class="terms-modal" v-if="showTerms" @click.self="showTerms = false">
      <div class="terms-content">
        <div class="terms-header">
          <h3>Términos y Condiciones</h3>
          <button class="close-button" @click="showTerms = false">
            <i class="pi pi-times"></i>
          </button>
        </div>
        <div class="terms-body">
          <p>Al enviar tus comentarios y valoración, aceptas que:</p>
          <ul>
            <li>Tu valoración puede ser utilizada con fines de mejora del servicio.</li>
            <li>Podemos contactarte para obtener más información sobre tu experiencia.</li>
            <li>Tu nombre y empresa pueden ser mencionados en nuestros testimonios, previo consentimiento adicional.</li>
          </ul>
        </div>
        <div class="terms-footer">
          <button class="accept-button" @click="acceptTerms">Aceptar y continuar</button>
        </div>
      </div>
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
      termsAccepted: false,
      loading: false,
      errors: [],
      successMessage: '',
      submitted: false,
      showTerms: false,
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
    toggleCategory(category) {
      if (this.selectedCategories.includes(category)) {
        this.selectedCategories = this.selectedCategories.filter(c => c !== category);
      } else if (this.selectedCategories.length < 2) {
        this.selectedCategories.push(category);
      }
    },
    
    getCategoryIcon(category) {
      const icons = {
        'Marketing': 'pi-megaphone',
        'Comunicacion': 'pi-comments',
        'Desarrollo': 'pi-code',
        'Customizacion': 'pi-sliders-h',
        'Startup': 'pi-chart-line',
        'Escalabilidad': 'pi-sitemap'
      };
      return icons[category] || 'pi-tag';
    },
    
    getCategoryDescription(category) {
      const descriptions = {
        'Marketing': 'Estrategias y visibilidad',
        'Comunicacion': 'Interacción y mensajería',
        'Desarrollo': 'Código y estructura técnica',
        'Customizacion': 'Personalización y adaptabilidad',
        'Startup': 'Soluciones para emprendedores',
        'Escalabilidad': 'Crecimiento y rendimiento'
      };
      return descriptions[category] || '';
    },
    
    getRatingText() {
      const texts = [
        'Muy insatisfecho',
        'Insatisfecho',
        'Neutral',
        'Satisfecho',
        'Muy satisfecho'
      ];
      return texts[this.score - 1];
    },
    
    getRatingClass() {
      if (this.score <= 2) return 'rating-low';
      if (this.score === 3) return 'rating-medium';
      return 'rating-high';
    },
    
    acceptTerms() {
      this.termsAccepted = true;
      this.showTerms = false;
    },
    
    async submitRating() {
      this.submitted = true;
      
      if (!this.validateForm()) {
        return;
      }
      
      this.loading = true;
      this.errors = [];
      
      try {
        const response = await axios.post('/api/ratings', {
          score: this.score,
          comment: this.comment,
          categories: this.selectedCategories,
          job_position: this.jobPosition,
          company: this.company
        });
        
        this.successMessage = response.data.message || '¡Gracias por compartir tu opinión! Tu valoración nos ayudará a seguir mejorando.';
        this.resetForm();
      } catch (error) {
        console.error('Error completo:', error);
        
        if (error.response) {
          if (error.response.data.errors) {
            this.errors = Object.values(error.response.data.errors).flat();
          } else if (error.response.data.error) {
            this.errors.push(error.response.data.error);
          } else if (error.response.data.message) {
            this.errors.push(error.response.data.message);
          } else {
            this.errors.push('Error al enviar la valoración (Código: ' + error.response.status + ')');
          }
        } else if (error.request) {
          this.errors.push('No se recibió respuesta del servidor');
        } else {
          this.errors.push('Error de configuración: ' + error.message);
        }
      } finally {
        this.loading = false;
      }
    },
    
    validateForm() {
      let isValid = true;
      
      if (!this.jobPosition) isValid = false;
      if (!this.company) isValid = false;
      if (!this.comment) isValid = false;
      if (this.selectedCategories.length === 0) isValid = false;
      if (!this.termsAccepted) isValid = false;
      
      return isValid;
    },
    
    resetForm() {
      this.score = 5;
      this.comment = '';
      this.selectedCategories = [];
      this.jobPosition = '';
      this.company = '';
      this.termsAccepted = false;
      this.submitted = false;
    }
  }
}
</script>

<style scoped>
/* Variables actualizadas con paleta más profesional */
:root {
  --primary-color: #3f51b5;
  --primary-light: #757de8;
  --primary-dark: #002984;
  --secondary-color: #6675e2;
  --accent-color: #ff4081;
  --success-color: #4caf50;
  --warning-color: #ff9800;
  --danger-color: #f44336;
  --light-bg: #f5f7fa;
  --card-bg: #ffffff;
  --dark-text: #37474f;
  --medium-text: #546e7a;
  --light-text: #78909c;
  --border-color: #e0e0e0;
  --border-radius: 10px;
  --box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Estilos globales */
.rating-container {
  max-width: 100%;
  padding: 0 1.5rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.rating-card {
  background: var(--card-bg);
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  overflow: hidden;
  position: relative;
}

/* Encabezado mejorado con diseño dual */
.rating-header {
  color: black;
  padding: 2.5rem 3rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
  overflow: hidden;
}

.rating-header::before {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.rating-header-content {
  flex: 1;
  position: relative;
  z-index: 2;
}

.rating-title {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem;
}

.rating-title i {
  font-size: 2rem;
  margin-right: 1rem;
  color: #ffeb3b;
  text-shadow: 0 0 10px rgba(255, 235, 59, 0.5);
}

.rating-title h2 {
  margin: 0;
  font-weight: 700;
  font-size: 2.2rem;
  line-height: 1.2;
}

.rating-subtitle {
  margin: 0;
  font-weight: 400;
  font-size: 1.1rem;
  opacity: 0.9;
  max-width: 80%;
  color:#64748b;
}

.rating-illustration {
  position: relative;
  z-index: 2;
}

.rating-illustration img {
  max-width: 200px;
  max-height: 140px;
  filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.2));
}

/* Formulario con secciones mejoradas */
.rating-form {
  padding: 2.5rem;
}

.form-section {
  margin-bottom: 2.5rem;
  background-color: var(--light-bg);
  padding: 1.5rem;
  border-radius: var(--border-radius);
  border-left: 4px solid var(--primary-color);
  transition: all 0.3s ease;
}

.form-section:hover {
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  transform: translateY(-3px);
}

.section-header {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem;
}

.section-icon {
  font-size: 1.3rem;
  color: var(--primary-color);
  margin-right: 0.75rem;
}

.form-label {
  display: block;
  margin: 0;
  font-weight: 700;
  color: var(--dark-text);
  font-size: 1.1rem;
}

.required-mark {
  color: var(--danger-color);
  margin-left: 3px;
}

.section-description {
  margin: 0 0 1.25rem;
  color: var(--medium-text);
  font-size: 0.95rem;
  padding-left: 2.15rem;
}

/* Estilo mejorado para la escala de puntuación */
.star-rating-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.star-rating {
  display: flex;
  align-items: center;
}

.star-btn {
  background: transparent;
  border: none;
  font-size: 2.2rem;
  color: rgba(0, 0, 0, 0.15);
  cursor: pointer;
  padding: 0.2rem;
  margin-right: 0.3rem;
  transition: all 0.3s ease;
}

.star-btn:hover {
  transform: scale(1.15) rotate(5deg);
}

.star-btn.active {
  color: #ffc107;
  text-shadow: 0 0 10px rgba(255, 193, 7, 0.4);
}

.rating-details {
  display: flex;
  align-items: center;
}

.rating-badge {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  color: white;
  min-width: 100px;
  text-align: center;
}

.rating-badge .rating-value {
  font-size: 1.8rem;
  font-weight: 700;
  line-height: 1;
  margin-bottom: 0.25rem;
}

.rating-badge .rating-text {
  font-size: 0.8rem;
  font-weight: 500;
}

.rating-badge.rating-low {
  background: linear-gradient(135deg, #f44336 0%, #ff9800 100%);
}

.rating-badge.rating-medium {
  background: linear-gradient(135deg, #ff9800 0%, #ffc107 100%);
}

.rating-badge.rating-high {
  background: linear-gradient(135deg, #4caf50 0%, #8bc34a 100%);
}

/* Categorías con diseño de tarjetas */
.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1rem;
}

.category-card {
  background: white;
  border-radius: 8px;
  padding: 1rem;
  display: flex;
  align-items: center;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
  position: relative;
}

.category-card:hover:not(.disabled) {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  border-color: var(--primary-light);
}

.category-card.selected {
  background: rgba(63, 81, 181, 0.05);
  border-color: var(--primary-color);
  box-shadow: 0 5px 15px rgba(63, 81, 181, 0.15);
}

.category-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--primary-color);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  flex-shrink: 0;
}

.category-icon i {
  font-size: 1.2rem;
  color: white;
}

.category-info {
  flex: 1;
}

.category-name {
  display: block;
  font-weight: 600;
  font-size: 1rem;
  color: var(--dark-text);
  margin-bottom: 0.2rem;
}

.category-description {
  display: block;
  font-size: 0.85rem;
  color: var(--light-text);
}

.selection-indicator {
  position: absolute;
  top: 10px;
  right: 10px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.category-card.selected .selection-indicator {
  opacity: 1;
}

.selection-indicator i {
  color: var(--primary-color);
  font-size: 1.2rem;
}

.selection-count {
  font-weight: 500;
  background: rgba(0, 0, 0, 0.05);
  border-radius: 12px;
  padding: 0.15rem 0.5rem;
  font-size: 0.85rem;
  margin-left: 0.5rem;
  transition: all 0.3s ease;
}

.selection-count.warning {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger-color);
}

.selection-count.success {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success-color);
}

/* Sección profesional */
.professional-info {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.info-field {
  position: relative;
}

.info-field label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--dark-text);
  font-size: 0.95rem;
}

.input-with-icon {
  display: flex;
  align-items: center;
  background-color: white;
  border-radius: 6px;
  padding: 0 1rem;
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
}

.input-with-icon:focus-within {
  box-shadow: 0 0 0 2px rgba(63, 81, 181, 0.25);
  border-color: var(--primary-color);
}

.input-with-icon.error {
  border-color: var(--danger-color);
  background-color: rgba(244, 67, 54, 0.02);
}

.input-with-icon i {
  color: var(--primary-color);
  font-size: 1.1rem;
}

.input-with-icon input,
.input-with-icon textarea {
  flex: 1;
  border: none;
  background: transparent;
  padding: 0.8rem;
  outline: none;
  font-size: 1rem;
  color: var(--dark-text);
  width: 100%;
}

.textarea-container {
  align-items: flex-start;
  padding-top: 0.8rem;
}

textarea {
  min-height: 150px;
  resize: vertical;
  font-family: inherit;
  line-height: 1.5;
}

.input-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0.5rem;
}

.char-counter {
  font-size: 0.85rem;
  color: var(--light-text);
}

.char-counter.near-limit {
  color: var(--warning-color);
}

.char-counter.limit {
  color: var(--danger-color);
}

.error-message {
  color: var(--danger-color);
  font-size: 0.85rem;
  display: block;
  margin-top: 0.4rem;
}

/* Términos y condiciones */
.terms-panel {
  background-color: rgba(63, 81, 181, 0.05);
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 2rem;
  border-left: 4px solid var(--primary-color);
}

.checkbox-container {
  display: flex;
  align-items: center;
}

.checkbox-container input[type="checkbox"] {
  margin-right: 0.8rem;
  width: 18px;
  height: 18px;
  accent-color: var(--primary-color);
}

.checkbox-container label {
  color: var(--dark-text);
  font-size: 0.95rem;
}

.checkbox-container a {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s ease;
}

.checkbox-container a:hover {
  text-decoration: underline;
}

/* Botón de envío mejorado */
.form-actions {
  text-align: center;
  margin-top: 2.5rem;
}

.submit-btn {
  background-color: #1067b3;
  color: white;
  border: none;
  border-radius: 30px;
  padding: 0.9rem 2.5rem;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 250px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  box-shadow: 0 4px 12px rgba(63, 81, 181, 0.3);
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 8px 20px rgba(63, 81, 181, 0.4);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.submit-btn i {
  font-size: 1rem;
}

/* Mensajes de resultado con diseño mejorado */
.message-container {
  margin: 0;
  padding: 0;
  border-radius: 0;
  overflow: hidden;
}

.message-header {
  padding: 1.5rem;
  display: flex;
  align-items: center;
  color: white;
}

.message-container.success .message-header {
  background: linear-gradient(135deg, #4caf50 0%, #8bc34a 100%);
}

.message-container.error .message-header {
  background: linear-gradient(135deg, #f44336 0%, #ff5252 100%);
}

.message-icon {
  font-size: 2rem;
  margin-right: 1rem;
}

.message-header h3 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 600;
}

.message-content {
  padding: 1.5rem;
  background: white;
}

.message-content p, 
.message-content ul {
  margin: 0;
  color: var(--dark-text);
  line-height: 1.6;
}

.message-content ul {
  padding-left: 1.5rem;
  margin-bottom: 1rem;
}

.message-content li {
  margin-bottom: 0.5rem;
}

.success-animation {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 1.5rem;
}

.success-animation i {
  font-size: 3rem;
  color: var(--success-color);
  animation: pulse 2s infinite;
}

.retry-button {
  background: transparent;
  border: 2px solid var(--danger-color);
  color: var(--danger-color);
  border-radius: 20px;
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 1rem;
}

.retry-button:hover {
  background-color: var(--danger-color);
  color: white;
}

/* Modal de términos */
.terms-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
  animation: fadeIn 0.3s;
}

.terms-content {
  background: white;
  border-radius: var(--border-radius);
  width: 100%;
  max-width: 600px;
  max-height: 80vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
  animation: slideUp 0.3s;
}

.terms-header {
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.terms-header h3 {
  margin: 0;
  font-size: 1.3rem;
  color: var(--dark-text);
}

.close-button {
  background: transparent;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: var(--light-text);
  transition: color 0.2s;
}

.close-button:hover {
  color: var(--dark-text);
}

.terms-body {
  padding: 1.5rem;
  overflow-y: auto;
  line-height: 1.6;
}

.terms-body p,
.terms-body ul {
  color: var(--medium-text);
}

.terms-body a {
  color: var(--primary-color);
  text-decoration: none;
}

.terms-body a:hover {
  text-decoration: underline;
}

.terms-footer {
  padding: 1.5rem;
  border-top: 1px solid var(--border-color);
  text-align: right;
}

.accept-button {
  background-color: var(--primary-color);
  color: white;
  border: none;
  border-radius: 20px;
  padding: 0.7rem 1.5rem;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.accept-button:hover {
  background-color: var(--primary-dark);
}

/* Animaciones */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s, transform 0.5s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

/* Responsive */
@media (max-width: 992px) {
  .rating-header {
    flex-direction: column;
    padding: 2rem;
    text-align: center;
  }
  
  .rating-header-content {
    margin-bottom: 1.5rem;
  }
  
  .rating-subtitle {
    max-width: 100%;
  }
  
  .rating-title {
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .rating-form {
    padding: 1.5rem;
  }
  
  .form-section {
    padding: 1.2rem;
  }
  
  .professional-info {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .star-rating-container {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .categories-grid {
    grid-template-columns: 1fr;
  }
}
@media (max-width: 580px) {
  .rating-title i{
    display: none;
  }
}
@media (max-width: 480px) {
  .rating-title h2 {
    font-size: 1.8rem;
  }
  
  .star-btn {
    font-size: 1.8rem;
  }
  
  .submit-btn {
    width: 100%;
  }
}
</style>