<template>
    <div class="login-bg">
        <div class="overlay"></div>
        <div class="logo-container">
            <img src="/images/LogoKanFlow.svg" alt="Kanflow Logo" class="login-logo" />
        </div>
        <div class="card login-card border-0 shadow-sm">
            <div class="card-body">
                <form @submit.prevent="submitLogin">
                    <div class="">
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ $t('email') }}</label>
                            <input v-model="loginForm.email" id="email" type="email" class="form-control" required autofocus autocomplete="username">
                            <!-- Validation Errors -->
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.email">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                {{ $t('password') }}
                            </label>
                            <input v-model="loginForm.password" id="password" type="password" class="form-control" required autocomplete="current-password">
                            <!-- Validation Errors -->
                            <div class="text-danger-600 mt-1">
                                <div v-for="message in validationErrors?.password">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <!-- Remember me -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" v-model="loginForm.remember" id="flexCheckIndeterminate">
                            <label class="form-check-label" for="flexCheckIndeterminate">
                                {{ $t('remember_me') }}
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end mt-4">
                            <button class="btn btn-primary" :class="{ 'opacity-25': processing }" :disabled="processing">
                                {{ $t('login') }}
                            </button>
                        </div>
                    </div>
                    <div class="notienescuenta">
                        <router-link to="/register">
                            ¿No tienes cuenta? ¡Regístrate!
                        </router-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>

import useAuth from '@/composables/auth'

const { loginForm, validationErrors, processing, submitLogin } = useAuth();

</script>

<style scoped>
.login-bg {
  background-color: #0d0b21;
  position: relative;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  font-family: "Figtree", serif;
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  z-index: -1;
}

.login-card {
  background: #fff;
  max-width: 500px;
  width: 90%;
  padding: 40px;
}
.notienescuenta {
  text-align: center;
  margin-top: 20px;
  font-size: 1rem;
  color: #6c757d;
}
.logo-container {
  text-align: center;
  margin-bottom: 20px;
}

.login-logo {
  max-width: 200px;
  display: block;
  margin: 0 auto;
}

.form-label {
  font-weight: bold;
  font-size: 1.1rem;
}

.form-control {
  padding: 12px;
  font-size: 1rem;
}

.btn-primary {
  font-size: 1rem;
  padding: 12px 24px;
  border-radius: 0px;
}
</style>
