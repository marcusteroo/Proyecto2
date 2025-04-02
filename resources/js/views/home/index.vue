<template>
    <section class="hero">
  <!-- Video de fondo con overlay de gradiente -->
  <video autoplay loop muted playsinline class="background-video">
    <source src="/videos/fondo.mp4" type="video/mp4" />
    Tu navegador no soporta videos.
  </video>


  <div class="overlay">
    <div class="gradient-overlay"></div>
    <div class="particles-container" id="particles-js"></div>
  </div>

  <!-- Navegación principal -->
  <header class="main-header">
    <div class="container">
      <div class="logo">
        <img src="/images/LogoKanFlow2.svg" alt="Kanflow Logo" />
      </div>
      <nav class="main-nav">
        <ul class="nav-links">
          <li><a href="#info-section">Características</a></li>
          <li><a href="#pricing">Precios</a></li>
          <li><a href="#company-story">Nosotros</a></li>
        </ul>
        <div class="nav-buttons">
          <router-link class="btn btn-outline" to="/login">{{ $t('Login') }}</router-link>
          <router-link class="btn btn-primary" to="/register">Prueba Gratis</router-link>
        </div>
      </nav>
      <button class="mobile-menu-toggle" @click="toggleMobileMenu">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </header>
  
  <!-- Contenido de Hero mejorado -->
  <div class="hero-content">
    <div class="container">
      <div class="hero-grid">
        <div class="hero-text">
          <h5 class="hero-tagline">
            <span class="badge">Nuevo</span> 
            Aumenta tu productividad hasta un 80%
          </h5>
          <h1 class="hero-headline">
            Automatización y gestión de tareas
            <span class="hero-headline-highlight">todo en uno</span>
          </h1>
          <p class="hero-description">
            Simplifica tu flujo de trabajo, automatiza procesos repetitivos y gestiona proyectos sin esfuerzo. La plataforma que se adapta a tu forma de trabajar.
          </p>
          <div class="hero-cta">
            <router-link to="/register" class="btn btn-primary btn-large">
              Comenzar
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </router-link>
          </div>
          <div class="hero-stats">
            <div class="stat-item">
              <div class="stat-value">4.9/5</div>
              <div class="rating-stars">★★★★★</div>
              <p>De 500+ valoraciones</p>
            </div>
            <div class="divider"></div>
            <div class="stat-item">
              <div class="stat-value">2.5K+</div>
              <p>Usuarios activos</p>
            </div>
          </div>
        </div>
        <div class="hero-image">
          <img src="/images/dashboard-preview.png" alt="KanFlow Dashboard Preview" class="dashboard-preview" />
          <div class="floating-element element-1">
            <div class="element-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="element-text">Tarea completada</div>
          </div>
          <div class="floating-element element-2">
            <div class="element-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <div class="element-text">Flujo automatizado</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
</section>
    <section class="info-section" ref="infoSection" id="info-section">
  <div class="info-container">
    <div class="info-header">
      <span class="info-badge">Solución todo-en-uno</span>
      <h2>Simplifica, automatiza y alcanza tus metas</h2>
      <p class="info-subtitle">
        Una plataforma potente diseñada para profesionales que valoran su tiempo
      </p>
    </div>

    <div class="info-features">
      <div class="feature-card">
        <div class="feature-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3.5 3.75a.25.25 0 01.25-.25h13.5a.25.25 0 01.25.25v10a.75.75 0 001.5 0v-10A1.75 1.75 0 0017.25 2H3.75A1.75 1.75 0 002 3.75v16.5c0 .966.784 1.75 1.75 1.75h7a.75.75 0 000-1.5h-7a.25.25 0 01-.25-.25V3.75z"></path>
            <path d="M6.25 7a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5zm-.75 4.75a.75.75 0 01.75-.75h4.5a.75.75 0 010 1.5h-4.5a.75.75 0 01-.75-.75zm16.28 4.53a.75.75 0 10-1.06-1.06l-4.97 4.97-1.97-1.97a.75.75 0 10-1.06 1.06l2.5 2.5a.75.75 0 001.06 0l5.5-5.5z"></path>
          </svg>
        </div>
        <h3>Gestión de tareas inteligente</h3>
        <p>Organiza tus proyectos con un sistema Kanban personalizable que se adapta a tu flujo de trabajo específico.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M16.5 6.75h5.25a.75.75 0 000-1.5H16.5a.75.75 0 000 1.5zm0 12h5.25a.75.75 0 000-1.5H16.5a.75.75 0 000 1.5zm-8.63-6.75h2.38a.75.75 0 000-1.5H7.87a.75.75 0 000 1.5zm-.87-3h7.5a.75.75 0 000-1.5H7a.75.75 0 000 1.5zm0 6h7.5a.75.75 0 000-1.5H7a.75.75 0 000 1.5zm-5.25-12h9.5A1.75 1.75 0 0012 2H1.75A1.75 1.75 0 000 3.75V20.5c0 .966.784 1.75 1.75 1.75h9.5A1.75 1.75 0 0013 20.5v-7.283a.761.761 0 00-.233-.546L9.37 9.275a6.75 6.75 0 01-1.5-2.08l-2.004-4a.75.75 0 00-1.342 0l-2.004 4a6.75 6.75 0 01-1.5 2.08l-3.397 3.396A.761.761 0 00.39 13.22V3.75C.39 2.784 1.174 2 2.14 2zm-.9 11.1l3.397-3.396a8.25 8.25 0 001.834-2.543l1.03-2.06a5.252 5.252 0 01.45-.787L8.96 6.9a8.25 8.25 0 001.834 2.543l3.397 3.397v7.66a.25.25 0 01-.25.25h-9.5a.25.25 0 01-.25-.25V13.1z"></path>
          </svg>
        </div>
        <h3>Automatización sin código</h3>
        <p>Crea flujos de trabajo automatizados en minutos y libera tu tiempo para tareas de mayor valor.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M8.75 11.25a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5zm0 4a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5zm0-8a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5zm8.75-4.5A1.75 1.75 0 0015.75 1h-7.5A1.75 1.75 0 006.5 2.75v18.5c0 .966.784 1.75 1.75 1.75h7.5a1.75 1.75 0 001.75-1.75V2.75zM15.75 2.5a.25.25 0 01.25.25v18.5a.25.25 0 01-.25.25h-7.5a.25.25 0 01-.25-.25V2.75a.25.25 0 01.25-.25h7.5z"></path>
          </svg>
        </div>
        <h3>Integración multiplataforma</h3>
        <p>Conecta con tus aplicaciones favoritas como WhatsApp, Email y otras herramientas para un flujo de trabajo sin fisuras.</p>
      </div>
    </div>

    <div class="info-stats-section">
      <div class="stats-header">
        <h3>Nuestros Usuarios en KanFlow</h3>
        <p>Únete a miles de profesionales que ya están transformando su productividad</p>
      </div>

      <div class="stats-container">
        <div class="stat-card">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 14.25c-3.728 0-6.75-3.022-6.75-6.75S8.272.75 12 .75s6.75 3.022 6.75 6.75S15.728 14.25 12 14.25zM12 2.25c-2.9 0-5.25 2.35-5.25 5.25S9.1 12.75 12 12.75s5.25-2.35 5.25-5.25S14.9 2.25 12 2.25z"></path>
              <path d="M6.848 14.77c-1.324 0-2.424 1.06-2.424 2.328 0 1.088-.72 1.643-1.3 1.643-.15 0-.3-.03-.449-.09a.752.752 0 00-.92.515c-.151.495.176 1.01.667 1.14.251.066.522.1.802.1 1.574 0 2.926-1.26 2.926-3.308 0-.364.36-.659.724-.659h10.25c.364 0 .723.295.723.66 0 2.047 1.352 3.307 2.926 3.307.28 0 .55-.034.802-.1a.746.746 0 00.667-1.14.752.752 0 00-.92-.516c-.149.06-.299.09-.45.09-.58 0-1.3-.555-1.3-1.643 0-1.267-1.1-2.328-2.424-2.328h-9.3zm-.848 0c-1.324 0-2.424 1.06-2.424 2.328 0 1.088-.72 1.643-1.3 1.643-.15 0-.3-.03-.449-.09a.752.752 0 00-.92.515c-.151.495.176 1.01.667 1.14.251.066.522.1.802.1 1.574 0 2.926-1.26 2.926-3.308 0-.364.36-.659.724-.659h10.25c.364 0 .723.295.723.66 0 2.047 1.352 3.307 2.926 3.307.28 0 .55-.034.802-.1a.746.746 0 00.667-1.14.752.752 0 00-.92-.516c-.149.06-.299.09-.45.09-.58 0-1.3-.555-1.3-1.643 0-1.267-1.1-2.328-2.424-2.328h-9.3z"></path>
            </svg>
          </div>
          <div class="stat-count" data-count="2500">
            <span>+</span>2,500
          </div>
          <span class="stat-label">Usuarios activos</span>
          <span class="stat-trend positive">↑ 27% este año</span>
        </div>

        <div class="stat-card highlight">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path d="M11.25 4.5l-7.5 7.5 7.5 7.5V16.5h9v-7.5h-9V4.5zm-1.5 3v3.75H3l6.75-6.75v3z"></path>
            </svg>
          </div>
          <div class="stat-count" data-count="18000">
            <span>+</span>18,000
          </div>
          <span class="stat-label">Automatizaciones creadas</span>
          <span class="stat-trend positive">↑ 53% este mes</span>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12.75 4.86l-8.25 8.25a.75.75 0 001.06 1.06l2.69-2.69V20.5a.75.75 0 001.5 0v-9.75l3-3 3 3V20.5a.75.75 0 001.5 0v-9.02l2.69 2.69a.75.75 0 101.06-1.06l-8.25-8.25z"></path>
            </svg>
          </div>
          <div class="stat-count" data-count="87">
            87<span>%</span>
          </div>
          <span class="stat-label">Aumento de productividad</span>
          <span class="stat-trend">Promedio reportado</span>
        </div>
      </div>
    </div>
  </div>

