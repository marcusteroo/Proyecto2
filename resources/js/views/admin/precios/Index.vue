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

                <!-- Modal de edición -->
                <Dialog 
                    v-model:visible="precioDialog" 
                    :modal="true" 
                    :closable="true"
                    :dismissableMask="false"
                    :style="{width: '650px'}"
                    class="plan-edit-dialog"
                    :showHeader="false"
                >
                    <!-- Header personalizado -->
                    <div class="custom-dialog-header">
                        <div class="dialog-title-section">
                            <i class="pi pi-tag mr-2"></i>
                            <h2>{{ precio.id ? `Editar Plan: ${precio.nombre_plan}` : 'Crear Nuevo Plan' }}</h2>
                        </div>
                        <Button icon="pi pi-times" class="p-button-rounded p-button-text" @click="hideDialog" />
                    </div>

                    <div class="plan-edit-container p-fluid">
                        <!-- Información del plan -->
                        <div class="plan-info-banner">
                            <div :class="'plan-icon-large ' + precio.categoria"></div>
                            <div class="plan-info-details">
                                <h3 class="plan-name">{{ precio.nombre_plan }}</h3>
                                <p class="plan-category">{{ getCategoriaName(precio.categoria) }}</p>
                            </div>
                        </div>

                        <!-- Sección de precios -->
                        <div class="edit-section prices-section">
                            <h3 class="section-title">
                                <i class="pi pi-money-bill section-icon"></i>
                                Precios
                            </h3>
                            
                            <div class="grid formgrid">
                                <div class="field col-12 md:col-6">
                                    <label for="precio_mensual" class="font-semibold">Precio Mensual</label>
                                    <div class="p-inputgroup">
                                        <span class="p-inputgroup-addon">€</span>
                                        <InputNumber 
                                            id="precio_mensual" 
                                            v-model="precio.precio_mensual" 
                                            mode="decimal" 
                                            :minFractionDigits="2" 
                                            :maxFractionDigits="2"
                                            placeholder="0.00"
                                            :class="{'p-invalid': submitted && precio.precio_mensual === null}"
                                        />
                                    </div>
                                    <small class="p-error" v-if="submitted && precio.precio_mensual === null">El precio mensual es obligatorio</small>
                                </div>
                                
                                <div class="field col-12 md:col-6">
                                    <label for="precio_anual" class="font-semibold">Precio Anual</label>
                                    <div class="p-inputgroup">
                                        <span class="p-inputgroup-addon">€</span>
                                        <InputNumber 
                                            id="precio_anual" 
                                            v-model="precio.precio_anual" 
                                            mode="decimal" 
                                            :minFractionDigits="2" 
                                            :maxFractionDigits="2"
                                            placeholder="0.00"
                                            :class="{'p-invalid': submitted && precio.precio_anual === null}"
                                        />
                                    </div>
                                    <small class="p-error" v-if="submitted && precio.precio_anual === null">El precio anual es obligatorio</small>
                                </div>
                            </div>
                            
                            <div v-if="precio.precio_mensual && precio.precio_anual" class="price-comparison">
                                <div class="comparison-item">
                                    <div class="comparison-label">Precio mensual × 12</div>
                                    <div class="comparison-value">{{ formatCurrency(precio.precio_mensual * 12) }}</div>
                                </div>
                                <i class="pi pi-arrow-right comparison-arrow"></i>
                                <div class="comparison-item">
                                    <div class="comparison-label">Precio anual</div>
                                    <div class="comparison-value">{{ formatCurrency(precio.precio_anual) }}</div>
                                </div>
                                <i class="pi pi-arrow-right comparison-arrow"></i>
                                <div class="comparison-item saving">
                                    <div class="comparison-label">Ahorro anual</div>
                                    <div class="comparison-value">{{ calcularAhorro }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección de características -->
                        <div class="edit-section">
                            <h3 class="section-title">
                                <i class="pi pi-check-circle section-icon"></i>
                                Características del Plan
                            </h3>
                            
                            <p class="mb-3">Edita o actualiza las características que incluye este plan:</p>
                            
                            <!-- Lista editable de características -->
                            <div class="caracteristicas-container">
                                <div v-for="(caracteristica, index) in caracteristicasArray" :key="index" class="caracteristica-item">
                                    <div class="p-inputgroup">
                                        <span class="p-inputgroup-addon">
                                            <i class="pi pi-check"></i>
                                        </span>
                                        <InputText v-model="caracteristicasArray[index]" placeholder="Ingrese una característica" />
                                        <Button icon="pi pi-times" class="p-button-danger" @click="eliminarCaracteristica(index)" />
                                    </div>
                                </div>
                                
                                <Button 
                                    label="Agregar característica" 
                                    icon="pi pi-plus" 
                                    class="p-button-outlined mt-2" 
                                    @click="agregarCaracteristica" 
                                />
                            </div>
                        </div>
                    </div>
                    
                    <template #footer>
                        <div class="dialog-footer">
                            <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="hideDialog" />
                            <Button 
                                label="Guardar cambios" 
                                icon="pi pi-check" 
                                class="p-button-primary" 
                                @click="savePrecio"
                                :loading="saving"
                            />
                        </div>
                    </template>
                </Dialog>
                
                <!-- Modal para ver detalles del plan -->
                <Dialog v-model:visible="viewPlanDialog" header="Detalles del Plan" :modal="true" 
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
import { ref, onMounted, reactive, computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

// Define FilterMatchMode
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
const precio = ref({});
const selectedPlan = ref(null);
const selectedPrecios = ref(null);
const submitted = ref(false);
const filters = reactive({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const caracteristicasArray = ref([]);
const toast = useToast();
const saving = ref(false);
const categorias = [
    { name: 'Básico', code: 'basico' },
    { name: 'Premium', code: 'premium' },
    { name: 'Business', code: 'business' }
];

const calcularAhorro = computed(() => {
    if (!precio.value.precio_mensual || !precio.value.precio_anual) return '0%';
    
    const precioMensualAnual = precio.value.precio_mensual * 12;
    const ahorro = precioMensualAnual - precio.value.precio_anual;
    const porcentajeAhorro = (ahorro / precioMensualAnual) * 100;
    
    return `${porcentajeAhorro.toFixed(0)}% (${formatCurrency(ahorro)})`;
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
        case 'basico': return 'info';
        case 'premium': return 'success';
        case 'business': return 'warning';
        default: return 'secondary';
    }
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
            saving.value = true;
            
            // Filtrar características vacías
            precio.value.caracteristicas = caracteristicasArray.value.filter(item => item.trim() !== '');
            
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
        } finally {
            saving.value = false;
        }
    }
};

const agregarCaracteristica = () => {
    caracteristicasArray.value.push('');
};

const eliminarCaracteristica = (index) => {
    caracteristicasArray.value.splice(index, 1);
};

const editPrecio = (data) => {
    // Crear un nuevo objeto para evitar problemas de reactividad
    precio.value = {
        id: data.id,
        categoria: data.categoria || null,
        nombre_plan: data.nombre_plan || '',
        descripcion: data.descripcion || '',
        precio_mensual: data.precio_mensual ? Number(data.precio_mensual) : 0,
        precio_anual: data.precio_anual ? Number(data.precio_anual) : 0,
        destacado: Boolean(data.destacado),
        activo: Boolean(data.activo),
        caracteristicas: []
    };
    
    // Inicializar el array de características
    caracteristicasArray.value = [];
    if (Array.isArray(data.caracteristicas) && data.caracteristicas.length > 0) {
        caracteristicasArray.value = [...data.caracteristicas];
    } else {
        // Agregar al menos una característica vacía para editar
        agregarCaracteristica();
    }
    
    // Abrir el diálogo
    setTimeout(() => {
        precioDialog.value = true;
    }, 100);
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
</script>

<style scoped>
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

.plan-edit-dialog {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

:deep(.plan-edit-dialog .p-dialog-content) {
    padding: 0;
    overflow: hidden;
}

.custom-dialog-header {
    background: linear-gradient(135deg, #106EBE 0%, #0078D4 100%);
    color: white;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dialog-title-section {
    display: flex;
    align-items: center;
}

.dialog-title-section h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.plan-edit-container {
    padding: 1rem;
    max-height: 70vh;
    overflow-y: auto;
}

.edit-section {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}

.edit-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
}

.section-title {
    display: flex;
    align-items: center;
    margin-top: 0;
    margin-bottom: 1.5rem;
    font-size: 1.2rem;
    color: #106EBE;
    font-weight: 600;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    padding-bottom: 0.75rem;
}

.section-icon {
    margin-right: 10px;
    font-size: 1.2rem;
}

/* Comparador de precios */
.price-comparison {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(16, 110, 190, 0.05);
    padding: 1rem;
    border-radius: 6px;
    margin-top: 1rem;
    flex-wrap: wrap;
}

.comparison-item {
    text-align: center;
    padding: 0.5rem 1rem;
}

.comparison-label {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.comparison-value {
    font-weight: 600;
    font-size: 1.1rem;
}

.comparison-arrow {
    color: #6c757d;
    margin: 0 0.5rem;
}

.saving .comparison-value {
    color: #2ecc71;
}

/* Dialog footer */
.dialog-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

/* Características */
.caracteristica-item {
    margin-bottom: 0.7rem;
}

.caracteristica-item .p-inputgroup {
    border-radius: 6px;
    overflow: hidden;
}

/* Plan info banner */
.plan-info-banner {
    background: linear-gradient(to right, rgba(0,0,0,0.03), rgba(0,0,0,0.01));
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.plan-icon-large {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}

.plan-icon-large.basico {
    background: linear-gradient(135deg, #4FACFE 0%, #42A5F5 100%);
}

.plan-icon-large.premium {
    background: linear-gradient(135deg, #43E97B 0%, #38F9D7 100%);
}

.plan-icon-large.business {
    background: linear-gradient(135deg, #FF9A44 0%, #FFA726 100%);
}

.plan-info-details {
    flex-grow: 1;
}

.plan-name {
    font-size: 1.4rem;
    margin: 0 0 0.2rem 0;
    color: #2c3e50;
}

.plan-category {
    margin: 0;
    color: #7f8c8d;
    font-size: 0.95rem;
}

.prices-section {
    background-color: #f1f8ff;
    border-left: 4px solid #42A5F5;
}

/* Responsive */
@media (max-width: 768px) {
    .price-comparison {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .comparison-arrow {
        transform: rotate(90deg);
    }
}
</style>