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
        <router-link class="login-btn" to="/login">{{ $t('Login') }}</router-link>
      </div>
  
      <!-- Contenido Central -->
      <div class="content">
        <h1>
          Optimiza tu trabajo combinando <br />
          automatización y gestión de tareas <br />
          <span class="highlight">en un solo lugar</span>
        </h1>
        <p>Automatiza procesos, organiza tareas y maximiza tu productividad</p>
        <router-link to="/login" class="start-btn">Comenzar</router-link>

      </div>
    </section>
    <section class="info-section" ref="infoSection">
      <div class="info-container">
        <h2><strong>Estamos aquí para ayudarte a ser más productivo y organizado</strong></h2>
        <p>
          Descubre la herramienta todo-en-uno que simplifica tu vida al combinar gestión de tareas, 
          planificación y automatizaciones. Diseñada para personas ocupadas que quieren hacer más en menos tiempo.
        </p>
        <div class="stats">
          <div class="stat-card">
            <span class="stat-value">+300</span>
            <span class="stat-label">Clientes</span>
          </div>
          <div class="stat-card">
            <span class="stat-value">+4</span>
            <span class="stat-label">Flows</span>
          </div>
          <div class="stat-card">
            <span class="stat-value">+300</span>
            <span class="stat-label">Clientes</span>
          </div>
        </div>
      </div>
    </section>
    <section class="pricing-section" ref="pricingSection">
      <div class="pricing-container">
        <h2>Precios</h2>

        <!-- Toggle Mensual / Anual -->
        <div class="toggle-container">
          <button 
            :class="{ active: isMonthly }" 
            @click="togglePlan(true)"
          >
            Mensual
          </button>
          <button 
            :class="{ active: !isMonthly }" 
            @click="togglePlan(false)"
          >
            Anual
          </button>
        </div>

        <!-- Tarjeta de precio -->
        <div class="pricing-card">
          <div class="pricing-card-left">
            <h3>{{ price }}€ <span>/{{ isMonthly ? 'mes' : 'año' }}</span></h3>
            <p>Ideal para organizarse el día a día</p>
            <button class="cta-button">Comenzar</button>
          </div>
          <div class="pricing-card-right">
            <!-- Beneficios -->
            <ul class="benefits">
              <li>✅ Hasta 4 automatizaciones</li>
              <li>✅ Conexión con WhatsApp</li>
              <li>✅ Integración con tu Email</li>
              <li>✅ Kanban totalmente personalizable</li>
            </ul>
          </div>
        </div>
      </div>
    </section>
    <section class="sobre-nosotros-container" ref="aboutSection">
      <div class="sobre-nosotros-content">
        <h2 class="sobre-nosotros-title">Quiénes somos</h2>
        <div class="sobre-nosotros-info">
          <img src="/images/sobre.jpg" alt="Sobre Nosotros" class="sobre-nosotros-image" />
          <p class="sobre-nosotros-text">
            En Kanflow, creemos que la productividad no tiene que ser complicada. Nuestra plataforma está diseñada para ayudar a personas ocupadas a organizar sus tareas, automatizar procesos y alcanzar sus objetivos sin estrés.
            Sabemos lo difícil que es gestionar múltiples responsabilidades, por eso combinamos herramientas de gestión de tareas y automatización en un solo lugar. Queremos que dediques menos tiempo a planificar y más a hacer lo que realmente importa.
          </p>
        </div>
      </div>
    </section>
    <section class="resenas-clientes-seccion">
      <h2 class="sobre-nosotros-opinion">Que opinan nuestros clientes</h2>
      <div class="resenas-clientes-container">
        <Swiper
          :modules="[Navigation, Pagination, EffectCoverflow]"
          loop="true"
          :autoplay="{ delay: 3000 }"
          class="resenas-clientes-slider"
          navigation
          :pagination="{ clickable: true }"
          effect="coverflow"
          :coverflowEffect="{ rotate: 50, stretch: 0, depth: 100, modifier: 1, slideShadows: false }"
        >
          <SwiperSlide v-for="(resena, index) in resenas" :key="index">
            <div class="resenas-clientes-card">
              <img :src="resena.foto" class="resenas-clientes-foto" alt="Foto de usuario" />
              <h3 class="resenas-clientes-nombre">{{ resena.nombre }}</h3>
              <div class="resenas-clientes-estrellas">★★★★★</div>
              <p class="resenas-clientes-texto">{{ resena.texto }}</p>
            </div>
          </SwiperSlide>
        </Swiper>
      </div>
    </section>
    <footer class="footer-container">
      
      <div class="seccion1-footer">
        <img src="/images/LogoKanFlow.svg" alt="Logo Kanflow">
        <ul class="ul-1">
          <li class="li-1">Producto</li>
          <li>Precio</li>
          <li>Quienes Somos</li>
          <li>Accesso a usuarios</li>
        </ul>
        <ul class="ul-2">
          <li class="li-2">Políticas</li>
          <li>Términos y condiciones de compra</li>
          <li>Política de privacidad</li>
          <li>Aviso legal</li>
        </ul>
      </div>
      <hr>
      <div class="social-icons">
        <a href="#" class="icon"><i class="fab fa-youtube"></i></a>
        <a href="#" class="icon"><i class="fab fa-instagram"></i></a>
        <a href="#" class="icon"><i class="fab fa-twitter"></i></a>
        <a href="#" class="icon"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="icon"><i class="fab fa-telegram"></i></a>
        <a href="#" class="icon"><i class="fas fa-envelope"></i></a>
      </div>
      
    </footer>

  </template>
  
  <script setup>
