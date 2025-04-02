<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">
                <Toast />
                <Toolbar class="mb-4">
                    <template v-slot:start>
                        <h3 class="m-0">Gestión de Precios</h3>
                    </template>
                </Toolbar>

                <DataTable ref="dt" :value="precios" v-model:selection="selectedPrecios" dataKey="id"
                           :paginator="true" :rows="10" :filters="filters"
                           paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                           :rowsPerPageOptions="[5, 10, 25]"
                           currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} precios">
                    <template #header>
                        <div class="flex flex-column md:flex-row md:justify-content-between md:align-items-center">
                            <h5 class="m-0">Planes disponibles</h5>
                            <span class="block mt-2 md:mt-0 p-input-icon-left">
                                <i class="pi pi-search" />
                                <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                            </span>
                        </div>
                    </template>

                    <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                    <Column field="id" header="ID" :sortable="true" headerStyle="width:5rem"></Column>
                    <Column field="nombre_plan" header="Plan" :sortable="true" headerStyle="min-width:10rem">
                        <template #body="slotProps">
                            <div class="flex align-items-center">
                                <span :class="'plan-icon ' + slotProps.data.categoria"></span>
                                <span class="font-bold ml-2">{{ slotProps.data.nombre_plan }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column field="categoria" header="Categoría" :sortable="true" headerStyle="min-width:10rem">
                        <template #body="slotProps">
                            <Tag :value="formatCategoria(slotProps.data.categoria)" 
                                 :severity="getCategoriaSeverity(slotProps.data.categoria)" />
                        </template>
                    </Column>
                    <Column field="precio_mensual" header="Precio Mensual" :sortable="true" headerStyle="min-width:8rem">
                        <template #body="slotProps">
                            <span class="font-semibold">{{ formatCurrency(slotProps.data.precio_mensual) }}</span>
                        </template>
                    </Column>
                    <Column field="precio_anual" header="Precio Anual" :sortable="true" headerStyle="min-width:8rem">
                        <template #body="slotProps">
                            <span class="font-semibold">{{ formatCurrency(slotProps.data.precio_anual) }}</span>
                        </template>
                    </Column>
                    <Column field="destacado" header="Destacado" :sortable="true" headerStyle="min-width:6rem">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.destacado ? 'Destacado' : 'No destacado'" 
                                 :severity="slotProps.data.destacado ? 'success' : 'info'" />
                        </template>
                    </Column>
                    <Column field="activo" header="Activo" :sortable="true" headerStyle="min-width:6rem">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.activo ? 'Activo' : 'Inactivo'" 
                                 :severity="slotProps.data.activo ? 'success' : 'danger'" />
                        </template>
                    </Column>
                    <Column headerStyle="min-width:10rem">
                        <template #header>
                            <div class="flex justify-content-center">
                                <span>Acciones</span>
                            </div>
                        </template>
                        <template #body="slotProps">
                            <div class="flex justify-content-center gap-2">
                                <Button icon="pi pi-pencil" class="p-button-rounded p-button-primary" 
                                        @click="editPrecio(slotProps.data)" 
                                        tooltip="Editar plan" :tooltipOptions="{position: 'top'}" />
                                <Button icon="pi pi-eye" class="p-button-rounded p-button-info"
                                        @click="viewPlanDetails(slotProps.data)" 
                                        tooltip="Ver detalles" :tooltipOptions="{position: 'top'}" />
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <!-- Modal de edición mejorado -->
                <Dialog v-model:visible="precioDialog" :header="precio.id ? 'Editar Plan: ' + precio.nombre_plan : 'Nuevo Plan'"
                       :modal="true" :dismissableMask="true" :closable="true" class="p-fluid" :style="{width: '650px'}">
                    <div class="grid p-fluid">
                        <div class="col-12">
                            <Card>
                                <template #header>
                                    <div class="flex align-items-center mb-3">
                                        <i class="pi pi-tag mr-2" style="font-size: 1.5rem"></i>
                                        <h4 class="m-0">Información General</h4>
                                    </div>
                                </template>
                                <template #content>
                                    <div class="grid formgrid">
                                        <div class="field col-12 md:col-6">
                                            <label for="nombre_plan" class="font-bold">Nombre del Plan*</label>
                                            <InputText id="nombre_plan" v-model="precio.nombre_plan" required 
                                                      :class="{'p-invalid': submitted && !precio.nombre_plan}"
                                                      placeholder="Ej: Plan Premium" />
                                            <small class="p-error" v-if="submitted && !precio.nombre_plan">El nombre del plan es obligatorio</small>
                                        </div>
                                        
                                        <div class="field col-12 md:col-6">
                                            <label for="categoria" class="font-bold">Categoría*</label>
                                            <Dropdown id="categoria" v-model="precio.categoria" 
                                                    :options="categorias" optionLabel="name" optionValue="code" 
                                                    placeholder="Selecciona una categoría" 
                                                    :class="{'p-invalid': submitted && !precio.categoria}">
                                                <template #value="slotProps">
                                                    <div v-if="slotProps.value" class="flex align-items-center">
                                                        <Tag :value="getCategoriaName(slotProps.value)" 
                                                            :severity="getCategoriaSeverity(slotProps.value)" />
                                                    </div>
                                                    <span v-else>Selecciona una categoría</span>
                                                </template>
                                                <template #option="slotProps">
                                                    <div class="flex align-items-center">
                                                        <Tag :value="slotProps.option.name" 
                                                            :severity="getCategoriaSeverity(slotProps.option.code)" />
                                                    </div>
                                                </template>
                                            </Dropdown>
                                            <small class="p-error" v-if="submitted && !precio.categoria">La categoría es obligatoria</small>
                                        </div>
                                    </div>
                                    
                                    <div class="field">
                                        <label for="descripcion" class="font-bold">Descripción</label>
                                        <Textarea id="descripcion" v-model="precio.descripcion" rows="3" autoResize
                                                 placeholder="Breve descripción del plan" />
                                    </div>
                                </template>
                            </Card>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <Card>
                                <template #header>
                                    <div class="flex align-items-center mb-3">
                                        <i class="pi pi-money-bill mr-2" style="font-size: 1.5rem"></i>
                                        <h4 class="m-0">Precios</h4>
                                    </div>
                                </template>
                                <template #content>
                                    <div class="grid formgrid">
                                        <div class="field col-12 md:col-6">
                                            <label for="precio_mensual" class="font-bold">Precio Mensual*</label>
                                            <div class="p-inputgroup">
                                                <span class="p-inputgroup-addon">€</span>
                                                <InputNumber id="precio_mensual" v-model="precio.precio_mensual"
                                                           mode="decimal" :minFractionDigits="2" :maxFractionDigits="2"
                                                           :class="{'p-invalid': submitted && precio.precio_mensual === null}" />
                                            </div>
                                            <small class="p-error" v-if="submitted && precio.precio_mensual === null">
                                                El precio mensual es obligatorio
                                            </small>
                                        </div>
                                        <div class="field col-12 md:col-6">
                                            <label for="precio_anual" class="font-bold">Precio Anual*</label>
                                            <div class="p-inputgroup">
                                                <span class="p-inputgroup-addon">€</span>
                                                <InputNumber id="precio_anual" v-model="precio.precio_anual"
                                                           mode="decimal" :minFractionDigits="2" :maxFractionDigits="2"
                                                           :class="{'p-invalid': submitted && precio.precio_anual === null}" />
                                            </div>
                                            <small class="p-error" v-if="submitted && precio.precio_anual === null">
                                                El precio anual es obligatorio
                                            </small>
                                        </div>
                                    </div>
                                    <div class="flex align-items-center justify-content-center my-3">
                                        <span class="text-sm text-600">
                                            Ahorro anual: <span class="font-bold text-primary">{{ calcularAhorroAnual }}%</span>
                                        </span>
                                    </div>
                                </template>
                            </Card>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <Card>
                                <template #header>
                                    <div class="flex align-items-center mb-3">
                                        <i class="pi pi-check-circle mr-2" style="font-size: 1.5rem"></i>
                                        <h4 class="m-0">Características</h4>
                                    </div>
                                </template>
                                <template #content>
                                    <div class="field">
                                        <label for="caracteristicas" class="font-bold">Características del Plan</label>
                                        <small class="block mb-2">Introduce una característica por línea</small>
                                        <Textarea id="caracteristicas" v-model="caracteristicasText" rows="5" 
                                                 autoResize placeholder="Ej: 10 usuarios incluidos&#10;Acceso a todas las funciones&#10;Soporte 24/7" />
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-3 mt-3" v-if="caracteristicasPreview.length > 0">
                                        <Chip v-for="(item, index) in caracteristicasPreview" 
                                              :key="index" 
                                              :label="item" 
                                              icon="pi pi-check" />
                                    </div>
                                </template>
                            </Card>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <Card>
                                <template #header>
                                    <div class="flex align-items-center mb-3">
                                        <i class="pi pi-cog mr-2" style="font-size: 1.5rem"></i>
                                        <h4 class="m-0">Configuración</h4>
                                    </div>
                                </template>
                                <template #content>
                                    <div class="grid formgrid">
                                        <div class="field-checkbox col-12 md:col-6">
                                            <div class="flex align-items-center">
                                                <Checkbox id="destacado" v-model="precio.destacado" :binary="true" />
                                                <label for="destacado" class="ml-2">
                                                    <span class="font-medium">Plan Destacado</span>
                                                    <small class="block text-500">Este plan aparecerá resaltado</small>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="field-checkbox col-12 md:col-6">
                                            <div class="flex align-items-center">
                                                <Checkbox id="activo" v-model="precio.activo" :binary="true" />
                                                <label for="activo" class="ml-2">
                                                    <span class="font-medium">Plan Activo</span>
                                                    <small class="block text-500">Este plan estará visible</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>
                    
                    <template #footer>
                        <div class="flex justify-content-end gap-2">
                            <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="hideDialog" />
                            <Button label="Guardar" icon="pi pi-check" class="p-button-primary" @click="savePrecio" />
                        </div>
                    </template>
                </Dialog>
                
                <!-- Modal para ver detalles del plan -->
                <Dialog v-model:visible="viewPlanDialog" :header="'Detalles del Plan'" :modal="true" 
                       :dismissableMask="true" :closable="true" :style="{width: '500px'}">
                    <div class="card" v-if="selectedPlan">
                        <div class="flex flex-column md:flex-row gap-3">
                            <div class="flex-1">
                                <div class="flex align-items-center">
                                    <div :class="'plan-badge-large ' + selectedPlan.categoria">
                                        <i class="pi pi-star" v-if="selectedPlan.categoria === 'premium'"></i>
                                        <i class="pi pi-check-circle" v-else-if="selectedPlan.categoria === 'basico'"></i>
                                        <i class="pi pi-building" v-else></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="m-0">{{ selectedPlan.nombre_plan }}</h3>
                                        <Tag :value="formatCategoria(selectedPlan.categoria)" 
                                             :severity="getCategoriaSeverity(selectedPlan.categoria)" 
                                             class="mt-2" />
                                    </div>
                                </div>
                                
                                <Divider />
                                
                                <div class="my-3">
                                    <p class="text-500 mb-2">{{ selectedPlan.descripcion }}</p>
                                </div>
                                
                                <div class="grid formgrid">
                                    <div class="field col-6">
                                        <label class="font-bold">Precio Mensual</label>
                                        <p class="text-xl font-bold text-primary">{{ formatCurrency(selectedPlan.precio_mensual) }}</p>
                                    </div>
                                    <div class="field col-6">
                                        <label class="font-bold">Precio Anual</label>
                                        <p class="text-xl font-bold text-primary">{{ formatCurrency(selectedPlan.precio_anual) }}</p>
                                    </div>
                                </div>
                                
                                <Divider />
                                
                                <h4>Características</h4>
                                <ul class="list-none p-0 m-0">
                                    <li v-for="(caracteristica, i) in selectedPlan.caracteristicas" :key="i" class="mb-2">
                                        <div class="flex align-items-center">
                                            <i class="pi pi-check text-green-500 mr-2"></i>
                                            <span>{{ caracteristica }}</span>
                                        </div>
                                    </li>
                                </ul>
                                
                                <Divider />
                                
                                <div class="flex justify-content-between">
                                    <span class="font-bold">Estado:</span>
                                    <Tag :value="selectedPlan.activo ? 'Activo' : 'Inactivo'" 
                                         :severity="selectedPlan.activo ? 'success' : 'danger'" />
                                </div>
                                
                                <div class="flex justify-content-between mt-3">
                                    <span class="font-bold">Destacado:</span>
                                    <Tag :value="selectedPlan.destacado ? 'Destacado' : 'No destacado'" 
                                         :severity="selectedPlan.destacado ? 'success' : 'info'" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <template #footer>
                        <div class="flex justify-content-end">
                            <Button label="Cerrar" icon="pi pi-times" class="p-button-text" @click="hideViewPlanDialog" />
                            <Button label="Editar" icon="pi pi-pencil" class="p-button-primary ml-2" 
                                   @click="editSelectedPlan" />
                        </div>
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useRouter } from 'vue-router';
import axios from 'axios';

