<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">

                <div class="card-header bg-transparent ps-0 pe-0">
                    <h5 class="float-start mb-0">Kanbans</h5>
                </div>

                <DataTable v-model:filters="filters" :value="kanbans?.data" paginator :rows="5"
                           :globalFilterFields="['id_tablero','nombre','id_creador','color_fondo','created_at','updated_at']" 
                           stripedRows dataKey="id_tablero" size="small">

                    <template #header>
                        <Toolbar pt:root:class="toolbar-table">
                            <template #start>
                                <IconField>
                                    <InputIcon class="pi pi-search"> </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Buscar" />
                                </IconField>

                                <Button type="button" icon="pi pi-filter-slash" label="Clear" class="ml-1" outlined @click="initFilters()" />
                                <Button type="button" icon="pi pi-refresh" class="h-100 ml-1" outlined @click="getKanbans()" />
                            </template>
                            <template #end>
                                <Button v-if="can('kanban-create')" icon="pi pi-external-link" label="Crear Kanban" @click="$router.push('kanbans/create')" class="float-end" />
                            </template>
                        </Toolbar>
                    </template>

                    <template #empty> No kanbans found. </template>

                    <Column field="id_tablero" header="ID" sortable></Column>
                    <Column field="nombre" header="Nombre" sortable></Column>
                    <Column field="id_creador" header="ID Creador" sortable></Column>
                    <Column field="color_fondo" header="Color Fondo" sortable></Column>

                    <!-- Columnas de Editar y Eliminar -->
                    <Column class="pe-0 me-0 icon-column-2">
                        <template #body="slotProps">
                            <router-link v-if="can('kanban-edit')" :to="{ name: 'kanbans.edit', params: { id: slotProps.data.id_tablero } }">
                                <Button icon="pi pi-pencil" severity="info" size="small" class="mr-1"/>
                            </router-link>

                            <Button v-if="can('kanban-delete')" icon="pi pi-trash" severity="danger" @click="deleteKanban(slotProps.data.id_tablero)" size="small"/>
                        </template>
                    </Column>

                </DataTable>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import useKanbans from "../../../composables/kanbans";
import { useAbility } from '@casl/vue'
import { FilterMatchMode } from "@primevue/core/api";

const { kanbans, getKanbans, deleteKanban } = useKanbans();
const { can } = useAbility();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS }
    };
};

onMounted(() => {
    getKanbans();
    console.log("Kanbans:", kanbans.value); // Verificar si llegan los datos
    console.log("Permisos:", can('kanban-edit'), can('kanban-delete')); // Verificar permisos
});
</script>