</section>
    <section class="pricing-section" ref="pricingSection" id="pricing">
      <div class="pricing-container">
        <div class="pricing-header">
          <span class="pricing-badge">Planes flexibles</span>
          <h2>Precios transparentes para cada necesidad</h2>
          <p class="pricing-subtitle">Elige el plan perfecto para potenciar tu productividad</p>
        </div>

        <!-- Toggle Mensual / Anual con descuento -->
        <div class="billing-toggle">
          <span :class="{ 'active-billing': isMonthly }">Facturación mensual</span>
          <div class="toggle-switch" @click="togglePlan(!isMonthly)" :class="{ 'toggled': !isMonthly }">
            <div class="toggle-knob"></div>
          </div>
          <div class="annual-option">
            <span :class="{ 'active-billing': !isMonthly }">Facturación anual</span>
            <span class="discount-badge">Ahorra 20%</span>
          </div>
        </div>

        <div class="pricing-cards">
          <!-- Plan Básico -->
          <div class="pricing-card">
            <div class="card-header">
              <div class="plan-name">{{ getPlanPorCategoria('basico')?.nombre_plan || 'Básico' }}</div>
              <div class="plan-price">
                <div class="price-amount" v-if="getPlanPorCategoria('basico')">
                  {{ isMonthly ? getPlanPorCategoria('basico').precio_mensual : getPlanPorCategoria('basico').precio_anual }}<sup>€</sup>
                </div>
                <span class="price-period">{{ isMonthly ? '/mes' : '/año' }}</span>
              </div>
              <p class="plan-description">{{ getPlanPorCategoria('basico')?.descripcion || 'Perfecto para empezar a organizarte' }}</p>
            </div>
            <div class="card-features">
              <ul>
                <li v-for="(caracteristica, index) in getPlanPorCategoria('basico')?.caracteristicas || []" :key="index">
                  <span class="check-icon">✓</span>
                  {{ caracteristica }}
                </li>
              </ul>
            </div>
            <div class="card-action">
              <button @click="llevarPaginaRegistro" class="plan-button">Comenzar ahora</button>
            </div>
          </div>

          <!-- Plan Pro (Destacado) -->
          <div class="pricing-card featured">
            <div class="featured-badge">Más popular</div>
            <div class="card-header">
              <div class="plan-name">{{ getPlanPorCategoria('premium')?.nombre_plan || 'Premium' }}</div>
              <div class="plan-price">
                <div class="price-amount" v-if="getPlanPorCategoria('premium')">
                  {{ isMonthly ? getPlanPorCategoria('premium').precio_mensual : getPlanPorCategoria('premium').precio_anual }}<sup>€</sup>
                </div>
                <span class="price-period">{{ isMonthly ? '/mes' : '/año' }}</span>
              </div>
              <p class="plan-description">{{ getPlanPorCategoria('premium')?.descripcion || 'Ideal para profesionales y equipos' }}</p>
            </div>
            <div class="card-features">
              <ul>
                <li v-for="(caracteristica, index) in getPlanPorCategoria('premium')?.caracteristicas || []" :key="index">
                  <span class="check-icon">✓</span>
                  {{ caracteristica }}
                </li>
              </ul>
            </div>
            <div class="card-action">
              <button @click="llevarPaginaRegistro" class="plan-button featured-button">Comenzar ahora</button>
              <p class="guarantee">14 días de prueba gratis</p>
            </div>
          </div>

          <!-- Plan Business -->
          <div class="pricing-card">
            <div class="card-header">
              <div class="plan-name">{{ getPlanPorCategoria('business')?.nombre_plan || 'Business' }}</div>
              <div class="plan-price">
                <div class="price-amount" v-if="getPlanPorCategoria('business')">
                  {{ isMonthly ? getPlanPorCategoria('business').precio_mensual : getPlanPorCategoria('business').precio_anual }}<sup>€</sup>
                </div>
                <span class="price-period">{{ isMonthly ? '/mes' : '/año' }}</span>
              </div>
              <p class="plan-description">{{ getPlanPorCategoria('business')?.descripcion || 'Para empresas y equipos grandes' }}</p>
            </div>
            <div class="card-features">
              <ul>
                <li v-for="(caracteristica, index) in getPlanPorCategoria('business')?.caracteristicas || []" :key="index">
                  <span class="check-icon">✓</span>
                  {{ caracteristica }}
                </li>
              </ul>
            </div>
            <div class="card-action">
              <button @click="llevarPaginaRegistro" class="plan-button">Comenzar ahora</button>
            </div>
          </div>
        </div>

        <!-- Información adicional -->
        <div class="pricing-trust">
          <div class="trust-item">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
              <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"></path>
            </svg>
            <span>Garantía de devolución de 30 días</span>
          </div>
          <div class="trust-item">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path>
            </svg>
            <span>Sin contratos a largo plazo, cancela cuando quieras</span>
          </div>
          <div class="trust-item">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
            </svg>
            <span>Pago seguro con cifrado SSL</span>
          </div>
        </div>

        <!-- FAQ Section -->
        <div class="pricing-faq">
          <h3>Preguntas frecuentes</h3>
          <div class="faq-container">
            <div class="faq-item">
              <h4>¿Puedo cambiar de plan en cualquier momento?</h4>
              <p>Sí, puedes actualizar o cambiar tu plan en cualquier momento. Los cambios en la facturación se aplicarán en el siguiente ciclo de facturación.</p>
            </div>
            <div class="faq-item">
              <h4>¿Qué métodos de pago aceptan?</h4>
              <p>Aceptamos todas las tarjetas de crédito/débito principales (Visa, Mastercard, American Express), PayPal y transferencia bancaria para planes anuales.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="sobre-nosotros-container" ref="aboutSection" id="about-section">
    <div class="sobre-nosotros-content">
      <h2 class="sobre-nosotros-title">Nuestra misión</h2>
      <p class="sobre-nosotros-subtitle">Simplificar la productividad para todos</p>
      
      <div class="sobre-nosotros-cards">
        <div class="mission-card">
          <div class="mission-icon">
            ⏱️
          </div>
          <h3>Ahorra tiempo</h3>
          <p>Automatiza tareas repetitivas y procesos para enfocarte en lo que realmente importa para tu negocio.</p>
        </div>
        
        <div class="mission-card">
          <div class="mission-icon">
            ⚡
          </div>
          <h3>Aumenta la eficiencia</h3>
          <p>Organiza tus tareas con nuestro sistema Kanban visual y mejora la colaboración en tu equipo.</p>
        </div>
        
        <div class="mission-card">
          <div class="mission-icon">
            📈
          </div>
          <h3>Crece constantemente</h3>
          <p>Visualiza tu progreso y analiza los resultados para optimizar tus flujos de trabajo.</p>
        </div>
      </div>
      
      <div class="company-story" id="company-story">
        <div class="story-content">
          <h3 class="story-title">Nuestra historia</h3>
          <p class="story-text">
            En KanFlow, creemos que la productividad no tiene que ser complicada. Fundada en 2023 por un equipo de expertos en gestión de proyectos, nuestra plataforma está diseñada para ayudar a personas ocupadas a organizar sus tareas, automatizar procesos y alcanzar sus objetivos sin estrés.
          </p>
          <p class="story-text">
            Sabemos lo difícil que es gestionar múltiples responsabilidades, por eso combinamos herramientas de gestión de tareas y automatización en un solo lugar. Queremos que dediques menos tiempo a planificar y más a hacer lo que realmente importa.
          </p>
        </div>
        <div class="story-image-container">
          <img src="/images/sobre.jpg" alt="El equipo de KanFlow" class="story-image" />
        </div>
      </div>
      
      <div class="team-values">
        <h3 class="values-title">Nuestros valores</h3>
        <div class="values-container">
          <div class="value-item">
            <span class="value-badge">Simplicidad</span>
            <p>Creemos que las mejores soluciones son simples e intuitivas.</p>
          </div>
          <div class="value-item">
            <span class="value-badge">Innovación</span>
            <p>Constantemente buscamos nuevas formas de mejorar nuestro producto.</p>
          </div>
          <div class="value-item">
            <span class="value-badge">Confiabilidad</span>
            <p>Construimos tecnología en la que puedes confiar todos los días.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="testimonials-section" id="testimonials">
  <div class="testimonials-container">
    <div class="testimonials-header">
      <span class="testimonials-badge">Testimonios</span>
      <h2 class="testimonials-title">Lo que dicen nuestros clientes</h2>
      <p class="testimonials-subtitle">Miles de profesionales ya han mejorado su productividad con KanFlow</p>
    </div>
    
    <div class="testimonials-slider-wrapper">
      <Swiper
        :modules="[Navigation, Pagination, EffectCoverflow]"
        :loop="true"
        :autoplay="{ delay: 5000, disableOnInteraction: false }"
        :speed="800"
        :effect="'coverflow'"
        :grabCursor="true"
        :centeredSlides="true"
        :slidesPerView="'auto'"
        :coverflowEffect="{
          rotate: 0,
          stretch: 0,
          depth: 100,
          modifier: 2.5,
          slideShadows: false,
        }"
        :pagination="{
          clickable: true,
          dynamicBullets: true
        }"
        :navigation="true"
        class="testimonials-slider"
      >
        <SwiperSlide v-for="(testimonial, index) in testimonials" :key="index">
          <div class="testimonial-card" :class="{ 'featured': testimonial.featured }">
            <div class="testimonial-card-inner">
              <div class="testimonial-rating">
                <div class="rating-stars">★★★★★</div>
                <div class="rating-score">{{ testimonial.rating }}/5</div>
              </div>

              <blockquote class="testimonial-text">
                "{{ testimonial.texto }}"
              </blockquote>

              <div class="testimonial-author">
                <div class="author-image-container">
                  <img :src="testimonial.foto" class="author-image" :alt="testimonial.nombre" />
                  <div v-if="testimonial.verified" class="verified-badge" title="Usuario verificado">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                  </div>
                </div>
                <div class="author-details">
                  <h3 class="author-name">{{ testimonial.nombre }}</h3>
                  <p class="author-position">{{ testimonial.position }}</p>
                  <div v-if="testimonial.company" class="author-company">
                    <span>{{ testimonial.company }}</span>
                  </div>
                </div>
              </div>
              
              <div v-if="testimonial.tags && testimonial.tags.length > 0" class="testimonial-tags">
                <span v-for="(tag, tagIndex) in testimonial.tags" :key="tagIndex" class="tag">
                  {{ tag }}
                </span>
              </div>
            </div>
          </div>
        </SwiperSlide>
      </Swiper>
    </div>
    
    <div class="testimonials-footer">
      <div class="trust-metrics">
        <div class="metric">
          <div class="metric-number">4.9</div>
          <div class="metric-stars">★★★★★</div>
          <div class="metric-label">Valoración media</div>
        </div>
        <div class="metric">
          <div class="metric-number">2.5k+</div>
          <div class="metric-label">Clientes satisfechos</div>
        </div>
        <div class="metric">
          <div class="metric-number">96%</div>
          <div class="metric-label">Recomendación</div>
        </div>
      </div>
    </div>
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
import { ref, onMounted, onBeforeMount } from "vue";
import { Swiper, SwiperSlide } from 'swiper/vue';
import { useRouter } from "vue-router";
import { authStore } from "@/store/auth";
import axios from 'axios'; // Asegúrate de importar axios
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-coverflow';
import { Navigation, Pagination, EffectCoverflow } from 'swiper/modules';

