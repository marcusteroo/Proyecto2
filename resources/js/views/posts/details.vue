<template>
    <div class="container">
        <div class="row g-5 mt-4">
            <div class="col-md-8">
                <h3 class="pb-4 mb-4 fst-italic border-bottom">
                    {{ post?.title }}
                </h3>
                <p class="blog-post-meta">1 de Enero de 2024 by <a href="#">{{ post?.user?.name}}</a></p>

                <article class="blog-post">
                    <!-- Aquí está el cambio principal para mostrar las imágenes -->
                    <div v-if="post?.media && post.media.length > 0" class="mb-4">
                        <div v-for="image in post.media" :key="image.id" class="mb-3">
                            <img :src="image.original_url" :alt="image.name || 'Imagen del post'" class="img-fluid">
                        </div>
                    </div>
                    <div class="mt-4" v-html="post?.content"></div>
                </article>

                <nav class="blog-pagination" aria-label="Pagination">
                    <a class="btn btn-outline-primary rounded-pill" href="#">Más antigua</a>
                    <a class="btn btn-outline-secondary rounded-pill disabled">Más nueva</a>
                </nav>
            </div>

            <div class="col-md-4">
                <!-- Resto del código... -->
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { useRoute } from "vue-router";

const post = ref(null);
const categories = ref([]);
const route = useRoute();

onMounted(() => {
    // Obtener el post con su media
    axios.get('/api/get-post/' + route.params.id)
        .then(response => {
            // Ajustamos cómo accedemos a los datos
            // Dependiendo de tu API, podría ser response.data o response.data.data
            post.value = response.data;
            console.log('Media recibida:', post.value.media); // Para debugging
        })
        .catch(error => {
            console.error('Error fetching post:', error);
        });
    
    // Obtener la lista de categorías
    axios.get('/api/category-list')
        .then(response => {
            categories.value = response.data.data;
        })
        .catch(error => {
            console.error('Error fetching categories:', error);
        });
});
</script>