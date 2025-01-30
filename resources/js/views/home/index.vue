<template>
    <section class="hero">
        <!-- Video de fondo -->
        <video autoplay loop muted playsinline class="background-video">
        <source src="/videos/fondo.mp4" type="video/mp4" />
        Tu navegador no soporta videos.
        </video>

        <!-- Capa oscura para mejorar la visibilidad del texto -->
        <div class="overlay"></div>
      <!-- Logo y Login -->
      <div class="top-bar">
        <div class="logo">
            <img src="/images/LogoKanFlow.svg" alt="Kanflow Logo" />
        </div>
        <button class="login-btn">Login</button>
      </div>
  
      <!-- Contenido Central -->
      <div class="content">
        <h1>
          Optimiza tu trabajo combinando <br />
          automatización y gestión de tareas <br />
          <span class="highlight">en un solo lugar</span>
        </h1>
        <p>Automatiza procesos, organiza tareas y maximiza tu productividad</p>
        <button class="start-btn">Comenzar</button>
        <router-link to="/user/flows" class="btn btn-primary">Ir a Flows</router-link>
      </div>
    </section>
  </template>
  
  <script setup>
  // Aquí puedes manejar eventos en el futuro, como redirección al login o empezar
  </script>
  
  <style scoped>
/* Contenedor principal */
.hero {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  overflow: hidden;
  color: white;
  text-align: center;
  padding: 20px;
}

/* Video de fondo */
.background-video {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transform: translate(-50%, -50%);
  z-index: -2;
}

/* Capa oscura sobre el video para mejorar legibilidad */
.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5); /* Oscurece el video */
  z-index: -1;
}

/* Barra superior */
.top-bar {
  position: absolute;
  top: 20px;
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 40px;
}

/* Logo */
.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 20px;
  font-weight: bold;
}

/* Botones */
.login-btn,
.start-btn {
  background-color: #2563eb;
  color: white;
  border: none;
  padding: 10px 20px;
  font-size: 16px;
  font-weight: bold;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.login-btn:hover,
.start-btn:hover {
  background-color: #1e4bb8;
}

/* Contenido */
.content {
  max-width: 600px;
  z-index: 1;
}

.content h1 {
  font-size: 32px;
  font-weight: bold;
}

.content .highlight {
  color: #3b82f6;
}

.content p {
  margin-top: 10px;
  font-size: 18px;
  color: #ccc;
}

.start-btn {
  margin-top: 20px;
}
</style>
  