// Inicializar el router y el store de autenticación
const router = useRouter();
const auth = authStore();

// Esto es para que si el usuario ya esta logeado lo rediriga a la pagina de app
onBeforeMount(() => {
  if (auth.authenticated) {
    router.push('/app');
  }
});

const isMonthly = ref(true);
const planes = ref([]); // Añadir esta línea

// Cargar precios de la API
const cargarPrecios = async () => {
  try {
    const response = await axios.get('/api/public/precios');
    planes.value = response.data;
  } catch (error) {
    console.error('Error al cargar los precios:', error);
  }
};

// Llamar a la función para cargar los precios
onMounted(() => {
  cargarPrecios();
});

const togglePlan = (monthly) => {
  isMonthly.value = monthly;
};

const llevarPaginaRegistro = () => {
  router.push('/register');
};

const getPlanPorCategoria = (categoria) => {
  return planes.value.find(plan => plan.categoria === categoria);
};

const testimonials = ref([
  {
    nombre: "Elena Martínez",
    position: "Directora de Proyectos",
    company: "TechFlow Solutions",
    foto: "/images/testimonials/elena.webp",
    texto: "KanFlow ha revolucionado la manera en que gestionamos los proyectos. Las automatizaciones nos permiten ahorrar horas cada semana en tareas repetitivas. La integración con otras herramientas es impecable.",
    rating: "5.0",
    verified: true,
    featured: true,
    tags: ["Automatización", "Productividad"]
  },
  {
    nombre: "Carlos Rodríguez",
    position: "Emprendedor",
    company: "GrowthHackers",
    foto: "/images/testimonials/carlos.webp",
    texto: "Después de probar decenas de herramientas, por fin encontré KanFlow. La combinación de gestión de tareas y automatizaciones es perfecta para mi startup. El ROI fue inmediato.",
    rating: "4.9",
    verified: true,
    tags: ["Startups", "Escalabilidad"]
  },
  {
    nombre: "Laura Gómez",
    position: "Desarrolladora Senior",
    company: "InnovaTech",
    foto: "/images/testimonials/laura.webp",
    texto: "Como desarrolladora, aprecio el enfoque limpio y eficiente de KanFlow. La API es robusta y la posibilidad de personalizar los flujos de trabajo hace que se adapte perfectamente a nuestro stack tecnológico.",
    rating: "5.0",
    verified: true,
    tags: ["Desarrollo", "Customización"]
  },
  {
    nombre: "Miguel Ángel",
    position: "Consultor de Marketing Digital",
    foto: "/images/testimonials/miguel.webp",
    texto: "KanFlow me permite gestionar todas las campañas de marketing de mis clientes en un solo lugar. Las automatizaciones con WhatsApp y email me han ahorrado horas de trabajo manual.",
    rating: "4.8",
    verified: true,
    tags: ["Marketing", "Comunicación"]
  },
  {
    nombre: "Sofía Herrera",
    position: "Gerente de Recursos Humanos",
    company: "Global Services Inc.",
    foto: "/images/testimonials/sofia.webp",
    texto: "Hemos implementado KanFlow en nuestro departamento de RRHH y la mejora en la eficiencia es notable. El seguimiento de procesos de selección y onboarding nunca había sido tan sencillo y efectivo.",
    rating: "4.9",
    verified: true,
    featured: true,
    tags: ["RRHH", "Onboarding"]
  },
  {
    nombre: "Javier Torres",
    position: "Autónomo",
    foto: "/images/testimonials/javier.webp",
    texto: "Como freelancer, necesitaba una herramienta que me ayudara a organizar mis proyectos sin perder tiempo en la gestión. KanFlow es exactamente lo que buscaba. La relación calidad-precio es insuperable.",
    rating: "5.0",
    verified: true,
    tags: ["Freelance", "Gestión de tiempo"]
  }
]);


