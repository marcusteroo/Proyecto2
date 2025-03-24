import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'

export default function Tareas() {
    const tareas = ref([])
    const tarea = ref({ name: '' })

    const router = useRouter()
    const validationErrors = ref({})
    const isLoading = ref(false)
    const swal = inject('$swal')

    const getTareas = async (
        page = 1,
        search_id = '',
        search_title = '',
        search_global = '',
        order_column = 'created_at',
        order_direction = 'desc'
    ) => {
        axios.get('/api/kanbans?page=' + page +
            '&search_id=' + search_id +
            '&search_title=' + search_title +
            '&search_global=' + search_global +
            '&order_column=' + order_column +
            '&order_direction=' + order_direction)
            .then(response => {
                if (Array.isArray(response.data)) {
                    tareas.value = { data: response.data }
                } else {
                    tareas.value = response.data
                }
            })
    }

    const getTarea = async (id) => {
        axios.get('/api/kanbans/' + id)
            .then(response => {
                tarea.value = response.data.data;
                console.log(tarea.value)
            })
    }

    const createTareaDB = async (id) => {
        return axios.put('/api/kanbans/db/create/' + id);
    }

    const deleteTareaDB = async (id) => {
        return axios.put('/api/kanbans/db/delete/' + id);
    }

    const changeTareaPasswordDB = async (id) => {
        return axios.put('/api/kanbans/db/password/' + id);
    }

    const createTareaProceduredDB = async (id) => {
        return axios.put('/api/kanbans/db/procedure/' + id);
    }

    const storeTarea = async (tarea) => {
        if (isLoading.value) return;

        isLoading.value = true
        validationErrors.value = {}

        let serializedPost = new FormData()
        for (let item in tarea) {
            if (tarea.hasOwnProperty(item)) {
                serializedPost.append(item, tarea[item])
            }
        }

        axios.post('/api/kanbans', serializedPost)
            .then(response => {
                router.push({ name: 'tareas.index' })
                swal({
                    icon: 'success',
                    title: 'Tarea guardada exitosamente'
                })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => isLoading.value = false)
    }

    const updateTarea = async (tarea) => {
        if (isLoading.value) return;

        isLoading.value = true
        validationErrors.value = {}

        axios.put('/api/kanbans/' + tarea.id, tarea)
            .then(response => {
                swal({
                    icon: 'success',
                    title: 'Tarea actualizada exitosamente'
                })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => isLoading.value = false)
    }

    const deleteTarea = async (id, index) => {
        swal({
            title: '¿Estás seguro?',
            text: '¡No podrás revertir esta acción!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            confirmButtonColor: '#ef4444',
            timer: 20000,
            timerProgressBar: true,
            reverseButtons: true
        })
            .then(result => {
                if (result.isConfirmed) {
                    axios.delete('/api/kanbans/' + id)
                        .then(response => {
                            tareas.value.data.splice(index, 1);
                            swal({
                                icon: 'success',
                                title: 'Tarea eliminada exitosamente'
                            })
                        })
                        .catch(error => {
                            swal({
                                icon: 'error',
                                title: 'Algo salió mal'
                            })
                        })
                }
            })
    }

    return {
        tareas,
        tarea,
        getTareas,
        getTarea,
        createTareaDB,
        deleteTareaDB,
        changeTareaPasswordDB,
        createTareaProceduredDB,
        storeTarea,
        updateTarea,
        deleteTarea,
        validationErrors,
        isLoading
    }
}
