<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">

                <div class="card-header bg-transparent ps-0 pe-0">
                    <h5 class="float-start mb-0">Ejercicios</h5>
                </div>

                <DataTable
                    v-model:filters="filters"
                    :value="kanbans?.data || []"
                    paginator
                    :rows="5"
                    :globalFilterFields="['id_tablero','nombre','id_creador','color_fondo','created_at','updated_at']"
                    stripedRows
                    dataKey="id_tablero"
                    size="small"
                >

                    <template #header>
                        <Toolbar pt:root:class="toolbar-table">
                            <template #start>
                                <IconField>
                                    <InputIcon class="pi pi-search"> </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Buscar" />
                                </IconField>

                                <Button
                                    type="button"
                                    icon="pi pi-filter-slash"
                                    label="Clear"
                                    class="ml-1"
                                    outlined
                                    @click="initFilters()"
                                />
                                <Button
                                    type="button"
                                    icon="pi pi-refresh"
                                    class="h-100 ml-1"
                                    outlined
                                    @click="getUsers()"
                                />
                            </template>
                            <template #end>
                                <Button
                                    v-if="can('exercise-create')"
                                    icon="pi pi-external-link"
                                    label="Crear Usuario"
                                    @click="$router.push('users/create')"
                                    class="float-end"
                                />
                            </template>
                        </Toolbar>
                    </template>

                    <template #empty>
                        No customers found.
                    </template>

                    <Column field="id_tablero" header="ID" sortable></Column>
                    <Column field="nombre" header="Nombre" sortable></Column>
                    <Column field="id_creador" header="ID Creador" sortable></Column>
                    <Column field="color_fondo" header="Color Fondo" sortable></Column>
                    <Column field="created_at" header="Creación" sortable></Column>
                    <Column field="updated_at" header="Actualización" sortable></Column>

                    <Column class="pe-0 me-0 icon-column-2">
                        <template #body="slotProps">
                            <router-link
                                v-if="can('user-edit')"
                                :to="{ name: 'users.edit', params: { id: slotProps.id_tablero } }"
                            >
                                <Button
                                    icon="pi pi-pencil"
                                    severity="info"
                                    size="small"
                                    class="mr-1"
                                />
                            </router-link>

                            <Button
                                icon="pi pi-trash"
                                severity="danger"
                                v-if="can('user-delete')"
                                @click.prevent="deleteUser(slotProps.id_tablero, slotProps.index)"
                                size="small"
                            />
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
import { useAbility } from '@casl/vue';
import { FilterMatchMode, FilterService } from "@primevue/core/api";

const { kanbans, getKanbans, deleteKanban, resetKanbanDB } = useKanbans();
const { can } = useAbility();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    };
};

onMounted(() => {
    getKanbans();
});

console.log(kanbans.data+"aaasasadsaojdsaod");
</script>
