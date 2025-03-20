<template>
    <div class="register-page">
      <div class="register-background">
        <div class="particles-container"></div>
        <div class="gradient-overlay"></div>
      </div>
      
      <div class="register-container">
        <div class="register-card-wrapper">
          <!-- Logo y título -->
          <div class="register-branding">
            <img src="/images/LogoKanFlow2.svg" alt="KanFlow" class="register-logo" />
            <h1 class="register-title">{{ currentStep === 1 ? 'Crear una cuenta' : 'Método de pago' }}</h1>
            <p class="register-subtitle">
              {{ currentStep === 1 ? 'Completa tus datos para comenzar' : 'Información de pago para activar tu cuenta' }}
            </p>
          </div>
          
          <!-- Formulario -->
          <div class="register-card">
            <div class="step-indicator" :class="{ 'step-2': currentStep === 2 }">
              <div class="step-bubble" :class="{ 'active': currentStep === 1 }">1</div>
              <div class="step-line"></div>
              <div class="step-bubble" :class="{ 'active': currentStep === 2 }">2</div>
            </div>
            
            <form @submit.prevent="handleSubmit" class="register-form">
              <!-- Paso 1: Datos personales -->
              <div v-if="currentStep === 1" class="step-content">
                <!-- Nombre -->
                <div class="form-group">
                  <label for="name" class="form-label">
                    <i class="pi pi-user"></i>
                    {{ $t('name') }}
                  </label>
                  <div class="input-wrapper">
                    <input 
                      v-model="registerForm.name" 
                      id="name" 
                      type="text" 
                      class="form-control" 
                      required 
                      autofocus
                      placeholder="Tu nombre completo"
                    >
                  </div>
                  <div class="validation-error">
                    <div v-for="message in validationErrors?.name" :key="message">
                      {{ message }}
                    </div>
                  </div>
                </div>
                
                <!-- Email -->
                <div class="form-group">
                  <label for="email" class="form-label">
                    <i class="pi pi-envelope"></i>
                    {{ $t('email') }}
                  </label>
                  <div class="input-wrapper">
                    <input 
                      v-model="registerForm.email" 
                      id="email" 
                      type="email" 
                      class="form-control" 
                      required 
                      autocomplete="username"
                      placeholder="ejemplo@email.com"
                    >
                  </div>
                  <div class="validation-error">
                    <div v-for="message in validationErrors?.email" :key="message">
                      {{ message }}
                    </div>
                  </div>
                </div>
                
                <!-- Contraseña -->
                <div class="form-group">
                  <label for="password" class="form-label">
                    <i class="pi pi-lock"></i>
                    {{ $t('password') }}
                  </label>
                  <div class="input-wrapper">
                    <input 
                      v-model="registerForm.password" 
                      id="password" 
                      type="password" 
                      class="form-control" 
                      required 
                      autocomplete="new-password"
                      placeholder="••••••••"
                    >
                  </div>
                  <div class="validation-error">
                    <div v-for="message in validationErrors?.password" :key="message">
                      {{ message }}
                    </div>
                  </div>
                  <div class="password-requirements">
                    <small>La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.</small>
                  </div>
                </div>
                
                <!-- Confirmación de contraseña -->
                <div class="form-group">
                  <label for="password_confirmation" class="form-label">
                    <i class="pi pi-shield"></i>
                    {{ $t('confirm_password') }}
                  </label>
                  <div class="input-wrapper">
                    <input 
                      v-model="registerForm.password_confirmation" 
                      id="password_confirmation" 
                      type="password" 
                      class="form-control" 
                      required 
                      autocomplete="new-password"
                      placeholder="••••••••"
                    >
                  </div>
                  <div class="validation-error">
                    <div v-for="message in validationErrors?.password_confirmation" :key="message">
                      {{ message }}
                    </div>
                  </div>
                </div>
  
                <div class="form-actions">
                  <button 
                    type="submit" 
                    class="btn-continue" 
                    :class="{ 'btn-disabled': processing }" 
                    :disabled="processing"
                  >
                    <span v-if="!processing">Continuar</span>
                    <span v-else class="register-spinner">
                      <i class="pi pi-spin pi-spinner"></i>
                    </span>
                  </button>
                </div>
              </div>
              
              <!-- Paso 2: Pago -->
              <div v-if="currentStep === 2" class="step-content">
                <!-- Número de tarjeta -->
                <div class="form-group">
                  <label for="card_number" class="form-label">
                    <i class="pi pi-credit-card"></i>
                    {{ $t('Número de tarjeta') }}
                  </label>
                  <div class="card-input-wrapper">
                    <input 
                      v-model="paymentForm.card_number" 
                      @input="formatCardNumber" 
                      @keypress="onlyNumbers" 
                      id="card_number" 
                      type="text" 
                      class="form-control" 
                      maxlength="19"
                      placeholder="0000 0000 0000 0000"
                    >
                    <div class="card-type-icon" v-if="cardType">
                      <img :src="`/images/cards/${cardType}.svg`" :alt="cardType">
                    </div>
                  </div>
                  <div class="validation-error">
                    <div v-for="message in validationErrors?.card_number" :key="message">
                      {{ message }}
                    </div>
                  </div>
                </div>
                
                <div class="form-row">
                  <!-- Fecha de expiración -->
                  <div class="form-group form-group-half">
                    <label for="expiry_date" class="form-label">
                      <i class="pi pi-calendar"></i>
                      {{ $t('Fecha de expiración') }}
                    </label>
                    <div class="input-wrapper">
                      <input 
                        v-model="paymentForm.expiry_date" 
                        @input="formatExpiryDate" 
                        @keypress="onlyNumbers" 
                        id="expiry_date" 
                        type="text" 
                        class="form-control" 
                        placeholder="MM/YY"
                        maxlength="5"
                      >
                    </div>
                    <div class="validation-error">
                      <div v-for="message in validationErrors?.expiry_date" :key="message">
                        {{ message }}
                      </div>
                    </div>
                  </div>
                  
                  <!-- CVV -->
                  <div class="form-group form-group-half">
                    <label for="cvv" class="form-label">
                      <i class="pi pi-shield"></i>
                      {{ $t('CVV') }}
                    </label>
                    <div class="input-wrapper">
                      <input 
                        v-model="paymentForm.cvv" 
                        @keypress="onlyNumbers" 
                        id="cvv" 
                        type="text" 
                        class="form-control" 
                        maxlength="4"
                        placeholder="123"
                      >
                    </div>
                    <div class="validation-error">
                      <div v-for="message in validationErrors?.cvv" :key="message">
                        {{ message }}
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="payment-security-info">
                  <i class="pi pi-lock"></i>
                  <span>Los datos de tu tarjeta están seguros. La información se encripta con SSL.</span>
                </div>
  
                <div class="form-actions form-actions-payment">
                  <button 
                    type="button" 
                    class="btn-back" 
                    @click="currentStep = 1"
                    :disabled="processing"
                  >
                    <i class="pi pi-arrow-left"></i>
                    Volver
                  </button>
                  <button 
                    type="submit" 
                    class="btn-register" 
                    :class="{ 'btn-disabled': processing }" 
                    :disabled="processing"
                  >
                    <span v-if="!processing">Completar registro</span>
                    <span v-else class="register-spinner">
                      <i class="pi pi-spin pi-spinner"></i>
                    </span>
                  </button>
                </div>
              </div>
            </form>
          </div>
          
          <!-- Opción de iniciar sesión -->
          <div class="login-option">
            <span>¿Ya tienes cuenta?</span>
            <router-link to="/login" class="login-link">
              Iniciar sesión
            </router-link>
          </div>
          
          <!-- Footer -->
          <div class="register-footer">
            <p>© {{ new Date().getFullYear() }} KanFlow. Todos los derechos reservados.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="back-to-home">
        <router-link to="/" class="home-link">
            <i class="pi pi-arrow-left"></i>
            <span>Volver a la página principal</span>
        </router-link>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import axios from 'axios';
  import useAuth from '@/composables/auth';
  
  const { registerForm, validationErrors, processing, submitRegister } = useAuth();
  const currentStep = ref(1);
  const paymentForm = ref({
    card_number: '',
    expiry_date: '',
    cvv: ''
  });
  const cardType = ref('');
  
  const handleSubmit = async () => {
    if (currentStep.value === 1) {
      // Validar el formulario de registro
      if (await validateRegisterForm()) {
        currentStep.value = 2;
      }
    } else if (currentStep.value === 2) {
      // Validar el formulario de método de pago y registrar el usuario
      if (await validatePaymentForm()) {
        await submitRegister();
      }
    }
  };
  
  const validateRegisterForm = async () => {
    validationErrors.value = {};
  
    // Verificar que todos los campos estén rellenos
    if (!registerForm.name || !registerForm.email || !registerForm.password || !registerForm.password_confirmation) {
      if (!registerForm.name) validationErrors.value.name = ['El nombre es obligatorio.'];
      if (!registerForm.email) validationErrors.value.email = ['El email es obligatorio.'];
      if (!registerForm.password) validationErrors.value.password = ['La contraseña es obligatoria.'];
      if (!registerForm.password_confirmation) validationErrors.value.password_confirmation = ['La confirmación de la contraseña es obligatoria.'];
      return false;
    }
  
    // Validar formato del email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(registerForm.email)) {
      validationErrors.value.email = ['El formato del email no es válido.'];
      return false;
    }
  
    // Verificar que el email no exista ya
    try {
      const response = await axios.post('/api/check-email', { email: registerForm.email });
      if (response.data.exists) {
        validationErrors.value.email = ['El email ya está registrado.'];
        return false;
      }
    } catch (error) {
      validationErrors.value.email = ['Error al verificar el email.'];
      return false;
    }
  
    // Validar la seguridad de la contraseña
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
    if (!passwordRegex.test(registerForm.password)) {
      validationErrors.value.password = ['La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.'];
      return false;
    }
  
    // Validar que la confirmación de contraseña coincida
    if (registerForm.password !== registerForm.password_confirmation) {
      validationErrors.value.password_confirmation = ['Las contraseñas no coinciden.'];
      return false;
    }
  
    return true;
  };
  
  const validatePaymentForm = async () => {
    validationErrors.value = {};
  
    // Verificar que todos los campos estén rellenos
    if (!paymentForm.value.card_number || !paymentForm.value.expiry_date || !paymentForm.value.cvv) {
      if (!paymentForm.value.card_number) validationErrors.value.card_number = ['El número de tarjeta es obligatorio.'];
      if (!paymentForm.value.expiry_date) validationErrors.value.expiry_date = ['La fecha de expiración es obligatoria.'];
      if (!paymentForm.value.cvv) validationErrors.value.cvv = ['El CVV es obligatorio.'];
      return false;
    }
  
    // Validar el formato del número de tarjeta de crédito
    const cardNumberRegex = /^[0-9]{13,19}$/;
    if (!cardNumberRegex.test(paymentForm.value.card_number.replace(/\s/g, ''))) {
      validationErrors.value.card_number = ['El número de tarjeta no es válido.'];
      return false;
    }
  
    // Validar el formato de la fecha de expiración (MM/YY)
    const expiryDateRegex = /^(0[1-9]|1[0-2])\/?([0-9]{2})$/;
    if (!expiryDateRegex.test(paymentForm.value.expiry_date)) {
      validationErrors.value.expiry_date = ['La fecha de expiración no es válida.'];
      return false;
    }
  
    // Validar el formato del CVV (3 o 4 dígitos)
    const cvvRegex = /^[0-9]{3,4}$/;
    if (!cvvRegex.test(paymentForm.value.cvv)) {
      validationErrors.value.cvv = ['El CVV no es válido.'];
      return false;
    }
  
    return true;
  };
  
  const formatCardNumber = () => {
    let value = paymentForm.value.card_number.replace(/\D/g, '');
    value = value.substring(0, 16);
    
    // Format with spaces every 4 digits
    value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
    paymentForm.value.card_number = value;
  
    // Detect card type
    const cardNumber = value.replace(/\s/g, '');
    if (/^4/.test(cardNumber)) {
      cardType.value = 'visa';
    } else if (/^5[1-5]/.test(cardNumber)) {
      cardType.value = 'mastercard';
    } else if (/^3[47]/.test(cardNumber)) {
      cardType.value = 'amex';
    } else {
      cardType.value = '';
    }
  };
  
  const formatExpiryDate = () => {
    let value = paymentForm.value.expiry_date.replace(/\D/g, '');
  
    if (value.length >= 2) {
      let month = parseInt(value.substring(0, 2));
      // Si el mes excede 12, forzarlo a 12
      if (month > 12) {
        month = 12;
      }
      // Tomar hasta 4 dígitos (mes + año)
      value = month.toString().padStart(2, '0') + value.substring(2, 4);
      // Insertar la barra tras los 2 primeros dígitos
      value = value.substring(0, 2) + '/' + value.substring(2);
    }
  
    paymentForm.value.expiry_date = value;
  };
  
  const onlyNumbers = (e) => {
    if (!/^\d*$/.test(e.key)) {
      e.preventDefault();
    }
  };
  </script>
  
  <style scoped>
  /* Base styles */
  .register-page {
    min-height: 100vh;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Figtree', sans-serif;
    color: #fff;
    padding: 40px 0;
  }
  
  /* Background styles */
  .register-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    background: linear-gradient(135deg, #0e0d26 0%, #1c1a47 100%);
    z-index: -2;
  }
  
  .gradient-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at center, rgba(102, 101, 221, 0.1) 0%, rgba(15, 14, 39, 0.5) 70%);
    z-index: -1;
  }
  
  .particles-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,0.05)' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
    z-index: -1;
  }
  
  /* Container styles */
  .register-container {
    width: 100%;
    max-width: 520px;
    padding: 0 20px;
    margin: 0 auto;
    position: relative;
    z-index: 10;
  }
  
  .register-card-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  
  /* Logo & branding styles */
  .register-branding {
    text-align: center;
    margin-bottom: 30px;
  }
  
  .register-logo {
    width: 160px;
    height: auto;
    margin-bottom: 20px;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
  }
  
  .register-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0 0 8px;
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }
  
  .register-subtitle {
    font-size: 1.1rem;
    font-weight: 400;
    margin: 0;
    color: rgba(255, 255, 255, 0.8);
  }
  
  /* Card styles */
  .register-card {
    background: rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 32px;
    width: 100%;
    backdrop-filter: blur(10px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 20px;
    position: relative;
  }
  
  /* Step indicator */
  .step-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 30px;
  }
  
  .step-bubble {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.7);
    font-weight: 600;
    border: 2px solid rgba(255, 255, 255, 0.2);
  }
  
  .step-bubble.active {
    background: #6665dd;
    color: white;
    border-color: #6665dd;
    box-shadow: 0 0 0 4px rgba(102, 101, 221, 0.3);
  }
  
  .step-line {
    flex-grow: 1;
    height: 2px;
    background: rgba(255, 255, 255, 0.1);
    margin: 0 15px;
  }
  
  /* Form styles */
  .register-form {
    margin-bottom: 0;
  }
  
  .form-group {
    margin-bottom: 24px;
  }
  
  .form-label {
    display: flex;
    align-items: center;
    font-size: 0.95rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 8px;
  }
  
  .form-label i {
    margin-right: 8px;
    color: rgba(255, 255, 255, 0.7);
  }
  
  .input-wrapper {
    position: relative;
  }
  
  .form-control {
    width: 100%;
    padding: 14px;
    font-size: 1rem;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    color: white;
    transition: all 0.3s ease;
  }
  
  .form-control:focus {
    outline: none;
    border-color: #6665dd;
    background: rgba(255, 255, 255, 0.12);
    box-shadow: 0 0 0 3px rgba(102, 101, 221, 0.15);
  }
  
  .form-control::placeholder {
    color: rgba(255, 255, 255, 0.4);
  }
  
  .validation-error {
    color: #ff6b6b;
    font-size: 0.85rem;
    margin-top: 8px;
  }
  
  .password-requirements {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.6);
    margin-top: 6px;
  }
  
  /* Form layout for payment info */
  .form-row {
    display: flex;
    gap: 16px;
  }
  
  .form-group-half {
    flex: 1;
  }
  
  .card-input-wrapper {
    position: relative;
  }
  
  .card-type-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .card-type-icon img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }
  
  .payment-security-info {
    display: flex;
    align-items: center;
    padding: 12px;
    background: rgba(102, 101, 221, 0.08);
    border-radius: 8px;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 24px;
  }
  
  .payment-security-info i {
    color: #6665dd;
    font-size: 1rem;
    margin-right: 8px;
  }
  
  /* Button styles */
  .form-actions {
    margin-top: 32px;
  }
  
  .form-actions-payment {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .btn-continue, .btn-register {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 14px;
    font-size: 1rem;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #6665dd 0%, #3f359b 100%);
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 6px 15px rgba(102, 101, 221, 0.3);
  }
  
  .btn-continue:hover, .btn-register:hover {
    background: linear-gradient(135deg, #7a79e3 0%, #4d41b5 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 101, 221, 0.4);
  }
  
  .btn-continue:active, .btn-register:active {
    transform: translateY(1px);
    box-shadow: 0 4px 10px rgba(102, 101, 221, 0.3);
  }
  
  .btn-back {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 14px 20px;
    font-size: 1rem;
    font-weight: 500;
    border: none;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.08);
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .btn-back:hover {
    background: rgba(255, 255, 255, 0.12);
    transform: translateY(-2px);
  }
  
  .btn-back i {
    margin-right: 8px;
  }
  
  .btn-register {
    width: auto;
    padding-left: 24px;
    padding-right: 24px;
  }
  
  .btn-disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }
  
  .register-spinner {
    display: inline-block;
    font-size: 1.2rem;
  }
  
  /* Login option */
  .login-option {
    margin-top: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.95rem;
  }
  
  .login-link {
    color: #6665dd;
    text-decoration: none;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
  }
  
  .login-link:hover {
    background: rgba(102, 101, 221, 0.1);
  }
  .register-footer {
  margin-top: 20px;
  text-align: center;
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.85rem;
}
.step-indicator::after {
  content: '';
  position: absolute;
  height: 2px;
  background: #6665dd;
  top: 18px;
  left: calc(25% + 18px);
  right: calc(75% - 18px);
  transition: all 0.5s ease;
}

.step-indicator.step-2::after {
  left: calc(25% + 18px);
  right: calc(25% - 18px);
}
.card-input-wrapper::before {
  content: '🔒';
  position: absolute;
  left: -24px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 14px;
  opacity: 0;
  transition: all 0.3s ease;
}

.card-input-wrapper:focus-within::before {
  opacity: 1;
  left: -20px;
}
.step-content {
  animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.back-to-home {
  position: absolute;
  top: 20px;
  left: 20px;
  z-index: 100;
}

.home-link {
  display: flex;
  align-items: center;
  padding: 10px 16px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50px;
  color: rgba(255, 255, 255, 0.9);
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.3s ease;
  backdrop-filter: blur(5px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.home-link:hover {
  background: rgba(255, 255, 255, 0.15);
  color: white;
  transform: translateX(-5px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.home-link i {
  margin-right: 8px;
  font-size: 0.85rem;
}

/* Ajuste responsive */
@media (max-width: 576px) {
  .back-to-home {
    top: 15px;
    left: 15px;
  }
  
  .home-link span {
    display: none;
  }
  
  .home-link {
    padding: 10px;
  }
  
  .home-link i {
    margin-right: 0;
    font-size: 1rem;
  }
}
</style>