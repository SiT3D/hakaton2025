import { createRouter, createWebHistory } from 'vue-router'
import Register from '../views/Register.vue'
import Login from '../views/Login.vue'
import List from '../views/List.vue'
import PlotCreate from '../views/PlotCreatePage.vue'

const routes = [
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/list', component: List, meta: { requiresAuth: true } },
    { path: '/create', component: PlotCreate, meta: { requiresAuth: true } },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth) {
        const token = localStorage.getItem('token')
        if (!token) {
            return next('/login')
        }
    }
    next()
})

export default router
