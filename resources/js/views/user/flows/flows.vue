<template>
    <div>
        <h1>Automatizaciones</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="flow in flows" :key="flow.id_workflow">
                    <td>{{ flow.id_workflow }}</td>
                    <td>{{ flow.nombre }}</td>
                    <td>{{ flow.descripcion }}</td>
                    <td>
                        <button @click="editFlow(flow.id_workflow)">Editar</button>
                        <button @click="deleteFlow(flow.id_workflow)">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
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
        async updateFlow(id, nombre, descripcion) {
            try {
                await axios.put(`/api/workflows/${id}`, { nombre, descripcion });
                this.fetchFlows(); // Vuelve a obtener los workflows después de actualizar uno
                alert(`Automatización con ID: ${id} actualizada`);
            } catch (error) {
                console.error('Error al actualizar el workflow:', error);
            }
        },
        async deleteFlow(id) {
            try {
                await axios.delete(`/api/workflows/${id}`);
                this.fetchFlows(); // Vuelve a obtener los workflows después de eliminar uno
                alert(`Automatización con ID: ${id} eliminada`);
            } catch (error) {
                console.error('Error al eliminar el workflow:', error);
            }
        }
    }
};
</script>

<style scoped>
div {
    margin: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
}

th {
    background-color: #f2f2f2;
}
</style>