// Define FilterMatchMode manualmente
const FilterMatchMode = {
    STARTS_WITH: 'startsWith',
    CONTAINS: 'contains',
    NOT_CONTAINS: 'notContains',
    ENDS_WITH: 'endsWith',
    EQUALS: 'equals',
    NOT_EQUALS: 'notEquals'
};

const dt = ref();
const precios = ref([]);
const precioDialog = ref(false);
const viewPlanDialog = ref(false);
const deletePrecioDialog = ref(false);
const deletePrecios = ref(false);
const precio = ref({});
const selectedPlan = ref(null);
const selectedPrecios = ref(null);
const submitted = ref(false);
const filters = reactive({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const toast = useToast();
const router = useRouter();
const caracteristicasText = ref('');

const categorias = [
    { name: 'Básico', code: 'basico' },
    { name: 'Premium', code: 'premium' },
    { name: 'Business', code: 'business' }
];

const caracteristicasPreview = computed(() => {
    return caracteristicasText.value
        .split('\n')
        .map(item => item.trim())
        .filter(item => item !== '');
});

const calcularAhorroAnual = computed(() => {
    if (!precio.value.precio_mensual || !precio.value.precio_anual) return 0;
    
    const precioMensualAnual = precio.value.precio_mensual * 12;
    const ahorro = precioMensualAnual - precio.value.precio_anual;
    const porcentajeAhorro = (ahorro / precioMensualAnual) * 100;
    
    return porcentajeAhorro.toFixed(0);
});

// Observar cambios en el texto de características para actualizar automáticamente
watch(caracteristicasText, (newValue) => {
    if (!precio.value) return;
    
    const caracteristicas = newValue
        .split('\n')
        .map(item => item.trim())
        .filter(item => item !== '');
        
    precio.value.caracteristicas = caracteristicas;
});

onMounted(() => {
    loadPrecios();
});

const loadPrecios = async () => {
    try {
        const response = await axios.get('/api/precios');
        precios.value = response.data;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los precios', life: 3000 });
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
};

const formatCategoria = (categoria) => {
    const cat = categorias.find(c => c.code === categoria);
    return cat ? cat.name : categoria;
};

const getCategoriaName = (code) => {
    const cat = categorias.find(c => c.code === code);
    return cat ? cat.name : code;
};

const getCategoriaSeverity = (categoria) => {
    switch (categoria) {
        case 'basico':
            return 'info';
        case 'premium':
            return 'success';
        case 'business':
            return 'warning';
        default:
            return 'secondary';
    }
};

const openNew = () => {
    precio.value = {
        categoria: null,
        nombre_plan: '',
        descripcion: '',
        precio_mensual: null,
        precio_anual: null,
        destacado: false,
        activo: true,
        caracteristicas: []
    };
    caracteristicasText.value = '';
    submitted.value = false;
    precioDialog.value = true;
};

const hideDialog = () => {
    precioDialog.value = false;
    submitted.value = false;
};

const savePrecio = async () => {
    submitted.value = true;

    if (precio.value.nombre_plan?.trim() && precio.value.categoria && 
        precio.value.precio_mensual !== null && precio.value.precio_anual !== null) {
        try {
            // Convertir el texto de características a array
            const caracteristicas = caracteristicasText.value
                .split('\n')
                .map(item => item.trim())
                .filter(item => item !== '');
            
            precio.value.caracteristicas = caracteristicas;
            
            if (precio.value.id) {
                await axios.put(`/api/precios/${precio.value.id}`, precio.value);
                toast.add({ severity: 'success', summary: 'Éxito', detail: 'Precio actualizado correctamente', life: 3000 });
            } else {
                await axios.post('/api/precios', precio.value);
                toast.add({ severity: 'success', summary: 'Éxito', detail: 'Precio creado correctamente', life: 3000 });
            }
            
            precioDialog.value = false;
            loadPrecios();
            precio.value = {};
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Error al guardar el precio', life: 3000 });
        }
    }
};

const editPrecio = (data) => {
    precio.value = JSON.parse(JSON.stringify(data));
    
    // Convertir array de características a texto
    caracteristicasText.value = Array.isArray(data.caracteristicas) 
        ? data.caracteristicas.join('\n') 
        : '';
        
    precioDialog.value = true;
};

const viewPlanDetails = (data) => {
    selectedPlan.value = JSON.parse(JSON.stringify(data));
    viewPlanDialog.value = true;
};

const hideViewPlanDialog = () => {
    viewPlanDialog.value = false;
    selectedPlan.value = null;
};

const editSelectedPlan = () => {
    if (selectedPlan.value) {
        editPrecio(selectedPlan.value);
        viewPlanDialog.value = false;
    }
};

const confirmDeletePrecio = (data) => {
    precio.value = data;
    deletePrecioDialog.value = true;
};

const deletePrecio = async () => {
    try {
        await axios.delete(`/api/precios/${precio.value.id}`);
        loadPrecios();
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Precio eliminado', life: 3000 });
        deletePrecioDialog.value = false;
        precio.value = {};
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al eliminar el precio', life: 3000 });
    }
};

const confirmDeleteSelected = () => {
    deletePrecios.value = true;
};

const deleteSelectedPrecios = async () => {
    try {
        for (let p of selectedPrecios.value) {
            await axios.delete(`/api/precios/${p.id}`);
        }
        
        loadPrecios();
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Precios eliminados', life: 3000 });
        deletePrecios.value = false;
        selectedPrecios.value = null;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al eliminar los precios', life: 3000 });
    }
};

const exportCSV = () => {
    dt.value?.exportCSV();
};
</script>

<style scoped>
.plan-badge {
    border-radius: 2px;
    padding: .25em .5rem;
    font-weight: 700;
    letter-spacing: .3px;
    text-transform: uppercase;
}

.plan-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: inline-block;
}

.plan-icon.basico {
    background-color: #90CAF9;
}

.plan-icon.premium {
    background-color: #66BB6A;
}

.plan-icon.business {
    background-color: #FFA726;
}

.destacado-true {
    background: #C8E6C9;
    color: #256029;
}

.destacado-false {
    background: #FEEDAF;
    color: #8A5340;
}

.status-true {
    background: #C8E6C9;
    color: #256029;
}

.status-false {
    background: #FFCDD2;
    color: #C63737;
}

.plan-badge-large {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.plan-badge-large.basico {
    background-color: #42A5F5;
}

.plan-badge-large.premium {
    background-color: #66BB6A;
}

.plan-badge-large.business {
    background-color: #FFA726;
}
</style>