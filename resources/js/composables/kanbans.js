import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'

export default function  Kanbans() {
    const kanbans = ref([])
    const kanban = ref({
        name: ''
    })

    const router = useRouter()
    const validationErrors = ref({})
    const isLoading = ref(false)
    const swal = inject('$swal')

    const getKanbans = async (
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
                kanbans.value = response.data;
            })
    }

    const getKanbansWithTasks = async () => {
        axios.get('/api/kanbanswithtasks')
            .then(response => {
                kanbans.value = response.data;
            })
    }

    const getKanban = async (id) => {
        axios.get('/api/kanbans/' + id)
            .then(response => {
                kanban.value = response.data.data;
                console.log(kanban.value)
            })
    }

    const createKanbanDB = async (id) => {
        return axios.put('/api/kanbans/db/create/' + id);
    }

    const deleteKanbanDB = async (id) => {
        return axios.put('/api/kanbans/db/delete/' + id);
    }

    const changeKanbanPasswordDB = async (id) => {
        return axios.put('/api/kanbans/db/password/' + id);
    }

    const createKanbanProceduredDB = async (id) => {
        return axios.put('/api/kanbans/db/procedure/' + id);
    }
    const storeKanban = async (kanban) => {
        if (isLoading.value) return;

        isLoading.value = true
        validationErrors.value = {}

        let serializedPost = new FormData()
        for (let item in kanban) {
            if (kanban.hasOwnProperty(item)) {
                serializedPost.append(item, kanban[item])
            }
        }

        axios.post('/api/kanbans', serializedPost)
            .then(response => {
                router.push({name: 'kanbans.index'})
                swal({
                    icon: 'success',
                    title: 'kanban saved successfully'
                })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => isLoading.value = false)
    }

    const updateKanban = async (kanban) => {
        if (isLoading.value) return;

        isLoading.value = true
        validationErrors.value = {}

        axios.put('/api/kanbans/' + kanban.id, kanban)
            .then(response => {
                //router.push({name: 'kanbans.index'})

                swal({
                    icon: 'success',
                    title: 'kanban updated successfully'
                })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => isLoading.value = false)
    }

    const deleteKanban = async (id, index) => {
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
                    axios.delete('/api/kanbans/' + id)
                        .then(response => {
                            kanbans.value.data.splice(index, 1);

                            //getKanbans()
                            //router.push({name: 'kanbans.index'})
                            swal({
                                icon: 'success',
                                title: 'kanban deleted successfully'
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
        getKanbans,
        getKanbansWithTasks,
        getKanban,
        createKanbanDB,
        deleteKanbanDB,
        changeKanbanPasswordDB,
        createKanbanProceduredDB,
        storeKanban,
        updateKanban,
        deleteKanban,
        validationErrors,
        isLoading
    }
}