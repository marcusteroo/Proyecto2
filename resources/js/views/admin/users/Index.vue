<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">

                <div class="card-header bg-transparent ps-0 pe-0">
                    <h5 class="float-start mb-0">Ejercicios</h5>
                    <router-link :to="{ name: 'users.create' }" class="btn btn-primary btn-sm float-end">
                        Create User
                    </router-link>
                </div>

                <DataTable
                    v-model:filters="filters"
                    :value="users.data"
                    paginator
                    :rows="5"
                    :globalFilterFields="['id','alias', 'name','surname1','surname2','email','created_at','roles']"
                    stripedRows
                    dataKey="id"
                    size="small">

                    <template #header>
                        <Toolbar pt:root:class="toolbar-table">
                            <template #start>
                                <IconField>
                                    <InputIcon class="pi pi-search"></InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Buscar" />
                                </IconField>
                                <Button type="button" icon="pi pi-filter-slash" label="Clear" class="ml-1" outlined @click="initFilters()" />
                                <Button type="button" icon="pi pi-refresh" class="h-100 ml-1" outlined @click="getUsers()" />
                            </template>
                            <template #end>
                                <Button v-if="can('exercise-create')" icon="pi pi-external-link" label="Crear Usuario" @click="$router.push('users/create')" class="float-end" />
                            </template>
                        </Toolbar>
                    </template>

                    <template #empty> No customers found. </template>

                    <Column field="id" header="ID" sortable></Column>
                    <Column field="alias" header="Alias" sortable></Column>
                    <Column field="name" header="Nombre" sortable></Column>
                    <Column field="surname1" header="Apellido1" sortable></Column>
                    <Column field="surname2" header="Apellido2" sortable></Column>
                    <Column field="email" header="Email" sortable></Column>
                    <Column field="created_at" header="Creado el" sortable></Column>
                    
                    <!-- Nueva columna para Roles -->
                    <Column header="Roles">
                        <template #body="slotProps">
                            <span v-if="slotProps.data.roles && slotProps.data.roles.length">
                                {{ slotProps.data.roles.map(role => role.name).join(', ') }}
                            </span>
                            <span v-else>
                                Sin roles
                            </span>
                        </template>
                    </Column>

                    <Column class="pe-0 me-0 icon-column-2">
                        <template #body="slotProps">
                            <router-link v-if="can('user-edit')" :to="{ name: 'users.edit', params: { id: slotProps.data.id } }">
                                <Button icon="pi pi-pencil" severity="info" size="small" class="mr-1"/>
                            </router-link>
                            <Button icon="pi pi-trash" severity="danger" v-if="can('user-delete')" @click.prevent="deleteUser(slotProps.data.id, slotProps.index)" size="small"/>
                        </template>
                    </Column>

                </DataTable>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import useUsers from "../../../composables/users";
import { useAbility } from '@casl/vue';
import { FilterMatchMode } from "@primevue/core/api";

const { users, getUsers, deleteUser } = useUsers();
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
    getUsers();
});
console.log(users.data);
</script>
