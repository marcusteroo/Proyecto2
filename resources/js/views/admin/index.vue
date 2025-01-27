<template>
  <div class="container">
    <h1>Enviar Mensaje</h1>
    <form @submit.prevent="sendMessage" class="form">
      <div class="form-group">
        <label for="number">Número:</label>
        <input type="text" id="number" v-model="number" placeholder="Número de WhatsApp" class="form-control" />
      </div>
      <div class="form-group">
        <label for="message">Mensaje:</label>
        <textarea id="message" v-model="message" placeholder="Escribe tu mensaje" class="form-control"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
    <p v-if="responseMessage" class="response-message">{{ responseMessage }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const number = ref('');
const message = ref('');
const responseMessage = ref('');

const sendMessage = async () => {
  try {
    const response = await axios.post('/api/send-message', {
      number: number.value,
      message: message.value,
    });
    responseMessage.value = response.data.message || 'Mensaje enviado con éxito';
  } catch (error) {
    responseMessage.value = 'Error al enviar el mensaje';
  }
};
</script>

<style scoped>
.container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
  text-align: center;
  margin-bottom: 20px;
  color: #333;
}

.form {
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.form-control {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.btn {
  padding: 10px 15px;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn:hover {
  background-color: #0056b3;
}

.response-message {
  margin-top: 20px;
  text-align: center;
  font-size: 16px;
  color: #28a745;
}
</style>