import { ref, onMounted } from "vue";
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-coverflow';
import { Navigation, Pagination, EffectCoverflow } from 'swiper/modules';

const isMonthly = ref(true);
const price = ref(40); // Precio inicial

const togglePlan = (monthly) => {
  isMonthly.value = monthly;
  price.value = monthly ? 40 : 400; // Cambia el precio según el plan
};

const resenas = ref([
  {
    nombre: 'Manolo',
    foto: '/images/manolo.png',
    texto: 'Una excelente aplicación la cual me ha ayudado mucho a organizarme mi día a día.',
  },
  {
    nombre: 'Oregario',
    foto: 'images/oregario.png',
    texto: 'Una excelente aplicación la cual me ha ayudado mucho a organizarme mi día a día.',
  },
]);

const infoSection = ref(null);
const pricingSection = ref(null);
const aboutSection = ref(null);

onMounted(() => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('fade-in-up');
      }
    });
  }, { threshold: 0.1 });

  [infoSection.value, pricingSection.value, aboutSection.value].forEach(section => {
    if (section) observer.observe(section);
  });
});
</script>
    
  <style scoped>
  /*Footer*/
  .seccion1-footer{
    display: flex;
    flex-direction: row;
   
    align-items: center; 
    justify-content: flex-start; 
    gap: 80px;
  }
  .seccion1-footer ul{
    list-style: none;
    padding: 0;
    margin: 0;
  }
  .ul-1{
    display: flex;
    flex-direction: column;
   
  }
  .ul-2{
    display: flex;
    flex-direction: column;
    
  }
  .ul-1 li, .ul-2 li {
  margin-bottom: 10px; 
  font-size: 1.2rem;
  font-weight: 300;
  }
  .seccion1-footer img{
    max-width: 250px;
    width: 100%;
  }
  .li-1, .li-2 {
    font-size: 1.5rem !important; 
    margin-bottom: 20px!important; 
    font-weight: 700!important;
  }
  /* Redes sociales */
  .social-icons {
    display: flex;
    justify-content: center;
    gap: 15px;
    padding: 20px 0;
  }

  .icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: 2px solid #fff;
    border-radius: 50%;
    text-decoration: none;
    color: white;
    font-size: 24px;
    transition: 0.3s ease;
  }

  .icon:hover {
    background: white;
    color: black;
  }
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
  font-family: "Figtree", serif;
  font-weight: bold;

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
  top: 5px;
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 40px;
}

