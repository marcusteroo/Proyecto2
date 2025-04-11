<template>

  <div class="grid">
      <div class="col-12 md:col-4 lg:col-4 xl:col-4">
          <div class="card mb-0">
              <div class="card-body">
                  <div class="account-settings">
                      <div class="user-profile">
                          <div class="user-avatar">
                            <div class="user-image-container">
                              <img :src="getUserImageUrl()" 
                                  alt="Imagen de perfil"
                                  class="object-fit-cover w-100 h-100 img-profile"
                                  @error="handleImageError">
                            </div>
                          </div>

                          <h5 class="user-name">{{ user.name }}</h5>
                          <h6 class="user-email">{{ user.email }}</h6>
                      </div>

                      <div class="about">

                          <div class="form-group">
                              <label for="roles">Roles</label>
                              <MultiSelect id="roles" v-model="user.role_id" display="chip" :options="roleList" optionLabel="name" dataKey="id" placeholder="Seleciona los roles" appendTo="self"
                                  class="w-100" />
                          </div>


                          <div class="text-right">
                              <button :disabled="isLoading" class="btn btn-primary w-100" @click="submitForm">
                                  <div v-show="isLoading" class=""></div>
                                  <span v-if="isLoading">Processing...</span>
                                  <span v-else>Guardar</span>
                              </button>
                          </div>

                      </div>
                  </div>
              </div>
          </div>
      </div>

      <div class="col-12 md:col-8 lg:col-8 xl:col-8">
          <div class="card mb-3">
              <div class="card-body">
                  {{ user.avatar }}
                  <h6 class="mb-2 text-primary">Personal Details</h6>

                  <div class="form-group">
                      <label for="name">Nombre</label>
                      <input v-model="user.name" type="text" class="form-control" id="name">
                      <div class="text-danger mt-1">{{ errors.name }}</div>
                      <div class="text-danger mt-1">
                          <div v-for="message in validationErrors?.name">
                              {{ message }}
                          </div>
                      </div>
                  </div>

                  <div class="form-group">
                      <label for="surname1">Apellido 1</label>
                      <input v-model="user.surname1" type="text" class="form-control" id="surname1">
                      <div class="text-danger mt-1">{{ errors.surname1 }}</div>
                      <div class="text-danger mt-1">
                          <div v-for="message in validationErrors?.surname1">
                              {{ message }}
                          </div>
                      </div>
                  </div>

                  <div class="form-group">
                      <label for="surname2">Apellido 2</label>
                      <input v-model="user.surname2" type="text" class="form-control" id="surname2">
                      <div class="text-danger mt-1">{{ errors.surname2 }}</div>
                      <div class="text-danger mt-1">
                          <div v-for="message in validationErrors?.surname2">
                              {{ message }}
                          </div>
                      </div>
                  </div>

                  <div class="form-group">
                      <label for="email">Email</label>
                      <input v-model="user.email" type="email" class="form-control" id="email">
                      <div class="text-danger mt-1">{{ errors.email }}</div>
                      <div class="text-danger mt-1">
                          <div v-for="message in validationErrors?.email">
                              {{ message }}
                          </div>
                      </div>
                  </div>

                  <div class="form-group">
                      <label for="password">Password</label>
                      <input v-model="user.password" type="password" class="form-control" id="password">
                      <div class="text-danger mt-1">{{ errors.password }}</div>
                      <div class="text-danger mt-1">
                          <div v-for="message in validationErrors?.password">
                              {{ message }}
                          </div>
                      </div>
                  </div>
              </div>
          </div>


      </div>
  </div>

  <Toast />
</template>

<script setup>
import { onMounted, reactive, ref, watchEffect } from "vue";
import { useRoute } from "vue-router";
import { usePrimeVue } from 'primevue/config';
import useRoles from "@/composables/roles";
import useUsers from "@/composables/users";
import { useToast } from 'primevue/usetoast';

const $primevue = usePrimeVue();
const toast = useToast();
const { roleList, getRoleList } = useRoles();
const { updateUser, getUser, user: postData, createUserDB, deleteUserDB, changeUserPasswordDB, createUserProceduredDB, validationErrors, isLoading } = useUsers();

import { useForm, useField, defineRule } from "vee-validate";
import { required, min } from "@/validation/rules"
defineRule('required', required)
defineRule('min', min);

// Define a validation schema
const schema = {
  name: 'required',
  email: 'required',
  password: 'min:8'
}

// Create a form context with the validation schema
const { validate, errors, resetForm } = useForm({ validationSchema: schema })
// Define actual fields for validation
const { value: name } = useField('name', null, { initialValue: '' });
const { value: email } = useField('email', null, { initialValue: '' });
const { value: surname1 } = useField('surname1', null, { initialValue: '' });
const { value: surname2 } = useField('surname2', null, { initialValue: '' });
const { value: password } = useField('password', null, { initialValue: '' });
const { value: role_id } = useField('role_id', null, { initialValue: '', label: 'role' });

const user = reactive({
  name,
  email,
  surname1,
  surname2,
  password,
  role_id
})

