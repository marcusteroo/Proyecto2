import { authStore } from "../store/auth";

const AuthenticatedLayout = () => import('../layouts/Authenticated.vue')
const AuthenticatedUserLayout = () => import('../layouts/AuthenticatedUser.vue')
const GuestLayout = ()  => import('../layouts/Guest.vue');
const PostsIndex  = ()  => import('../views/admin/posts/Index.vue');
const PostsCreate  = ()  => import('../views/admin/posts/Create.vue');
const PostsEdit  = ()  => import('../views/admin/posts/Edit.vue');
const PreciosIndex = () => import('../views/admin/precios/Index.vue');

async function requireLogin(to, from, next) {
    const auth = authStore();
    let isLogin = !!auth.authenticated;

    if (isLogin) {
        next()
    } else {
        next('/login')
    }
}

function hasAdmin(roles) {
    for (let rol of roles) {
        if (rol.name && rol.name.toLowerCase().includes('admin')) {
            return true;
        }
    }
    return false;
}
async function guest(to, from, next) {
    const auth = authStore()

    let isLogin = !!auth.authenticated;

    if (isLogin) {
        next('/')
    } else {
        next()
    }
}

async function requireAdmin(to, from, next) {

    const auth = authStore();
    let isLogin = !!auth.authenticated;
    let user = auth.user;

    if (isLogin) {
        if( hasAdmin(user.roles)){
            next()
        }else{
            next('/app')
        }
    } else {
        next('/login')
    }
}