/* Logo */
.logo {
  display: inline-flex;
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
  font-size: 20px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s ease;
  width: 150px;
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
  font-size: 36px;
  font-weight: bold;
}

.content .highlight {
  color: #3b82f6;
}

.content p {
  margin-top: 10px;
  font-size: 19px;
  color: #ccc;
}

.start-btn {
  margin-top: 20px;
}
.info-section {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 60px 20px;
  background-color: #ffffff;
  font-family: "Figtree", serif;
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.7s ease, transform 0.7s ease;
}

.info-container {
  max-width: 800px;
  text-align: center;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
  padding: 40px;
  border-radius: 12px;
  background: #fff;
}

.info-section h2 {
  font-size: 22px;
  font-weight: bold;
}

.info-section p {
  font-size: 16px;
  color: #333;
  margin-top: 10px;
}

.stats {
  display: flex;
  justify-content: space-around;
  margin-top: 20px;
}

.stat-card {
  background: #00ff00;
  color: white;
  padding: 15px 20px;
  border-radius: 8px;
  font-size: 18px;
  font-weight: bold;
  text-align: center;
  min-width: 100px;
}

.stat-value {
  font-size: 24px;
  font-weight: bold;
}

.stat-label {
  display: block;
  font-size: 14px;
}
/*Precio*/
.pricing-section {
  display: flex;
  justify-content: center;
  min-height: 100vh;
  background-color: #0d0b21;
  padding: 100px;
  font-family: "Figtree", serif;
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.7s ease, transform 0.7s ease;
}

.pricing-container {
  text-align: center;
  max-width: 1000px;
  width: 100%;
  background: #1c1440;
  padding-top: 30px;
}

.pricing-section h2 {
  font-size: 48px;
  color: white;
  font-weight: bold;
}

/* Toggle Switch */
.toggle-container {
  display: flex;
  justify-content: center;
  margin: 20px 0;
}

.toggle-container button {
  padding: 10px 20px;
  border: none;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  background: #282053;
  color: white;
  transition: 0.3s;
  max-width: 200px;
  width: 100%;
}

.toggle-container button.active {
  background: #4a6cf7;
}

.toggle-container button:first-child {
  border-radius: 8px 0 0 8px;
}

.toggle-container button:last-child {
  border-radius: 0 8px 8px 0;
}

/* Tarjeta de Precio */
.pricing-card {
  background: #102a75;
  padding: 40px;
  margin-top: 50px;
  display: flex; 
  justify-content: space-between; 
  align-items: center; 
  width: 100%; 
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
  height:80%;
  padding-bottom: 20px;
}

.pricing-card-left {
  text-align: left; 
  flex: 1;
  padding-left: 20px; 
}

.pricing-card-right {
  text-align: left; 
  flex: 1; 
}

.pricing-section h3 {
  font-size: 5em!important;
  color: white;
  margin-bottom: 20px; 
}

.pricing-section h3 span {
  font-size: 0.8em; 
  color: #ccc;
}

.pricing-section p {
  color: #bbb;
  font-size: 2em; 
  margin-bottom: 20px;
}

/* Botón CTA */
.cta-button {
  
  width: 200px;
  background: #4a6cf7;
  color: white;
  padding: 15px; 
  margin-top: 20px;
  border-radius: 6px;
  font-weight: bold;
  font-size: 20px; 
  cursor: pointer;
  border: none;
}

/* Beneficios */
.benefits {
  list-style: none;
  margin-top: 20px;
  padding: 0;
  text-align: left;
  color: white;
  padding-left: 20px;
}

