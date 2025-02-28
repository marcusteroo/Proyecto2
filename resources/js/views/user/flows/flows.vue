<template>
    <div class="container">
        <h1>Automatizaciones</h1>

        <!-- Botón para agregar una nueva automatización -->
        <button class="add-button" @click="addFlow">
            ➕ Nueva Automatización
        </button>

        <!-- Contenedor de tarjetas -->
        <div class="flows-container">
            <div v-for="flow in flows" :key="flow.id_workflow" class="flow-card">
                <h2>{{ flow.nombre }}</h2>
                <p>{{ flow.descripcion }}</p>
                <div class="actions">
                    <button class="delete-btn" @click="deleteFlow(flow.id_workflow)">🗑️ Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            flows: []
        };
    },
    created() {
        this.fetchFlows();
    },
    methods: {
        async fetchFlows() {
            try {
                const response = await axios.get('/api/workflows');
                this.flows = response.data;
            } catch (error) {
                console.error('Error al obtener los workflows:', error);
            }
        },
        editFlow(id) {
            this.$router.push({ name: 'flows.edit', params: { id } });
        },
        async deleteFlow(id) {
            if (confirm("¿Seguro que quieres eliminar esta automatización?")) {
                try {
                    await axios.delete(`/api/workflows/${id}`);
                    this.fetchFlows();
                    alert(`Automatización con ID: ${id} eliminada`);
                } catch (error) {
                    console.error('Error al eliminar el workflow:', error);
                }
            }
        },
        addFlow() {
            this.$router.push({ name: 'user.flows' }); 
        }
    }
};
</script>

<style scoped>
/* Contenedor principal */
.container {
    margin: 20px;
    font-family: "Figtree", serif;
    text-align: center;
    max-width: 100%;
    width: 100%;
}

/* Título */
h1 {
    text-align: center;
    margin-bottom: 20px;
}

/* Botón de añadir nueva automatización */
.add-button {
    display: block;
    width: fit-content;
    margin: 0 auto 20px;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

.add-button:hover {
    background-color: #45a049;
}

/* Contenedor de tarjetas */
.flows-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

/* Tarjetas */
.flow-card {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
    width: 60%;
    margin: 0 auto;
}

.flow-card:hover {
    transform: translateY(-5px);
}

.flow-card h2 {
    margin: 0 0 10px;
    font-size: 18px;
}

.flow-card p {
    font-size: 14px;
    color: #666;
}

/* Botones de acción */
.actions {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.edit-btn, .delete-btn {
    padding: 8px 12px;
    border: none;
    cursor: pointer;
    border-radius: 4px;
    font-size: 14px;
    transition: 0.3s;
}

.edit-btn {
    background-color: #ffcc00;
    color: black;
}

.edit-btn:hover {
    background-color: #e6b800;
}

.delete-btn {
    background-color: #f44336;
    color: white;
}

.delete-btn:hover {
    background-color: #d32f2f;
}

/* Responsividad */
@media (max-width: 600px) {
    .flows-container {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 767px) {
    .container{
        margin: 0;
    }
    .flow-card {
        width: 100%;
    }
}
</style>