const infoSection = ref(null);
const pricingSection = ref(null);
const aboutSection = ref(null);
const toggleMobileMenu = () => {
  const nav = document.querySelector('.main-nav');
  nav.classList.toggle('active');
};
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
/*******************************
 * ESTILOS GLOBALES
 *******************************/
:root {
  --primary-color: #3f359b;
  --primary-gradient: linear-gradient(135deg, #3f359b, #6665dd);
  --accent-color: #4a6cf7;
  --dark-bg: #0d0b21;
  --dark-panel: #1c1440;
  --text-light: #e4e6eb;
  --text-secondary: #64748b;
  --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  --hover-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

/*******************************
 * ESTILOS HERO SECTION
 *******************************/
 .hero {
  position: relative;
  min-height: 100vh;
  overflow: hidden;
  color: white;
  font-family: "Figtree", sans-serif;
  display: flex;
  flex-direction: column;
}

/* Video de fondo y overlay */
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

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
}

.gradient-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, rgba(13, 11, 33, 0.9) 0%, rgba(19, 16, 51, 0.8) 100%);
  z-index: -1;
}

.particles-container {
  position: absolute;
  width: 100%;
  height: 100%;
  z-index: -1;
}

/* Header y navegación */
.main-header {
  width: 100%;
  padding: 25px 0;
  position: relative;
  z-index: 100;
}

.container {
  max-width: 1300px;
  margin: 0 auto;
  padding: 0 20px;
  width: 100%;
}

.main-header .container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  display: flex;
  align-items: center;
}

.logo img {
  height: 50px;
}

.main-nav {
  display: flex;
  align-items: center;
  gap: 30px;
}

.nav-links {
  display: flex;
  list-style: none;
  gap: 30px;
  margin: 0;
  padding: 0;
}

.nav-links a {
  color: rgba(255, 255, 255, 0.85);
  text-decoration: none;
  font-size: 1rem;
  font-weight: 500;
  transition: color 0.3s;
  position: relative;
}

.nav-links a:hover {
  color: white;
}

.nav-links a::after {
  content: '';
  position: absolute;
  width: 100%;
  height: 2px;
  bottom: -5px;
  left: 0;
  background-color: #6665dd;
  transform: scaleX(0);
  transform-origin: bottom right;
  transition: transform 0.3s ease;
}

.nav-links a:hover::after {
  transform-origin: bottom left;
  transform: scaleX(1);
}

.nav-buttons {
  display: flex;
  gap: 15px;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  cursor: pointer;
  text-decoration: none;
}

.btn-outline {
  background: transparent;
  color: white;
  border: 1.5px solid rgba(255, 255, 255, 0.3);
}

.btn-outline:hover {
  border-color: white;
  background: rgba(255, 255, 255, 0.1);
}

.btn-primary {
  background: linear-gradient(90deg, #3f359b 0%, #6665dd 100%);
  color: white;
  border: none;
  box-shadow: 0 5px 15px rgba(102, 101, 221, 0.3);
}

.btn-primary:hover {
  box-shadow: 0 8px 25px rgba(102, 101, 221, 0.5);
  transform: translateY(-2px);
}

.btn-large {
  padding: 15px 30px;
  font-size: 1.05rem;
}

.btn-text {
  background: transparent;
  color: white;
  padding: 10px;
}

.btn-text:hover {
  background: rgba(255, 255, 255, 0.1);
}

.btn-with-icon {
  display: inline-flex;
  align-items: center;
  gap: 10px;
}

.mobile-menu-toggle {
  display: none;
  background: transparent;
  border: none;
  width: 40px;
  height: 40px;
  position: relative;
  cursor: pointer;
}

.mobile-menu-toggle span {
  display: block;
  width: 30px;
  height: 2px;
  background-color: white;
  margin: 6px auto;
  transition: all 0.3s ease-in-out;
}

/* Contenido principal del Hero */
.hero-content {
  flex-grow: 1;
  display: flex;
  align-items: center;
  padding: 40px 0;
  margin-top: 40px;
}

.hero-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
}

.hero-text {
  max-width: 580px;
}

.hero-tagline {
  font-size: 1.1rem;
  font-weight: 500;
  margin: 0 0 20px;
  display: flex;
  align-items: center;
  color: rgba(255, 255, 255, 0.9);
}

