<template>
    <div>
      <h1>Enviar Mensaje</h1>
      <form @submit.prevent="sendMessage">
        <div>
          <label>Número:</label>
          <input type="text" v-model="number" placeholder="Número de WhatsApp" />
        </div>
        <div>
          <label>Mensaje:</label>
          <textarea v-model="message" placeholder="Escribe tu mensaje"></textarea>
        </div>
        <button type="submit">Enviar</button>
      </form>
      <p v-if="responseMessage">{{ responseMessage }}</p>
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
  /* Estilos específicos para este componente */
  </style>
  