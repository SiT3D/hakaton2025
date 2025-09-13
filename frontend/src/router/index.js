import { createRouter, createWebHistory } from 'vue-router'
import Register from '../views/Register.vue'
import Login from '../views/Login.vue'
import List from '../views/List.vue'
import PlotCreate from '../views/PlotCreatePage.vue'
import Logout from '../views/Logout.vue'
import User from '../views/User.vue'

const routes = [
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/logout', component: Logout },
    { path: '/list', component: List, meta: { requiresAuth: true } },
    { path: '/create', component: PlotCreate, meta: { requiresAuth: true } },
    { path: '/user', component: User, meta: { requiresAuth: true } },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')

    if (to.meta.requiresAuth && !token) {
        return next('/login')
    }

    if (token && (to.path === '/login' || to.path === '/register')) {
        return next('/list')
    }

    return next()
})

export default router