const route = useRoute()
function submitForm() {
  validate().then(form => { if (form.valid) updateUser(user) })
}

onMounted(() => {
  getRoleList()
  getUser(route.params.id)

})
// https://vuejs.org/api/reactivity-core.html#watcheffect
watchEffect(() => {
  user.id = postData.value.id
  user.name = postData.value.name
  user.email = postData.value.email
  user.surname1 = postData.value.surname1
  user.surname2 = postData.value.surname2
  user.role_id = postData.value.role_id
  user.avatar = postData.value.avatar
})

const totalSize = ref(0);
const totalSizePercent = ref(0);
const files = ref([]);

const onBeforeUpload = (event) => {
  // console.log('onBeforeUpload')
  event.formData.append('id', user.id)
};
const onRemoveTemplatingFile = (file, removeFileCallback, index) => {
  removeFileCallback(index);
  totalSize.value -= parseInt(formatSize(file.size));
  totalSizePercent.value = totalSize.value / 10;
};

const onClearTemplatingUpload = (clear) => {
  clear();
  totalSize.value = 0;
  totalSizePercent.value = 0;
};

const onSelectedFiles = (event) => {
  console.log('onSelectedFiles');
  files.value = event.files;

  if (event.files.length > 1) {
      event.files = event.files.splice(0, event.files.length - 1);
  }

  files.value.forEach((file) => {
      totalSize.value += parseInt(formatSize(file.size));
  });
};

const uploadEvent = async (callback, uploadedFiles) => {
  console.log('uploadEvent');
  totalSizePercent.value = totalSize.value / 10;
  await callback();
  // if (uploadedFiles.length > 1) {
  //     uploadedFiles = uploadedFiles.splice(0, uploadedFiles.length - 1);
  // }
};

const createUserDBView = async (id) => {
  createUserDB(id).then(response => {
          toast.add({ severity: 'info', summary: 'Base de datos creada', detail: 'Base de datos creada correctamente.', life: 3000 });
      })
      .catch(error => {
          toast.add({ severity: 'warning', summary: 'Error al crear la base de datos', detail: error.response.data.message, life: 3000 });
          console.log(error.response.data.message)
      })
}

const changeUserPasswordDBView = async (id) => {
  changeUserPasswordDB(id).then(response => {
          toast.add({ severity: 'info', summary: 'Base de datos modificada', detail: 'Contraseña cambiada correctamente.', life: 3000 });
      })
      .catch(error => {
          toast.add({ severity: 'danger', summary: 'Error al cambiar la contraseña', detail: error.response.data.message, life: 3000 });
      })
}

const createUserProceduredDBView = async (id) => {
  createUserProceduredDB(id).then(response => {
          toast.add({ severity: 'info', summary: 'Base de datos creada', detail: 'Base de datos creada correctamente.', life: 3000 });
      })
      .catch(error => {
          toast.add({ severity: 'warning', summary: 'Error al crear la base de datos', detail: error.response.data.message, life: 3000 });
          console.log(error.response.data.message)
      })
}

const deleteUserDBView = async (id) => {
  deleteUserDB(id).then(response => {
          toast.add({ severity: 'info', summary: 'Base de datos creada', detail: 'Base de datos creada correctamente.', life: 3000 });
      })
      .catch(error => {
          toast.add({ severity: 'warning', summary: 'Error al crear la base de datos', detail: error.response.data.message, life: 3000 });
          console.log(error.response.data.message)
      })
}

