<template>
    <div class="login-page">
      <div class="login-background">
        <div class="particles-container"></div>
        <div class="gradient-overlay"></div>
      </div>
      
      <div class="login-container">
        <div class="login-card-wrapper">
          <!-- Logo y título -->
          <div class="login-branding">
            <img src="/images/LogoKanFlow2.svg" alt="KanFlow" class="login-logo" />
            <h1 class="login-title">Acceder a KanFlow</h1>
            <p class="login-subtitle">Inicia sesión para continuar</p>
          </div>
          
          <!-- Formulario -->
          <div class="login-card">
            <form @submit.prevent="submitLogin" class="login-form">
              <!-- Email -->
              <div class="form-group">
                <label for="email" class="form-label">
                  <i class="pi pi-envelope"></i>
                  {{ $t('email') }}
                </label>
                <div class="input-wrapper">
                  <input 
                    v-model="loginForm.email" 
                    id="email" 
                    type="email" 
                    class="form-control" 
                    required 
                    autofocus 
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
              
              <!-- Password -->
              <div class="form-group">
                <label for="password" class="form-label">
                  <i class="pi pi-lock"></i>
                  {{ $t('password') }}
                </label>
                <div class="input-wrapper">
                  <input 
                    v-model="loginForm.password" 
                    id="password" 
                    type="password" 
                    class="form-control" 
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••"
                  >
                </div>
                <div class="validation-error">
                  <div v-for="message in validationErrors?.password" :key="message">
                    {{ message }}
                  </div>
                </div>
              </div>
              
              <!-- Submit Button -->
              <div class="form-actions">
                <button 
                  type="submit" 
                  class="btn-login" 
                  :class="{ 'btn-disabled': processing }" 
                  :disabled="processing"
                >
                  <span v-if="!processing">{{ $t('login') }}</span>
                  <span v-else class="login-spinner">
                    <i class="pi pi-spin pi-spinner"></i>
                  </span>
                </button>
              </div>
            </form>
            
            <!-- Separador -->
            <div class="login-separator">
              <span>O</span>
            </div>
            
            <!-- Opción de registro -->
            <div class="register-option">
              <router-link to="/register" class="register-link">
                <i class="pi pi-user-plus"></i>
                Crear una cuenta nueva
              </router-link>
            </div>
          </div>
          
          <!-- Footer del login -->
          <div class="login-footer">
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
  import useAuth from '@/composables/auth';
  const { loginForm, validationErrors, processing, submitLogin } = useAuth();
  </script>
  
  <style scoped>
  /* Base styles */
  .login-page {
    min-height: 100vh;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Figtree', sans-serif;
    color: #fff;
  }
  
  /* Background styles */
  .login-background {
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
  .login-container {
    width: 100%;
    max-width: 440px;
    padding: 0 20px;
    margin: 0 auto;
    position: relative;
    z-index: 10;
  }
  
  .login-card-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  
  /* Logo & branding styles */
  .login-branding {
    text-align: center;
    margin-bottom: 30px;
  }
  
  .login-logo {
    width: 160px;
    height: auto;
    margin-bottom: 20px;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
  }
  
  .login-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0 0 8px;
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }
  
  .login-subtitle {
    font-size: 1.1rem;
    font-weight: 400;
    margin: 0;
    color: rgba(255, 255, 255, 0.8);
  }
  
  /* Card styles */
  .login-card {
    background: rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 32px;
    width: 100%;
    backdrop-filter: blur(10px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 20px;
  }
  
  /* Form styles */
  .login-form {
    margin-bottom: 20px;
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
  
  /* Button styles */
  .form-actions {
    margin-top: 32px;
  }
  
  .btn-login {
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
  
  .btn-login:hover {
    background: linear-gradient(135deg, #7a79e3 0%, #4d41b5 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 101, 221, 0.4);
  }
  
  .btn-login:active {
    transform: translateY(1px);
    box-shadow: 0 4px 10px rgba(102, 101, 221, 0.3);
  }
  
  .btn-disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }
  
  .login-spinner {
    display: inline-block;
    font-size: 1.2rem;
  }
  
  /* Separator style */
  .login-separator {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 24px 0;
    color: rgba(255, 255, 255, 0.6);
  }
  
  .login-separator::before,
  .login-separator::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
  }
  
  .login-separator span {
    padding: 0 16px;
    font-size: 0.9rem;
  }
  
  /* Register option */
  .register-option {
    text-align: center;
    padding: 4px 0;
  }
  
  .register-link {
    display: inline-flex;
    align-items: center;
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.2s ease;
    background: rgba(255, 255, 255, 0.08);
  }
  
  .register-link:hover {
    background: rgba(255, 255, 255, 0.12);
    color: white;
    transform: translateY(-2px);
  }
  
  .register-link i {
    margin-right: 8px;
  }
  
  /* Footer */
  .login-footer {
    margin-top: 20px;
    text-align: center;
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.85rem;
  }
  
  /* Responsive adjustments */
  @media (max-width: 576px) {
    .login-card {
      padding: 24px;
    }
    
    .login-title {
      font-size: 1.8rem;
    }
    
    .login-subtitle {
      font-size: 1rem;
    }
    
    .form-control {
      padding: 12px;
    }
    
    .btn-login {
      padding: 12px;
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