.badge {
  display: inline-block;
  background: linear-gradient(90deg, #3f359b 0%, #6665dd 100%);
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  margin-right: 12px;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.hero-headline {
  font-size: 3.5rem;
  font-weight: 800;
  line-height: 1.2;
  margin: 0 0 20px;
  letter-spacing: -0.5px;
}

.hero-headline-highlight {
  background: linear-gradient(90deg, #4a6cf7, #9c7af6);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  display: block;
}

.hero-description {
  font-size: 1.2rem;
  line-height: 1.6;
  color: rgba(255, 255, 255, 0.8);
  margin: 0 0 30px;
}

.hero-cta {
  display: flex;
  gap: 20px;
  align-items: center;
  margin-bottom: 40px;
}

.btn svg {
  width: 16px;
  height: 16px;
  margin-left: 5px;
}

.play-icon {
  display: flex;
  width: 40px;
  height: 40px;
}

.play-icon svg {
  width: 100%;
  height: 100%;
}

/* Stats sección */
.hero-stats {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  padding: 20px;
  backdrop-filter: blur(5px);
}

.stat-item {
  padding: 0 20px;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 5px;
}

.rating-stars {
  color: #FFD700;
  font-size: 1.1rem;
  margin-bottom: 5px;
}

.stat-item p {
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.9rem;
  margin: 0;
}

.divider {
  width: 1px;
  align-self: stretch;
  background: rgba(255, 255, 255, 0.2);
}

/* Imagen de hero con elementos flotantes */
.hero-image {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dashboard-preview {
  max-width: 100%;
  height: auto;
  border-radius: 20px;
  box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.floating-element {
  position: absolute;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 12px;
  padding: 15px;
  display: flex;
  align-items: center;
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
  color: #1A1A1A;
  animation: float 6s ease-in-out infinite;
}

.floating-element.element-1 {
  top: 15%;
  left: -70px;
  animation-delay: 0s;
}

.floating-element.element-2 {
  bottom: 20%;
  right: -70px;
  animation-delay: 3s;
}

.element-icon {
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
}

.element-icon svg {
  width: 24px;
  height: 24px;
  color: #3f359b;
}

.element-text {
  font-weight: 600;
  font-size: 0.9rem;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-15px);
  }
}

/* Sección trusted-by */
.trusted-by {
  position: relative;
  padding: 30px 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.4), transparent);
  margin-top: auto;
}

.trusted-by-text {
  text-align: center;
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 20px;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 500;
}

.trusted-by-logos {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 60px;
  flex-wrap: wrap;
}

.trusted-by-logos img {
  height: 30px;
  opacity: 0.7;
  filter: grayscale(100%) brightness(200%);
  transition: all 0.3s ease;
}

.trusted-by-logos img:hover {
  opacity: 1;
  filter: grayscale(0%) brightness(100%);
  transform: translateY(-3px);
}

/*******************************
 * ESTILOS INFO SECTION
 *******************************/
 .info-section {
  position: relative;
  padding-top: 100px;
  background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  overflow: hidden;
  font-family: "Figtree", sans-serif;
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.7s ease, transform 0.7s ease;
}

.info-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(90deg, #3f359b 0%, #6665dd 100%);
}

.info-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Header de la sección */
.info-header {
  text-align: center;
  margin-bottom: 80px;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.info-badge {
  display: inline-block;
  background: linear-gradient(90deg, #3f359b 0%, #6665dd 100%);
  color: white;
  padding: 8px 20px;
  border-radius: 50px;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 20px;
  text-transform: uppercase;
  letter-spacing: 1.2px;
}

.info-header h2 {
  font-size: 2.8rem;
  font-weight: 800;
  margin-bottom: 20px;
  color: #1A1A1A;
  line-height: 1.2;
}

.info-subtitle {
  font-size: 1.2rem;
  color: #64748b;
  margin: 0 auto;
  line-height: 1.6;
}

/* Tarjetas de características */
.info-features {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
  margin-bottom: 100px;
}

.feature-card {
  background: white;
  border-radius: 16px;
  padding: 40px 30px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  position: relative;
  z-index: 1;
  overflow: hidden;
}

.feature-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.4s ease;
  z-index: 1;
}

.feature-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.feature-card:hover::before {
  transform: scaleX(1);
}

.feature-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 70px;
  height: 70px;
  border-radius: 50%;
  background: rgba(102, 101, 221, 0.1);
  margin-bottom: 25px;
  transition: all 0.3s ease;
}


.feature-icon svg {
  width: 32px;
  height: 32px;
  color: #6665dd;
  transition: all 0.3s ease;
}

.feature-card h3 {
  font-size: 1.3rem;
  font-weight: 700;
  margin-bottom: 15px;
  color: #1A1A1A;
}

.feature-card p {
  font-size: 1rem;
  color: #64748b;
  line-height: 1.6;
}

/* Sección de estadísticas */
.info-stats-section {
  position: relative;
  overflow: hidden;
  width: 100vw;
  margin-left: calc(-50vw + 50%);
  margin-right: calc(-50vw + 50%);
  padding: 80px 0;
  margin-top: 80px;
  position: relative;
  background-color: #131033;
}

.info-stats-section::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(102, 101, 221, 0.3) 0%, rgba(102, 101, 221, 0) 70%);
  border-radius: 50%;
  transform: translate(30%, -30%);
}

.stats-header {
  text-align: center;
  margin-bottom: 50px;
}

.stats-header h3 {
  font-size: 2rem;
  font-weight: 700;
  color: white;
  margin-bottom: 15px;
}

.stats-header p {
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.7);
  max-width: 600px;
  margin: 0 auto;
}

.stats-container {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
}

.stat-card {
  background: rgba(255, 255, 255, 0.04);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 35px 25px;
  text-align: center;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.stat-card:hover {
  transform: translateY(-3px);
  border-color: rgba(255, 255, 255, 0.2);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.stat-card.highlight {
  background: linear-gradient(135deg, rgba(63, 53, 155, 0.4), rgba(102, 101, 221, 0.4));
  border: 1px solid rgba(102, 101, 221, 0.5);
  transform: scale(1.05);
  z-index: 2;
}

.stat-card.highlight:hover {
  transform: translateY(-5px) scale(1.05);
}

.stat-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: rgba(102, 101, 221, 0.1);
  margin: 0 auto 20px;
}

.stat-icon svg {
  width: 30px;
  height: 30px;
  color: #6665dd;
}

.stat-count {
  font-size: 3.5rem;
  font-weight: 800;
  color: white;
  line-height: 1;
  margin-bottom: 10px;
}

.stat-count span {
  font-size: 2rem;
  font-weight: 600;
}

.stat-label {
  display: block;
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 10px;
}

.stat-trend {
  display: block;
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.5);
}

.stat-trend.positive {
  color: #4caf50;
}

.quote-icon {
  width: 60px;
  height: 60px;
  color: rgba(102, 101, 221, 0.1);
  margin-bottom: 20px;
}

.testimonial-quote blockquote {
  font-size: 1.4rem;
  font-weight: 600;
  line-height: 1.6;
  color: #1A1A1A;
  margin-bottom: 30px;
  font-style: italic;
  position: relative;
  z-index: 1;
}


.author-image {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
  margin-right: 15px;
}

.author-info {
  display: flex;
  flex-direction: column;
}

.author-name {
  font-weight: 700;
  color: #1A1A1A;
  font-size: 1.1rem;
}

.author-position {
  color: #64748b;
  font-size: 0.9rem;
}

/* CTA */

.cta-buttons {
  display: flex;
  justify-content: center;
  gap: 20px;
}

.cta-button {
  padding: 15px 30px;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
}

.cta-button.primary {
  background: linear-gradient(90deg, #3f359b 0%, #6665dd 100%);
  color: white;
  border: none;
  box-shadow: 0 10px 20px rgba(102, 101, 221, 0.3);
}

.cta-button.primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 25px rgba(102, 101, 221, 0.4);
}

.cta-button.secondary {
  background: transparent;
  color: #3f359b;
  border: 2px solid #3f359b;
}

.cta-button.secondary:hover {
  background: rgba(63, 53, 155, 0.05);
  transform: translateY(-3px);
}
.info-stats-section .stats-header,
.info-stats-section .stats-container {
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
  padding: 0 20px;
}

/* Animaciones para los números de estadísticas */
@keyframes countUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in-up {
  opacity: 1;
  transform: translateY(0);
}

/*******************************
 * ESTILOS PRICING SECTION
 *******************************/
 .pricing-section {
  display: flex;
  justify-content: center;
  min-height: 100vh;
  background-color: #131033;
  padding: 60px 20px;
  font-family: "Figtree", sans-serif;
  color: white;

}

.pricing-container {
  text-align: center;
  max-width: 1200px;
  width: 100%;
}

/* Header de precios */
.pricing-header {
  margin-bottom: 60px;
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
}

