import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'

export default function Tareas() {
    const kanbans = ref([])
    const kanban = ref({ name: '' })

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
        axios.get('/api/kanban?page=' + page +
            '&search_id=' + search_id +
            '&search_title=' + search_title +
            '&search_global=' + search_global +
            '&order_column=' + order_column +
            '&order_direction=' + order_direction)
            .then(response => {
                if (Array.isArray(response.data)) {
                    kanbans.value = { data: response.data }
                } else {
                    kanbans.value = response.data
                }
            })
    }

    /*const getTareasWithTasks = async () => {
        axios.get('/api/kanbanwithtasks')
            .then(response => {
                if (Array.isArray(response.data)) {
                    kanbans.value = { data: response.data }
                } else {
                    kanbans.value = response.data
                }
            })
    }*/

    const getTarea = async (id) => {
        axios.get('/api/kanban/' + id)
            .then(response => {
                kanban.value = response.data.data;
                console.log(kanban.value)
            })
    }

    const createTareasDB = async (id) => {
        return axios.put('/api/kanban/db/create/' + id);
    }

    const deleteTareasDB = async (id) => {
        return axios.put('/api/kanban/db/delete/' + id);
    }

    const changeTareasPasswordDB = async (id) => {
        return axios.put('/api/kanban/db/password/' + id);
    }

    const createTareasProceduredDB = async (id) => {
        return axios.put('/api/kanban/db/procedure/' + id);
    }

    const storeTareas = async (kanban) => {
        if (isLoading.value) return;

        isLoading.value = true
        validationErrors.value = {}

        let serializedPost = new FormData()
        for (let item in kanban) {
            if (kanban.hasOwnProperty(item)) {
                serializedPost.append(item, kanban[item])
            }
        }

        axios.post('/api/kanban', serializedPost)
            .then(response => {
                router.push({ name: 'kanbans.index' })
                swal({
                    icon: 'success',
                    title: 'Tareas saved successfully'
                })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => isLoading.value = false)
    }

    const updateTareas = async (kanban) => {
        if (isLoading.value) return;

        isLoading.value = true
        validationErrors.value = {}

        axios.put('/api/kanban/' + kanban.id, kanban)
            .then(response => {
                swal({
                    icon: 'success',
                    title: 'Tareas updated successfully'
                })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => isLoading.value = false)
    }

    const deleteTareas = async (id, index) => {
        swal({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this action!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            confirmButtonColor: '#ef4444',
            timer: 20000,
            timerProgressBar: true,
            reverseButtons: true
        })
            .then(result => {
                if (result.isConfirmed) {
                    axios.delete('/api/kanban/' + id)
                        .then(response => {
                            kanbans.value.data.splice(index, 1);
                            swal({
                                icon: 'success',
                                title: 'Tareas deleted successfully'
                            })
                        })
                        .catch(error => {
                            swal({
                                icon: 'error',
                                title: 'Something went wrong'
                            })
                        })
                }
            })
    }

    return {
        kanbans,
        kanban,
        getTareas,
        getTarea,
        createTareasDB,
        deleteTareasDB,
        changeTareasPasswordDB,
        createTareasProceduredDB,
        storeTareas,
        updateTareas,
        deleteTareas,
        validationErrors,
        isLoading
    }
}
