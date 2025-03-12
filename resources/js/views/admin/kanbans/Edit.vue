<script setup>
import { onMounted, reactive, ref, watchEffect } from "vue";
import { useRoute } from "vue-router";
import { usePrimeVue } from 'primevue/config';
import useTableros from "@/composables/tableros";
import { useToast } from 'primevue/usetoast';

const $primevue = usePrimeVue();
const toast = useToast();
const { getTablero, updateTablero, tablero: postData } = useTableros();

import { useForm, useField, defineRule } from "vee-validate";
import { required } from "@/validation/rules"
defineRule('required', required);

// Define una nueva estructura de validación
const schema = {
    id_tablero: 'required',
    nombre: 'required',
    id_creador: 'required',
    color_fondo: 'required'
}

// Crea un contexto para el formulario con el esquema de validación
const { validate, errors, resetForm } = useForm({ validationSchema: schema });

// Define los campos a ser validados
const { value: id_tablero } = useField('id_tablero', null, { initialValue: '' });
const { value: nombre } = useField('nombre', null, { initialValue: '' });
const { value: id_creador } = useField('id_creador', null, { initialValue: '' });
const { value: color_fondo } = useField('color_fondo', null, { initialValue: '' });

const tablero = reactive({
    id_tablero,
    nombre,
    id_creador,
    color_fondo
})

const route = useRoute()
function submitForm() {
    validate().then(form => { if (form.valid) updateTablero(tablero) })
}

onMounted(() => {
    getTablero(route.params.id)
})

watchEffect(() => {
    tablero.id_tablero = postData.value.id_tablero
    tablero.nombre = postData.value.nombre
    tablero.id_creador = postData.value.id_creador
    tablero.color_fondo = postData.value.color_fondo
})

</script>