.pricing-badge {
  display: inline-block;
  background: linear-gradient(90deg, #3f359b 0%, #6665dd 100%);
  color: white;
  padding: 8px 20px;
  border-radius: 50px;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 15px;
  text-transform: uppercase;
  letter-spacing: 1.2px;
}

.pricing-header h2 {
  font-size: 2.8rem;
  font-weight: 800;
  margin-bottom: 15px;
  background: linear-gradient(90deg, #ffffff, #c3c2ff);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.pricing-subtitle {
  font-size: 1.2rem;
  color: rgba(255, 255, 255, 0.7);
  margin: 0 auto;
}

/* Toggle de facturación */
.billing-toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 40px 0 60px;
  gap: 15px;
  font-weight: 500;
}

.toggle-switch {
  position: relative;
  width: 56px;
  height: 30px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.toggle-knob {
  position: absolute;
  left: 4px;
  top: 4px;
  width: 22px;
  height: 22px;
  background: white;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.toggle-switch.toggled .toggle-knob {
  left: 30px;
  background: #6665dd;
}

.toggle-switch:hover {
  background: rgba(255, 255, 255, 0.3);
}

.active-billing {
  color: white;
  font-weight: 600;
}

.annual-option {
  display: flex;
  align-items: center;
  position: relative;
}

.discount-badge {
  position: absolute;
  top: -20px;
  right: -15px;
  background: #4caf50;
  color: white;
  font-size: 0.7rem;
  padding: 3px 8px;
  border-radius: 20px;
  font-weight: 700;
}

/* Contenedor de tarjetas de precios */
.pricing-cards {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-bottom: 60px;
  flex-wrap: wrap;
}

.pricing-card {
  background: rgba(255, 255, 255, 0.04);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 40px 25px;
  width: 100%;
  max-width: 350px;
  text-align: left;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}

.pricing-card:hover {
  transform: translateY(-10px);
  border-color: rgba(255, 255, 255, 0.2);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

/* Plan destacado */
.pricing-card.featured {
  background: linear-gradient(135deg, rgba(63, 53, 155, 0.4), rgba(102, 101, 221, 0.4));
  border: 1px solid rgba(102, 101, 221, 0.5);
  transform: scale(1.05);
  z-index: 2;
  box-shadow: 0 15px 50px rgba(63, 53, 155, 0.5);
}

.pricing-card.featured:hover {
  transform: scale(1.05) translateY(-10px);
  box-shadow: 0 20px 60px rgba(63, 53, 155, 0.6);
}

.featured-badge {
  position: absolute;
  top: 12px;
  right: -35px;
  background: linear-gradient(90deg, #3f359b 0%, #6665dd 100%);
  color: white;
  padding: 8px 40px;
  font-size: 0.8rem;
  font-weight: 600;
  transform: rotate(45deg);
}

/* Encabezado de tarjeta */
.card-header {
  padding-bottom: 30px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  margin-bottom: 30px;
}

.plan-name {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 15px;
}

.plan-price {
  display: flex;
  align-items: baseline;
  margin-bottom: 15px;
}

.price-amount {
  font-size: 3.5rem;
  font-weight: 800;
  line-height: 1;
}

.price-amount sup {
  font-size: 1.4rem;
  font-weight: 600;
  margin-left: 2px;
}

.price-period {
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.6);
  margin-left: 5px;
}

.plan-description {
  font-size: 0.95rem;
  color: rgba(255, 255, 255, 0.6);
  line-height: 1.4;
}

/* Características del plan */
.card-features {
  flex-grow: 1;
  margin-bottom: 30px;
}

.card-features ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.card-features li {
  display: flex;
  align-items: center;
  padding: 12px 0;
  font-size: 0.95rem;
  color: rgba(255, 255, 255, 0.9);
}

.check-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  margin-right: 12px;
  border-radius: 50%;
  background: rgba(102, 101, 221, 0.2);
  color: #6665dd;
  flex-shrink: 0;
}

.card-features li strong {
  font-weight: 600;
}

/* Acción de tarjeta */
.card-action {
  text-align: center;
}

.plan-button {
  width: 100%;
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  padding: 15px 20px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.plan-button:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
}

.featured-button {
  background: linear-gradient(90deg, #3f359b 0%, #6665dd 100%);
  border: none;
  box-shadow: 0 10px 25px -10px rgba(102, 101, 221, 0.5);
}

.featured-button:hover {
  background: linear-gradient(90deg, #352c85 0%, #5554c9 100%);
  box-shadow: 0 15px 25px -10px rgba(102, 101, 221, 0.7);
}

.guarantee {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.5);
  margin-top: 15px;
}

/* Elementos de confianza */
.pricing-trust {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 30px;
  margin: 40px 0 60px;
  color: rgba(255, 255, 255, 0.7);
}

.trust-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.95rem;
}

.trust-item svg {
  color: #6665dd;
}

/* FAQ Section */
.pricing-faq {
  max-width: 800px;
  margin: 0 auto;
  text-align: left;
}

.pricing-faq h3 {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 30px;
  text-align: center;
}

.faq-container {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 30px;
}

.faq-item h4 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 10px;
  color: rgba(255, 255, 255, 0.9);
}

.faq-item p {
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.95rem;
  line-height: 1.6;
}

/*******************************
 * ESTILOS SOBRE NOSOTROS SECTION
 *******************************/
 .sobre-nosotros-container {
  position: relative;
  background: linear-gradient(to bottom, #ffffff 0%, #f5f7fa 100%);
  padding: 80px 40px;
  text-align: center;
  min-height: 600px;
}

.sobre-nosotros-content {
  max-width: 1200px;
  margin: 0 auto;
  font-family: "Figtree", sans-serif;
}

.sobre-nosotros-title {
  font-size: 3rem;
  font-weight: 800;
  margin-bottom: 10px;
  color: #1A1A1A;
  position: relative;
  display: inline-block;
}

.sobre-nosotros-title::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 4px;
  background: #3f359b;
  border-radius: 2px;
}

.sobre-nosotros-subtitle {
  font-size: 1.3rem;
  color: #666;
  margin-bottom: 60px;
}

/* Tarjetas de misión */
.sobre-nosotros-cards {
  display: flex;
  justify-content: center;
  gap: 30px;
  margin-bottom: 80px;
  flex-wrap: wrap;
}

.mission-card {
  background: white;
  border-radius: 12px;
  padding: 40px 30px;
  text-align: center;
  flex: 1;
  min-width: 280px;
  max-width: 350px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.mission-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.mission-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #3f359b, #6665dd);
  border-radius: 50%;
  margin: 0 auto 25px;
  font-size: 36px; /* Tamaño del emoji */
}

.company-story {
  display: flex;
  align-items: center;
  gap: 50px;
  margin-bottom: 80px;
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  text-align: left;
}

.story-content {
  flex: 1;
  padding: 50px;
}

.story-image-container {
  flex: 1;
  height: 400px;
  overflow: hidden;
}

.story-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 1s ease;
}

.company-story:hover .story-image {
  transform: scale(1.05);
}

.story-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 20px;
  color: #1A1A1A;
  position: relative;
  padding-bottom: 15px;
}

.story-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 4px;
  background: #3f359b;
  border-radius: 2px;
}

.story-text {
  font-size: 1.05rem;
  line-height: 1.8;
  color: #444;
  margin-bottom: 20px;
}

/* Valores del equipo */
.team-values {
  text-align: center;
  margin-top: 60px;
}

.values-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 40px;
  color: #1A1A1A;
  position: relative;
  display: inline-block;
}

.values-title::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 40px;
  height: 3px;
  background: #3f359b;
  border-radius: 2px;
}

