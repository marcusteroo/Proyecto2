<template>
    <div class="container">
        <h1>Tableros</h1>

        <!-- Botón para agregar un nuevo tablero -->
        <button class="add-button" @click="addTablero">
            ➕ Nuevo Tablero
        </button>

        <!-- Contenedor de tarjetas -->
        <div class="tableros-container">
            <div v-for="tablero in tableros" :key="tablero.id_tablero" class="tablero-card">
                <h2>{{ tablero.nombre }}</h2>
                <!-- Puedes mostrar otro dato, por ejemplo el id del creador -->
                <p>ID Creador: {{ tablero.id_creador }}</p>
                <div class="actions">
                    <button class="edit-btn" @click="editTablero(tablero.id_tablero)">✏️ Editar</button>
                    <button class="delete-btn" @click="deleteTablero(tablero.id_tablero)">🗑️ Eliminar</button>
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
            tableros: []
        };
    },
    created() {
        this.fetchTableros();
    },
    methods: {
        async fetchTableros() {
            try {
                const response = await axios.get('/api/tableros');
                this.tableros = response.data;
            } catch (error) {
                console.error('Error al obtener los tableros:', error);
            }
        },
        editTablero(id) {
            this.$router.push({ name: 'tableros.edit', params: { id } });
        },
        async deleteTablero(id) {
            if (confirm("¿Seguro que quieres eliminar este tablero?")) {
                try {
                    await axios.delete(`/api/tableros/${id}`);
                    this.fetchTableros();
                    alert(`Tablero con ID: ${id} eliminado`);
                } catch (error) {
                    console.error('Error al eliminar el tablero:', error);
                }
            }
        },
        addTablero() {
            this.$router.push({ name: 'tableros.create' });
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

/* Botón de añadir nuevo tablero */
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
.tableros-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

/* Tarjetas */
.tablero-card {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
    width: 60%;
    margin: 0 auto;
}

.tablero-card:hover {
    transform: translateY(-5px);
}

.tablero-card h2 {
    margin: 0 0 10px;
    font-size: 18px;
}

.tablero-card p {
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
    .tableros-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 767px) {
    .container {
        margin: 0;
    }
    .tablero-card {
        width: 100%;
    }
}
</style>
