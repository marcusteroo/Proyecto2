<template>
    <div class="row justify-content-center my-5">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form @submit.prevent="submitForm">
                        <div class="mb-3">
                            <label for="post-title" class="form-label">id_kanban</label>
                            <input v-model="post.id_kanban" id="post-title" type="text" class="form-control">
                            <div class="text-danger mt-1">
                                {{ errors.id_kanban }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.id_kanban">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input v-model="post.email" id="email" type="email" class="form-control">
                            <div class="text-danger mt-1">
                                {{ errors.email }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.email">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input v-model="post.password" id="password" type="password" class="form-control">
                            <div class="text-danger mt-1">
                                {{ errors.password }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.password">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <!-- Role -->
                        <div class="mb-3">
                            <label for="post-category" class="form-label">
                                Role
                            </label>
                            <div class="text-danger mt-1">
                                {{ errors.role_id }}
                            </div>
                            <div class="text-danger mt-1">
                                <div v-for="message in validationErrors?.role_id">
                                    {{ message }}
                                </div>
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="mt-4">
                            <button :disabled="isLoading" class="btn btn-primary">
                                <div v-show="isLoading" class=""></div>
                                <span v-if="isLoading">Processing...</span>
                                <span v-else>Save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive } from "vue";
import useKanbans from "@/composables/kanbans";

const { storeKanban, validationErrors, isLoading } = useKanbans();

import { useForm, useField, defineRule } from "vee-validate";
import { required, min } from "@/validation/rules";
defineRule('required', required);
defineRule('min', min);

// Define a validation schema
const schema = {
    id_kanban: 'required',
    email: 'required',
    password: 'required|min:8',
}
// Create a form context with the validation schema
const { validate, errors } = useForm({ validationSchema: schema })
// Define actual fields for validation
const { value: id_kanban } = useField('id_kanban', null, { initialValue: '' });
const { value: email } = useField('email', null, { initialValue: '' });
const { value: password } = useField('password', null, { initialValue: '' });
const { value: role_id } = useField('role_id', null, { initialValue: '', label: 'role' });

const post = reactive({
    id_kanban,
    email,
    password,
    role_id,
})

function submitForm() {
    validate().then(form => { if (form.valid) storeKanban(post) })
}

onMounted(() => {
    getRoleList()
})
</script>