.values-container {
  display: flex;
  justify-content: center;
  gap: 30px;
  flex-wrap: wrap;
}

.value-item {
  flex: 1;
  min-width: 200px;
  max-width: 300px;
  padding: 20px;
}

.value-badge {
  display: inline-block;
  padding: 8px 20px;
  background: linear-gradient(135deg, #3f359b, #6665dd);
  color: white;
  font-weight: 600;
  border-radius: 30px;
  font-size: 1rem;
  margin-bottom: 15px;
}

.value-item p {
  color: #666;
  line-height: 1.6;
}


/*******************************
 * ESTILOS TESTIMONIOS SECTION
 *******************************/
 .testimonials-section {
  background: linear-gradient(135deg, #0e0d26 0%, #1c1a47 100%);
  padding: 100px 0;
  position: relative;
  overflow: hidden;
  font-family: "Figtree", sans-serif;
}

.testimonials-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent 0%, rgba(102, 101, 221, 0.5) 50%, transparent 100%);
}

.testimonials-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.testimonials-header {
  text-align: center;
  margin-bottom: 60px;
}

.testimonials-badge {
  display: inline-block;
  background: linear-gradient(90deg, #3f359b 0%, #6665dd 100%);
  color: white;
  padding: 8px 20px;
  border-radius: 50px;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 20px;
  text-transform: uppercase;
  letter-spacing: 1.2px;
  box-shadow: 0 4px 15px rgba(102, 101, 221, 0.3);
}

.testimonials-title {
  font-size: 2.8rem;
  font-weight: 800;
  margin-bottom: 20px;
  color: white;
  line-height: 1.2;
}

.testimonials-subtitle {
  font-size: 1.2rem;
  color: rgba(255, 255, 255, 0.7);
  max-width: 650px;
  margin: 0 auto;
}

.testimonials-slider-wrapper {
  position: relative;
  margin-bottom: 60px;
}

.testimonials-slider {
  overflow: visible;
  padding: 30px 0 60px;
}

/* Estilo de las tarjetas de testimonios */
.testimonial-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 0;
  height: auto;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  overflow: hidden;
  max-width: 500px;
  margin: 0 auto;
  backdrop-filter: blur(10px);
}

.testimonial-card.featured {
  background: linear-gradient(135deg, rgba(63, 53, 155, 0.2), rgba(102, 101, 221, 0.2));
  border: 1px solid rgba(102, 101, 221, 0.3);
  position: relative;
}

.testimonial-card.featured::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #3f359b 0%, #6665dd 100%);
}

.testimonial-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
}

