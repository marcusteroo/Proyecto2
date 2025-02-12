<template>
    <div class="register-bg">
        <div class="overlay"></div>
        <div class="logo-container">
            <img src="/images/LogoKanFlow.svg" alt="Kanflow Logo" class="register-logo" />
        </div>
        <div class="container">
            <div class="row justify-content-center my-5">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <form @submit.prevent="handleSubmit">
                                <div v-if="currentStep === 1">
                                    <!-- Formulario de Registro -->
                                    <div class="mb-3">
                                        <label for="name" class="form-label">{{ $t('name') }}</label>
                                        <input v-model="registerForm.name" id="name" type="text" class="form-control" autofocus>
                                        <div class="text-danger mt-1">
                                            <div v-for="message in validationErrors?.name">
                                                {{ message }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">{{ $t('email') }}</label>
                                        <input v-model="registerForm.email" id="email" type="email" class="form-control" autocomplete="username">
                                        <div class="text-danger mt-1">
                                            <div v-for="message in validationErrors?.email">
                                                {{ message }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label">{{ $t('password') }}</label>
                                        <input v-model="registerForm.password" id="password" type="password" class="form-control" autocomplete="current-password">
                                        <div class="text-danger-600 mt-1">
                                            <div v-for="message in validationErrors?.password">
                                                {{ message }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="password_confirmation" class="form-label">{{ $t('confirm_password') }}</label>
                                        <input v-model="registerForm.password_confirmation" id="password_confirmation" type="password" class="form-control" autocomplete="current-password">
                                        <div class="text-danger-600 mt-1">
                                            <div v-for="message in validationErrors?.password_confirmation">
                                                {{ message }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end mt-4">
                                        <button class="btn btn-primary" :class="{ 'opacity-25': processing }" :disabled="processing">
                                            {{ $t('Siguiente') }}
                                        </button>
                                    </div>
                                </div>
                                <div v-if="currentStep === 2">
                                    <!-- Formulario de Método de Pago -->
                                    <div class="mb-3">
                                        <label for="card_number" class="form-label">{{ $t('card_number') }}</label>
                                        <input v-model="paymentForm.card_number" id="card_number" type="text" class="form-control">
                                        <div class="text-danger mt-1">
                                            <div v-for="message in validationErrors?.card_number">
                                                {{ message }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="expiry_date" class="form-label">{{ $t('expiry_date') }}</label>
                                        <input v-model="paymentForm.expiry_date" id="expiry_date" type="text" class="form-control">
                                        <div class="text-danger mt-1">
                                            <div v-for="message in validationErrors?.expiry_date">
                                                {{ message }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cvv" class="form-label">{{ $t('cvv') }}</label>
                                        <input v-model="paymentForm.cvv" id="cvv" type="text" class="form-control">
                                        <div class="text-danger mt-1">
                                            <div v-for="message in validationErrors?.cvv">
                                                {{ message }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end mt-4">
                                        <button class="btn btn-primary" :class="{ 'opacity-25': processing }" :disabled="processing">
                                            {{ $t('register') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    const cardNumberRegex = /^[0-9]{16}$/;
    if (!cardNumberRegex.test(paymentForm.value.card_number)) {
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
</script>

<style scoped>
.register-bg {
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

.register-card {
  background: #fff;
  max-width: 500px;
  width: 90%;
  padding: 40px;
}

.logo-container {
  text-align: center;
  margin-bottom: 20px;
}

.register-logo {
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