export default [
    {
        path: '/',
        // redirect: { name: 'login' },
        component: GuestLayout,
        children: [

            {
                path: '/',
                name: 'home',
                component: () => import('../views/home/index.vue'),
            },
            {
                path: 'posts',
                name: 'public-pos ts.index',
                component: () => import('../views/posts/index.vue'),
            },
            {
                path: 'posts/:id',
                name: 'public-posts.details',
                component: () => import('../views/posts/details.vue'),
            },
            {
                path: 'category/:id',
                name: 'category-posts.index',
                component: () => import('../views/category/posts.vue'),
            },
            {
                path: 'login',
                name: 'auth.login',
                component: () => import('../views/login/Login.vue'),
                beforeEnter: guest,
            },
            {
                path: 'register',
                name: 'auth.register',
                component: () => import('../views/register/index.vue'),
                beforeEnter: guest,
            },
            {
                path: 'forgot-password',
                name: 'auth.forgot-password',
                component: () => import('../views/auth/passwords/Email.vue'),
                beforeEnter: guest,
            },
            {
                path: 'reset-password/:token',
                name: 'auth.reset-password',
                component: () => import('../views/auth/passwords/Reset.vue'),
                beforeEnter: guest,
            },
        ]
    },

    {
        path: '/app',
        component: AuthenticatedUserLayout,
        beforeEnter: requireLogin,
        redirect: { name: 'user.kanbans' },
        children: [
            {
                path: 'flows/flow',
                name: 'user.flows',
                component: () => import('../views/user/flows/index.vue'),
            },
            {
                path: 'kanbans',
                name: 'user.kanbans',
                component: () => import('../views/user/kanban/kanbans.vue'),
                meta: { breadCrumb: 'Kanbans' }
            },
            {
                path: 'kanban/:id',
                name: 'user.kanban',
                component: () => import('../views/user/kanban/index.vue'),
                meta: { breadCrumb: 'Kanban' }
            },
            {
                name: 'user.favoritos',
                path: 'favoritos',
                component: () => import('../views/user/favoritos/Index.vue'),
                meta: { breadCrumb: 'Favoritos' }
            },
            
            {
                path: 'flows',
                name: 'user.flows.flows',
                component: () => import('../views/user/flows/flows.vue'),
            },
            {
                path: 'settings',
                name: 'user.settings',
                component: () => import('../views/user/settings/Index.vue'),
                meta: { breadCrumb: 'Configuración' }
            },
            {
                path: 'rate',
                name: 'user.rate',
                component: () => import('../views/user/rating/Index.vue'), 
                meta: { breadCrumb: 'Valorar' }
            }              
        ]

    },



    {
        path: '/admin',
        component: AuthenticatedLayout,
        // redirect: {
        //     name: 'admin.index'
        // },
        beforeEnter: requireAdmin,
        meta: { breadCrumb: 'Dashboard' },
        children: [
            {
                name: 'admin.index',
                path: '',
                component: () => import('../views/admin/index.vue'),
                meta: { breadCrumb: 'Admin' }
            },
            {
                name: 'profile.index',
                path: 'profile',
                component: () => import('../views/admin/profile/index.vue'),
                meta: { breadCrumb: 'Profile' }
            },
            {
                name: 'posts.index',
                path: 'posts',
                component: PostsIndex,
                meta: { breadCrumb: 'Posts' }
            },
            {
                name: 'posts.create',
                path: 'posts/create',
                component: PostsCreate,
                meta: { breadCrumb: 'Add new post' }
            },
            {
                name: 'posts.edit',
                path: 'posts/edit/:id',
                component: PostsEdit,
                meta: { breadCrumb: 'Edit post' }
            },
            {
                name: 'categories',
                path: 'categories',
                meta: { breadCrumb: 'Categories'},
                children: [
                    {
                        name: 'categories.index',
                        path: '',
                        component: () => import('../views/admin/categories/Index.vue'),
                        meta: { breadCrumb: 'View category' }
                    },
                    {
                        name: 'categories.create',
                        path: 'create',
                        component: () => import('../views/admin/categories/Create.vue'),
                        meta: {
                            breadCrumb: 'Add new category' ,
                            linked: false,
                        }
                    },
                    {
                        name: 'categories.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/categories/Edit.vue'),
                        meta: {
                            breadCrumb: 'Edit category',
                            linked: false,
                        }
                    }
                ]
            },
            {
                name: 'permissions',
                path: 'permissions',
                meta: { breadCrumb: 'Permisos'},
                children: [
                    {
                        name: 'permissions.index',
                        path: '',
                        component: () => import('../views/admin/permissions/Index.vue'),
                        meta: { breadCrumb: 'Permissions' }
                    },
                    {
                        name: 'permissions.create',
                        path: 'create',
                        component: () => import('../views/admin/permissions/Create.vue'),
                        meta: {
                            breadCrumb: 'Create Permission',
                            linked: false,
                        }
                    },
                    {
                        name: 'permissions.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/permissions/Edit.vue'),
                        meta: {
                            breadCrumb: 'Permission Edit',
                            linked: false,
                        }
                    }
                ]
            },
            {
                name: 'users',
                path: 'users',
                meta: { breadCrumb: 'Usuarios'},
                children: [
                    {
                        name: 'users.index',
                        path: '',
                        component: () => import('../views/admin/users/Index.vue'),
                        meta: { breadCrumb: 'Usuarios' }
                    },
                    {
                        name: 'users.create',
                        path: 'create',
                        component: () => import('../views/admin/users/Create.vue'),
                        meta: {
                            breadCrumb: 'Crear Usuario',
                            linked: false
                        }
                    },
                    {
                        name: 'users.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/users/Edit.vue'),
                        meta: {
                            breadCrumb: 'Editar Usuario',
                            linked: false
                        }
                    }
                ]
            },
            {
                name: 'kanbans',
                path: 'kanbans',
                meta: { breadCrumb: 'Kanbans' },
                children: [
                    {
                        name: 'kanbans.index',
                        path: '',
                        component: () => import('../views/admin/kanbans/Index.vue'),
                        meta: { breadCrumb: 'Kanbans' } // Antes: 'Usuarios'
                    },
                    {
                        name: 'kanbans.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/kanbans/Edit.vue'),
                        meta: {
                            breadCrumb: 'Editar Kanban', // Antes: 'Editar Usuario'
                            linked: false
                        }
                    }
                ]
            },
            {
                name: 'tareas',
                path: 'tareas',
                meta: { breadCrumb: 'Tareas' },
                children: [
                    {
                        name: 'tareas.index',
                        path: '',
                        component: () => import('../views/admin/tareas/Index.vue'),
                        meta: { breadCrumb: 'Tareas' } // Antes: 'Usuarios'
                    },
                    {
                        name: 'tareas.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/tareas/Edit.vue'),
                        meta: {
                            breadCrumb: 'Editar Tarea', // Antes: 'Editar Usuario'
                            linked: false
                        }
                    }
                ]
            },
            {
                name: 'workflows',
                path: 'workflows',
                meta: { breadCrumb: 'Workflows' },
                children: [
                    {
                        name: 'workflows.index',
                        path: '',
                        component: () => import('../views/admin/workflows/Index.vue'),
                        meta: { breadCrumb: 'Workflows' } // Antes: 'Usuarios'
                    },
                    {
                        name: 'workflows.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/workflows/Edit.vue'),
                        meta: {
                            breadCrumb: 'Editar Workflow', // Antes: 'Editar Usuario'
                            linked: false
                        }
                    }
                ]
            },
            //TODO Organizar rutas
            {
                name: 'roles.index',
                path: 'roles',
                component: () => import('../views/admin/roles/Index.vue'),
                meta: { breadCrumb: 'Roles' }
            },
            {
                name: 'roles.create',
                path: 'roles/create',
                component: () => import('../views/admin/roles/Create.vue'),
                meta: { breadCrumb: 'Create Role' }
            },
            {
                name: 'roles.edit',
                path: 'roles/edit/:id',
                component: () => import('../views/admin/roles/Edit.vue'),
                meta: { breadCrumb: 'Role Edit' }
            },
            {
                name: 'ratings.admin',
                path: 'ratings',
                component: () => import('../views/admin/ratings/Index.vue'),
                meta: { breadCrumb: 'Reseñas Destacadas' }
            },
            {
                name: 'precios.index',
                path: 'precios',
                component: () => import('../views/admin/precios/Index.vue'),
                meta: { breadCrumb: 'Precios' }

            }

        ]
    },
    {
        path: "/:pathMatch(.*)*",
        name: 'NotFound',
        component: () => import("../views/errors/404.vue"),
    },
];