.testimonial-card-inner {
  padding: 30px;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.testimonial-rating {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.rating-stars {
  color: #ffcc00;
  font-size: 1.2rem;
  letter-spacing: 2px;
}

.rating-score {
  color: #ffcc00;
  font-weight: 700;
  font-size: 1.1rem;
}

.testimonial-text {
  color: rgba(255, 255, 255, 0.95);
  font-size: 1.1rem;
  line-height: 1.7;
  margin: 0 0 25px;
  flex-grow: 1;
  position: relative;
  font-style: italic;
  quotes: "❝" "❞";
}

.testimonial-text::before {
  content: open-quote;
  font-size: 3em;
  line-height: 0.1em;
  position: absolute;
  top: -10px;
  left: -10px;
  color: rgba(102, 101, 221, 0.3);
}

.testimonial-author {
  display: flex;
  align-items: center;
  padding-top: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.author-image-container {
  position: relative;
  margin-right: 15px;
}

.author-image {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid rgba(255, 255, 255, 0.2);
}

.verified-badge {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 22px;
  height: 22px;
  background: #6665dd;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #0e0d26;
}

.verified-badge svg {
  width: 14px;
  height: 14px;
  color: white;
}

.author-details {
  flex-grow: 1;
}

.author-name {
  color: white;
  font-weight: 600;
  font-size: 1.1rem;
  margin: 0 0 3px;
}

.author-position {
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.9rem;
  margin: 0;
}

.author-company {
  display: inline-block;
  background: rgba(255, 255, 255, 0.1);
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 0.8rem;
  margin-top: 6px;
  color: rgba(255, 255, 255, 0.8);
}

.testimonial-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 20px;
}

.tag {
  background: rgba(102, 101, 221, 0.2);
  color: rgba(255, 255, 255, 0.8);
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
}

/* Personalizando los controles de Swiper */
:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
  color: #6665dd;
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

:deep(.swiper-button-next::after),
:deep(.swiper-button-prev::after) {
  font-size: 20px;
}

:deep(.swiper-pagination-bullet) {
  background: rgba(255, 255, 255, 0.5);
}

:deep(.swiper-pagination-bullet-active) {
  background: #6665dd;
}

:deep(.swiper-slide) {
  opacity: 0.5;
  transform: scale(0.85);
  transition: all 0.4s ease;
}

:deep(.swiper-slide-active) {
  opacity: 1;
  transform: scale(1);
}

/* Métricas de confianza */
.testimonials-footer {
  text-align: center;
  margin-top: 40px;
  padding-top: 40px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.trust-metrics {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 60px;
}

.metric {
  text-align: center;
}

.metric-number {
  font-size: 3rem;
  font-weight: 700;
  color: white;
  line-height: 1;
  margin-bottom: 5px;
  background: linear-gradient(90deg, #ffffff, #a1a0ff);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.metric-stars {
  color: #ffcc00;
  font-size: 1.4rem;
  margin-bottom: 10px;
}

.metric-label {
  color: rgba(255, 255, 255, 0.7);
  font-size: 1rem;
}

/* Estilos para los controles de Swiper */
.swiper-button-next,
.swiper-button-prev {
  color: white;
  top: 50%;
  transform: translateY(-50%);
}

:deep(.swiper-button-next:focus),
:deep(.swiper-button-prev:focus),
:deep(.swiper-pagination-bullet:focus) {
  outline: none !important;
  box-shadow: none !important;
}

:deep(.swiper-pagination-bullet) {
  bottom: 20px !important; 
  background-color: #AFAFAF; 
}

:deep(.swiper-pagination-bullet-active) {
  background-color: #3770FF;
}

/*******************************
 * ESTILOS FOOTER
 *******************************/
.footer-container {
  background-color: black;
  color: white;
  padding: 20px;
  font-family: "Figtree", serif;
}

.seccion1-footer {
  display: flex;
  flex-direction: row;
  align-items: center; 
  justify-content: flex-start; 
  gap: 80px;
}

.seccion1-footer img {
  max-width: 250px;
  width: 100%;
}

.seccion1-footer ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.ul-1, .ul-2 {
  display: flex;
  flex-direction: column;
}

.ul-1 li, .ul-2 li {
  margin-bottom: 10px; 
  font-size: 1.2rem;
  font-weight: 300;
}

.li-1, .li-2 {
  font-size: 1.5rem !important; 
  margin-bottom: 20px !important; 
  font-weight: 700 !important;
}

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

/*******************************
 * ANIMACIÓN FADE-IN
 *******************************/
.fade-in-up {
  opacity: 1;
  transform: translateY(0);
}

/*******************************
 * MEDIA QUERIES
 *******************************/
 @media (max-width: 1200px) {
  .hero-headline {
    font-size: 3rem;
  }
  
  .floating-element.element-1 {
    left: -20px;
  }
  
  .floating-element.element-2 {
    right: -20px;
  }
}
@media (max-width: 1024px) {
  /* Pricing section responsive */
  .pricing-section {
    padding: 60px 20px;
  }
  
  .pricing-card.featured {
    transform: scale(1.03);
  }
  
  .pricing-card.featured:hover {
    transform: scale(1.03) translateY(-10px);
  }
  
  /* Sobre nosotros responsive */
  .company-story {
    flex-direction: column;
  }
  
  .story-content {
    padding: 40px;
  }
  
  .story-image-container {
    width: 100%;
    height: 300px;
  }
  /*Info section responsive*/
  .info-section {
    padding: 0px 20px;
  }
  
  .info-header h2 {
    font-size: 2.4rem;
  }
  
  .info-features {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .info-stats-section {
    padding: 50px 30px;
  }
  
  .stats-container {
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
  }
  
  .stat-count {
    font-size: 3rem;
  }

}
@media (max-width: 991px) {
  .testimonials-section {
    padding: 80px 0;
  }
  
  .testimonials-title {
    font-size: 2.4rem;
  }
}
@media (max-width: 768px) {
  /* Hero responsive */
  .main-nav {
    display: none;
  }
  
  .mobile-menu-toggle {
    display: block;
  }
  
  /* Mostrar menú móvil cuando tiene clase active */
  .main-nav.active {
    display: flex;
    flex-direction: column;
    position: absolute;
    top: 80px;
    left: 0;
    width: 100%;
    background: linear-gradient(135deg, rgba(13, 11, 33, 0.95) 0%, rgba(19, 16, 51, 0.95) 100%);
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    backdrop-filter: blur(10px);
  }
  
  .main-nav.active .nav-links {
    flex-direction: column;
    gap: 15px;
    margin-bottom: 20px;
    width: 100%;
  }
  
  .main-nav.active .nav-buttons {
    flex-direction: column;
    width: 100%;
  }
  
  .main-nav.active .btn {
    width: 100%;
  }
  
  .hero-headline {
    font-size: 2.5rem;
  }
  
  .hero-description {
    font-size: 1.1rem;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 20px;
  }
  
  .divider {
    width: 80%;
    height: 1px;
  }
  
  .trusted-by-logos img {
    height: 25px;
  }

  /* Info section responsive */
  .info-section {
    padding: 0px 20px;
  }
  
  .info-header h2 {
    font-size: 2rem;
  }
  
  .info-subtitle {
    font-size: 1.1rem;
  }
  
  .info-features {
    grid-template-columns: 1fr;
  }
  
  .stats-container {
    grid-template-columns: 1fr;
    gap: 30px;
  }
  
  .stat-card.highlight {
    transform: none;
    order: -1;
  }
  
  .stat-card.highlight:hover {
    transform: translateY(-5px);
  }
  
  
  .testimonial-quote blockquote {
    font-size: 1.2rem;
  }
  
  
  .cta-buttons {
    flex-direction: column;
    align-items: center;
    gap: 15px;
  }
  
  .cta-button {
    width: 100%;
    max-width: 320px;
    text-align: center;
  }

  /* Pricing section responsive */
  .pricing-header h2 {
    font-size: 2.2rem;
  }
  
  .pricing-cards {
    flex-direction: column;
    align-items: center;
    gap: 40px;
  }
  
  .pricing-card,
  .pricing-card.featured {
    max-width: 100%;
    width: 100%;
    transform: none;
  }
  
  .pricing-card.featured {
    order: -1; /* Mueve el plan destacado al principio en móvil */
  }
  
  .faq-container {
    grid-template-columns: 1fr;
  }
  
  .trust-item {
    width: 100%;
    justify-content: center;
  }

  /* Sobre nosotros responsive */
  .sobre-nosotros-container {
    padding: 60px 20px;
  }
  
  .sobre-nosotros-title {
    font-size: 2.5rem;
  }
  
  .mission-card {
    min-width: 100%;
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

  /* Testimonios responsive */
  .testimonials-section {
    padding: 60px 0;
  }
  
  .testimonials-title {
    font-size: 2rem;
  }
  
  .testimonials-subtitle {
    font-size: 1rem;
  }
  
  .testimonial-card {
    max-width: 100%;
  }
  
  .trust-metrics {
    gap: 30px;
  }
  
  .metric-number {
    font-size: 2.5rem;
  }
  
  :deep(.swiper-button-next),
  :deep(.swiper-button-prev) {
    display: none;
  }
  
  /* Footer responsive */
  .seccion1-footer {
    flex-direction: column;
    align-items: flex-start;
  }

  .seccion1-footer img {
    margin-bottom: 20px; 
  }
}

@media (max-width: 480px) {
  /* Hero responsive */
  .main-header {
    padding: 15px 0;
  }
  
  .logo img {
    height: 32px;
  }
  
  .hero-content {
    padding: 20px 0;
  }
  .hero-tagline{
    display: none;
  }
  
  .hero-headline {
    font-size: 2rem;
  }
  
  .hero-description {
    font-size: 1rem;
  }
  
  .hero-cta {
    flex-direction: column;
    gap: 15px;
    width: 100%;
  }
  
  .hero-cta .btn {
    width: 100%;
  }
  
  .trusted-by-logos {
    gap: 20px;
  }
  
  .trusted-by-logos img {
    height: 20px;
  }

  /* Info section responsive */
  .info-section {
    padding: 0px 15px;
  }
  
  .info-header {
    margin-bottom: 50px;
  }
  
  .info-header h2 {
    font-size: 1.8rem;
    margin-bottom: 15px;
  }
  
  .info-subtitle {
    font-size: 1rem;
  }
  
  .feature-card {
    padding: 30px 20px;
  }
  
  
  .stat-card {
    padding: 25px 15px;
  }
  
  .stat-count {
    font-size: 2.5rem;
  }
  
  .stat-count span {
    font-size: 1.5rem;
  }
  

  /* Pricing section responsive */
  .pricing-section {
    padding: 40px 15px;
  }
  
  .pricing-header h2 {
    font-size: 1.8rem;
  }
  
  .discount-badge {
    font-size: 0.6rem;
    top: -15px;
    right: -10px;
  }
  
  .billing-toggle {
    flex-direction: column;
    gap: 20px;
  }
  
  .annual-option {
    margin-top: 10px;
  }
  
  .discount-badge {
    position: static;
    margin-left: 10px;
  }
  
  .price-amount {
    font-size: 2.8rem;
  }
  
  .card-features li {
    font-size: 0.9rem;
  }
  
  .pricing-trust {
    flex-direction: column;
    align-items: center;
    gap: 20px;
  }

  /* Sobre nosotros responsive */
  .sobre-nosotros-container {
    padding: 30px 10px; 
    min-height: 300px; 
  }

  .sobre-nosotros-title {
    font-size: 2rem;
  }
  
  .sobre-nosotros-subtitle {
    font-size: 1.1rem;
    margin-bottom: 40px;
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

  /* Testimonios responsive */
  .testimonials-section {
    padding: 50px 0;
  }
  
  .testimonials-badge {
    font-size: 0.8rem;
    padding: 6px 15px;
  }
  
  .testimonials-title {
    font-size: 1.8rem;
  }
  
  .testimonial-card-inner {
    padding: 20px;
  }
  
  .testimonial-text {
    font-size: 1rem;
  }
  
  .author-image {
    width: 50px;
    height: 50px;
  }
  
  .metric {
    width: 100%;
    margin-bottom: 20px;
  }
  
  .trust-metrics {
    flex-direction: column;
    gap: 20px;
  }
  
  /* Logo responsive */
  .logo img {
    max-width: 100%;
  }
  
  /* Footer responsive */
  .seccion1-footer {
    flex-direction: column;
    align-items: flex-start;
    gap: 20px;
  }

  .seccion1-footer img {
    max-width: 250px;
    width: 100%;
    height: 150px;
    display: block;
    object-fit: cover; 
    margin: 0; 
  }
  
  .seccion1-footer ul {
    padding-left: 10px; 
  }
  
  .footer-container hr {
    margin-top: 10px;
  }
}
</style>