.benefits li {
  margin: 50px 0;
  font-size: 1.5em;
}
.sobre-nosotros-container {
  position: relative;
  background: linear-gradient(to bottom, #ffffff 0%, #f0f0f0 50%, #101028 100%); 
  padding: 60px 40px; 
  text-align: center;
  min-height: 600px; 
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.7s ease, transform 0.7s ease;
}

.sobre-nosotros-content {
  max-width: 800px;
  margin: 0 auto;
  font-family: "Figtree", serif;
}

.sobre-nosotros-title {
  font-size: 2.5rem; 
  font-weight: bold;
  margin-bottom: 30px; 
  color: #333;
}

.sobre-nosotros-info {
  display: flex;
  align-items: center;
  gap: 30px; 
}

.sobre-nosotros-image {
  width: 350px; 
  border-radius: 12px; 
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.sobre-nosotros-text {
  text-align: left;
  font-size: 1.2rem;
  line-height: 1.8; 
  color: black; 
  font-weight: 600;
}

.sobre-nosotros-degradado {
  height: 80px;
  background: linear-gradient(to bottom, rgba(255, 255, 255, 0), #101028); 
}

.sobre-nosotros-opinion {
  font-size: 1.8rem; 
  font-weight: bold;
  color: white;
  padding-top: 20px;
}
.resenas-clientes-container {
  background-color: #101028;
  padding: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
  position: relative;
  font-family: "Figtree", serif;
}
.resenas-clientes-seccion {
  background-color: #101028;
  padding: 20px;
  text-align: center;
  font-family: "Figtree", serif;
}
.resenas-clientes-seccion h2 {
  font-size: 2.5rem;
  font-weight: bold;
  color: white;

}

.resenas-clientes-slider {
  width: 80%;
  overflow: hidden;
  position: relative;
  perspective: 1000px; 
  transition: transform 0.5s ease;
}

.resenas-clientes-track {
  display: flex;
  transition: transform 0.5s ease-in-out;
}

.resenas-clientes-slide {
  min-width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: all 0.5s ease; 
}

.resenas-clientes-card {
  color: white;
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  width: 100%;
}

.resenas-clientes-foto {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  margin-bottom: -20px; /* Para que la foto se ajuste bien al texto al ser circular se separaba mucho*/
}

.resenas-clientes-nombre {
  font-size: 2em;
  margin-bottom: 5px;
}

.resenas-clientes-estrellas {
  color: gold;
  font-size: 1.8em;
}

.resenas-clientes-texto {
  font-size: 1.2em;
  margin-top: 10px;
  margin-bottom: 10px;
  font-weight: 400;
}
.resenas-clientes-card,
.resenas-clientes-foto,
.resenas-clientes-nombre,
.resenas-clientes-estrellas,
.resenas-clientes-texto {
  user-select: none; /* Esto lo estoy haciendo porque al pasar el slider muchas veces rapido se subrayaba el contenido, osea he quitado que se pueda seleccionar */
  -webkit-user-select: none; 
  -webkit-tap-highlight-color: transparent; 
}

.swiper-button-next,
.swiper-button-prev {
  color: white;
  top: 50%;
  transform: translateY(-50%);
}
:deep(.swiper-button-next:focus,
.swiper-button-prev:focus,
.swiper-pagination-bullet:focus) {
  outline: none!important;
  box-shadow: none !important;
}
:deep(.swiper-pagination-bullet) {
  bottom: 20px !important; 
  background-color: #AFAFAF; 
}

:deep(.swiper-pagination-bullet-active) {
  background-color: #3770FF;
}
.footer-container {
  background-color: black;
  color: white;
  padding: 20px;
  font-family: "Figtree", serif;
}


@media (max-width: 1024px) {
  .pricing-card {
    flex-direction: column;
    text-align: center; 
    height: auto; 
  }

  .pricing-card-left, .pricing-card-right {
    flex: none; 
    width: 100%; 
    padding: 0; 
  }

  .pricing-section h3 {
    font-size: 3em !important; 
  }

  .pricing-section p {
    font-size: 1.5em; 
  }

  .cta-button {
    width: 100%; 
    font-size: 18px; 
  }

  .benefits li {
    font-size: 1.2em; 
    margin: 20px 0; 
  }
}

@media (max-width: 768px) {
  .hero {
    padding: 10px; 
  }

  .content h1 {
    font-size: 24px; 
  }

  .content p {
    font-size: 16px; 
  }

  .start-btn {
    font-size: 14px; 
  }

  .info-section {
    padding: 40px 10px; 
  }

  .info-container {
    padding: 20px;
  }

  .info-section h2 {
    font-size: 20px; 
  }

  .info-section p {
    font-size: 14px; 
  }

  .stat-card {
    padding: 10px 15px; 
    font-size: 16px; 
  }

  .stat-value {
    font-size: 20px; 
  }

  .stat-label {
    font-size: 12px; 
  }

  .pricing-section {
    padding: 50px 10px; 
  }

  .pricing-container {
    padding-top: 20px; 
  }

  .pricing-section h2 {
    font-size: 36px; 
  }

  .toggle-container button {
    font-size: 14px; 
  }

  .sobre-nosotros-container {
    padding: 40px 10px; 
    min-height: 400px; 
  }

  .sobre-nosotros-title {
    font-size: 2rem; 
  }

  .sobre-nosotros-info {
    flex-direction: column; 
    gap: 20px; 
  }

  .sobre-nosotros-image {
    width: 100%; 
    max-width: 300px; 
  }

  .sobre-nosotros-text {
    font-size: 1rem; 
  }

  .resenas-clientes-seccion h2 {
    font-size: 2rem; 
  }

  .resenas-clientes-card {
    padding: 10px; 
  }

  .resenas-clientes-foto {
    width: 150px; 
    height: 150px; 
  }

  .resenas-clientes-nombre {
    font-size: 1.5em; 
  }

  .resenas-clientes-estrellas {
    font-size: 1.5em; 
  }

  .resenas-clientes-texto {
    font-size: 1em; 
  }
  .seccion1-footer {
    flex-direction: column;
    align-items: flex-start;
  }

  .seccion1-footer img {
    margin-bottom: 20px; 
  }
}

@media (max-width: 480px) {
  .content{
    max-height: 80vh;
  }
  .content h1 {
    font-size: 2em; 
  }

  .content p {
    font-size: 1.2em; 
    margin-bottom: 40px;
  }

  .start-btn {
    font-size: 1.6em;

  }

  .info-section {
    padding: 30px 10px; 
  }

  .info-container {
    padding: 15px; 
  }

  .info-section h2 {
    font-size: 18px; 
  }

  .info-section p {
    font-size: 12px; 
  }

  .stat-card {
    padding: 8px 10px; 
    font-size: 14px; 
  }

  .stat-value {
    font-size: 18px; 
  }

  .stat-label {
    font-size: 10px; 
  }

  .pricing-section {
    padding: 40px 10px; 
  }

  .pricing-container {
    padding-top: 15px; 
  }

  .pricing-section h2 {
    font-size: 30px; 
  }

  .toggle-container button {
    font-size: 12px; 
  }

  .sobre-nosotros-container {
    padding: 30px 10px; 
    min-height: 300px; 
  }

  .sobre-nosotros-title {
    font-size: 1.5rem; 
  }

  .sobre-nosotros-info {
    gap: 15px; 
  }

  .sobre-nosotros-image {
    max-width: 250px; 
  }

  .sobre-nosotros-text {
    font-size: 0.9rem; 
  }

  .resenas-clientes-seccion h2 {
    font-size: 1.5rem; 
  }

  .resenas-clientes-card {
    padding: 8px; 
  }

  .resenas-clientes-foto {
    width: 120px; 
    height: 120px; 
  }

  .resenas-clientes-nombre {
    font-size: 1.2em; 
  }

  .resenas-clientes-estrellas {
    font-size: 1.2em; 
  }

  .resenas-clientes-texto {
    font-size: 0.9em; 
  }
  .logo img{
    max-width: 100%;
  }
  .seccion1-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px; /* Ajusta el gap para pantallas pequeñas */
    }

    .seccion1-footer img {
      max-width: 250px;
      width: 100%;
      height: 150px; /* Mantiene la proporción de la imagen */
      display: block; /* Asegura que la imagen no tenga espacio extra */
      object-fit: cover; 
      margin: 0; 
    }
    .seccion1-footer ul {
      padding-left: 10px; 
    }
    .footer-container hr{
      margin-top: 10px;
    }
}
.fade-in-up {
  opacity: 1;
  transform: translateY(0);
}
</style>
