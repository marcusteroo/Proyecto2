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
                                        <label for="card_number" class="form-label">{{ $t('Numero de tarjeta') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text card-type">
                                                <img v-if="cardType" :src="`/images/cards/${cardType}.svg`" :alt="cardType" class="card-icon">
                                            </span>
                                            <input
                                                v-model="paymentForm.card_number"
                                                @input="formatCardNumber"
                                                @keypress="onlyNumbers"
                                                id="card_number"
                                                type="text"
                                                class="form-control"
                                                maxlength="19"
                                            >
                                            <span class="input-group-text card-type">
                                                <img v-if="cardType" :src="`/images/cards/${cardType}.svg`" :alt="cardType" class="card-icon">
                                            </span>
                                        </div>
                                        <div class="text-danger mt-1">
                                            <div v-for="message in validationErrors?.card_number">
                                                {{ message }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="expiry_date" class="form-label">{{ $t('Fecha de vencimiento') }}</label>
                                        <div class="input-group">
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
                                        <div class="text-danger mt-1">
                                            <div v-for="message in validationErrors?.expiry_date">
                                                {{ message }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cvv" class="form-label">{{ $t('CVV (Código Seguridad)') }}</label>
                                        <input v-model="paymentForm.cvv" id="cvv" type="text" class="form-control" maxlength="3" @keypress="onlyNumbers">
                                        <div class="text-danger mt-1">
                                            <div v-for="message in validationErrors?.cvv">
                                                {{ message }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end mt-4">
                                        <button class="btn btn-primary" :class="{ 'opacity-25': processing }" :disabled="processing">
                                            {{ $t('Pagar') }}
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

.input-group {
  position: relative;
  display: flex;
  align-items: center;
}
.input-group .form-control {
    padding-right: 40px; 
}

.card-type {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border-left: none;
    pointer-events: none;
    z-index: 9999;
    border: none;
}

.card-icon {
    width: 24px;
    height: 24px;
    object-fit: contain;
}

.form-control:focus {
  box-shadow: none;
  border-color: #4a6cf7;
}
</style>