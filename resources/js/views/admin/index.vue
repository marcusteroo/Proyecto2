<template>
  <div class="admin-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
      <h1>Panel de Administración</h1>
      <p>Bienvenido al panel de control. Gestione la configuración de su aplicación desde aquí.</p>
    </div>

    <!-- Stats Overview Section -->
    <div class="stats-section">
      <div class="grid">
        <div class="col-12 md:col-6 lg:col-3">
          <div class="stat-card users-stat">
            <div class="stat-icon">
              <i class="pi pi-users"></i>
            </div>
            <div class="stat-content">
              <h3>Usuarios</h3>
              <div class="stat-value">{{ stats.users }}</div>
            </div>
          </div>
        </div>

        <div class="col-12 md:col-6 lg:col-3">
          <div class="stat-card boards-stat">
            <div class="stat-icon">
              <i class="pi pi-th-large"></i>
            </div>
            <div class="stat-content">
              <h3>Tableros</h3>
              <div class="stat-value">{{ stats.boards }}</div>
            </div>
          </div>
        </div>

        <div class="col-12 md:col-6 lg:col-3">
          <div class="stat-card tasks-stat">
            <div class="stat-icon">
              <i class="pi pi-check-square"></i>
            </div>
            <div class="stat-content">
              <h3>Tareas</h3>
              <div class="stat-value">{{ stats.tasks }}</div>
            </div>
          </div>
        </div>

        <div class="col-12 md:col-6 lg:col-3">
          <div class="stat-card ratings-stat">
            <div class="stat-icon">
              <i class="pi pi-star"></i>
            </div>
            <div class="stat-content">
              <h3>Reseñas</h3>
              <div class="stat-value">{{ stats.ratings }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Dashboard Navigation Cards -->
    <div class="navigation-cards">
      <h2>Gestión del Sistema</h2>
      <div class="grid justify-content-center">
        <!-- Users Management -->
        <div class="col-12 md:col-10 lg:col-6">
          <div class="admin-card">
            <div class="card-header">
              <i class="pi pi-users"></i>
              <h3>Gestión de Usuarios</h3>
            </div>
            <div class="card-content">
              <p>Administre usuarios, asigne roles y permisos, y controle el acceso al sistema.</p>
              <div class="card-actions">
                <router-link :to="{ name: 'users.index' }" class="p-button p-button-primary">
                  <i class="pi pi-user-edit mr-2"></i>Gestionar Usuarios
                </router-link>
                <router-link :to="{ name: 'roles.index' }" class="p-button p-button-outlined mt-2">
                  <i class="pi pi-shield mr-2"></i>Gestionar Roles
                </router-link>
                <router-link :to="{ name: 'permissions.index' }" class="p-button p-button-outlined mt-2">
                  <i class="pi pi-lock mr-2"></i>Gestionar Permisos
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Kanban Management -->
        <div class="col-12 md:col-10 lg:col-6">
          <div class="admin-card">
            <div class="card-header">
              <i class="pi pi-th-large"></i>
              <h3>Gestión de Tableros</h3>
            </div>
            <div class="card-content">
              <p>Administre tableros Kanban, tareas y flujos de trabajo.</p>
              <div class="card-actions">
                <router-link :to="{ name: 'kanbans.index' }" class="p-button p-button-primary">
                  <i class="pi pi-table mr-2"></i>Gestionar Tableros
                </router-link>
                <router-link :to="{ name: 'tareas.index' }" class="p-button p-button-outlined mt-2">
                  <i class="pi pi-check-square mr-2"></i>Gestionar Tareas
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Pricing Plans -->
        <div class="col-12 md:col-10 lg:col-6">
          <div class="admin-card">
            <div class="card-header">
              <i class="pi pi-money-bill"></i>
              <h3>Planes y Precios</h3>
            </div>
            <div class="card-content">
              <p>Configure y administre planes de suscripción y precios.</p>
              <div class="card-actions">
                <router-link :to="{ name: 'precios.index' }" class="p-button p-button-primary">
                  <i class="pi pi-tag mr-2"></i>Gestionar Precios
                </router-link>
              </div>
            </div>
          </div>
        </div>


        <!-- Reviews Management -->
        <div class="col-12 md:col-10 lg:col-6">
          <div class="admin-card">
            <div class="card-header">
              <i class="pi pi-star"></i>
              <h3>Gestión de Reseñas</h3>
            </div>
            <div class="card-content">
              <p>Administre las valoraciones y comentarios de los usuarios.</p>
              <div class="card-actions">
                <router-link :to="{ name: 'ratings.admin' }" class="p-button p-button-primary">
                  <i class="pi pi-star-fill mr-2"></i>Gestionar Reseñas
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const stats = ref({
  users: '...',
  boards: '...',
  tasks: '...',
  ratings: '...'
});

onMounted(async () => {
  try {
    // Fetch dashboard statistics
    await fetchDashboardStats();
  } catch (error) {
    console.error('Error loading dashboard data:', error);
  }
});

const fetchDashboardStats = async () => {
  try {
    // Obtener estadísticas reales desde la base de datos
    const response = await axios.get('/api/admin/stats');
    stats.value = response.data;
  } catch (error) {
    console.error('Error fetching statistics:', error);
    // Fallback a valores de ejemplo en caso de error
    stats.value = {
      users: '0',
      boards: '0',
      tasks: '0',
      ratings: '0'
    };
  }
};
</script>

<style scoped>
.admin-dashboard {
  padding: 1rem;
}

.dashboard-header {
  margin-bottom: 2rem;
  padding: 1rem;
  background: linear-gradient(135deg, #673ab7 0%, #4f2b95 100%);
  color: white;
  border-radius: 8px;
}

.dashboard-header h1 {
  font-size: 2rem;
  margin: 0 0 0.5rem 0;
}

.dashboard-header p {
  margin: 0;
  opacity: 0.9;
}

.stats-section {
  margin-bottom: 2.5rem;
}

.stat-card {
  display: flex;
  align-items: center;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  height: 100%;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.users-stat {
  background: linear-gradient(to right, #26c6da, #00acc1);
  color: white;
}

.boards-stat {
  background: linear-gradient(to right, #5c6bc0, #3949ab);
  color: white;
}

.tasks-stat {
  background: linear-gradient(to right, #66bb6a, #43a047);
  color: white;
}

.ratings-stat {
  background: linear-gradient(to right, #ffca28, #ffb300);
  color: white;
}

.stat-icon {
  font-size: 2.5rem;
  margin-right: 1rem;
  opacity: 0.85;
}

.stat-content h3 {
  margin: 0;
  font-size: 1rem;
  font-weight: 500;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 600;
}

.navigation-cards, .quick-actions-section {
  margin-bottom: 2.5rem;
}

.navigation-cards h2, .quick-actions-section h2 {
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
  color: #444;
  position: relative;
  padding-bottom: 10px;
}

.navigation-cards h2:after, .quick-actions-section h2:after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 3px;
  background-color: #673ab7;
}

.admin-card {
  height: 100%;
  border-radius: 8px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  background-color: white;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  overflow: hidden;
}

.admin-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
}

.card-header {
  padding: 1.5rem;
  display: flex;
  align-items: center;
  background-color: #f5f7fb;
}

.card-header i {
  font-size: 1.5rem;
  margin-right: 0.75rem;
  color: #673ab7;
}

.card-header h3 {
  margin: 0;
  font-size: 1.2rem;
  color: #3f3f3f;
  font-weight: 600;
}

.card-content {
  padding: 1.5rem;
}

.card-content p {
  color: #666;
  margin-bottom: 1.5rem;
  min-height: 3rem;
}

.card-actions {
  display: flex;
  flex-direction: column;
}

:deep(.p-button) {
  justify-content: center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .stat-card {
    margin-bottom: 1rem;
  }
  
  .admin-card {
    margin-bottom: 1.5rem;
  }
  
  .quick-actions-section button {
    margin-bottom: 1rem;
  }
}

/* Dark theme support */
:deep(.dark-theme) .admin-dashboard, 
:deep(html.dark-theme) .admin-dashboard, 
:deep(body.dark-theme) .admin-dashboard {
  background-color: #1e1e2d;
  color: #e0e0e0;
}

:deep(.dark-theme) .admin-card, 
:deep(html.dark-theme) .admin-card, 
:deep(body.dark-theme) .admin-card {
  background-color: #282836;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
}

:deep(.dark-theme) .card-header, 
:deep(html.dark-theme) .card-header, 
:deep(body.dark-theme) .card-header {
  background-color: #32324a;
}

:deep(.dark-theme) .card-content p,
:deep(html.dark-theme) .card-content p,
:deep(body.dark-theme) .card-content p {
  color: #b5bac1;
}

:deep(.dark-theme) .navigation-cards h2,
:deep(.dark-theme) .quick-actions-section h2,
:deep(html.dark-theme) .navigation-cards h2,
:deep(html.dark-theme) .quick-actions-section h2,
:deep(body.dark-theme) .navigation-cards h2,
:deep(body.dark-theme) .quick-actions-section h2 {
  color: #e0e0e0;
}
</style>