const getUserImageUrl = () => {
  try {
    // 1. Verificar si es uno de los usuarios testimoniales predefinidos
    const testimonialUsers = [
      'elena.martinez@example.com',
      'carlos.rodriguez@example.com',
      'laura.gomez@example.com',
      'miguel.angel@example.com',
      'sofia.herrera@example.com',
      'javier.torres@example.com'
    ];
    
    if (user.email && testimonialUsers.includes(user.email.toLowerCase())) {
      // Mapeo de emails a nombres de archivos
      const emailToImage = {
        'elena.martinez@example.com': '/images/testimonials/elena.webp',
        'carlos.rodriguez@example.com': '/images/testimonials/carlos.webp',
        'laura.gomez@example.com': '/images/testimonials/laura.webp',
        'miguel.angel@example.com': '/images/testimonials/miguel.webp',
        'sofia.herrera@example.com': '/images/testimonials/sofia.webp',
        'javier.torres@example.com': '/images/testimonials/javier.webp'
      };
      
      return emailToImage[user.email.toLowerCase()];
    }
    
    // 2. Si el usuario tiene un objeto media o información de media
    if (user.media && Array.isArray(user.media) && user.media.length > 0) {
      const media = user.media[0];
      if (media.original_url) return media.original_url;
      if (media.url) return media.url;
    }
    
    // 3. Si tiene un avatar en la colección de Media Library (usuarios normales)
    if (user.avatar) {
      // Tratar diferentes formatos de URL
      if (typeof user.avatar === 'string') {
        // Determinar si es una ruta de storage o una URL completa
        if (user.avatar.includes('/storage/')) {
          return user.avatar; // Ya tiene el formato correcto
        } else if (user.avatar.startsWith('/')) {
          // Asegurarse de que apunta a /storage/ si viene de MediaLibrary
          if (user.avatar.includes('/app/public/')) {
            return user.avatar.replace('/app/public/', '/storage/');
          }
          return user.avatar;
        } else if (user.avatar.startsWith('http')) {
          return user.avatar;
        } else {
          // Si es una ruta relativa sin barra al inicio
          return `/${user.avatar}`;
        }
      }
    }
    
    // 4. NUEVA LÓGICA: Probar directamente con el ID del usuario
    if (user.id) {
      // Intentar acceder a la imagen en la carpeta del usuario por ID
      // Como no conocemos el nombre del archivo, pero sabemos que debe estar en la carpeta con el ID del usuario
      return `/storage/${user.id}/avatar-${user.id}`; // Intenta con nombre probable
    }
    
    // 5. Si tiene una imagen definida en photo_path (otra ruta específica)
    if (user.photo_path) {
      return user.photo_path;
    }
    
    // 6. Si tiene una imagen definida en image
    if (user.image) {
      if (user.image.startsWith('testimonials/')) {
        return `/images/${user.image}`;
      }
      return `/images/testimonials/${user.image}`;
    }

    console.log('Usando imagen por defecto para:', user.email);
    
    // 7. Imagen por defecto si no hay nada más
    return '/images/default-avatar.png';
  } catch (error) {
    console.error('Error al generar URL de imagen:', error);
    return '/images/default-avatar.png';
  }
};
const handleImageError = async (e) => {
  const currentSrc = e.target.src;
  console.error('Error al cargar la imagen de perfil:', currentSrc);
  
  // Si ya intentamos demasiadas veces, usar imagen por defecto
  if (e.target.dataset.retryCount >= 3) {
    console.log('Demasiados intentos, usando imagen por defecto');
    e.target.src = '/images/default-avatar.png';
    return;
  }
  
  // Inicializar contador de intentos
  e.target.dataset.retryCount = parseInt(e.target.dataset.retryCount || '0') + 1;
  
  // Para el primer intento, consultar al backend por el archivo real
  if (parseInt(e.target.dataset.retryCount) === 1 && user.id) {
    try {
      // Solicitar al backend la lista de archivos en la carpeta del usuario
      const response = await axios.get(`/api/user/${user.id}/media-files`);
      
      if (response.data && response.data.avatar_url) {
        // Corregir la URL si tiene doble barra
        let fixedUrl = response.data.avatar_url.replace('//', '/');
        // Pero mantener http:// o https://
        fixedUrl = fixedUrl.replace('http:/', 'http://').replace('https:/', 'https://');
        
        console.log('Imagen encontrada en el servidor (URL corregida):', fixedUrl);
        e.target.src = fixedUrl;
        return;
      }
    } catch (error) {
      console.error('Error al consultar archivos de usuario:', error);
    }
  }
  
  // Si el servidor no devolvió ninguna URL específica, intentar solo con el ID del usuario
  if (user.id) {
    const baseUrl = `/storage/${user.id}/`;
    
    try {
      // Hacer una solicitud para obtener la lista real de archivos en el directorio
      const filesResponse = await axios.get(`/api/user/${user.id}/list-files`);
      
      if (filesResponse.data && filesResponse.data.files && filesResponse.data.files.length > 0) {
        // Usar el primer archivo de imagen encontrado
        const imageFile = filesResponse.data.files[0];
        console.log(`Usando archivo encontrado en directorio: ${baseUrl}${imageFile}`);
        e.target.src = `${baseUrl}${imageFile}`;
        return;
      }
    } catch (listError) {
      console.error('Error al listar archivos del usuario:', listError);
    }
  }
  
  // Si nada funciona, usar imagen por defecto
  e.target.src = '/images/default-avatar.png';
};
const onTemplatedUpload = (event) => {
  // console.log('onTemplatedUpload');
  // console.log(event);
};

const formatSize = (bytes) => {
  const k = 1024;
  const dm = 3;
  const sizes = $primevue.config.locale.fileSizeTypes;

  if (bytes === 0) {
      return `0 ${sizes[0]}`;
  }

  const i = Math.floor(Math.log(bytes) / Math.log(k));
  const formattedSize = parseFloat((bytes / Math.pow(k, i)).toFixed(dm));

  return `${formattedSize} ${sizes[i]}`;
};

</script>

<style>
.fu-content {
  padding: 0px !important;
  border: 0px !important;
  border-radius: 6px;
}

.fu-header {
  border: 0px !important;
  border-radius: 6px;
}

.fu {
  display: flex;
  flex-direction: column-reverse;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}


.img-profile {
  border-top-right-radius: 6px;
  border-top-left-radius: 6px;
  aspect-ratio: 1/1;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  margin-bottom: 0.3rem;
}
.user-image-container {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  margin: 0 auto;
  border: 3px solid #f0f0f0;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.img-profile {
  width: 100%;
  height: 100%;
  object-fit: cover;
}   
</style>